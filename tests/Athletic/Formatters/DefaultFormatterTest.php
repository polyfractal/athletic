<?php
/**
 * User: zach
 * Date: 6/21/13
 * Time: 2:24 PM
 */

namespace Athletic\Tests\Formatters;

use ArrayIterator;
use Athletic\Formatters\DefaultFormatter;
use Athletic\Results;
use Athletic\Results\MethodResults;
use Mockery as m;

class DefaultFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }


    public function testOneClassOneMethod()
    {
        /** @var MethodResults $mockMethodResult */
        $mockMethodResult             = m::mock('\Athletic\Results\MethodResults');
        $mockMethodResult->methodName = 'testName';
        $mockMethodResult->avg        = 5;
        $mockMethodResult->min        = 5;
        $mockMethodResult->max        = 5;
        $mockMethodResult->sum        = 5;
        $mockMethodResult->iterations = 5;
        $mockMethodResult->ops        = 5;


        $mockMethodResults = new ArrayIterator(array($mockMethodResult));

        $mockClassResult = m::mock('\Athletic\Results\ClassResults')
                           ->shouldReceive('getClassName')
                           ->andReturn('testClass')
                           ->getMock()
                           ->shouldReceive('getIterator')
                           ->andReturn($mockMethodResults)
                           ->getMock();

        $suiteResults[] = $mockClassResult;


        $formatter = new DefaultFormatter();
        $ret       = $formatter->getFormattedResults($suiteResults);

        $expected = <<<EOF

testClass
    Method Name   Iterations   Average Time      Ops/second
    ------------ ------------ ----------------- ------------
    testName   : [         5] [5.0000000000000] [5.00000   ]



EOF;

        $this->assertEquals($expected, $ret);
    }


    public function testOneClassThreeMethods()
    {
        /** @var MethodResults $mockMethodResult */
        $mockMethodResult             = m::mock('\Athletic\Results\MethodResults');
        $mockMethodResult->methodName = 'testName';
        $mockMethodResult->avg        = 5;
        $mockMethodResult->min        = 5;
        $mockMethodResult->max        = 5;
        $mockMethodResult->sum        = 5;
        $mockMethodResult->iterations = 5;
        $mockMethodResult->ops        = 5;


        $mockMethodResults = new ArrayIterator(array($mockMethodResult, $mockMethodResult, $mockMethodResult));

        $mockClassResult = m::mock('\Athletic\Results\ClassResults')
                           ->shouldReceive('getClassName')
                           ->andReturn('testClass')
                           ->getMock()
                           ->shouldReceive('getIterator')
                           ->andReturn($mockMethodResults)
                           ->getMock();

        $suiteResults[] = $mockClassResult;


        $formatter = new DefaultFormatter();
        $ret       = $formatter->getFormattedResults($suiteResults);

        $expected = <<<EOF

testClass
    Method Name   Iterations   Average Time      Ops/second
    ------------ ------------ ----------------- ------------
    testName   : [         5] [5.0000000000000] [5.00000   ]
    testName   : [         5] [5.0000000000000] [5.00000   ]
    testName   : [         5] [5.0000000000000] [5.00000   ]



EOF;

        $this->assertEquals($expected, $ret);
    }


    public function testThreeClassOneMethod()
    {
        /** @var MethodResults $mockMethodResult */
        $mockMethodResult             = m::mock('\Athletic\Results\MethodResults');
        $mockMethodResult->methodName = 'testName';
        $mockMethodResult->avg        = 5;
        $mockMethodResult->min        = 5;
        $mockMethodResult->max        = 5;
        $mockMethodResult->sum        = 5;
        $mockMethodResult->iterations = 5;
        $mockMethodResult->ops        = 5;


        $mockMethodResults = new ArrayIterator(array($mockMethodResult));

        $mockClassResult = m::mock('\Athletic\Results\ClassResults')
                           ->shouldReceive('getClassName')
                           ->andReturn('testClass')
                           ->getMock()
                           ->shouldReceive('getIterator')
                           ->andReturn($mockMethodResults)
                           ->getMock();

        $suiteResults = array($mockClassResult, $mockClassResult, $mockClassResult);


        $formatter = new DefaultFormatter();
        $ret       = $formatter->getFormattedResults($suiteResults);

        $expected = <<<EOF

testClass
    Method Name   Iterations   Average Time      Ops/second
    ------------ ------------ ----------------- ------------
    testName   : [         5] [5.0000000000000] [5.00000   ]


testClass
    Method Name   Iterations   Average Time      Ops/second
    ------------ ------------ ----------------- ------------
    testName   : [         5] [5.0000000000000] [5.00000   ]


testClass
    Method Name   Iterations   Average Time      Ops/second
    ------------ ------------ ----------------- ------------
    testName   : [         5] [5.0000000000000] [5.00000   ]



EOF;

        $this->assertEquals($expected, $ret);
    }
}