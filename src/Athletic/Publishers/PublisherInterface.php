<?php
/**
 * User: zach
 * Date: 6/15/13
 * Time: 9:53 PM
 */

namespace Athletic\Publishers;

/**
 * Class PublisherInterface
 * @package Athletic\Publishers
 */
interface PublisherInterface
{
    /**
     * @param \Athletic\Results\ClassResults $results
     *
     * @return mixed
     */
    public function publish($results);
}
