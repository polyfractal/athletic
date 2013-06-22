<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 10:18 PM
 */

namespace Athletic\Runners;

use Athletic\AthleticEvent;
use Athletic\Factories\MethodResultsFactory;
use Athletic\Results\ClassResults;
use Athletic\Results\MethodResults;
use ReflectionClass;

/**
 * Class ClassRunner
 * @package Athletic\Runners
 */
class ClassRunner
{
    /** @var  string */
    private $class;

    /** @var  MethodResultsFactory */
    private $methodResultsFactory;

    /** @var ClassResults  */
    private $classResults;


    /**
     * @param MethodResultsFactory $methodResultsFactory
     * @param string               $class
     */
    public function __construct(MethodResultsFactory $methodResultsFactory, $class)
    {
        $this->class = $class;
        $this->methodResultsFactory = $methodResultsFactory;
    }


    /**
     * @return MethodResults
     */
    public function run()
    {
        if ($this->isBenchmarkableClass() !== true) {
            return array();
        }

        $class = $this->class;

        /** @var AthleticEvent $object */
        $object = new $class();

        $object->setMethodFactory($this->methodResultsFactory);
        return $object->run();
    }


    /**
     * @return bool
     */
    private function isBenchmarkableClass()
    {
        $reflectionClass = new ReflectionClass($this->class);
        return ($reflectionClass->isAbstract() !== true && $reflectionClass->isSubclassOf(
                '\Athletic\AthleticEvent'
            ) === true);
    }

}