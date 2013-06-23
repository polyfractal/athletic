<?php
/**
 * User: zach
 * Date: 6/21/13
 * Time: 12:07 PM
 */

namespace Athletic\Tests\Discovery;

use Athletic\Discovery\Parser;
use Mockery as m;
use org\bovigo\vfs\vfsStream;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    private $root;


    public function setUp()
    {
        $this->root = vfsStream::setup('root');

        $structure = array(
            'testAthleticWithNamespace.php'              => '<?php namespace Vendor\Package\Child1; class Class1 extends \Athletic\AthleticEvent {}',
            'testNonAthleticWithNamespace.php'           => '<?php namespace Vendor\Package\Child1; class Class1',
            'testAthleticNoNamespace.php'                => '<?php class Class1 extends \Athletic\AthleticEvent {}',
            'testNonAthleticNoNamespace.php'             => '<?php class Class1 {}',
            'testAthleticWithNamespaceImplements.php'    => '<?php namespace Vendor\Package\Child1; class Class1 extends \Athletic\AthleticEvent implements Abc{}',
            'testNonAthleticWithNamespaceImplements.php' => '<?php namespace Vendor\Package\Child1; class Class1 implements Abc',
            'testAthleticNoNamespaceImplements.php'      => '<?php class Class1 extends \Athletic\AthleticEvent implements Abc {}',
            'testNonAthleticNoNamespaceImplements.php'   => '<?php class Class1 implements Abc {}',
            'testNoClass.php'                            => '<?php function() {echo "abc";}'
        );

        vfsStream::create($structure, $this->root);
    }


    public function tearDown()
    {
        m::close();
    }


    public function testAthleticWithNamespace()
    {
        $path   = vfsStream::url('root\testAthleticWithNamespace.php');
        $parser = new Parser($path);
        $this->assertTrue($parser->isAthleticEvent());
        $this->assertEquals('Vendor\Package\Child1\Class1', $parser->getFQN());
        $this->assertEquals('vfs://root/testAthleticWithNamespace.php', $parser->getPath());
    }


    public function testNonAthleticWithNamespace()
    {
        $path   = vfsStream::url('root\testNonAthleticWithNamespace.php');
        $parser = new Parser($path);
        $this->assertFalse($parser->isAthleticEvent());
        $this->assertEquals('Vendor\Package\Child1\Class1', $parser->getFQN());
        $this->assertEquals('vfs://root/testNonAthleticWithNamespace.php', $parser->getPath());
    }


    public function testAthleticNoNamespace()
    {
        $path   = vfsStream::url('root\testAthleticNoNamespace.php');
        $parser = new Parser($path);
        $this->assertTrue($parser->isAthleticEvent());
        $this->assertEquals('\Class1', $parser->getFQN());
        $this->assertEquals('vfs://root/testAthleticNoNamespace.php', $parser->getPath());
    }


    public function testNonAthleticNoNamespace()
    {
        $path   = vfsStream::url('root\testNonAthleticNoNamespace.php');
        $parser = new Parser($path);
        $this->assertFalse($parser->isAthleticEvent());
        $this->assertEquals('\Class1', $parser->getFQN());
        $this->assertEquals('vfs://root/testNonAthleticNoNamespace.php', $parser->getPath());
    }


    public function testAthleticWithNamespaceImplements()
    {
        $path   = vfsStream::url('root\testAthleticWithNamespaceImplements.php');
        $parser = new Parser($path);
        $this->assertTrue($parser->isAthleticEvent());
        $this->assertEquals('Vendor\Package\Child1\Class1', $parser->getFQN());
        $this->assertEquals('vfs://root/testAthleticWithNamespaceImplements.php', $parser->getPath());
    }


    public function testNonAthleticWithNamespaceImplements()
    {
        $path   = vfsStream::url('root\testNonAthleticWithNamespaceImplements.php');
        $parser = new Parser($path);
        $this->assertFalse($parser->isAthleticEvent());
        $this->assertEquals('Vendor\Package\Child1\Class1', $parser->getFQN());
        $this->assertEquals('vfs://root/testNonAthleticWithNamespaceImplements.php', $parser->getPath());
    }


    public function testAthleticNoNamespaceImplements()
    {
        $path   = vfsStream::url('root\testAthleticNoNamespaceImplements.php');
        $parser = new Parser($path);
        $this->assertTrue($parser->isAthleticEvent());
        $this->assertEquals('\Class1', $parser->getFQN());
        $this->assertEquals('vfs://root/testAthleticNoNamespaceImplements.php', $parser->getPath());
    }


    public function testNonAthleticNoNamespaceImplements()
    {
        $path   = vfsStream::url('root\testNonAthleticNoNamespaceImplements.php');
        $parser = new Parser($path);
        $this->assertFalse($parser->isAthleticEvent());
        $this->assertEquals('\Class1', $parser->getFQN());
        $this->assertEquals('vfs://root/testNonAthleticNoNamespaceImplements.php', $parser->getPath());
    }


    public function testNoClass()
    {
        $path   = vfsStream::url('root\testNoClass.php');
        $parser = new Parser($path);
        $this->assertFalse($parser->isAthleticEvent());
        $this->assertEmpty($parser->getFQN());
        $this->assertEquals('vfs://root/testNoClass.php', $parser->getPath());
    }


}