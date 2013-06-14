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


    /**
     * @return array
     */
    public function run()
    {
        $classReflector = new ReflectionClass(get_class($this));
        $classAnnotations = new Annotations($classReflector);

        $methodAnnotations = array();
        foreach ($classReflector->getMethods() as $methodReflector) {
            $methodAnnotations[$methodReflector->getName()] = new Annotations($methodReflector);
        }

        $this->setUp();
        $results = $this->runBenchmarks($methodAnnotations);
        $this->tearDown();

        return $results;
    }


    /**
     * @param Annotations[] $methods
     * @return array
     */
    private function runBenchmarks($methods)
    {
        $results = array();

        foreach ($methods as $methodName => $annotations) {
            if (isset($annotations['iterations']) === true) {
                $results[$methodName] = $this->runMethodBenchmark($methodName, $annotations['iterations']);
            }
        }
        return $results;
    }


    /**
     * @param string $method
     * @param int     $iterations
     * @return Results
     */
    private function runMethodBenchmark($method, $iterations)
    {
        $avgCalibration = $this->getCalibrationTime($iterations);

        $results = array();
        for ($i = 0; $i < $iterations; ++$i) {
            $results[$i] = $this->timeMethod($method) - $avgCalibration;
        }

        $ret = new Results($results, $iterations);

        return $ret;

    }

    private function timeMethod($method)
    {
        $start = microtime(true);
        $this->$method();
        return microtime(true) - $start;
    }

    private function getCalibrationTime($iterations)
    {
        $emptyCalibrationMethod = 'emptyCalibrationMethod';
        $resultsCalibration = array();
        for ($i = 0; $i < $iterations; ++$i) {
            $resultsCalibration[$i] = $this->timeMethod($emptyCalibrationMethod);
        }
        return array_sum($resultsCalibration) / count($resultsCalibration);
    }

    private function emptyCalibrationMethod()
    {

    }

}