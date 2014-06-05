<?php
/**
 * User: zach
 * Date: 6/22/13
 * Time: 5:33 PM
 */

namespace Athletic\Common;


use Athletic\Athletic;
use Commando\Command;

/**
 * Class DICBuilder
 * @package Athletic\Common
 */
class DICBuilder
{
    /** @var  Athletic */
    private $athletic;


    /**
     * @param Athletic $athletic
     */
    public function __construct($athletic)
    {
        $this->athletic = $athletic;
    }


    public function buildDependencyGraph()
    {
        $this->setupDiscovery();
        $this->setupParser();
        $this->setupCmdLine();
        $this->setupFormatter();
        $this->setupParser();
        $this->setupPublisher();
        $this->setupSuiteRunner();
        $this->setupClassRunner();
        $this->setupMethodResults();
        $this->setupClassResults();
        $this->setupErrorHandler();
    }


    private function setupClassRunner()
    {
        $this->athletic['classRunnerClass'] = '\Athletic\Runners\ClassRunner';
        $this->athletic['classRunner']      = function ($dic) {
            return function ($class) use ($dic) {
                return new $dic['classRunnerClass']($dic['methodResultsFactory'], $class);
            };
        };

        $this->athletic['classRunnerFactoryClass'] = '\Athletic\Factories\ClassRunnerFactory';
        $this->athletic['classRunnerFactory']      = function ($dic) {
            return new $dic['classRunnerFactoryClass']($dic);
        };
    }


    private function setupDiscovery()
    {
        $this->athletic['discoveryClass'] = '\Athletic\Discovery\RecursiveFileLoader';
        $this->athletic['discovery']      = function ($dic) {
            /** @var CmdLine $cmdLine */
            $cmdLine = $dic['cmdLine'];
            $path    = $cmdLine->getSuitePath();
            return new $dic['discoveryClass']($dic['parserFactory'], $path);
        };
    }


    private function setupParser()
    {
        $this->athletic['parserFactoryClass'] = '\Athletic\Factories\ParserFactory';
        $this->athletic['parserFactory']      = function ($dic) {
            return new $dic['parserFactoryClass']($dic);
        };

        $this->athletic['parserClass'] = '\Athletic\Discovery\Parser';
        $this->athletic['parser']      = function ($dic) {
            return function ($path) use ($dic) {
                return new $dic['parserClass']($path);
            };
        };
    }


    private function setupClassResults()
    {
        $this->athletic['classResultsFactoryClass'] = '\Athletic\Factories\ClassResultsFactory';
        $this->athletic['classResultsFactory']      = function ($dic) {
            return new $dic['classResultsFactoryClass']($dic);
        };

        $this->athletic['classResultsClass'] = '\Athletic\Results\ClassResults';
        $this->athletic['classResults']      = function ($dic) {
            return function ($name, $results) use ($dic) {
                return new $dic['classResultsClass']($name, $results);
            };
        };
    }


    private function setupMethodResults()
    {
        $this->athletic['methodResultsFactoryClass'] = '\Athletic\Factories\MethodResultsFactory';
        $this->athletic['methodResultsFactory']      = function ($dic) {
            return new $dic['methodResultsFactoryClass']($dic);
        };

        $this->athletic['methodResultsClass'] = '\Athletic\Results\MethodResults';
        $this->athletic['methodResults']      = function ($dic) {
            return function ($name, $results, $iterations) use ($dic) {
                return new $dic['methodResultsClass']($name, $results, $iterations);
            };
        };
    }


    private function setupCmdLine()
    {
        $this->athletic['cmdLine'] = function ($dic) {
            return new CmdLine($dic['command']);
        };

        $this->athletic['command'] = function ($dic) {
            return new Command();
        };
    }


    private function setupFormatter()
    {
        /** @var CmdLine $cmdLine */
        $cmdLine   = $this->athletic['cmdLine'];
        $formatter = $cmdLine->getFormatter();

        if (isset($formatter) === true) {
            if (!class_exists($formatter)) {
                // Use a built-in formatter.
                $formatter =  "\\Athletic\\Formatters\\$formatter";
            }
            $this->athletic['formatterClass'] = $formatter;
        } else {
            $this->athletic['formatterClass'] = '\Athletic\Formatters\DefaultFormatter';
        }
        $this->assertValidFormatterClass($this->athletic['formatterClass']);

        $this->athletic['formatter'] = function ($dic) {
            return new $dic['formatterClass']();
        };
    }


    private function setupPublisher()
    {
        $this->athletic['publisherClass'] = '\Athletic\Publishers\StdOutPublisher';
        $this->athletic['publisher']      = function ($dic) {
            return new $dic['publisherClass']($dic['formatter']);
        };
    }


    private function setupSuiteRunner()
    {
        $this->athletic['suiteRunnerClass'] = '\Athletic\Runners\SuiteRunner';
        $this->athletic['suiteRunner']      = function ($dic) {
            return new $dic['suiteRunnerClass']($dic['publisher'], $dic['classResultsFactory'], $dic['classRunnerFactory']);
        };

    }


    private function setupErrorHandler()
    {

        $this->athletic['errorExceptionClass'] = '\ErrorException';
        $this->athletic['errorException']      = function ($dic) {
            return function ($errorLevel, $errorMessage, $errorFile, $errorLine, $errorContext) use ($dic) {
                return new $dic['errorExceptionClass'](
                    $errorMessage,
                    0,
                    $errorLevel,
                    $errorFile,
                    $errorLine
                );
            };
        };

        $this->athletic['errorExceptionFactoryClass'] = '\Athletic\Factories\ErrorExceptionFactory';
        $this->athletic['errorExceptionFactory']      = function ($dic) {
            return new $dic['errorExceptionFactoryClass']($dic);
        };

        $this->athletic['errorHandlerClass'] = '\Athletic\Common\CmdLineErrorHandler';
        $this->athletic['errorHandler']      = function ($dic) {
            return new $dic['errorHandlerClass']($dic['command'], $dic['errorExceptionFactory']);
        };
    }

    /**
     * Asserts that the provided class can be used as output formatter.
     *
     * @param string $formatterClass
     * @throws \InvalidArgumentException If the class doe not meet the requirements (interface and empty constructor)
     */
    private function assertValidFormatterClass($formatterClass)
    {
        $formatterInfo = new \ReflectionClass($formatterClass);
        if (!$formatterInfo->implementsInterface('Athletic\Formatters\FormatterInterface')) {
            $message = sprintf('%s does not implement the formatter interface.', $formatterClass);
            throw new \InvalidArgumentException($message);
        }
        $constructor = $formatterInfo->getConstructor();
        if ($constructor !== null && $constructor->getNumberOfRequiredParameters() > 0) {
            $message = sprintf('Formatter %s must provide a constructor without required parameters.', $formatterClass);
            throw new \InvalidArgumentException($message);
        }
    }
}