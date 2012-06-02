<?php
namespace Arkas;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class Compiler
{
    protected $version;

    /**
     * Compiles the Arkas source code into one single Phar file.
     *
     * @param string $pharFile Name of the output Phar file
     */
    public function compile($pharFile = 'arkas.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $process = new Process('git log --pretty="%h %ci" -n1 HEAD');
        if ($process->run() > 0) {
            throw new \RuntimeException('The git binary cannot be found.');
        }
        $this->version = trim($process->getOutput());

        $phar = new \Phar($pharFile, 0, 'arkas.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('Compiler.php')
            ->in(__DIR__.'/..')
            ->in(__DIR__.'/../../vendor/pimple/pimple/lib')
            ->in(__DIR__.'/../../vendor/symfony/class-loader/Symfony/Component/ClassLoader')
            ->in(__DIR__.'/../../vendor/symfony/console/Symfony/Component/Console')
        ;

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../LICENSE'), false);

        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/autoload.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/ClassLoader.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_namespaces.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_classmap.php'));
		
        // Stubs
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        // $phar->compressFiles(\Phar::GZ);

        unset($phar);
    }

    protected function addFile(\Phar $phar, \splFileInfo $file, $strip = true)
    {
        $path = str_replace(
            dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', $file->getRealPath()
        );

        $content = file_get_contents($file);
        if ($strip) {
            $content = self::stripWhitespace($content);
        }
        $content = str_replace('@package_version@', $this->version, $content);

        $phar->addFromString($path, $content);
    }

    protected function getStub()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php

/*
 * This file is part of the Arkas code search tool.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Phar::mapPhar('arkas.phar');

require_once 'phar://arkas.phar/vendor/autoload.php';

if ('cli' === php_sapi_name() && basename(__FILE__) === basename($_SERVER['argv'][0]) && isset($_SERVER['argv'][1])) {
    switch ($_SERVER['argv'][1]) {
        case '--update':
            $remoteFilename = 'http://arkas.whateverthing.com/get/arkas.phar';
            $localFilename = __DIR__.'/arkas.phar';

            file_put_contents($localFilename, file_get_contents($remoteFilename));
            break;

        case '--check':
            $latest = trim(file_get_contents('http://arkas.whateverthing.com/get/version'));

            if ($latest != Arkas\Application::VERSION) {
                printf("A newer Arkas version is available (%s).\n", $latest);
            } else {
                print("You are using the latest Arkas version.\n");
            }
            break;

        case '--version':
            printf("Arkas version %s\n", Arkas\Application::VERSION);
            break;

        default:
          break;
    }
}

$app = new \Arkas\Application('Arkas');
$app['grok_factory'] = function ( $filename ) use ( $app ) {
  $grok = new \Arkas\GrokFactory( $filename );
  return $grok;
};

$app['arkas_settings'] = function ( $c ) {
  $settings = array();
  $global_settings = array();
  $user_settings = array();
  
  $default_settings = array(
    'dir_excludes' => array(
      '.svn',
      '.git',
    ),
    'file_excludes' => array(
      '.phar',
      '.tgz',
      '.gz',
      '.zip',
    ),
  );

  if ( file_exists( $_SERVER[ 'HOME' ] . '.arkas' ) )
  {
    $user_settings = parse_ini_file( $_SERVER[ 'HOME' ] . '.arkas' );
  }

  if ( file_exists( '/etc/arkas.config' ) )
  {
    $global_settings = parse_ini_file( '/etc/arkas.config' );
  }

  $settings = array_merge( $default_settings, $global_settings, $user_settings );

  return $settings;
};

$app->command( new \Arkas\Command\SearchCommand() );
$app->run();

__HALT_COMPILER();
EOF;
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * Based on Kernel::stripComments(), but keeps line numbers intact.
     *
     * @param string $source A PHP string
     *
     * @return string The PHP string with the whitespace removed
     */
    static public function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }
}
