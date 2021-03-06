<?php

/**
 * Handles all components, including legacy ones.
 * 
 * This is a wrapper class for SystemComponentRepository and LegacyComponentHandler
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Michael Ritter <michael.ritter@comvation.com>
 * @package     contrexx
 * @subpackage  core_core
 * @link        http://www.contrexx.com/ contrexx homepage
 * @since       v3.1.0
 */

namespace Cx\Core\Core\Controller;

/**
 * ComponentException is thrown for legacy components without an exception in LegacyComponentHandler
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Michael Ritter <michael.ritter@comvation.com>
 * @package     contrexx
 * @subpackage  core_core
 * @link        http://www.contrexx.com/ contrexx homepage
 * @since       v3.1.0
 */
class ComponentException extends \Exception {}

/**
 * Handles all components, including legacy ones.
 *
 * This is a wrapper class for SystemComponentRepository and LegacyComponentHandler
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Michael Ritter <michael.ritter@comvation.com>
 * @package     contrexx
 * @subpackage  core_core
 * @link        http://www.contrexx.com/ contrexx homepage
 * @since       v3.1.0
 */
class ComponentHandler {
    
    /**
     * Legacy code for old components
     * @var LegacyContentHandler
     */
    private $legacyComponentHandler;
    
    /**
     * Are we in frontend or backend mode?
     * @var boolean
     */
    private $frontend;
    
    /**
     * Repository of SystemComponents
     * @var \Cx\Core\Core\Model\Repository\SystemComponentRepository
     */
    protected $systemComponentRepo;
    
    /**
     * Available (and legal by license) components
     * This list should be written in constructor (read from license). This
     * does not work by now since license has different component names.
     * @var array
     */
    private $components = array(
        'ComponentHandler',
    );
    
    /**
     * Instanciates a new ComponentHandler
     * @todo Read component list from license (see $this->components for why we didn't do that yet)
     * @param \Cx\Core_Modules\License\License $license Current license
     * @param boolean $frontend Wheter we are in frontend mode or not
     * @param \Doctrine\ORM\EntityManager $em Doctrine entity manager
     */
    public function __construct(\Cx\Core_Modules\License\License $license, $frontend, \Doctrine\ORM\EntityManager $em) {
        $this->legacyComponentHandler = new LegacyComponentHandler();
        $this->frontend = $frontend;
        //$this->components = $license->getLegalComponentsList();
        $this->systemComponentRepo = $em->getRepository('Cx\\Core\\Core\\Model\\Entity\\SystemComponent');
        $this->systemComponentRepo->findAll();
        
        $this->callRegisterEventsHooks();
        $this->callRegisterEventListenersHooks();
    }
    
    /**
     * Wheter the component with the supplied name is a legacy one or not
     * @param string $componentName Name of the component to check
     * @return boolean True if it's a legacy component, false otherwise
     */
    public function isLegacyComponent($componentName) {
        return !$this->systemComponentRepo->findOneBy(array('name'=>$componentName));
    }
    
    /**
     * Checks for existance of legacy exception and executes it if available
     * @param String $action The action to be executed
     * @param String $componentName Name of the component to execute the action
     * @return boolean True if legacy has an exception for this action and component
     */
    private function checkLegacy($action, $componentName) {
        if ($this->legacyComponentHandler->executeException($this->frontend, $action, $componentName) === false) {
            return false;
        }
        return true;
    }
    
    /**
     * Calls hook scripts on legacy and non-legacy components to register events
     * @param string $mode (optional) One of 'all', 'proper' and 'legacy', default is 'all'
     */
    public function callRegisterEventsHooks($mode = 'all') {
        if ($mode != 'all' && $mode != 'proper') {
            return;
        }
        $this->systemComponentRepo->callRegisterEventsHooks();
    }
    
    /**
     * Calls hook scripts on legacy and non-legacy components to register event listeners
     * @param string $mode (optional) One of 'all', 'proper' and 'legacy', default is 'all'
     */
    public function callRegisterEventListenersHooks($mode = 'all') {
        if ($mode != 'all' && $mode != 'proper') {
            return;
        }
        $this->systemComponentRepo->callRegisterEventListenersHooks();
    }
    
    /**
     * Calls hook scripts on legacy and non-legacy components before resolving
     * @param string $mode (optional) One of 'all', 'proper' and 'legacy', default is 'all'
     */
    public function callPreResolveHooks($mode = 'all') {
        if ($mode == 'all' || $mode == 'legacy') {
            foreach ($this->components as $componentName) {
                if ($this->checkLegacy('preResolve', $componentName)) {
                    continue;
                }
            }
        }
        if ($mode == 'all' || $mode == 'proper') {
            $this->systemComponentRepo->callPreResolveHooks();
        }
    }
    
    /**
     * Calls hook scripts on legacy and non-legacy components after resolving
     * @param string $mode (optional) One of 'all', 'proper' and 'legacy', default is 'all'
     */
    public function callPostResolveHooks($mode = 'all') {
        if ($mode == 'all' || $mode == 'legacy') {
            foreach ($this->components as $componentName) {
                if ($this->checkLegacy('postResolve', $componentName)) {
                    continue;
                }
            }
        }
        if ($mode == 'all' || $mode == 'proper') {
            $this->systemComponentRepo->callPostResolveHooks();
        }
    }
    
    /**
     * Calls hook scripts on legacy and non-legacy components before loading content
     */
    public function callPreContentLoadHooks() {
        foreach ($this->components as $componentName) {
            if ($this->checkLegacy('preContentLoad', $componentName)) {
                continue;
            }
        }
        $this->systemComponentRepo->callPreContentLoadHooks();
    }
    
    /**
     * Calls hook scripts on legacy and non-legacy components before loading module content
     */
    public function callPreContentParseHooks() {
        foreach ($this->components as $componentName) {
            if ($this->checkLegacy('preContentParse', $componentName)) {
                continue;
            }
        }
        $this->systemComponentRepo->callPreContentParseHooks();
    }
    
    /**
     * Calls hook scripts on legacy and non-legacy components after loading module content
     */
    public function callPostContentParseHooks() {
        foreach ($this->components as $componentName) {
            if ($this->checkLegacy('postContentParse', $componentName)) {
                continue;
            }
        }
        $this->systemComponentRepo->callPostContentParseHooks();
    }
    
    /**
     * Calls hook scripts on legacy and non-legacy components after loading content
     */
    public function callPostContentLoadHooks() {
        foreach ($this->components as $componentName) {
            if ($this->checkLegacy('postContentLoad', $componentName)) {
                continue;
            }
        }
        $this->systemComponentRepo->callPostContentLoadHooks();
    }
    
    /**
     * Calls hook scripts on legacy and non-legacy components before finalizing
     */
    public function callPreFinalizeHooks() {
        foreach ($this->components as $componentName) {
            if ($this->checkLegacy('preFinalize', $componentName)) {
                continue;
            }
        }
        $this->systemComponentRepo->callPreFinalizeHooks();
    }
    
    /**
     * Calls hook scripts on legacy and non-legacy components after finalizing
     */
    public function callPostFinalizeHooks() {
        foreach ($this->components as $componentName) {
            if ($this->checkLegacy('postFinalize', $componentName)) {
                continue;
            }
        }
        $this->systemComponentRepo->callPostFinalizeHooks();
    }
    
    /**
     * Load the component with the name specified (legacy or not)
     * @param \Cx\Core\Core\Controller\Cx $cx Main class instance
     * @param string $componentName Name of component to load
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page Resolved page
     * @return null
     * @throws ComponentException For legacy components without a load entry in LegacyClassLoader
     */
    public function loadComponent(\Cx\Core\Core\Controller\Cx $cx, $componentName, \Cx\Core\ContentManager\Model\Entity\Page $page = null) {
        if ($this->checkLegacy('load', $componentName)) {
            \DBG::msg('This is a legacy component (' . $componentName . '), load via LegacyComponentHandler');
            return;
        }
        $component = $this->systemComponentRepo->findOneBy(array('name'=>$componentName));
        if (!$component) {
            \DBG::msg('This is an ugly legacy component (' . $componentName . '), load via LegacyComponentHandler');
            \DBG::msg('Add an exception for this component in LegacyComponentHandler!');
            throw new ComponentException('This is an ugly legacy component(' . $componentName . '), load via LegacyComponentHandler!');
        }
        $component->load($page);
        //\DBG::msg('<b>WELL, THIS IS ONE NICE COMPONENT!</b>');
    }
}
