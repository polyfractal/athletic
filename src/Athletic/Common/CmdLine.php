<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 10:45 PM
 */

namespace Athletic\Common;


use Commando\Command;

/**
 * Class CmdLine
 * @package Athletic\Common
 */
class CmdLine
{
    /** @var  Command */
    private $cmdArgs;


    public function parseArgs()
    {
        $cmdArgs = new Command();
        $this->setCmdArgs($cmdArgs);

        if ($cmdArgs['bootstrap'] !== null) {
            require($cmdArgs['bootstrap']);
        }

        $this->cmdArgs = $cmdArgs;
    }


    /**
     * @param Command $cmdArgs
     */
    private function setCmdArgs($cmdArgs)
    {
        $cmdArgs->option('p')
        ->require()
        ->aka('path')
        ->describedAs('Path to benchmark events.');

        $cmdArgs->flag('b')
        ->aka('bootstrap')
        ->describedAs('Path to bootstrap file for your project');
    }


    /**
     * @return mixed
     */
    public function getSuitePath()
    {
        return $this->cmdArgs['path'];
    }
}