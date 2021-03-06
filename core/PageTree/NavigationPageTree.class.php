<?php

/**
 * NavigationPageTree
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  core_pagetree
 */

namespace Cx\Core\PageTree;

/**
 * NavigationPageTree
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  core_pagetree
 */
class NavigationPageTree extends SigmaPageTree {
    protected $topLevelBlockName = null;
    protected $output = '';

    const styleNameActive = "active";
    const styleNameNormal = "inactive";

    protected $branchNodeIds = array(); //holds all ids of the $currentPage's Node and it's parents

    public function __construct($entityManager, $license, $maxDepth = 0, $activeNode = null, $lang = null, $currentPage = null) { 
        parent::__construct($entityManager, $license, $maxDepth, $activeNode, $lang, $currentPage, false);

        //go up the branch and collect all node ids. used later in renderElement().
        $node = $currentPage->getNode();        
        while($node) {
            $this->branchNodeIds[] = $node->getId();
            $node = $node->getParent();
        }
    }
    
    /**
     * Get the first level index which should be shown
     * @return int the first level
     */
    protected function getFirstLevel() {
        $match = array();
        if (preg_match_all('/level_(\d)*/', trim($this->template->_blocks['navigation']), $match)) {
            return intval(current($match[1]));
        }
        return 1;
    }

    /**
     * Get the last level index which should be shown
     * @return int the last level
     */
    protected function getLastLevel() {
        $match = array();
        if (preg_match_all('/level_(\d)*/', trim($this->template->_blocks['navigation']), $match)) {
            return intval(end($match[1]));
        }
        return 0;
    }

    /**
     * @see PageTree::renderElement()
     */
    protected function renderElement($title, $level, $hasChilds, $lang, $path, $current, $page) {
        if (!$this->isParentNodeInsideCurrentBranch($page->getNode())) {
            return '';
        }
        
        $blockName = 'level_'.$level;
        $hideLevel = false;
        $hasCustomizedBlock = $this->template->blockExists($blockName);
        if($hasCustomizedBlock) {
            if(!$this->topLevelBlockName) {
                $this->topLevelBlockName = $blockName;
            }                
        }
        else {
            if ($this->topLevelBlockName) {
                //checks for the standard block e.g. "level"
                if ($this->template->blockExists('level')) {
                    $blockName = 'level';
                } else {
                    $hideLevel = true;
                }
            }
        }
        // get the parent path
        try {
            $parentPath = $page->getParent()->getPath();
        } catch (\Cx\Core\ContentManager\Model\Entity\PageException $e) {
            $parentPath = '/';
        }
        
        if($this->topLevelBlockName && !$hideLevel && $page->isVisible()) {
//TODO: invisible childs
//      maybe the return value of this function could set whether the childs
//      are rendered.
            $style = $current ? self::styleNameActive : self::styleNameNormal;
//TODO: navigation_id
            $linkTarget = $page->getLinkTarget();
            $this->template->setCurrentBlock($blockName);
            $this->template->setVariable(array(
                'URL' => \Cx\Core\Core\Controller\Cx::instanciate()->getWebsiteOffsetPath().$this->virtualLanguageDirectory.$path,
                'NAME' => $title,
                'TARGET' => empty($linkTarget) ? '_self' : $linkTarget,
                'LEVEL_INFO' => $hasChilds ? '' : 'down',
                'STYLE' => $style,
                'CSS_NAME' => $page->getCssNavName()
            ));
            $this->template->parse($blockName);
            $this->output .= $this->template->get($blockName, true);
        }
    }

    protected function postRender($lang) {
        if($this->topLevelBlockName) {
            // replaces the top level block with the complete parsed navigation
            // this is because the Sigma Template system don't support nested blocks
            // with difference object based orders
            $this->template->replaceBlock($this->topLevelBlockName, $this->output, true);
            $this->template->touchBlock($this->topLevelBlockName);
            if ($this->template->blockExists('navigation')){
                $this->template->parse('navigation');
            }

            return $this->template->get();
        }
    }
    
    private function isNodeInsideCurrentBranch($node)
    {
        return in_array($node->getId(), $this->branchNodeIds);
    }

    private function isParentNodeInsideCurrentBranch($node)
    {
        if (!$node->getParent()) {
            return true;
        }
        return $this->isNodeInsideCurrentBranch($node->getParent());
    }
    
    public function preRenderLevel($level, $lang, $parentNode) {}
    
    public function postRenderLevel($level, $lang, $parentNode) {}

    protected function preRenderElement($level, $hasChilds, $lang, $page) {}

    protected function postRenderElement($level, $hasChilds, $lang, $page) {}
    
    protected function renderHeader($lang) {}
    
    protected function renderFooter($lang) {}
    
    protected function realPreRender($lang) {}
    
    /**
     * Called on construction. Override if you do not want to override the ctor.
     */
    protected function init() {}
}
