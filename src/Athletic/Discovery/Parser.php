<?php
/**
 * User: zach
 * Date: 6/20/13
 * Time: 7:32 PM
 */

namespace Athletic\Discovery;

/**
 * Class Parser
 * @package Athletic\Discovery
 */
class Parser
{
    private $className;
    private $parentClassName;
    private $namespace;

    /** @var  string */
    private $path;


    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
        $php        = file_get_contents($path);
        $this->parsePHP($php);
    }


    /**
     * @return bool
     */
    public function isAthleticEvent()
    {
        if (strpos($this->parentClassName, 'AthleticEvent') !== false) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @return string
     */
    public function getFQN()
    {
        $fqn = '';

        if (isset($this->namespace)) {
            $fqn .= $this->namespace;
        }

        if (isset($this->className) === true) {
            $fqn .= '\\' . $this->className;
        }

        return $fqn;
    }


    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }


    private function parsePHP($php)
    {
        $tokens = token_get_all($php);

        $this->setNamespace($tokens);
        $this->setClassName($tokens);
        $this->setParentClass($tokens);
    }


    /**
     * @param array $tokens
     */
    private function setNamespace($tokens)
    {
        $tokenCount = count($tokens);

        for ($i = 0; $i < $tokenCount; ++$i) {
            if ($tokens[$i][0] === T_CLASS) {
                return;
            }

            if ($tokens[$i][0] === T_NAMESPACE) {
                $this->namespace = $this->extractNamespacePath($tokens, $i);
                return;
            }
        }
        return;
    }


    /**
     * @param array $tokens
     * @param int   $i
     *
     * @return string
     */
    private function extractNamespacePath($tokens, $i)
    {
        $namespace = '';
        $i += 2;

        while ($tokens[$i][0] === T_NS_SEPARATOR || $tokens[$i][0] === T_STRING) {
            $namespace .= $tokens[$i][1];
            $i += 1;
        }

        return $namespace;
    }


    /**
     * @param array $tokens
     */
    private function setClassName($tokens)
    {
        $tokenCount = count($tokens);

        for ($i = 0; $i < $tokenCount; ++$i) {
            if (is_array($tokens[$i]) === true && $tokens[$i][0] === T_CLASS) {
                $this->className = $tokens[$i + 2][1];
                return;
            }
        }
    }


    /**
     * @param array $tokens
     */
    private function setParentClass($tokens)
    {
        $tokenCount = count($tokens);

        for ($i = 0; $i < $tokenCount; ++$i) {
            if ($tokens[$i][0] === T_EXTENDS) {
                $this->parentClassName = $this->extractParentClassName($tokens, $i);
                return;
            }
        }
        return;
    }


    /**
     * @param array $tokens
     * @param int   $i
     *
     * @return string
     */
    private function extractParentClassName($tokens, $i)
    {
        $parentClass = '';
        $i += 2;

        while ($tokens[$i][0] !== T_WHITESPACE) {
            $parentClass .= $tokens[$i][1];
            $i += 1;
        }
        return $parentClass;
    }

}
