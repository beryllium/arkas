<?php

namespace Arkas;

use Symfony\Component\Console\Application as ParentApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleApplication extends ParentApplication
{
  public function doRun( InputInterface $input, OutputInterface $output )
  {
    $name = $this->getCommandName( $input );

    //Check for some scenarios before passing to the new default command ('search')
    switch ( $name )
    {
    case '--help':
    case '-h':
      //This might affect some Silex/Cilex/Arkas use cases, needs testing
      $input = new ArrayInput( array( 'command' => 'help' ) );
      break;
    case '--list':
      $input = new ArrayInput( array( 'command' => 'list' ) );
      break;
    default:
      //This is the money shot. Create a default command and pass it to the Console component.
      $args = $_SERVER[ 'argv' ];
      array_shift( $args );
      array_unshift( $args, 'throwaway', 'arkas:search' );
      $input = new ArgvInput( $args );
      break;
    }

    return parent::doRun( $input, $output );
  }
}
