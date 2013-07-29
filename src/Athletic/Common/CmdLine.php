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


    public function __construct(Command $command)
    {
        $cmdArgs = $command;
        $this->setCmdArgs($cmdArgs);

        if ($cmdArgs['bootstrap'] !== null) {
            require_once($cmdArgs['bootstrap']);
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

        $cmdArgs->flag('f')
        ->aka('formatter')
        ->describedAs('User-configured formatter to use instead of DefaultFormatter');
    }


    /**
     * @return mixed
     */
    public function getSuitePath()
    {
        return $this->cmdArgs['path'];
    }


    /**
     * @return mixed
     */
    public function getFormatter()
    {
        return $this->cmdArgs['formatter'];
    }
}