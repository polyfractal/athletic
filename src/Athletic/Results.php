<?php
/**
 * User: zach
 * Date: 6/14/13
 * Time: 2:02 PM
 */

namespace Athletic;


class Results
{
    public $results;
    public $iterations;
    public $sum;
    public $avg;
    public $max;
    public $min;
    public $ops;

    public function __construct($results, $iterations)
    {
        $this->results    = $results;
        $this->iterations = $iterations;
        $this->sum        = array_sum($results);
        $this->avg        = ($this->sum / count($results));
        $this->max        = max($results);
        $this->min        = min($results);
        $this->ops        = $iterations / $this->sum;
    }

    public function __toString()
    {
        $ret = $this->avg.', ';
        $ret .= $this->min.', ';
        $ret .= $this->max.', ';
        $ret .= $this->ops.', ';
        return $ret;
    }
}