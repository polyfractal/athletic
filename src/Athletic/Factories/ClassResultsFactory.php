<?php
/**
 * User: zach
 * Date: 6/21/13
 * Time: 3:11 PM
 */

namespace Athletic\Factories;

use Athletic\Factories\AbstractFactory;

class ClassResultsFactory extends AbstractFactory
{
    public function create($name, $results)
    {
        return $this->container['classResults']($name, $results);
    }
}