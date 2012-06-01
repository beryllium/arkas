<?php

namespace Arkas\Command;

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
            ->addArgument('dir', InputArgument::OPTIONAL, 'Directory to begin searching from', getcwd() );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $keyword = $input->getArgument('keyword');
        $dir = $input->getArgument( 'dir' );

        $output->writeln('Search for ' . $keyword . ' in ' . $dir );
    }
}
