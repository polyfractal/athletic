<?php
/**
 * User: zach
 * Date: 6/14/13
 * Time: 10:58 AM
 */

namespace Athletic;

use ReflectionClass;
use zpt\anno\Annotations;

abstract class AthleticEvent
{
    public function __construct()
    {

    }

    protected function setUp()
    {

    }

    protected function tearDown()
    {

    }

    public function run()
    {
        $classReflector = new ReflectionClass(get_class($this));
        $classAnnotations = new Annotations($classReflector);

        $methodAnnotations = array();
        foreach ($classReflector->getMethods() as $methodReflector) {
            $methodAnnotations[$methodReflector->getName()] = new Annotations($methodReflector);
        }
        print_r($methodAnnotations);
    }

}