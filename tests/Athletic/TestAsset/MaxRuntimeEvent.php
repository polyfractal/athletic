<?php

namespace Athletic\TestAsset;

use Athletic\AthleticEvent;

/**
 * Event that is used to test the runtime restriction feature.
 *
 * @author Matthias Molitor <matthias@matthimatiker.de>
 * @package Athletic\TestAsset
 */
class MaxRuntimeEvent extends AthleticEvent
{

    /**
     * Counts how often testAdditionalRuntimeRestriction() was called.
     *
     * @var integer
     */
    public $iterationAndRuntimeRuns = 0;

    /**
     * Counts how often testOnlyRuntimeRestriction() was called.
     *
     * @var integer
     */
    public $onlyRuntimeRuns = 0;

    /**
     * Benchmark method with an additional runtime restriction in seconds.
     *
     * @Iterations 10
     * @MaxRuntime 0.0
     */
    public function testAdditionalRuntimeRestriction()
    {
        $this->iterationAndRuntimeRuns++;
    }

    /**
     * Benchmark method that defines only a runtime restriction.
     *
     * @MaxRuntime 0.0
     */
    public function testOnlyRuntimeRestriction()
    {
        $this->onlyRuntimeRuns++;
    }

}
