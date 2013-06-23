<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 9:47 PM
 */

namespace Athletic\Formatters;

use Athletic\Results;
use Athletic\Results\ClassResults;
use Athletic\Results\MethodResults;

/**
 * Class DefaultFormatter
 * @package Athletic\Formatters
 */
class DefaultFormatter implements FormatterInterface
{
    /**
     * @param ClassResults[] $results
     *
     * @return string
     */
    public function getFormattedResults($results)
    {
        $returnString = "\n";


        foreach ($results as $result) {
            $returnString .= $result->getClassName() . "\n";

            $longest = 0;
            /** @var MethodResults $methodResult */
            foreach ($result as $methodResult) {
                if (strlen($methodResult->methodName) > $longest) {
                    $longest = strlen($methodResult->methodName);
                }
            }
            $returnString .= '    ' . str_pad(
                    'Method Name',
                    $longest
                ) . "   Iterations    Average Time      Ops/second\n";

            $returnString .= '    ' . str_repeat('-', $longest) . "  ------------  --------------    -------------\n";

            foreach ($result as $methodResult) {

                $method     = str_pad($methodResult->methodName, $longest);
                $iterations = str_pad(number_format($methodResult->iterations), 10);
                $avg        = str_pad(number_format($methodResult->avg, 13), 13);
                $ops        = str_pad(number_format($methodResult->ops, 5), 7);
                $returnString .= "    $method: [$iterations] [$avg] [$ops]\n";
            }
            $returnString .= "\n\n";
        }

        return $returnString;
    }
}