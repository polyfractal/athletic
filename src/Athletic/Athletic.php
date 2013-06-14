<?php
/**
 * User: zach
 * Date: 6/14/13
 * Time: 11:48 AM
 */

namespace Athletic;


use Commando;

class Athletic
{
    /** @var  Commando\Command() */
    private $cmdArgs;

    public function __construct()
    {
        $this->initializeCmdArgs();
    }

    public function run()
    {
        echo $this->cmdArgs['path'];
    }

    private function initializeCmdArgs()
    {
        $this->cmdArgs = new Commando\Command();
        $this->setCmdArgs();
    }
    private function setCmdArgs()
    {
        $this->cmdArgs->option('p')
            ->aka('path')
            ->describedAs('Path to benchmark events.');
    }
}

$athletic = new Athletic();
$athletic->run();