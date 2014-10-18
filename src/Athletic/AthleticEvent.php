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


    protected function classSetUp()
    {

    }


    protected function classTearDown()
    {

    }


    protected function setUp()
    {

    }


    protected function tearDown()
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

        $this->classSetUp();
        $results = $this->runBenchmarks($methodAnnotations);
        $this->classTearDown();

        return $results;
    }


    /**
     * @param Annotations[] $methods
     *
     * @return MethodResults[]
     */
    private function runBenchmarks($methods)
    {
        $results = array();

        foreach ($methods as $methodName => $annotations) {
            if (substr($methodName, 0, 7) === 'perform') {
                if ($annotations['dataProvider']) {
                    $providerMethod = $annotations['dataProvider'];

                    if (!method_exists($this, $providerMethod)) {
                        throw new \Exception(sprintf(
                            'Provider method "%s" does not exist',
                            $providerMethod
                        ));
                    }

                    $dataSets = $this->$providerMethod();

                    if (!is_array($dataSets)) {
                        throw new \Exception(sprintf(
                            'Data provider method "%s" must return an array',
                            $providerMethod
                        ));
                    }

                    $refl = new \ReflectionClass($this);
                    $method = $refl->getMethod($methodName);
                    $args = $method->getParameters();

                    $newDataSets = array();
                    foreach ($dataSets as $dataSet) {
                        $newDataSet = array();
                        foreach ($dataSet as $i => $value) {
                            if (isset($args[$i])) {
                                $newDataSet[$args[$i]->name] = $value;
                            }
                        }
                        $newDataSets[] = $newDataSet;
                    }
                    $dataSets = $newDataSets;

                } else {
                    $dataSets = array(
                        array()
                    );
                }

                foreach ($dataSets as $dataSet) {
                    $iterationCounts = (array) $annotations['iterations'];

                    foreach ($iterationCounts as $iterationCount) {
                        $results[] = $this->runMethodBenchmark($methodName, $dataSet, $annotations, $iterationCount);
                    }
                }
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
    private function runMethodBenchmark($method, $dataSet, $annotations, $iterationCount)
    {
        $avgCalibration = $this->getCalibrationTime($iterationCount, $dataSet);

        $results = array();
        for ($i = 0; $i < $iterationCount; ++$i) {
            $this->setUp();
            $results[$i] = $this->timeMethod($method, $dataSet) - $avgCalibration;
            $this->tearDown();
        }

        $finalResults = $this->methodResultsFactory->create($method, $results, $iterationCount, $dataSet);

        $this->setOptionalAnnotations($finalResults, $annotations);

        return $finalResults;

    }


    /**
     * @param string $method
     *
     * @return mixed
     */
    private function timeMethod($method, $dataSet)
    {
        $start = microtime(true);
        call_user_func_array(array($this, $method), $dataSet);
        return microtime(true) - $start;
    }


    /**
     * @param int $iterations
     *
     * @return float
     */
    private function getCalibrationTime($iterations, $dataSet)
    {
        $emptyCalibrationMethod = 'emptyCalibrationMethod';
        $resultsCalibration     = array();
        for ($i = 0; $i < $iterations; ++$i) {
            $resultsCalibration[$i] = $this->timeMethod($emptyCalibrationMethod, $dataSet);
        }
        return array_sum($resultsCalibration) / count($resultsCalibration);
    }


    private function emptyCalibrationMethod()
    {

    }


    /**
     * @param MethodResults $finalResults
     * @param array         $annotations
     */
    private function setOptionalAnnotations(MethodResults $finalResults, $annotations)
    {
        if (isset($annotations['group']) === true) {
            $finalResults->setGroup($annotations['group']);
        }

        if (isset($annotations['baseline']) === true) {
            $finalResults->setBaseline();
        }
    }

}
