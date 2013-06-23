<?php
/**
 * User: zach
 * Date: 6/21/13
 * Time: 4:46 PM
 */

namespace Athletic\Factories;

use Athletic\Factories\AbstractFactory;
use Athletic\Runners\ClassRunner;

/**
 * Class ClassRunnerFactory
 * @package Athletic\Factories
 */
class ClassRunnerFactory extends AbstractFactory
{
    /**
     * @param string $class
     *
     * @return ClassRunner
     */
    public function create($class)
    {
        return $this->container['classRunner']($class);
    }
}