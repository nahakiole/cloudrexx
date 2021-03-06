<?php

/**
 * PageGuard
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  core
 */

/**
 * PageGuardException
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  core
 */
class PageGuardException extends Exception {}

/**
 * Handles access restriction administration on Pages.
 * (Retrieve / Store)
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  core
 */
class PageGuard {
    protected $db = null;

    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Returns the group ids with access to front- or backend of a page
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page Page to get the group ids of
     * @param boolean $frontend True for frontend access groups, false for backend
     * @return mixed Array of group ids or false on error
     * @throws PageGuardException 
     */
    public function getAssignedGroupIds($page, $frontend) {
        if ($frontend && !$page->isFrontendProtected()) {
            return array();
        }
        if (!$frontend && !$page->isBackendProtected()) {
            return array();
        }

	try {
	    $accessId = $this->getAccessId($page, $frontend);
	}
	catch (PageGuardException $e) {
	    // the selected page is listed as protected but does not have an access id.
	    // this is probably due to a db inconsistency, which we should be able to handle gracefully:
	    $accessId = \Permission::createNewDynamicAccessId();

	    if ($frontend && $accessId) {
		$page->setFrontendAccessId($accessId);
	    }
	    elseif (!$frontend && $accessId) {
		$page->setBackendAccessId($accessId);
	    }
	    else {
		// cannot create a new dynamic access id.
		throw new PageGuardException('This protected page doesn\'t have an access id associated with
it. Contrexx encountered an error while generating a new access id.');
	    }
	    Env::get('em')->persist($page);
	    Env::get('em')->flush();
	}

        return \Permission::getGroupIdsForAccessId($accessId);
    }

    public function setAssignedGroupIds($page, $ids, $frontend) {
        $accessId = $this->getAccessId($page, $frontend);
        \Permission::setAccess($accessId, 'dynamic', $ids);
    }

    protected function getAccessId($page, $frontend) {
        $accessId = $page->getFrontendAccessId();
        if(!$frontend)
            $accessId = $page->getBackendAccessId();
        if($accessId === 0)
            throw new PageGuardException('Tried to protect Page without accessid. Call setFrontendProtection() / setBackendProtection() first');
        return $accessId;
    }

    /**
     * Returns an array of all front- or backend groups
     * @param boolean $frontend True for frontend access groups, false for backend
     * @return mixed Array (id=>name) or false on error
     */
    public function getGroups($frontend) {
        return \Permission::getGroups($frontend);
    }
}
