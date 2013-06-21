<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 9:57 PM
 */

namespace Athletic\Discovery;

/**
 * Class RecursiveFileLoader
 * @package Athletic\Discovery
 */
class RecursiveFileLoader
{
    /** @var array  */
    private $fqns = array();


    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $files           = $this->scanDirectory($path);
        $parsedPHPFiles  = $this->parsePHPFiles($files);
        $athleticClasses = $this->getAthleticClasses($parsedPHPFiles);

        //$this->includeClasses($athleticClasses);
        $this->fqns = $this->getFQN($athleticClasses);

    }

    /**
     * @return string[]
     */
    public function getClasses()
    {
        return $this->fqns;
    }


    /**
     * @param array $files
     *
     * @return array
     */
    private function parsePHPFiles($files)
    {
        $parsedPHP = array();
        foreach ($files as $file) {
            $parsedPHP[] = new PHPParser($file);
        }

        return $parsedPHP;
    }


    /**
     * @param PHPParser[] $parsedPHPFiles
     * @return PHPParser[]
     */
    private function getAthleticClasses($parsedPHPFiles)
    {
        /** @var PHPParser[] $athleticClasses */
        $athleticClasses = array();

        foreach ($parsedPHPFiles as $class) {
            /** @var PHPParser $class */
            if ($class->isAthleticEvent() === true) {
                $athleticClasses[] = $class;
            }
        }

        return $athleticClasses;
    }


    /**
     * @param PHPParser[] $classesToInclude
     */
    private function includeClasses($classesToInclude)
    {
        foreach ($classesToInclude as $class) {
            require_once($class->getPath());
        }
    }


    /**
     * @param PHPParser[] $athleticClasses
     * @return array
     */
    private function getFQN($athleticClasses)
    {
        $fqns = array();
        foreach ($athleticClasses as $class) {
            $fqns[] = $class->getFQN();
        }

        return $fqns;
    }

    /**
     * @param string $dir
     * @param string $prefix
     *
     * @return array
     */
    private function scanDirectory($dir, $prefix = '')
    {
        $dir    = rtrim($dir, '\\/');
        $result = array();

        foreach (scandir($dir) as $f) {
            if ($f !== '.' and $f !== '..') {
                if (is_dir("$dir/$f")) {
                    $result = array_merge($result, $this->scanDirectory("$dir/$f", "$prefix$f/"));
                } else {
                    $result[] = $dir .'/' . $f;
                }
            }
        }

        return $result;
    }
}