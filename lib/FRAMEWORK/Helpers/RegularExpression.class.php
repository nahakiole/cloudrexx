<?php

/**
 * Class RegularExpression
 *
 * @copyright   CONTREXX CMS - CLOUDREXX AG
 * @author      Project Team SS4U <info@comvation.com>
 * @package     contrexx
 * @subpackage  lib_helpers
 */

namespace Cx\Lib\Helpers;

/**
 * Class RegularExpression
 *
 * @copyright   CONTREXX CMS - CLOUDREXX AG
 * @author      Project Team SS4U <info@comvation.com>
 * @package     contrexx
 * @subpackage  lib_helpers
 */
class RegularExpression
{
    /**
     * Regex
     * 
     * @var string 
     */
    protected $regex = '';
    
    /**
     * Replacement string
     * 
     * @var string
     */
    protected $replacement;

    /**
     * Wheter this regex has a replacement or not
     *
     * @var boolean
     */
    protected $hasReplacement = false;
    
    /**
     * Delimiter
     * 
     * @var string 
     */
    protected $delimiter = '/';
    
    /**
     * Flags
     * 
     * @var array
     */
    protected $flags = array();

    /**
     * Contructor for RegularExpression
     * 
     * @param string $regex Regular expression
     */
    public function __construct($regex = '')
    {
        if (empty($regex)) {
            return;
        }

        $this->delimiter = substr($regex, 0, 1);
        $parts = explode($this->delimiter, $regex);

        $this->regex = $parts[1];
        if (count($parts) == 3) {
            $this->flags = str_split($parts[2]);
            $this->hasReplacement = false;
        } else if (count($parts) == 4) {
            $this->replacement = $parts[2];
            $this->hasReplacement = true;
            $this->flags = str_split($parts[3]);
        } else {
            throw new \Exception('Illegal regex syntax');
        }
    }

    /**
     * Getter for $regex
     * 
     * @return string
     */
    function getRegex()
    {
        return $this->regex;
    }

    /**
     * Getter for $replacement
     * 
     * @return string
     */
    function getReplacement()
    {
        return $this->replacement;
    }
    
    /**
     * Getter for Delimiter
     * 
     * @return string
     */
    function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Getter for flags
     * 
     * @return array
     */
    function getFlags()
    {
        return $this->flags;
    }
    
    /**
     * Set the regular expression
     * 
     * @param string $regex
     */
    function setRegex($regex)
    {
        $this->regex = $regex;
    }
    
    /**
     * Set the replacement string
     * 
     * @param string $replacement
     */
    function setReplacement($replacement)
    {
        $this->replacement = $replacement;
        $this->hasReplacement = true;
    }
    
    /**
     * Set the delimiter
     * 
     * @param string $delimiter
     */
    function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * Set the flags
     * 
     * @param array $flags
     */
    function setFlags($flags)
    {
        $this->flags = $flags;
    }
    
    /**
     * Match the input string with regular expression
     * 
     * @param string $input Input string
     * 
     * @return boolean True|False True on regular expression matches the string
     */
    function match($input)
    {
        return preg_match($this->delimiter . $this->regex . $this->delimiter, $input, $matches);
    }
    
    /**
     * Search and replace in the Input string
     * 
     * @param string $input Input string
     * 
     * @return string Replaced string
     */
    function replace($input)
    {
        if (!$this->hasReplacement) {
            throw new \Exception('Nothing to replace with');
        }
        return preg_replace($this->delimiter . $this->regex . $this->delimiter, $this->replacement, $input);
    }
    
    /**
     * Return the regular expression concatenated by delimiter
     * 
     * @return string Return the regular expression concatenated by delimiter
     */
    function __toString()
    {
        $regularExpression = $this->delimiter . $this->regex . $this->delimiter;
        if ($this->hasReplacement) {
            $regularExpression .= $this->replacement . $this->delimiter;
        }
        $regularExpression .= implode('', $this->flags);
        return $regularExpression;
    }
}
