<?php
namespace Arkas\Grok;

class GrokResult
{
    public $line;
    public $data;
    public $keyword;
    public $filename;

    /**
     * @param $line Line number of the match
     * @param $data Raw text of the line
     * @param $keyword The keyword that was matched
     * @param $filename The filename of the matching file
     */
    public function __construct($line, $data, $keyword, $filename)
    {
        $this->line = $line;
        $this->data = $data;
        $this->keyword = $keyword;
        $this->filename = $filename;
    }

    /**
     * Output the result
     *
     * @param $output An output interface (requires ->writeln() method)
     * @param string $color Optional colour for symfony2 console highlighting
     */
    public function output($output, $color = 'info')
    {
        $open_color = '<' . $color . '>';
        $close_color = '</' . $color . '>';

        $data = str_replace($this->keyword, $open_color . $this->keyword . $close_color, $this->data);
        $output->writeln($this->filename . ':' . $this->line . ":\t" . rtrim($data));
    }
}
