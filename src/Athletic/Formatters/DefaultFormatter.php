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

        $header = array(
            'Method Name',
            'Iterations',
            'Average Time',
            'Ops/second',
        );

        foreach ($results as $result) {
            $returnString .= $result->getClassName() . "\n";

            // build a table containing the formatted numbers
            $table = array();
            foreach ($result as $methodResult) {
                $table[] = array(
                    $methodResult->methodName,
                    number_format($methodResult->iterations),
                    number_format($methodResult->avg, 13),
                    number_format($methodResult->ops, 5),
                );
            }

            // determine column widths for table layout
            $lengths = array_map('strlen', $header);
            foreach ($table as $row) {
                foreach ($row as $name => $value) {
                    $lengths[$name] = max(strlen($value), $lengths[$name]);
                }
            }

            // format header and table rows
            $returnString .= sprintf(
                "    %s   %s   %s   %s\n",
                str_pad($header[0], $lengths[0]),
                str_pad($header[1], $lengths[1]),
                str_pad($header[2], $lengths[2]),
                str_pad($header[3], $lengths[3])
            );
            $returnString .= sprintf(
                "    %s %s %s %s\n",
                str_repeat('-', $lengths[0] + 1),
                str_repeat('-', $lengths[1] + 2),
                str_repeat('-', $lengths[2] + 2),
                str_repeat('-', $lengths[3] + 2)
            );
            foreach ($table as $row) {
                $returnString .= sprintf(
                    "    %s: [%s] [%s] [%s]\n",
                    str_pad($row[0], $lengths[0]),
                    str_pad($row[1], $lengths[1]),
                    str_pad($row[2], $lengths[2]),
                    str_pad($row[3], $lengths[3])
                );
            }

            $returnString .= "\n\n";
        }

        return $returnString;
    }
}