<?php
/**
 * User: ocramius
 * Date: 10/11/13
 * Time: 3:46 PM
 */

namespace Athletic;

use Athletic\Results\MethodResults;
use Athletic\TestAsset\MaxRuntimeEvent;
use Athletic\TestAsset\RunsCounter;
use PHPUnit_Framework_TestCase;

/**
 * Tests for {@see \Athletic\AthleticEvent}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @covers \Athletic\AthleticEvent
 */
class AthleticEventTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Athletic\Factories\MethodResultsFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultsFactory;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->resultsFactory = $this->getMock('Athletic\Factories\MethodResultsFactory', array(), array(), '', false);

        $this
            ->resultsFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(function ($name, $result, $iterations) {
                return new MethodResults($name, $result, $iterations);
            }));
    }

    public function testCorrectRunsCount()
    {
        $event = new RunsCounter();

        $event->setMethodFactory($this->resultsFactory);

        $results = $event->run();

        $this->assertCount(1, $results);

        /* @var $result MethodResults */
        $result = reset($results);

        $this->assertInstanceOf('Athletic\Results\MethodResults', $result);

        $this->assertCount(5, $result->results);
        $this->assertSame(5, $result->iterations);
        $this->assertSame(5, $event->runs);
        $this->assertSame(5, $event->setUps);
        $this->assertSame(5, $event->tearDowns);
    }

    /**
     * Ensures that a benchmark is aborted if the annotated maximal runtime is reached.
     */
    public function testBenchmarkIsAbortedIfMaxRuntimeIsReached()
    {
        $event = new MaxRuntimeEvent();
        $event->setMethodFactory($this->resultsFactory);

        $event->run();

        $this->assertEquals(1, $event->iterationAndRuntimeRuns);
    }

    /**
     * Ensures that a benchmark is executed if it defines only a maximal runtime
     * and no number of iterations.
     */
    public function testBenchmarkIsExecutedIfOnlyRuntimeRestrictionIsSpecified()
    {
        $event = new MaxRuntimeEvent();
        $event->setMethodFactory($this->resultsFactory);

        $event->run();

        $this->assertEquals(1, $event->onlyRuntimeRuns);
    }

}
