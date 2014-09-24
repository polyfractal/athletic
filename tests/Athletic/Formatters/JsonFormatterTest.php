<?php

namespace Athletic\Tests\Formatters;

use Athletic\Formatters\JsonFormatter;
use Athletic\Results\MethodResults;
use Athletic\Results\ClassResults;
use Mockery;

/**
 * This tests suite assures that JsonFormatter returns what it has to return.
 *
 * @version 0.1.0
 * @since   0.1.9
 * @package Athletic\Tests\Formatters
 * @author  Fike Etki <etki@etki.name>
 */
class JsonFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested class FQCN for mocking.
     *
     * @type string
     * @since 0.1.0
     */
    protected $testedClass = 'Athletic\Formatters\JsonFormatter';
    /**
     * MethodResults FQCN for mocking.
     *
     * @type string
     * @since 0.1.0
     */
    protected $methodResultsClass = 'Athletic\Results\MethodResults';
    /**
     * ClassResults FQCN for mocking.
     *
     * @type string
     * @since 0.1.0
     */
    protected $classResultsClass = 'Athletic\Results\ClassResults';
    /**
     * Some static data to mock Athletic output.
     *
     * @type array
     * @since 0.1.0
     */
    protected $staticData = array(
        'ClassA' => array(
            'A' => array(
                'iterations' => 1000,
                'sum' => 10,
                'min' => 0.005,
                'max' => 0.015,
                'avg' => 0.01,
                'ops' => 100,
                'group' => null,
            ),
            'B' => array(
                'iterations' => 1000,
                'sum' => 10,
                'min' => 0.005,
                'max' => 0.015,
                'avg' => 0.01,
                'ops' => 100,
                'group' => null,
            ),
            'C' => array(
                'iterations' => 1000,
                'sum' => 10,
                'min' => 0.005,
                'max' => 0.015,
                'avg' => 0.01,
                'ops' => 100,
                'group' => 'A',
            ),
            'D' => array(
                'iterations' => 1000,
                'sum' => 10,
                'min' => 0.005,
                'max' => 0.015,
                'avg' => 0.01,
                'ops' => 100,
                'group' => 'B',
            ),
        ),
        'ClassB' => array(
            'E' => array(
                'iterations' => 1000,
                'sum' => 10,
                'min' => 0.005,
                'max' => 0.015,
                'avg' => 0.01,
                'ops' => 100,
                'group' => null,
            ),
        )
    );

    /**
     * This is **not** a PHPUnit data provider. Deal with it.
     *
     * Also it is messed as hell -_-.
     *
     * @return ClassResults[] Mocked Athletic results.
     * @since 0.1.0
     */
    public function resultsProvider()
    {
        $mockedOutput = array();
        foreach ($this->staticData as $className => $classStats) {
            $results = array();
            foreach ($classStats as $methodName => $methodStats) {
                /** @type MethodResults $methodResults */
                $methodResults = Mockery::mock($this->methodResultsClass);
                $methodResults->methodName = $methodName;
                foreach ($methodStats as $key => $value) {
                    $methodResults->$key = $value;
                }
                $results[] = $methodResults;
            }
            /** @type ClassResults $classResults */
            $classResults = Mockery::mock(
                $this->classResultsClass,
                array($className, $results)
            )
                ->shouldReceive('getClassName')
                ->andReturn($className)
                ->getMock()
                ->shouldReceive('getIterator')
                ->andReturn(new \ArrayIterator($results))
                ->getMock();
            $mockedOutput[] = $classResults;
        }
        return $mockedOutput;
    }

    /**
     * This is **not** a PHPUnit data provider. Deal with it.
     *
     * @return array Expected JsonFormatter output for mixed strategy.
     * @since 0.1.0
     */
    protected function mixedMethodsResultsProvider()
    {
        $data = $this->staticData;
        foreach ($data as $className => $methodResults) {
            $groups = array();
            $methods = array();
            foreach ($methodResults as $methodName => $stats) {
                $group = $stats['group'];
                unset($stats['group']);
                if ($group) {
                    if (!isset($groups[$group])) {
                        $groups[$group] = array();
                    }
                    $groups[$group][$methodName] = $stats;
                } else {
                    $methods[$methodName] = $stats;
                }
            }
            $data[$className] = array(
                'groups' => $groups,
                'methods' => $methods
            );
        }
        return $data;
    }

    /**
     * This is **not** a PHPUnit data provider. Deal with it.
     *
     * @return array Expected JsonFormatter output for grouped strategy.
     * @since 0.1.0
     */
    protected function groupedMethodsResultsProvider()
    {
        $data = $this->mixedMethodsResultsProvider();
        foreach ($data as $className => $methodResults) {
            $data[$className] = $methodResults['groups'];
        }
        return $data;
    }

    /**
     * This is **not** a PHPUnit data provider. Deal with it.
     *
     * @return array Expected JsonFormatter output for nongrouped strategy.
     * @since 0.1.0
     */
    protected function plainMethodsResultsProvider()
    {
        $data = $this->mixedMethodsResultsProvider();
        foreach ($data as $className => $methodResults) {
            $data[$className] = $methodResults['methods'];
        }
        return $data;
    }

    // tests

    /**
     * Tests output for various strategies.
     *
     * @return void
     * @since 0.1.0
     */
    public function testStrategies()
    {
        /** @type JsonFormatter $formatter */
        //$formatter = Mockery::mock($this->testedClass);
        $formatter = new JsonFormatter();
        $results = $this->resultsProvider();

        // 'show as plain list' strategy
        $formatted = $formatter->getFormattedResults($results);
        $this->assertSame(json_decode($formatted, true), $this->staticData);

        // 'show both plain methods and groups' strategy
        $formatted = $formatter->getFormattedResults(
            $results,
            JsonFormatter::STRATEGY_MIX_VIEWS
        );
        $this->assertEquals(
            json_decode($formatted, true),
            $this->mixedMethodsResultsProvider()
        );

        // 'show only groups' strategy
        $formatted = $formatter->getFormattedResults(
            $results,
            JsonFormatter::STRATEGY_SHOW_GROUPED
        );
        $this->assertEquals(
            json_decode($formatted, true),
            $this->groupedMethodsResultsProvider()
        );

        // 'show only methods without groups' strategy
        $formatted = $formatter->getFormattedResults(
            $results,
            JsonFormatter::STRATEGY_SHOW_NONGROUPED
        );
        $this->assertEquals(
            json_decode($formatted, true),
            $this->plainMethodsResultsProvider()
        );
    }
}
