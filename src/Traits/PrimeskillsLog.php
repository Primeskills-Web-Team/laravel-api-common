<?php

namespace Primeskills\ApiCommon\Traits;

use Symfony\Component\Console\Output\ConsoleOutput;

trait PrimeskillsLog
{

    /**
     * @return Commands
     */
    public function write(): Commands
    {
        return new Commands(new ConsoleOutput());
    }
}
