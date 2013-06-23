<?php
/**
 * User: zach
 * Date: 6/14/13
 * Time: 2:02 PM
 */

namespace Athletic\Results;

/**
 * Class Results
 * @package Athletic
 */
class MethodResults
{
    public $methodName;
    public $results;
    public $iterations;
    public $sum;
    public $avg;
    public $max;
    public $min;
    public $ops;


    public function __construct($name, $results, $iterations)
    {
        $this->methodName = $name;
        $this->results    = $results;
        $this->iterations = $iterations;
        $this->sum        = array_sum($results);
        $this->avg        = ($this->sum / count($results));
        $this->max        = max($results);
        $this->min        = min($results);
        $this->ops        = $iterations / $this->sum;
    }
}