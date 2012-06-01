<?php

namespace Arkas\Command;

use Arkas\Grok;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Example command for testing purposes.
 */
class SearchCommand extends Command
{
    protected function configure()
    {
      $this
        ->setName('arkas:search')
        ->setDescription('Search for code within a directory')
        ->addArgument('keyword', InputArgument::REQUIRED, 'Keyword to search for')
        ->addArgument('dir', InputArgument::OPTIONAL, 'Directory to begin searching from', './' );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $keyword = $input->getArgument('keyword');
        $dir = $input->getArgument( 'dir' );
        $realtime=false;
        $results = array();

        $output->writeln('Search for ' . $keyword . ' in ' . $dir . ' ...' . "\n" );

        if ( is_dir( $dir ) )
        {
          $begin = new \RecursiveDirectoryIterator( $dir );

          foreach( new \RecursiveIteratorIterator( $begin ) as $filename=>$curfile )
          {
            //$output->writeln( 'Checking ' . $filename );
            $grok = $this->container[ 'grok_factory' ]->getGrok($filename);
            $grok->file( $filename );
            $result = $grok->grok( $keyword );

            if ( $result )
            {
              if ( $realtime )
              {
                foreach( $result as $grok_result )
                {
                  $grok_result->output($output);
                }
              }

              $results[] = $result;
            }
          }

          if ( !$realtime )
          {
            foreach( $results as $result )
            {
              foreach( $result as $grok_result )
              {
                $grok_result->output($output);
              }
            }
          }

          $output->writeln( "\n" . count( $results ) . ' files matched' );

        }
    }
}
