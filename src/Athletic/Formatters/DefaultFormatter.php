<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 9:47 PM
 */

namespace Athletic\Formatters;

/**
 * Class DefaultFormatter
 * @package Athletic\Formatters
 */
class DefaultFormatter implements FormatterInterface
{
    /**
     * @param array $results
     *
     * @return string
     */
    public function getFormattedResults($results)
    {
        $returnString = "\n";
        $results      = array_filter($results);

        foreach ($results as $class => $result) {
            $returnString .= "$class\n";

            $longest = 0;
            foreach ($result as $method => $stats) {
                if (strlen($method) > $longest) {
                    $longest = strlen($method);
                }
            }
            $returnString .= '    ' . str_pad(
                    'Method Name',
                    $longest
                ) . "   Iterations    Average Time      Ops/second\n";

            $returnString .= '    ' . str_repeat('-', $longest) . "  ------------  --------------    -------------\n";

            foreach ($result as $method => $stats) {

                $method = str_pad($method, $longest);
                $iterations = str_pad(number_format($stats->iterations), 10);
                $avg = str_pad(number_format($stats->avg, 13), 13);
                $ops = str_pad(number_format($stats->ops, 5), 7);
                $returnString .= "    $method: [$iterations] [$avg] [$ops]\n";
            }
            $returnString .= "\n\n";
        }

        return $returnString;
    }
}