<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 10:18 PM
 */

namespace Athletic\Runners;

use Athletic\Publishers\PublisherInterface;

/**
 * Class SuiteRunner
 * @package Athletic\Runners
 */
class SuiteRunner
{
    /** @var PublisherInterface */
    private $publisher;

    /** @var  callback */
    private $classRunner;

    /** @var  array */
    private $results;


    /**
     * @param PublisherInterface $publisher
     * @param                    $classRunner
     */
    public function __construct(PublisherInterface $publisher, $classRunner)
    {
        $this->publisher   = $publisher;
        $this->classRunner = $classRunner;
    }


    /**
     * @param string[] $classesToRun
     */
    public function runSuite($classesToRun)
    {
        $results = array();

        foreach ($classesToRun as $class) {
            $results[$class] = $this->runClass($class);
        }

        $this->results = $results;
    }


    /**
     * @param string $class
     *
     * @return array
     */
    public function runClass($class)
    {
        $classRunnerBuider = $this->classRunner;

        /** @var ClassRunner $classRunner */
        $classRunner = $classRunnerBuider($class);
        return $classRunner->run();

    }


    /**
     * @return array
     */
    public function publishResults()
    {
        $this->publisher->publish($this->results);
    }
}