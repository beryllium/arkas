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
}
