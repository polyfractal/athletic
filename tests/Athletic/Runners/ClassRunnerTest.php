<?php
/**
 * User: zach
 * Date: 6/22/13
 * Time: 8:25 PM
 */

namespace Athletic\Tests\Runners;

use Athletic\Runners\ClassRunner;
use Mockery as m;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;
use ReflectionException;

class ClassRunnerTest extends PHPUnit_Framework_TestCase
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
        $class = 'abc';
        $mockMethodFactory = m::mock('\Athletic\Factories\MethodResultsFactory');
        $classRunner = new ClassRunner($mockMethodFactory, $class);
    }


    /**
     * @expectedException ReflectionException
     */
    public function testRunWithNonExistantClass()
    {
        $class = 'abc';
        $mockMethodFactory = m::mock('\Athletic\Factories\MethodResultsFactory');
        $classRunner = new ClassRunner($mockMethodFactory, $class);

        $classRunner->run();
    }


    public function testRunWithAthleticClass()
    {

        $classFile = <<<'EOF'
<?php

namespace Athletic\Tests\Runners;

class TestRunWithAthleticClass extends \Athletic\AthleticEvent
{
    public function setMethodFactory($method)
    {
        echo "setMethodFactory\n";
    }

    public function run()
    {
        echo "run\n";
        return array('field' => 'value');
    }

}
EOF;



        $structure = array(
            'classRunner' => array(
                'testRunWithAthleticClass.php' => $classFile
            )
        );

        vfsStream::create($structure, $this->root);
        $path = vfsStream::url('root\classRunner\testRunWithAthleticClass.php');

        include_once($path);

        $class = 'Athletic\Tests\Runners\TestRunWithAthleticClass';

        $mockMethodFactory = m::mock('\Athletic\Factories\MethodResultsFactory');
        $classRunner = new ClassRunner($mockMethodFactory, $class);

        $ret      = $classRunner->run();
        $expected = array('field' => 'value');

        $this->expectOutputString("setMethodFactory\nrun\n");
        $this->assertEquals($expected, $ret);
    }


    public function testRunWithNonAthleticClass()
    {

        $classFile = <<<'EOF'
<?php

namespace Athletic\Tests\Runners;

class TestRunWithNonAthleticClass
{
    public function setMethodFactory($method)
    {
        echo "setMethodFactory\n";
    }

    public function run()
    {
        echo "run\n";
        return array('field' => 'value');
    }

}
EOF;



        $structure = array(
            'classRunner' => array(
                'testRunWithNonAthleticClass.php' => $classFile
            )
        );

        vfsStream::create($structure, $this->root);
        $path = vfsStream::url('root\classRunner\testRunWithNonAthleticClass.php');

        include_once($path);

        $class = 'Athletic\Tests\Runners\TestRunWithNonAthleticClass';

        $mockMethodFactory = m::mock('\Athletic\Factories\MethodResultsFactory');
        $classRunner = new ClassRunner($mockMethodFactory, $class);

        $ret = $classRunner->run();

        $this->expectOutputString("");
        $this->assertEmpty($ret);
    }
}