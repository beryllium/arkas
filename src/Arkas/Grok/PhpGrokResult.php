<?php
namespace Arkas\Grok;

class PhpGrokResult extends GrokResult
{
  public $state;

  public function __construct( $line, $data, $keyword, $filename, $current_state = array() )
  {
    parent::__construct( $line, $data, $keyword, $filename );
    $this->state = $current_state;
  }

  public function output( $output, $color = 'info' )
  {
    $start_color = '<' . $color . '>';
    $end_color = '</' . $color . '>';

    $data = str_replace( $this->keyword, $start_color . $this->keyword . $end_color, $this->data );
    $output->writeln( $this->filename . ':' . $this->line . ":\t" . rtrim($data) );
    if ( !empty( $this->state[ 'class' ] ) ) $this->state[ 'class' ]->output( $output, 'comment' ); 
    if ( !empty( $this->state[ 'function' ] ) ) $this->state[ 'function' ]->output( $output, 'comment' ); 
  }
}
