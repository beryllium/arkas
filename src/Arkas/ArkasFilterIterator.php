<?php
namespace Arkas;

/**
 * Custom Filter Iterator to let us skip folders like .svn, .git, as well as ignoring files by extension
 */
class ArkasFilterIterator extends \RecursiveFilterIterator
{
  public static $DIR_FILTERS = array();
  public static $FILE_FILTERS = array();

  public function setDirFilters( $filters )
  {
    foreach( $filters as &$value ) $value = trim( $value );
    self::$DIR_FILTERS = $filters;
  }

  public function setFileFilters( $filters )
  {
    foreach( $filters as &$value ) $value = trim( $value );
    self::$FILE_FILTERS = $filters;
  }

  public function accept() {
    if ( $this->current()->isDir() )
    {
      if ( in_array( $this->current()->getFilename(), self::$DIR_FILTERS) )
      {
        return false;
      }
    }
    else 
    {
      if ( in_array( '.' . pathinfo( $this->current()->getFilename(), PATHINFO_EXTENSION ), self::$FILE_FILTERS ) )
      {
        return false;
      }
    }

    return true;
  }
}
