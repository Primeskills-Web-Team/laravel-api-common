<?php

namespace Primeskills\ApiCommon\Traits;

use Symfony\Component\Console\Output\ConsoleOutput;

class Commands
{
    /**
     * @var ConsoleOutput
     */
    private $output;

    /**
     * @param ConsoleOutput $output
     */
    public function __construct(ConsoleOutput $output)
    {
        $this->output = $output;
    }

    /**
     * @param string $string
     * @param mixed ...$other
     * @return void
     */
    public function info(string $string, ...$other)
    {
        foreach ($other as $e) {
            $string .= "\n" . $e;
        }
        $this->line($string, 'info');
    }

    /**
     * @param string $string
     * @return void
     */
    public function error(string $string)
    {
        $this->line($string, 'error');
    }

    /**
     * @param $string
     * @param $style
     * @return void
     */
    private function line($string, $style = null)
    {
        $string = sprintf("%s [ %s ] -> %s", date("Y-m-d h:i:s"), strtoupper($style), $string);
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled);
    }
}
