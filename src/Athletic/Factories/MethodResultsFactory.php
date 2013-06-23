<?php
/**
 * User: zach
 * Date: 6/21/13
 * Time: 3:11 PM
 */

namespace Athletic\Factories;


use Athletic\Factories\AbstractFactory;
use Athletic\Results\MethodResults;

/**
 * Class MethodResultsFactory
 * @package Athletic\Factories
 */
class MethodResultsFactory extends AbstractFactory
{
    /**
     * @param       $name
     * @param array $results
     * @param int   $iterations
     *
     * @return MethodResults
     */
    public function create($name, $results, $iterations)
    {
        return $this->container['methodResults']($name, $results, $iterations);
    }
}