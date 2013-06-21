<?php
/**
 * User: zach
 * Date: 6/20/13
 * Time: 4:33 PM
 */

namespace Athletic\Tests\Discovery;


use Athletic\Discovery\RecursiveFileLoader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit_Framework_TestCase;


class RecursiveFileLoaderTest extends PHPUnit_Framework_TestCase
{
    private $root;

    public function setUp()
    {
        $this->root = vfsStream::setup('root');
    }

    public function testLoadThreeClassesFromPath()
    {
        $class1 = '<?php namespace Vendor\Package\Child1; class Class1 extends \Athletic\AthleticEvent {}';
        $class2 = '<?php namespace Vendor\Package\Child2; class Class2 extends \Athletic\AthleticEvent {}';
        $class3 = '<?php namespace Vendor\Package\Child3; class Class3 extends \Athletic\AthleticEvent {}';

        $structure = array(
            'threeClasses' => array(
                'Package' => array(
                    'Child1' => array(
                        'Class1.php' => $class1
                    ),
                    'Child2' => array(
                        'Class2.php' => $class2,
                        'Class3.php' => $class3
                    )
                )
            )
        );

        vfsStream::create($structure, $this->root);

        $fileLoader = new RecursiveFileLoader(vfsStream::url('root\threeClasses\Package'));
        $classes = $fileLoader->getClasses();

        $expectedClasses = array(
            'Vendor\Package\Child1\Class1',
            'Vendor\Package\Child2\Class2',
            'Vendor\Package\Child3\Class3',
        );

        $this->assertEquals($expectedClasses, $classes);
    }

    public function testLoadThreeClassesFromPathOnlyOneIsAthletic()
    {
        $class1 = '<?php namespace Vendor\Package\Child1; class Class1 extends \Athletic\AthleticEvent {}';
        $class2 = '<?php namespace Vendor\Package\Child2; class Class2 {}';
        $class3 = '<?php namespace Vendor\Package\Child3; class Class3 {}';

        $structure = array(
            'threeClassesOneAthletic' => array(
                'Package' => array(
                    'Child1' => array(
                        'Class1.php' => $class1
                    ),
                    'Child2' => array(
                        'Class2.php' => $class2,
                        'Class3.php' => $class3
                    )
                )
            )
        );

        vfsStream::create($structure, $this->root);

        $fileLoader = new RecursiveFileLoader(vfsStream::url('root\threeClassesOneAthletic\Package'));
        $classes = $fileLoader->getClasses();

        $expectedClasses = array(
            'Vendor\Package\Child1\Class1',
        );

        $this->assertEquals($expectedClasses, $classes);
    }

    public function testLoadThreeClassesFromPathButNoAthletic()
    {
        $class1 = '<?php namespace Vendor\Package\Child1; class Class1 {}';
        $class2 = '<?php namespace Vendor\Package\Child2; class Class2 {}';
        $class3 = '<?php namespace Vendor\Package\Child3; class Class3 {}';

        $structure = array(
            'threeClassesOneAthletic' => array(
                'Package' => array(
                    'Child1' => array(
                        'Class1.php' => $class1
                    ),
                    'Child2' => array(
                        'Class2.php' => $class2,
                        'Class3.php' => $class3
                    )
                )
            )
        );

        vfsStream::create($structure, $this->root);

        $fileLoader = new RecursiveFileLoader(vfsStream::url('root\threeClassesOneAthletic\Package'));
        $classes = $fileLoader->getClasses();

        $this->assertEmpty($classes);
    }
}