<?php
namespace Arkas;

class GrokFactory
{
  public function __construct()
  {
  }
   
  public function getGrok( $filename )
  {
    if ( $filename instanceof \SplFileInfo )
    {
      $ext = $filename->getExtension();
    }
    else
    {
      $ext = pathinfo( basename( $filename ), PATHINFO_EXTENSION );
    }

    switch( strtolower( $ext ) )
    {
    case 'php':
      $grok = new Grok\PhpFileGrok();
      $grok->file( $filename );
      break;
    default:
      $grok = new Grok\DefaultFileGrok();
      $grok->file( $filename );
      break;
    }

    return $grok;
  }
}
