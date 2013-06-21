<?php
/**
 * User: zach
 * Date: 6/21/13
 * Time: 11:15 AM
 */

namespace Athletic\Discovery;

/**
 * Class ParserFactory
 * @package Athletic\Discovery
 */
class ParserFactory
{
    /** @var  callback */
    private $parser;


    /**
     * @param callback $parser
     */
    public function __construct($parser)
    {
        $this->parser = $parser;
    }


    /**
     * @param string $path
     *
     * @return Parser
     */
    public function getParser($path)
    {
        $parser = $this->parser;
        return $parser($path);
    }
}