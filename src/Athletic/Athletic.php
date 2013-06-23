<?php
/**
 * User: zach
 * Date: 6/14/13
 * Time: 11:48 AM
 */

namespace Athletic;


use Athletic\Common\DICBuilder;
use Athletic\Discovery\RecursiveFileLoader;
use Athletic\Runners\SuiteRunner;
use Pimple;

/**
 * Class Athletic
 * @package Athletic
 */
class Athletic extends Pimple
{
    /** @var  DICBuilder */
    private $dicBuilder;


    public function __construct()
    {
        $this->dicBuilder = new DICBuilder($this);
        $this->dicBuilder->buildDependencyGraph();
    }


    public function run()
    {
        $classesToBenchmark = $this->getClassesToBenchmark();
        $this->benchmark($classesToBenchmark);
    }


    /**
     * @return string[]
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
}

