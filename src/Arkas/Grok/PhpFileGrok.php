<?php
namespace Arkas\Grok;

/**
 * Custom search for PHP files
 *
 * Currently uses a very simplistic regular expression based approach that parses line-by-line and attempts to keep
 * track of the current Function and Class, with the hope that such information will be useful for tracking down all
 * references to troublesome code.
 *
 * Some limitations:
 *   - Line-by-line approach has serious drawbacks, and a lexer-based approach would be preferable
 *   - Comments are not ignored, which can taint the results
 */
class PhpFileGrok extends DefaultFileGrok
{
    /**
     * @param $keyword The needle
     * @return array|bool An array of PhpGrokResult objects
     */
    public function grok($keyword)
    {
        if (!file_exists($this->filename)) return false;

        $data = file($this->filename);
        $results = array();

        $this->current_class = null;
        $this->current_function = null;

        foreach ($data as $key => $value)
        {
            //Keep track of the current Class and Function
            $this->setCurrentClass($value, $key);
            $this->setCurrentFunction($value, $key);

            //Run the keyword search
            if (false !== strpos($value, $keyword))
            {
                $results[] = new PhpGrokResult(
                    $key,
                    rtrim($value),
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
            'class' => $this->current_class,
            'function' => $this->current_function,
        );
    }

    public function setCurrentClass($line, $line_number)
    {
        $type = 'class';
        $pattern = '/' . $type . ' ([a-zA-Z0-9_]+)/';

        $result = preg_match($pattern, $line, $matches);

        if ($result)
        {
            $this->current_class = new GrokResult($line_number, trim($line), $matches[1], '    > ' . $type);
            $this->current_function = null;
        }
    }

    public function setCurrentFunction($line, $line_number)
    {
        $type = 'function';
        $pattern = '/' . $type . ' ([a-zA-Z0-9_]+) *\(/';

        $result = preg_match($pattern, $line, $matches);

        if ($result)
        {
            $this->current_function = new GrokResult($line_number, trim($line), $matches[1], '    > ' . $type);
        }
    }
}
