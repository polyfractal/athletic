<?php
/**
 * User: zach
 * Date: 6/21/13
 * Time: 3:41 PM
 */

namespace Athletic\Factories;


use Pimple;

/**
 * Class AbstractFactory
 * @package Athletic\Factories
 */
abstract class AbstractFactory
{
    /** @var  Pimple */
    protected $container;


    /**
     * @param Pimple $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

}