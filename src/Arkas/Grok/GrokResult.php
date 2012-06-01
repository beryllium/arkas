<?php
namespace Arkas\Grok;

class GrokResult
{
  public $line;
  public $data;
  public $keyword;
  public $filename;

  public function __construct( $line, $data, $keyword, $filename )
  {
    $this->line = $line;
    $this->data = $data;
    $this->keyword = $keyword;
    $this->filename = $filename;
  }

  public function output( $output, $color = 'info' )
  {
    $open_color = '<' . $color . '>';
    $close_color = '</' . $color . '>';

    $data = str_replace( $this->keyword, $open_color . $this->keyword . $close_color, $this->data );
    $output->writeln( $this->filename . ':' . $this->line . ":\t" . rtrim($data) );
  }
}
