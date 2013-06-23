<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 10:18 PM
 */

namespace Athletic\Runners;

use Athletic\Factories\ClassResultsFactory;
use Athletic\Factories\ClassRunnerFactory;
use Athletic\Publishers\PublisherInterface;
use Athletic\Results\ClassResults;

/**
 * Class SuiteRunner
 * @package Athletic\Runners
 */
class SuiteRunner
{
    /** @var PublisherInterface */
    private $publisher;

    /** @var  ClassRunnerFactory */
    private $classRunnerFactory;

    /** @var  ClassResults[] */
    private $results;

    /** @var  ClassResultsFactory */
    private $classResultsFactory;


    /**
     * @param PublisherInterface  $publisher
     * @param ClassResultsFactory $classResultsFactory
     * @param ClassRunnerFactory  $classRunnerFactory
     *
     */
    public function __construct(
        PublisherInterface $publisher,
        ClassResultsFactory $classResultsFactory,
        ClassRunnerFactory $classRunnerFactory
    ) {
        $this->publisher           = $publisher;
        $this->classRunnerFactory  = $classRunnerFactory;
        $this->classResultsFactory = $classResultsFactory;
    }


    /**
     * @param string[] $classesToRun
     */
    public function runSuite($classesToRun)
    {
        $results = array();

        foreach ($classesToRun as $class) {
            $results[] = $this->runClass($class);
        }

        $this->results = $results;
    }


    /**
     * @param string $class
     *
     * @return ClassResults
     */
    public function runClass($class)
    {
        $classRunner   = $this->classRunnerFactory->create($class);
        $methodResults = $classRunner->run();

        return $this->classResultsFactory->create($class, $methodResults);

    }


    /**
     * @return array
     */
    public function publishResults()
    {
        $this->publisher->publish($this->results);
    }
}