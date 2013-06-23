<?php
/**
 * User: zach
 * Date: 6/22/13
 * Time: 7:54 PM
 */

namespace Athletic\Tests\Common;

use Athletic\Common\CmdLine;
use Mockery as m;
use org\bovigo\vfs\vfsStream;

class CmdLineTest extends \PHPUnit_Framework_TestCase
{
    private $root;


    public function setUp()
    {
        $this->root = vfsStream::setup('root');
    }


    public function tearDown()
    {
        m::close();
    }


    public function testConstructor()
    {
        $mockCommand = m::mock('\Commando\Command');
        $mockCommand->shouldReceive('option->require->aka->describedAs')->once();
        $mockCommand->shouldReceive('flag->aka->describedAs')->once();
        $mockCommand->shouldReceive('offsetGet')->once()->with('bootstrap')->andReturnNull();

        $cmdLine = new CmdLine($mockCommand);
    }


    public function testConstructorWithBootstrap()
    {
        $bootstrap = '<?php namespace Vendor\Package\Child1; class Class1 {}';

        $structure = array(
            'bootstrap' => array(
                'bootstrap.php' => $bootstrap
            )
        );

        vfsStream::create($structure, $this->root);
        $path = vfsStream::url('root\bootstrap\bootstrap.php');

        $mockCommand = m::mock('\Commando\Command');
        $mockCommand->shouldReceive('option->require->aka->describedAs')->once();
        $mockCommand->shouldReceive('flag->aka->describedAs')->once();
        $mockCommand->shouldReceive('offsetGet')->twice()->with('bootstrap')->andReturn($path);

        $cmdLine = new CmdLine($mockCommand);

        $loadedClasses = get_declared_classes();

        $this->assertContains('Vendor\Package\Child1\Class1', $loadedClasses);
    }
}