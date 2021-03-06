<?php
/**
 * Member manager
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author		Comvation Development Team <info@comvation.com>
 * @version		1.0.0
 * @package     contrexx
 * @subpackage  module_member
 * @todo        Edit PHP DocBlocks!
 */

/**
 * Member manager
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author		Comvation Development Team <info@comvation.com>
 * @access		public
 * @version		1.0.0
 * @package     contrexx
 * @subpackage  module_member
 */
class MemberManager
{
	var $_objTpl;

        private $act = '';
        
	/**
	 * PHP 5 Constructor
	 */
	function __construct()
	{
		$this->_objTpl = new \Cx\Core\Html\Sigma(ASCMS_CORE_MODULE_PATH.'/member/View/Template/Backend');
                \Cx\Core\Csrf\Controller\Csrf::add_placeholder($this->_objTpl);
		$this->_objTpl->setErrorHandling(PEAR_ERROR_DIE);
                $this->act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
                $this->setNavigation();
	}
        private function setNavigation()
        {
                global $objTemplate, $_ARRAYLANG;

                $objTemplate->setVariable("CONTENT_NAVIGATION", "
                    <a href='index.php?cmd=Contact' title=".$_ARRAYLANG['TXT_CONTACT_CONTACT_FORMS']." class='".($this->act == '' ? 'active' : '')."'>".$_ARRAYLANG['TXT_CONTACT_CONTACT_FORMS']."</a>
                    <a href='index.php?cmd=Contact&amp;act=settings' title=".$_ARRAYLANG['TXT_CONTACT_SETTINGS']." class='".($this->act == 'settings' ? 'active' : '')."'>".$_ARRAYLANG['TXT_CONTACT_SETTINGS']."</a>");
        }

	function MemberManager()
	{
		$this->__construct();                
	}


}


?>
