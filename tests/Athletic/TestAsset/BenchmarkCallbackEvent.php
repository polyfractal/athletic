<?php

namespace Athletic\TestAsset;

use Athletic\AthleticEvent;

/**
 * Test event which will execute a constructor given callback in its one benchmark function.
 *
 * @author Daniel A. R. Werner <daniel.a.r.werner@gmail.com>
 *
 * @package Athletic\TestAsset
 */
class BenchmarkCallbackEvent extends AthleticEvent
{
	/** @var array( callback ) */
	protected $benchmarkCode;

	/**
	 * @param callable $benchmarkCode Callback executed in the "someBenchmark" benchmark member.
	 *        The callback gets the BenchmarkCallbackEvent instance passed in as first argument.
	 */
    public function __construct( $benchmarkCode ) {
		// Have to put this in an array or AthleticEvent will recognize it as an actual benchmark.
		// That might be no problem but make sure and invoke callback in a more controlled fashion.
		$this->benchmarkCode = [ $benchmarkCode ];
	}

    /**
     * @iterations 3
     */
    public function someBenchmark()
    {
        return $this->benchmarkCode[ 0 ]( $this );
    }
}
