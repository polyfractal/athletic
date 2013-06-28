<?php
/**
 * User: zach
 * Date: 6/28/13
 * Time: 7:35 AM
 */

namespace Athletic\Factories;

use Athletic\Factories\AbstractFactory;
use ErrorException;

/**
 * Class ErrorExceptionFactory
 * @package Athletic\Factories
 */
class ErrorExceptionFactory extends AbstractFactory
{
    /**
     * @param int    $errorLevel
     * @param string $errorMessage
     * @param string $errorFile
     * @param int    $errorLine
     * @param array  $errorContext
     *
     * @return ErrorException
     */
    public function create($errorLevel, $errorMessage, $errorFile, $errorLine, $errorContext = array())
    {
        return $this->container['errorException']($errorLevel, $errorMessage, $errorFile, $errorLine, $errorContext);
    }
}