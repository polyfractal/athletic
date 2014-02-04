<?php
/**
 * User: zach
 * Date: 6/14/13
 * Time: 10:58 AM
 */

namespace Athletic;

use Athletic\Factories\MethodResultsFactory;
use Athletic\Results\MethodResults;
use ReflectionClass;
use zpt\anno\Annotations;

/**
 * Class AthleticEvent
 * @package Athletic
 */
abstract class AthleticEvent
{
    /** @var  MethodResultsFactory */
    private $methodResultsFactory;


    public function __construct()
    {

    }


    protected function classSetUp(array $methods)
    {

    }


    protected function classTearDown()
    {

    }

    protected function setUpMethod($method, $iterationsCount)
    {

    }

    protected function tearDownMethod($method, $iterationsCount)
    {

    }

    protected function setUp($method, $currentIteration, $iterationsCount)
    {

    }


    protected function tearDown($method, $iterationsCount)
    {

    }


    /**
     * @param MethodResultsFactory $methodResultsFactory
     */
    public function setMethodFactory($methodResultsFactory)
    {
        $this->methodResultsFactory = $methodResultsFactory;
    }


    /**
     * @return MethodResults[]
     */
    public function run()
    {
        $classReflector   = new ReflectionClass(get_class($this));
        $classAnnotations = new Annotations($classReflector);

        $methodAnnotations = array();
        foreach ($classReflector->getMethods() as $methodReflector) {
            $methodAnnotations[$methodReflector->getName()] = new Annotations($methodReflector);
        }

        $this->classSetUp($methodAnnotations);
        $results = $this->runBenchmarks($methodAnnotations);
        $this->classTearDown();

        return $results;
    }


    /**
     * @param Annotations[] $methods
     *
     * @return MethodResults[]
     */
    protected function runBenchmarks($methods)
    {
        $results = array();

        foreach ($methods as $methodName => $annotations) {
            if (isset($annotations['iterations']) === true) {
                $results[] = $this->runMethodBenchmark($methodName, $annotations);
            }
        }
        return $results;
    }


    /**
     * @param string $method
     * @param int    $annotations
     *
     * @return MethodResults
     */
    protected function runMethodBenchmark($method, $annotations)
    {
        $iterations = $annotations['iterations'];
        $avgCalibration = $this->getCalibrationTime($iterations);

        $results = array();
        $this->setUpMethod($method, $iterations);
        for ($i = 0; $i < $iterations; ++$i) {
            $this->setUp($method, $i, $iterations);
            $results[$i] = $this->timeMethod($method) - $avgCalibration;
            $this->tearDown($method, $iterations);
        }
        $this->tearDownMethod($method, $iterations);

        $finalResults = $this->methodResultsFactory->create($method, $results, $iterations);

        $this->setOptionalAnnotations($finalResults, $annotations);

        return $finalResults;

    }


    /**
     * @param string $method
     *
     * @return mixed
     */
    protected function timeMethod($method)
    {
        $start = microtime(true);
        $this->$method();
        return microtime(true) - $start;
    }


    /**
     * @param int $iterations
     *
     * @return float
     */
    protected function getCalibrationTime($iterations)
    {
        $emptyCalibrationMethod = 'emptyCalibrationMethod';
        $resultsCalibration     = array();
        for ($i = 0; $i < $iterations; ++$i) {
            $resultsCalibration[$i] = $this->timeMethod($emptyCalibrationMethod);
        }
        return array_sum($resultsCalibration) / count($resultsCalibration);
    }


    protected function emptyCalibrationMethod()
    {

    }


    /**
     * @param MethodResults $finalResults
     * @param array         $annotations
     */
    protected function setOptionalAnnotations(MethodResults $finalResults, $annotations)
    {
        if (isset($annotations['group']) === true) {
            $finalResults->setGroup($annotations['group']);
        }

        if (isset($annotations['baseline']) === true) {
            $finalResults->setBaseline();
        }
    }

}
