<?php
/**
 * User: ocramius
 * Date: 10/11/13
 * Time: 3:46 PM
 */

namespace Athletic;

use Athletic\Results\MethodResults;
use Athletic\TestAsset\RunsCounter;
use Athletic\TestAsset\BenchmarkCallbackEvent;
use PHPUnit_Framework_TestCase;

/**
 * Tests for {@see \Athletic\AthleticEvent}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @author Daniel A. R. Werner <daniel.a.r.werner@gmail.com>
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
     * @expectedException LogicException
     * @expectedExceptionMessageRegExp !resume.* has not been called after pause.*!
     */
    public function testPauseWithoutResume() {
        $event = new BenchmarkCallbackEvent(function($self) {
            $self->pause();
        });
        $event->run();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessageRegExp !pause.* still active, resume.* call expected before next pause!
     */
    public function testPauseAndPauseAgainBeforeResume() {
        $event = new BenchmarkCallbackEvent(function($self) {
            $self->pause();
            $self->pause();
        });
        $event->run();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessageRegExp !can not resume.* a  benchmark before initiating pause.*!
     */
    public function testResumeWithoutPause() {
        $event = new BenchmarkCallbackEvent(function($self) {
            $self->resume();
        });
        $event->run();
    }

    public function testPausedExecutionTimeNotInResult() {
        $pauseTimeInSeconds = 0.01;
        $event = new BenchmarkCallbackEvent(
            function($self) use($pauseTimeInSeconds)
            {
                $pauseTimeInMicroSeconds = $pauseTimeInSeconds * 1000 * 1000;

                $self->pause();
                usleep($pauseTimeInMicroSeconds / 2);
                $self->resume();

                $self->pause();
                usleep($pauseTimeInMicroSeconds / 2);
                $self->resume();
            }
        );
        $event->setMethodFactory($this->resultsFactory);

        $results = $event->run();

        $this->assertCount(3, $results[0]->results);

        foreach($results[0]->results as $result) {
            $this->assertLessThan($pauseTimeInSeconds, $result);
        }
    }
}
