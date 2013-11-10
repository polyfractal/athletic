<?php
/**
 * User: ocramius
 * Date: 10/11/13
 * Time: 3:46 PM
 */

namespace Athletic\TestAsset;

use Athletic\AthleticEvent;

/**
 * Test runner athletic event - used to test against {@see \Athletic\AthleticEvent}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @package Athletic\TestAsset
 */
class RunsCounter extends AthleticEvent
{
    /**
     * @var int
     */
    public $runs = 0;

    /**
     * @var int
     */
    public $setUps = 0;

    /**
     * @var int
     */
    public $tearDowns = 0;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->setUps += 1;
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $this->tearDowns += 1;
    }

    /**
     * @iterations 5
     */
    public function testRuns()
    {
        $this->runs += 1;
    }
}
