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
        $files = $this->scanDirectory($path);
        return $this->loadClasses($files);

    }


    /**
     * @param array $files
     * @return array
     */
    private function loadClasses($files)
    {
        $initialClasses = get_declared_classes();
        foreach ($files as $file) {
            include $file;
        }
        $updatedClassList = get_declared_classes();

        return array_values(array_diff($updatedClassList, $initialClasses));
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