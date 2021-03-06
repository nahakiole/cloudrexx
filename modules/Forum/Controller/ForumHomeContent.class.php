<?php
/**
 * Forum home content
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Comvation Development Team <info@comvation.com>
 * @version     1.0.0
 * @package     contrexx
 * @subpackage  module_forum
 * @todo        Edit PHP DocBlocks!
 */

namespace Cx\Modules\Forum\Controller;

/**
 * Forum home content
 *
 * Show Forum Block Content
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Comvation Development Team <info@comvation.com>
 * @access      public
 * @version     1.0.0
 * @package     contrexx
 * @subpackage  module_forum
 */
class ForumHomeContent extends ForumLibrary {

	var $_pageContent;
	var $_objTpl;

	/**
	 * Constructor php5
	 */
	function __construct($pageContent) {
		global $_LANGID;
	    $this->_pageContent = $pageContent;
	    $this->_objTpl = new \Cx\Core\Html\Sigma('.');
        \Cx\Core\Csrf\Controller\Csrf::add_placeholder($this->_objTpl);
	    $this->_intLangId = $_LANGID;
		$this->_arrSettings = $this->createSettingsArray();
	}

	/**
	 * Constructor php4
	 */
    function ForumHomeContent($pageContent) {
    	$this->__construct($pageContent);
	}

	/**
	 * Fetch latest entries and parse forumtemplate
	 *
	 * @return string parsed latest entries
	 */
	function getContent()
	{
		global $_CONFIG, $objDatabase, $_ARRAYLANG;
		$this->_objTpl->setTemplate($this->_pageContent,true,true);
		$this->_showLatestEntries($this->_getLatestEntries());
		return $this->_objTpl->get();
	}


	/**
     * Returns html-source for an tagcloud.  Just a wrapper-method.
     *
     * @return    string        html-source for the tagcloud.
     */
    function getHomeTagCloud()
    {
        return $this->getTagCloud();
    }

    /**
     * Check if a keywords occurs in a given text / content.
     *
     * @param    string        $strKeyword: This keyword will be searched in the content (=Needle).
     * @param    string        $strContent: This string will be looked through (=Haystack).
     * @return    boolean        true, if the key occured.
     */
    function searchKeywordInContent($strKeyword, $strContent)
    {
        return preg_match('/\{'.$strKeyword.'\}/mi', $strContent);
    }


    /**
     * Replaces the string $strNeedle in $strHaystack with $strReplace, if $boolActivated is true.
     *
     * @param    string        $strNeedle: This keyword will be searched in the haystack.
     * @param    string        $strReplace: This keyword will replace the original value in $strNeedle.
     * @param    stirng        $strHaystack: This string will be looked through for $strNeedle.
     * @param    boolean        $boolActivated: Only if this parameter is true, the replacement will be done.
     * @return    string        If $boolActivated, the modified $strHaystack, otherwise the original $strHaystack without any changes.
     */
    function fillVariableIfActivated($strNeedle, $strReplace, $strHaystack, $boolActivated)
    {
        if ($boolActivated) {
            return preg_replace('/\{'.$strNeedle.'\}/mi', $strReplace, $strHaystack);
        }
        return $strHaystack;
    }
}
