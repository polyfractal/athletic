<?php
/**
 * User: zach
 * Date: 7/29/13
 * Time: 10:32 AM
 */

namespace Athletic\Formatters;

use Athletic\Common\Exceptions\OnlyOneBaselineAllowedException;
use Athletic\Results;
use Athletic\Results\ClassResults;
use Athletic\Results\MethodResults;

/**
 * Class GroupedFormatter
 * @package Athletic\Formatters
 */
class GroupedFormatter implements FormatterInterface
{
    /**
     * @param ClassResults[] $results
     *
     * @return string
     */
    public function getFormattedResults($results)
    {

        $returnData   = $this->transformResultsToArray($results);
        $returnString = $this->getFormattedString($returnData);

        return $returnString;
    }


    /**
     * @param ClassResults[] $results
     *
     * @return array
     */
    private function transformResultsToArray($results)
    {
        $returnData = array();

        foreach ($results as $result) {

            $class = $result->getClassName();
            $returnData[$class] = array();

            foreach ($result as $methodResult) {
                if (isset($methodResult->group) === true) {
                    $group = $methodResult->group;
                } else {
                    $group = "No Group";
                }

                $baseline = $methodResult->baseline;

                if ($baseline === true) {
                    $baseline = "baseline";
                } else {
                    $baseline = "experimental";
                }
                $returnData[$class][$group][$baseline][] = $methodResult;

            }
        }

        return $returnData;
    }


    /**
     * @param array $data
     *
     * @return string
     */
    private function getFormattedString($data)
    {
        $returnString = "";

        foreach ($data as $className => $groups) {
            $returnString .= "$className\n";

            foreach ($groups as $groupName => $results) {

                $returnString .= $this->getGroupHeaderString($groupName);
                $returnString .= $this->getBaselineString($results);

                $baselineTime = $this->getBaselineTime($results);

                if (isset($results['experimental']) === true) {
                    foreach ($results['experimental'] as $result) {
                        $returnString .= $this->parseMethodResultToString($result, false, $baselineTime);

                    }
                }
                $returnString .= "\n";
            }
            $returnString .= "\n";
        }


        return $returnString;
    }


    /**
     * @param string $groupName
     * @param int    $padding
     *
     * @return string
     */
    private function getGroupHeaderString($groupName, $padding = 30)
    {
        $returnString =  "  $groupName\n    " . str_pad(
                'Method Name',
                $padding
            ) . "              Iterations    Average Time      Ops/s    Relative\n";

        $returnString .= '    ' . str_repeat('-', $padding) . "  ----------  ------------ --------------   ---------  ---------\n";

        return $returnString;
    }


    /**
     * @param array $results
     *
     * @return string
     * @throws \Athletic\Common\Exceptions\OnlyOneBaselineAllowedException
     */
    private function getBaselineString($results)
    {
        if (isset($results['baseline']) !== true) {
            return "";
        }

        if (count($results['baseline']) > 1) {
            throw new OnlyOneBaselineAllowedException("Only one baseline may be specified per group.");
        }

        return $this->parseMethodResultToString($results['baseline'][0], true);
    }


    /**
     * @param array $results
     *
     * @return int
     */
    private function getBaselineTime($results)
    {
        if (isset($results['baseline'][0]) === true) {
            return $results['baseline'][0]->avg;
        } else {
            return 0;
        }
    }

    /**
     * @param MethodResults $result
     * @param null|float    $baselineTime
     * @param bool          $baseline
     *
     * @return string
     */
    private function parseMethodResultToString(MethodResults $result, $baseline, $baselineTime = null)
    {
        $method     = str_pad($result->methodName, 30);
        $iterations = str_pad(number_format($result->iterations), 10);
        $avg        = str_pad(number_format($result->avg, 13), 13);
        $ops        = str_pad(number_format($result->ops, 5), 7);

        if ($baseline === true) {
            return "    $method: [Baseline] [$iterations] [$avg] [$ops]\n";
        } else {

            if ($result->avg === $baselineTime) {
                $percentage = "[100%]";
            } elseif ($baselineTime === 0) {
                $percentage = "";
            } else {
                $percentage = str_pad(number_format(($result->avg / $baselineTime)*100, 2), 2);
                $percentage = "[$percentage%]";
            }

            return "    $method:            [$iterations] [$avg] [$ops] $percentage\n";
        }

    }
}