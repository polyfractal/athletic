<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 9:53 PM
 */

namespace Athletic\Publishers;


use Athletic\Formatters\FormatterInterface;

/**
 * Class PublisherInterface
 * @package Athletic\Publishers
 */
interface PublisherInterface
{
    /**
     * @param FormatterInterface $formatter
     */
    public function __construct(FormatterInterface $formatter);


    /**
     * @param array $results
     *
     * @return mixed
     */
    public function publish($results);
}