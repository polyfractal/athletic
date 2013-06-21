<?php
/**
 * User: zach
 * Date: 6/21/13
 * Time: 11:15 AM
 */

namespace Athletic\Discovery;

use Athletic\Factories\AbstractFactory;

/**
 * Class ParserFactory
 * @package Athletic\Discovery
 */
class ParserFactory extends AbstractFactory
{
    /**
     * @param string $path
     *
     * @return Parser
     */
    public function create($path)
    {
        return $this->container['parser']($path);
    }
}