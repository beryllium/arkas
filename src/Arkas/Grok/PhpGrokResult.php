<?php
namespace Arkas\Grok;

class PhpGrokResult
{
  public $line;
  public $data;
  public $keyword;
  public $filename;
  public $state;

  public function __construct( $line, $data, $keyword, $filename, $current_state = array() )
  {
    $this->line = $line;
    $this->data = $data;
    $this->keyword = $keyword;
    $this->filename = $filename;
    $this->state = $current_state;
  }

  public function output( $output )
  {
    $data = str_replace( $this->keyword, '<info>' . $this->keyword . '</info>', $this->data );
    $output->writeln( $this->filename . ':' . $this->line . ":\t" . rtrim($data) );
    if ( !empty( $this->state[ 'class' ] ) ) $this->state[ 'class' ]->output( $output, 'comment' ); 
    if ( !empty( $this->state[ 'function' ] ) ) $this->state[ 'function' ]->output( $output, 'comment' ); 
  }
}
