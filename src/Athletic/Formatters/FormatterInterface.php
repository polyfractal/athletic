<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 9:50 PM
 */

namespace Athletic\Formatters;

/**
 * Class FormatterInterface
 * @package Athletic\Formatters
 */
interface FormatterInterface
{
    /**
     * @param array $results
     *
     * @return string
     */
    public function getFormattedResults($results);
}