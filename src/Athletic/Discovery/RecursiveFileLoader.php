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

    /** @var  ParserFactory */
    private $parserFactory;

    /**
     * @param ParserFactory $parserFactory
     * @param string           $path
     */
    public function __construct(ParserFactory $parserFactory, $path)
    {
        $this->parserFactory = $parserFactory;
        $files               = $this->scanDirectory($path);
        $parsedPHPFiles      = $this->parsePHPFiles($files);
        $athleticClasses     = $this->getAthleticClasses($parsedPHPFiles);

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
            $parsedPHP[] = $this->parserFactory->create($file);
        }

        return $parsedPHP;
    }


    /**
     * @param Parser[] $parsedPHPFiles
     * @return Parser[]
     */
    private function getAthleticClasses($parsedPHPFiles)
    {
        /** @var Parser[] $athleticClasses */
        $athleticClasses = array();

        foreach ($parsedPHPFiles as $class) {
            /** @var Parser $class */
            if ($class->isAthleticEvent() === true) {
                $athleticClasses[] = $class;
            }
        }

        return $athleticClasses;
    }

    /**
     * @param Parser[] $athleticClasses
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