<?php

namespace Athletic\Common;

use Commando\Command;
use ErrorException;

/**
 * CmdLineErrorHandler
 * @package Athletic
 */
class CmdLineErrorHandler implements ErrorHandlerInterface
{
    /** @var Command $command */
    private $command;


    /**
     * @param Command $command
     */
    public function __construct($command)
    {
        $this->command = $command;
    }


    /*
     * {@inheritDoc}
     */
    public function handleException(\Exception $exception)
    {
        $this->command->error($exception);
    }


    /*
     * {@inheritDoc}
     */
    public function handleError($errorLevel, $errorMessage, $errorFile, $errorLine, $errorContext = array())
    {
        // Translate the error to an ErrorException and delegate it to the
        // exception handler:
        $this->handleException(
            new ErrorException($errorMessage, $errorLevel, null, $errorFile, $errorLine)
        );
    }
}
