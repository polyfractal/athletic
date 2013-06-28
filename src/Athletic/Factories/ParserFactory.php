<?php
/**
 * User: zach
 * Date: 6/21/13
 * Time: 11:15 AM
 */

namespace Athletic\Factories;

use Athletic\Discovery\Parser;
use Athletic\Factories\AbstractFactory;

/**
 * Class ParserFactory
 * @package Athletic\Factories
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