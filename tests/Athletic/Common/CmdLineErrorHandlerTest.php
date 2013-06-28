<?php
/**
 * User: zach
 * Date: 6/28/13
 * Time: 7:19 AM
 */

namespace Athletic\Tests\Common;

use Mockery as m;
use \Athletic\Common\CmdLineErrorHandler;

/**
 * Class CmdLineErrorHandler
 * @package Athletic\Tests\Common
 */
class CmdLineErrorHandlerTest  extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testException()
    {
        $mockException = m::mock('\Exception');
        $mockCommand = m::mock('\Commando\Command')
                       ->shouldReceive('error')
                       ->with($mockException)
                       ->getMock();

        $mockErrorExceptionFactory = m::mock('\Athletic\Factories\ErrorExceptionFactory');

        $handler = new CmdLineErrorHandler($mockCommand, $mockErrorExceptionFactory);
        $handler->handleException($mockException);

    }

    public function testError()
    {
        $errorLevel   = 1;
        $errorMessage = 'abc';
        $errorFile    = 'abc';
        $errorLine    = 1;

        $mockException = m::mock('\Exception');
        $mockCommand = m::mock('\Commando\Command')
                       ->shouldReceive('error')
                       ->with($mockException)
                       ->getMock();

        $mockErrorExceptionFactory = m::mock('\Athletic\Factories\ErrorExceptionFactory')
                                     ->shouldReceive('create')
                                     ->with($errorLevel, $errorMessage, $errorFile, $errorLine, array())
                                     ->andReturn($mockException)
                                     ->getMock();

        $handler = new CmdLineErrorHandler($mockCommand, $mockErrorExceptionFactory);

        $handler->handleError($errorLevel, $errorMessage, $errorFile, $errorLine);
    }


}