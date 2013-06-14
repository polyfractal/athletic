<?php
/**
 * User: zach
 * Date: 6/14/13
 * Time: 11:48 AM
 */

namespace Athletic;


use Commando;
use ReflectionClass;

class Athletic
{
    /** @var  Commando\Command() */
    private $cmdArgs;

    /** @var array  */
    private $declaredClasses = array();

    public function __construct()
    {
        $this->initializeCmdArgs();
    }

    public function run()
    {
        $classesToBenchmark = $this->loadClassesFromPath($this->cmdArgs['path']);
        $this->benchmark($classesToBenchmark);

    }

    private function benchmark($classes)
    {
        $results = array();
        foreach ($classes as $class) {
            $results[$class] = $this->benchmarkClass($class);
        }

        $this->formatResults($results);
    }


    /**
     * @param $class
     *
     * @return array
     */
    private function benchmarkClass($class)
    {
        if ($this->isBenchmarkableClass($class) !== true) {
            return array();
        }

        $object = new $class();
        return $object->run();
    }

    private function isBenchmarkableClass($class)
    {
        $reflectionClass = new ReflectionClass($class);
        return ($reflectionClass->isAbstract() !== true && $reflectionClass->isSubclassOf('\Athletic\AthleticEvent') === true);
    }

    private function initializeCmdArgs()
    {
        $this->cmdArgs = new Commando\Command();
        $this->setCmdArgs();
        if ($this->cmdArgs['bootstrap'] !== null) {
            require ($this->cmdArgs['bootstrap']);
        }
    }

    private function setCmdArgs()
    {
        $this->cmdArgs->option('p')
            ->require()
            ->aka('path')
            ->describedAs('Path to benchmark events.');

        $this->cmdArgs->flag('b')
                      ->aka('bootstrap')
                      ->describedAs('Path to bootstrap file for your project');
    }

    private function loadClassesFromPath($path) {
        $this->declaredClasses = get_declared_classes();
        $files = $this->scanDirectory($path);

        foreach ($files as $file) {
            require_once("$path/$file");
        }

        $updatedClassList = get_declared_classes();

        $newClasses = array_diff($updatedClassList,$this->declaredClasses);
        return $newClasses;
    }

    private function scanDirectory($dir, $prefix = '') {
        $dir = rtrim($dir, '\\/');
        $result = array();

        foreach (scandir($dir) as $f) {
            if ($f !== '.' and $f !== '..') {
                if (is_dir("$dir/$f")) {
                    $result = array_merge($result, $this->scanDirectory("$dir/$f", "$prefix$f/"));
                } else {
                    $result[] = $prefix.$f;
                }
            }
        }

        return $result;
    }

    private function formatResults($results)
    {
        echo "\n";
        $results = array_filter($results);

        foreach ($results as $class => $result) {
            echo "$class\n";

            $longest = 0;
            foreach ($result as $method => $stats) {
                if (strlen($method) > $longest) {
                    $longest = strlen($method);
                }
            }
            echo '    '.str_pad('Method Name', $longest)."   Iterations      Average Time         Ops/second\n";

            echo '    '.str_repeat('-', $longest)."  ------------    --------------       -------------\n";

            foreach($result as $method => $stats) {

                $method = str_pad($method, $longest);
                $iterations = str_pad($stats->iterations, 10);
                $avg        = str_pad($stats->avg, 13);
                $ops        = str_pad($stats->ops, 5);
                echo "    $method: [$iterations] [$avg] [$ops/s]\n";
            }
            echo "\n\n";
        }
    }
}

