<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 9:54 PM
 */

namespace Athletic\Publishers;

use Athletic\Formatters\FormatterInterface;

/**
 * Class StdOutPublisher
 * @package Athletic\Publishers
 */
class StdOutPublisher implements PublisherInterface
{
    /** @var FormatterInterface */
    private $formatter;


    /**
     * @param FormatterInterface $formatter
     */
    public function __construct(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }


    /**
     * @param array $results
     *
     * @return void
     */
    public function publish($results)
    {
        $data = $this->formatter->getFormattedResults($results);
        echo $data;
    }
}