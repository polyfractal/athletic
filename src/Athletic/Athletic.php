<?php
/**
 * User: zach
 * Date: 6/14/13
 * Time: 11:48 AM
 */

namespace Athletic;


use Athletic\Common\CmdLine;
use Athletic\Discovery\RecursiveFileLoader;
use Athletic\Runners\SuiteRunner;
use Pimple;

/**
 * Class Athletic
 * @package Athletic
 */
class Athletic extends Pimple
{
    /** @var  CmdLine */
    private $cmdLine;


    public function __construct()
    {
        $this->buildDIC();
        $this->getCmdLineArgs();
    }


    public function run()
    {
        $classesToBenchmark = $this->getClassesToBenchmark();
        $this->benchmark($classesToBenchmark);
    }


    /**
     * @return \string[]
     */
    private function getClassesToBenchmark()
    {
        /** @var RecursiveFileLoader $discovery */
        $discovery = $this['discovery'];
        return $discovery->getClasses();
    }


    /**
     * @param string[] $classes
     */
    private function benchmark($classes)
    {
        /** @var SuiteRunner $suite */
        $suite = $this['suiteRunner'];

        $suite->runSuite($classes);
        $suite->publishResults();

    }


    private function getCmdLineArgs()
    {
        $this->cmdLine = $this['cmdLine'];
    }


    private function buildDIC()
    {
        $this['cmdLine'] = function ($dic) {
            $cmdLine =  new CmdLine();
            $cmdLine->parseArgs();
            return $cmdLine;
        };

        $this['formatterClass'] = '\Athletic\Formatters\DefaultFormatter';
        $this['formatter']      = function ($dic) {
            return new $dic['formatterClass']();
        };

        $this['publisherClass'] = '\Athletic\Publishers\StdOutPublisher';
        $this['publisher']      = function ($dic) {
            return new $dic['publisherClass']($dic['formatter']);
        };

        $this['discoveryClass'] = '\Athletic\Discovery\RecursiveFileLoader';
        $this['discovery']      = function ($dic) {
            /** @var CmdLine $cmdLine */
            $cmdLine = $dic['cmdLine'];
            $path = $cmdLine->getSuitePath();
            return new $dic['discoveryClass']($path);
        };

        $this['classRunnerClass'] = '\Athletic\Runners\ClassRunner';
        $this['classRunner']      = function ($dic) {
            return function ($class) use ($dic) {
                return new $dic['classRunnerClass']($class);
            };
        };

        $this['suiteRunnerClass'] = '\Athletic\Runners\SuiteRunner';
        $this['suiteRunner']      = function ($dic) {
            return new $dic['suiteRunnerClass']($dic['publisher'], $dic['classRunner']);
        };

        $this['parserClass'] = '\Athletic\Discovery\Parser';
        $this['parser']      = function ($dic) {
            return function ($path) use ($dic) {
                return new $dic['parserClass']($path);
            };
        };

        $this['parserFactoryClass'] = '\Athletic\Discovery\ParserFactory';
        $this['parserFactory']      = function ($dic) {
            return new $dic['parserFactoryClass']($dic['parser']);
        };
    }

}

