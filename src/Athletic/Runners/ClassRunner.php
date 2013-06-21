<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 10:18 PM
 */

namespace Athletic\Runners;

use Athletic\AthleticEvent;
use ReflectionClass;

/**
 * Class ClassRunner
 * @package Athletic\Runners
 */
class ClassRunner
{
    /** @var  string */
    private $class;


    /**
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }


    public function run()
    {
        if ($this->isBenchmarkableClass() !== true) {
            return array();
        }

        $class = $this->class;

        /** @var AthleticEvent $object */
        $object = new $class();

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