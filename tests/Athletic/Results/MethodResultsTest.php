<?php

namespace Athletic\Tests\Results;

use Athletic\Results\MethodResults;

/**
 * Tests the MethodResults class.
 *
 * @package Athletic
 */
class MethodResultsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Ensures that the MethodResult constructor can handle cases where
     * the duration of the events is zero.
     *
     * This is mainly a problem in the tests, where the events (sometimes)
     * run that fast.
     */
    public function testConstructorCanHandleResultWithDurationOfZero()
    {
        $results = array(
            0.0,
            0.0,
            0.0
        );

        // No "Division by zero" warning must occur.
        $this->setExpectedException(null);
        new MethodResults('fastBenchmark', $results, count($results));
    }

}
