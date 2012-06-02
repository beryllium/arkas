<?php

namespace Arkas\Command;

use Arkas\Grok;
use Arkas\ArkasFilterIterator;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
          
          $dir_excludes = $this->container[ 'arkas_settings' ][ 'dir_excludes' ];
          $file_excludes = $this->container[ 'arkas_settings' ][ 'file_excludes' ];

          $chaff = new ArkasFilterIterator( $begin );
          $chaff->setDirFilters( $dir_excludes );
          $chaff->setFileFilters( $file_excludes );

          foreach( new \RecursiveIteratorIterator( $chaff, \RecursiveIteratorIterator::SELF_FIRST ) as $filename=>$curfile )
          {
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
