<?php
namespace Arkas;

use Cilex\Compiler as CilexCompiler;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class Compiler extends CilexCompiler
{
  public function compile($pharFile = 'arkas')
  {
    return parent::compile( $pharFile );
  }

  public function getStub()
  {
    return <<<'EOF'
<?php

/*
 * This file is part of the Arkas source code search tool, based on the Cilex framework.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Phar::mapPhar('arkas');

require_once 'phar://arkas/vendor/autoload.php';

if ('cli' === php_sapi_name() && basename(__FILE__) === basename($_SERVER['argv'][0]) && isset($_SERVER['argv'][1])) {
    switch ($_SERVER['argv'][1]) {
        case '--update':
            $remoteFilename = 'http://arkas.whateverthing.com/get/arkas.phar';
            $localFilename = __DIR__.'/arkas';

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

        case 'version':
            printf("Arkas version %s\n", Arkas\Application::VERSION);
            break;

        default:
            printf("Unknown command '%s' (available commands: version, check, and update).\n", $_SERVER['argv'][1]);
    }

    exit(0);
}

__HALT_COMPILER();
EOF;
  }
}
