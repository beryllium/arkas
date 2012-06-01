<?php
namespace Arkas\Grok;

class PhpFileGrok extends DefaultFileGrok
{
  public function grok( $keyword )
  {
    if ( !file_exists( $this->filename ) ) return false;

    $data = file( $this->filename );
    $results = array();

    $this->current_class = null;
    $this->current_function = null;

    foreach( $data as $key=>$value )
    {
      $this->setCurrentClass( $value, $key);
      $this->setCurrentFunction( $value, $key);
      if ( false !== strpos( $value, $keyword ) )
      {
        $results[] = new PhpGrokResult( 
          $key, 
          rtrim( $value ), 
          $keyword, 
          $this->filename, 
          $this->currentState()
        );
      }
    }

    return $results;
  }

  public function currentState()
  {
    return array(
      'class'=>$this->current_class,
      'function'=>$this->current_function,
    );
  }

  public function setCurrentClass( $line, $line_number )
  {
    $type = 'class';
    $pattern = '/' . $type . ' ([a-zA-Z0-9_]+)/';

    $result = preg_match( $pattern, $line, $matches );

    if ( $result )
    {
      $this->current_class = new GrokResult( $line_number, trim($line), $matches[1], '    > ' . $type );
      $this->current_function = null;
    }
  }

  public function setCurrentFunction( $line, $line_number )
  {
    $type = 'function';
    $pattern = '/' . $type . ' ([a-zA-Z0-9_]+) *\(/';

    $result = preg_match( $pattern, $line, $matches );

    if ( $result )
    {
      $this->current_function = new GrokResult( $line_number, trim($line), $matches[1], '    > ' . $type );
    }
  }
}
