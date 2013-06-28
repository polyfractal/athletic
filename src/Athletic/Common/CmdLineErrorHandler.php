<?php

namespace Athletic\Common;

use Athletic\Factories\ErrorExceptionFactory;
use Commando\Command;

/**
 * CmdLineErrorHandler
 * @package Athletic
 */
class CmdLineErrorHandler implements ErrorHandlerInterface
{
    /** @var Command $command */
    private $command;

    /** @var ErrorExceptionFactory */
    private $errorExceptionFactory;


    /**
     * @param Command               $command
     * @param ErrorExceptionFactory $errorExceptionFactory
     */
    public function __construct($command, ErrorExceptionFactory $errorExceptionFactory)
    {
        $this->command               = $command;
        $this->errorExceptionFactory = $errorExceptionFactory;
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
        $exception = $this->errorExceptionFactory->create(
            $errorLevel,
            $errorMessage,
            $errorFile,
            $errorLine,
            $errorContext
        );

        $this->handleException($exception);
    }
}
