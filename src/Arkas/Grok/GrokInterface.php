<?php
namespace Arkas\Grok;

interface GrokInterface
{
  public function setData( $data );
  public function grok( $keyword );
}
