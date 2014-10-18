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
    public $group;
    public $baseline = false;
    public $dataSet;


    public function __construct($name, $results, $iterations, $dataSet)
    {
        $this->methodName = $name;
        $this->results    = $results;
        $this->iterations = $iterations;
        $this->sum        = array_sum($results);
        $this->avg        = ($this->sum / count($results));
        $this->max        = max($results);
        $this->min        = min($results);
        $this->ops        = ($this->sum == 0.0) ? NAN : ($iterations / $this->sum);
        $this->baseline   = false;
        $this->dataSet    = $dataSet;
    }

    public function getDataSetAsString()
    {
        $res = array();
        foreach ($this->dataSet as $name => $value) {
            $res[] = $name . '=' . $value;
        }
        return implode(',', $res);
    }

    public function getFullMethodName()
    {
        return substr($this->methodName, 7) . '(' . $this->getDataSetAsString() .')';
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function setBaseline()
    {
        $this->baseline = true;
    }
}
