<?php

namespace Athletic\Common;

/**
 * ErrorHandlerInterface
 * @package Athletic
 */
interface ErrorHandlerInterface
{
    /**
     * @param int    $errorLevel
     * @param string $errorMessage
     * @param string $errorFile
     * @param int    $errorLine
     * @param array  $errorContext
     */
    public function handleError($errorLevel, $errorMessage, $errorFile, $errorLine, $errorContext = array());


    /**
     * @param \Exception $exception
     */
    public function handleException(\Exception $exception);
}
