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
    /**
     * @param string $path
     *
     * @return string[]
     */
    public function getClasses($path)
    {
        return $this->loadClassesFromPath($path);
    }


    /**
     * @param string $path
     *
     * @return array
     */
    private function loadClassesFromPath($path)
    {
        $initialClasses = get_declared_classes();

        $this->includeFiles($path);

        $updatedClassList = get_declared_classes();
        return array_diff($updatedClassList, $initialClasses);

    }


    /**
     * @param string $path
     */
    private function includeFiles($path)
    {
        $files = $this->scanDirectory($path);

        foreach ($files as $file) {
            include "$path/$file";
        }
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
                    $result[] = $prefix . $f;
                }
            }
        }

        return $result;
    }
}