<?php
/**
 * User: zach
 * Date: 7/29/13
 * Time: 10:45 AM
 */

namespace Athletic\Tests\Formatters;

use ArrayIterator;
use Athletic\Common\Exceptions\OnlyOneBaselineAllowedException;
use Athletic\Formatters\GroupedFormatter;
use Athletic\Results;
use Athletic\Results\MethodResults;
use Mockery as m;

class GroupedFormatterTest extends \PHPUnit_Framework_TestCase
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
        $mockMethodResult->group      = 'Group1';
        $mockMethodResult->baseline   = true;


        $mockMethodResults = new ArrayIterator(array($mockMethodResult));

        $mockClassResult = m::mock('\Athletic\Results\ClassResults')
                           ->shouldReceive('getClassName')
                           ->andReturn('testClass')
                           ->getMock()
                           ->shouldReceive('getIterator')
                           ->andReturn($mockMethodResults)
                           ->getMock();

        $suiteResults[] = $mockClassResult;


        $formatter = new GroupedFormatter();
        $ret       = $formatter->getFormattedResults($suiteResults);

        $expected = <<<EOF
testClass
  Group1
    Method Name                                 Iterations    Average Time      Ops/s    Relative
    ------------------------------  ----------  ------------ --------------   ---------  ---------
    testName                      : [Baseline] [5         ] [5.0000000000000] [5.00000]



EOF;

        $this->assertEquals($expected, $ret);
    }


    /**
     * @expectedException Athletic\Common\Exceptions\OnlyOneBaselineAllowedException
     */
    public function testOneClassThreeBaselines()
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
        $mockMethodResult->group      = 'Group1';
        $mockMethodResult->baseline   = true;


        $mockMethodResults = new ArrayIterator(array($mockMethodResult, $mockMethodResult, $mockMethodResult));

        $mockClassResult = m::mock('\Athletic\Results\ClassResults')
                           ->shouldReceive('getClassName')
                           ->andReturn('testClass')
                           ->getMock()
                           ->shouldReceive('getIterator')
                           ->andReturn($mockMethodResults)
                           ->getMock();

        $suiteResults[] = $mockClassResult;


        $formatter = new GroupedFormatter();
        $ret       = $formatter->getFormattedResults($suiteResults);

    }

    public function testOneClassThreeTests()
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
        $mockMethodResult->group      = 'Group1';
        $mockMethodResult->baseline   = true;

        /** @var MethodResults $mockMethodResult */
        $mockMethodResult2             = m::mock('\Athletic\Results\MethodResults');
        $mockMethodResult2->methodName = 'testName';
        $mockMethodResult2->avg        = 3;
        $mockMethodResult2->min        = 3;
        $mockMethodResult2->max        = 5;
        $mockMethodResult2->sum        = 5;
        $mockMethodResult2->iterations = 5;
        $mockMethodResult2->ops        = 5;
        $mockMethodResult2->group      = 'Group1';
        $mockMethodResult2->baseline   = false;


        $mockMethodResults = new ArrayIterator(array($mockMethodResult, $mockMethodResult2, $mockMethodResult2));

        $mockClassResult = m::mock('\Athletic\Results\ClassResults')
                           ->shouldReceive('getClassName')
                           ->andReturn('testClass')
                           ->getMock()
                           ->shouldReceive('getIterator')
                           ->andReturn($mockMethodResults)
                           ->getMock();

        $suiteResults[] = $mockClassResult;


        $formatter = new GroupedFormatter();
        $ret       = $formatter->getFormattedResults($suiteResults);
        $expected = <<<EOF
testClass
  Group1
    Method Name                                 Iterations    Average Time      Ops/s    Relative
    ------------------------------  ----------  ------------ --------------   ---------  ---------
    testName                      : [Baseline] [5         ] [5.0000000000000] [5.00000]
    testName                      :            [5         ] [3.0000000000000] [5.00000] [60.00%]
    testName                      :            [5         ] [3.0000000000000] [5.00000] [60.00%]



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
        $mockMethodResult->group      = 'Group1';
        $mockMethodResult->baseline   = true;

        /** @var MethodResults $mockMethodResult2 */
        $mockMethodResult2             = m::mock('\Athletic\Results\MethodResults');
        $mockMethodResult2->methodName = 'testName';
        $mockMethodResult2->avg        = 5;
        $mockMethodResult2->min        = 5;
        $mockMethodResult2->max        = 5;
        $mockMethodResult2->sum        = 5;
        $mockMethodResult2->iterations = 5;
        $mockMethodResult2->ops        = 5;
        $mockMethodResult2->group      = 'Group2';
        $mockMethodResult2->baseline   = true;

        /** @var MethodResults $mockMethodResult3 */
        $mockMethodResult3             = m::mock('\Athletic\Results\MethodResults');
        $mockMethodResult3->methodName = 'testName';
        $mockMethodResult3->avg        = 5;
        $mockMethodResult3->min        = 5;
        $mockMethodResult3->max        = 5;
        $mockMethodResult3->sum        = 5;
        $mockMethodResult3->iterations = 5;
        $mockMethodResult3->ops        = 5;
        $mockMethodResult3->group      = 'Group3';
        $mockMethodResult3->baseline   = true;

        $mockMethodResults = new ArrayIterator(array($mockMethodResult));
        $mockMethodResults2 = new ArrayIterator(array($mockMethodResult2));
        $mockMethodResults3 = new ArrayIterator(array($mockMethodResult3));

        $mockClassResult = m::mock('\Athletic\Results\ClassResults')
                           ->shouldReceive('getClassName')
                           ->andReturn('testClass')
                           ->getMock()
                           ->shouldReceive('getIterator')
                           ->andReturn($mockMethodResults)
                           ->getMock();

        $mockClassResult2 = m::mock('\Athletic\Results\ClassResults')
                           ->shouldReceive('getClassName')
                           ->andReturn('testClass2')
                           ->getMock()
                           ->shouldReceive('getIterator')
                           ->andReturn($mockMethodResults2)
                           ->getMock();

        $mockClassResult3 = m::mock('\Athletic\Results\ClassResults')
                           ->shouldReceive('getClassName')
                           ->andReturn('testClass3')
                           ->getMock()
                           ->shouldReceive('getIterator')
                           ->andReturn($mockMethodResults3)
                           ->getMock();

        $suiteResults = array($mockClassResult, $mockClassResult2, $mockClassResult3);


        $formatter = new GroupedFormatter();
        $ret       = $formatter->getFormattedResults($suiteResults);

        $expected = <<<EOF
testClass
  Group1
    Method Name                                 Iterations    Average Time      Ops/s    Relative
    ------------------------------  ----------  ------------ --------------   ---------  ---------
    testName                      : [Baseline] [5         ] [5.0000000000000] [5.00000]


testClass2
  Group2
    Method Name                                 Iterations    Average Time      Ops/s    Relative
    ------------------------------  ----------  ------------ --------------   ---------  ---------
    testName                      : [Baseline] [5         ] [5.0000000000000] [5.00000]


testClass3
  Group3
    Method Name                                 Iterations    Average Time      Ops/s    Relative
    ------------------------------  ----------  ------------ --------------   ---------  ---------
    testName                      : [Baseline] [5         ] [5.0000000000000] [5.00000]



EOF;

        $this->assertEquals($expected, $ret);
    }
}