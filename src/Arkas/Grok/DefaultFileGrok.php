<?php
namespace Arkas\Grok;

/**
 * Basic keyword searching using strpos().
 */
class DefaultFileGrok implements GrokInterface
{
    public $filename;

    public function file($filename)
    {
        $this->setData($filename);
    }

    public function setData($data)
    {
        $this->filename = $data;
    }

    /**
     * Run the search on the current filename using a simple strpos()
     *
     * @param $keyword The needle
     * @return array|bool An array of GrokResult objects
     */
    public function grok($keyword)
    {
        if (!file_exists($this->filename)) return false;

        //Open the file and iterate through, matching keywords to the provided one
        $data = file($this->filename);
        $results = array();

        foreach ($data as $key => $value)
        {
            if (false !== strpos($value, $keyword))
            {
                $results[] = new GrokResult($key, $value, $keyword, $this->filename);
            }
        }

        return $results;
    }
}
