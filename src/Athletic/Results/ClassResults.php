<?php
/**
 * User: zach
 * Date: 6/21/13
 * Time: 3:10 PM
 */

namespace Athletic\Results;


use ArrayIterator;
use IteratorAggregate;

class ClassResults implements IteratorAggregate
{
    /** @var  string */
    private $className;

    /** @var  MethodResults[] */
    private $results;


    /**
     * @param string          $className
     * @param MethodResults[] $results
     */
    public function __construct($className, $results)
    {
        $this->className = $className;
        $this->results   = $results;
    }


    /**
     * @return ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->results);
    }


    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

}