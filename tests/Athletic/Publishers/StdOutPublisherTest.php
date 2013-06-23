<?php
/**
 * User: zach
 * Date: 6/22/13
 * Time: 8:18 PM
 */

namespace Athletic\Tests\Publishers;

use Athletic\Publishers\StdOutPublisher;
use Mockery as m;

class StdOutPublisherTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testPublish()
    {
        $output = 'abc';
        $mockResults   = m::mock('\Athletic\Results\ClassResults');
        $mockFormatter = m::mock('\Athletic\Formatters\FormatterInterface')
                         ->shouldReceive('getFormattedResults')
                         ->with($mockResults)
                         ->andReturn($output)
                         ->getMock();

        $publisher = new StdOutPublisher($mockFormatter);
        $publisher->publish($mockResults);

        $this->expectOutputString($output);
    }

}