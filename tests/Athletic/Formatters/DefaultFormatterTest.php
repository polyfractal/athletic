<?php
/**
 * User: zach
 * Date: 6/21/13
 * Time: 2:24 PM
 */

namespace Athletic\Tests\Formatters;

use Athletic\Formatters\DefaultFormatter;
use Athletic\Results;
use Mockery as m;

class DefaultFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testDefaultFormatter()
    {
        $mockResult = m::mock('\Athletic\Results');
        /** @var Results $mockResult */
        $mockResult->avg = 5;
        $mockResult->iterations = 5;
        $mockResult->max = 5;
        $mockResult->min = 5;
        $mockResult->sum = 5;
        $mockResult->ops = 5;

        $mockResults['testClass'] = $mockResult;

        $formatter = new DefaultFormatter();
        $ret = $formatter->getFormattedResults($mockResults);
        echo($ret);
    }
}