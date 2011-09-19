<?php

namespace Cx\Model\ContentManager\Repository;

use Doctrine\Common\Util\Debug as DoctrineDebug;
use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping\ClassMetadata,
    Doctrine\ORM\Query\Expr;

class PageRepositoryException extends \Exception {};
class TranslateException extends \Exception {};

class PageRepository extends EntityRepository {
    protected $em = null;
    const DataProperty = '__data';

    public function __construct(EntityManager $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->em = $em;
    }

    /**
     * An array of pages sorted by their langID for specified module and cmd.
     *
     * @param string $module
     * @param string $cmd optional
     *
     * @return array ( langId => Page )
     */
    public function getFromModuleCmdByLang($module, $cmd = null) {
        $crit = array( 'module' => $module );
        if($cmd)
            $crit['cmd'] = $cmd;

        $pages = $this->findBy($crit);
        $ret = array();

        foreach($pages as $page) {
            $ret[$page->getLang()] = $page;
        }

        return $ret;
    }

    /**
     * Get a tree of all Nodes with their Pages assigned.
     *
     * @param Node $rootNode limit query to subtree.
     * @param int $lang limit query to language.
     * @param boolean $titlesOnly fetch titles only. You may want to use @link getTreeByTitle()
     * @return array
     */
    public function getTree($rootNode = null, $lang = null, $titlesOnly = false) {
        $repo = $this->em->getRepository('Cx\Model\ContentManager\Node');
        $qb = $this->em->createQueryBuilder();

        $joinConditionType = null;
        $joinCondition = null;

        //language filtering
        if($lang) {
            $joinConditionType = Expr\Join::WITH;
            $joinCondition = $qb->expr()->eq('p.lang',$lang);
        }
        $qb->addSelect('p');

        //join the pages
        $qb->leftJoin('node.pages', 'p', $joinConditionType, $joinCondition);
        $qb->andWhere($qb->expr()->gt('node.lvl', 0)); //exclude root node

        //get all nodes
        $tree = $repo->children($rootNode, false, 'lft', 'ASC', $qb);

        return $tree;
    }

    /**
     * Get a tree mapping titles to Page, Node and language.
     *
     * @see getTree()
     * @return array ( title => array( '__data' => array(lang => langId, page =>), child1Title => array, child2Title => array, ... ) ) recursively array-mapped tree.
     */
    public function getTreeByTitle($rootNode = null, $lang = null, $titlesOnly = false, $useSlugAsTitle=false) {
        $tree = $this->getTree($rootNode, $lang, true);
        $result = array();

        $isRootQuery = !$rootNode || ( isset($rootNode) && $rootNode->getLvl() == 0);

        foreach($tree as $node) {
            $lang2arr = null;
            $rightLevel = false;
            if($isRootQuery)
                $rightLevel = $node->getLvl() == 1;
            else
                $rightLevel = $node->getLvl() == $rootNode->getLvl() + 1;
            if($rightLevel) {
                $this->treeByTitle($node, $result, $useSlugAsTitle, $lang2arr, $lang);
            }
        }

        return $result;
    }

    protected function treeByTitle($root, &$result, $useSlugAsTitle=false, &$lang2arr = null, $lang = null) {
        $myLang2arr = array();
        //(I) get titles of all Pages linked to this Node
        $pages = null;

        if(!$lang) {
            $pages = $root->getPages();
        }
        else {
            $pages = array();
            $page = $root->getPage($lang);
            if($page)
                $pages[] = $page;
        }

        foreach($pages as $page) {
            $title = $page->getTitle();

            if($useSlugAsTitle)
                $title = $page->getSlug();

            $lang = $page->getLang();

            if($lang2arr) //this won't be set for the first node
                $target = &$lang2arr[$lang];
            else
                $target = &$result;

            if(isset($target[$title])) { //another language's Page has the same title
                //add the language
                $target[$title]['__data']['lang'][] = $lang;
            }
            else {
                $target[$title] = array();
                $target[$title]['__data'] = array(
                                                  'lang' => array($lang),
                                                  'page' => $page,
                                                  'node' => $root,
                                                  );
            }
            //remember mapping for recursion
            $myLang2arr[$lang] = &$target[$title];
        }

        //(II) recursion for child Nodes
        foreach($root->getChildren() as $child) {
            $this->treeByTitle($child, $result, $useSlugAsTitle, $myLang2arr, $lang);
        }
    }

    /**
     * Tries to find the path's Page.
     *
     * @param string $path e.g. Hello/APage/AModuleObject
     * @param Node $root
     * @param int $lang
     * @param boolean $exact if true, returns null on partially matched path
     * @return array (
     *     matchedPath => string (e.g. 'Hello/APage/'),
     *     unmatchedPath => string (e.g. 'AModuleObject') | null,
     *     node => Node,
     *     lang => array (the langIds where this matches),
     *     [ pages = array ( all pages ) ] #langId = null only
     *     [ page => Page ] #langId != null only
     * )
     */
    public function getPagesAtPath($path, $root = null, $lang = null, $exact = false) {
        $tree = $this->getTreeByTitle($root, $lang, true, true);

        //this is a mock strategy. if we use this method, it should be rewritten to use bottom up
        $pathParts = explode('/', $path);
        $matchedLen = 0;
        $treePointer = &$tree;

        foreach($pathParts as $part) {
            if(isset($treePointer[$part])) {
                $treePointer = &$treePointer[$part];
                $matchedLen += strlen($part);
                if('/' == substr($path,$matchedLen,1))
                    $matchedLen++;
            }
            else {
                if($exact)
                    return null;
                break;
            }
        }

        //no level matched
        if($matchedLen == 0)
            return null;

        $unmatchedPath = substr($path, $matchedLen);
        if(!$unmatchedPath) { //beautify the to empty string
            $unmatchedPath = '';
        }

        $result = array(
            'matchedPath' => substr($path, 0, $matchedLen),
            'unmatchedPath' => $unmatchedPath
        );
        if(!$lang) {
            $result['pages'] = $treePointer['__data']['node']->getPagesByLang();
            $result['lang'] = $treePointer['__data']['lang'];
        }
        else {
            $page = $treePointer['__data']['node']->getPagesByLang();
            $page = $page[$lang];
            $result['page'] = $page;
        }

        return $result;
    }

    /**
     * Get a pages' path. Quite costly
     * @todo should be rewritten to use a custom query on heavy usage
     *
     * @param \Cx\Model\ContentManager\Page $page
     * @param boolean $useSlugsAsTitle use this to get a navigation page
     * @return string path, e.g. 'This/Is/It'
     */
    public function getPath($page, $useSlugsAsTitle=false) {
        $lang = $page->getLang();
        $node = $page->getNode();
        $nodeRepo = $this->em->getRepository('Cx\Model\ContentManager\Node');
        $pathNodes = $nodeRepo->getPath($node);

        $path = '';
        foreach($pathNodes as $node) {
            if($node->getLvl() > 0) { //all but top node (it's pageless).
                $pages = $node->getPagesByLang();
                $thePageInOurLang = $pages[$page->getLang()];

//TODO: what happens if $thePageInOurLang is still null?
//      This should be restricted by the content manager.
//      Throwing below is a first attempt to react in this case.
                if(!$thePageInOurLang)
                    throw new PageRepositoryException('getPath(): Missing Page while moving up the tree to collect Path for Page with title "' . $page->getTitle() . '". Node ' . $node->getId() . ' at level ' . $node->getLvl() . ' has no Page in language ' . $page->getLang());

                if(!$useSlugsAsTitle)
                    $path .= '/'.$thePageInOurLang->getTitle();
                else
                    $path .= '/'.$thePageInOurLang->getSlug();
            }
        }

        //cut leading /
        return substr($path,1);
    }

    /**
     * Searches the content and returns an array that is built as needed by the search module.
     *
     * Please do not use this anywhere else, write a search method with proper results instead. Ideally, this
     * method would then be invoked by searchResultsForSearchModule().
     *
     * @param string $string the string to match against.
     * @return array (
     *     'Score' => int
     *     'Title' => string
     *     'Content' => string
     *     'Link' => string
     * )
     */
    public function searchResultsForSearchModule($string) {
        if($string == '')
            return array();

//TODO: use MATCH AGAINST for score
//      Doctrine can be extended as mentioned in http://groups.google.com/group/doctrine-user/browse_thread/thread/69d1f293e8000a27
//TODO: shorten content in query rather than in php

        $qb = $this->em->createQueryBuilder();
        $qb->add('select', 'p')
            ->add('from', 'Cx\Model\ContentManager\Page p')
            ->add('where',
                  $qb->expr()->orx(
                      $qb->expr()->like('p.content', ':searchString'),
                      $qb->expr()->like('p.title', ':searchString')
                  )
            );
        $qb->setParameter('searchString', '%'.$string.'%');

        $pages = $qb->getQuery()->getResult();

        $config = \Env::get('config');

        $results = array();

        foreach($pages as $page) {
            $results[] = array(
                'Score' => 100,
                'Title' => $page->getTitle(),
                'Content' => substr($page->getTitle(),0, $config['searchDescriptionLength']),
//TODO: awww this is sooo costly. @see getPath()
                'Link' => ASCMS_PATH_OFFSET.$this->getPath($page, true)
            );
        }

        return $results;
    }

    /**
     * Creates a copy of $source in the desired language and returns it.
     *
     * Does not flush EntityManager.
     *
     * This function takes care of maintaining the tree.
     * It creates empty Pages in the desired language where the parent Nodes do not have such associated Pages.
     *
     * @param \Cx\Model\ContentManager\Page $source the source page
     * @param int $targetLang target language id
     * @param boolean $activate whether the copy should be activated. defaults to false.
     * @param boolean $copyContent whether the page content should be copied. defaults to false.
     * @param boolean $copyModuleAndCmd whether module and cmd should be copied. defaults to false.
     * @throws \Cx\Model\ContentManager\Repository\PageRepository\TranslateException if the page is already translated
     *
     * @returns \Cx\Model\ContentManager\Page the copy
     */
    public function translate($source, $targetLang, $activate = false, $copyContent = false, $copyModuleAndCmd = false) {
        //copy data.
        $page = new \Cx\Model\ContentManager\Page();

        $page->copyFrom($source, $copyContent, $copyModuleAndCmd);

        $page->setLang($targetLang);
        $page->setActive($activate);

        /*
          sanitize tree.
          for all parent Nodes without a Page in the desired target language,
          the Page with the sources' language id is copied.
        */
        $sourceLanguage = $source->getLang();

        $node = $page->getNode()->getParent();

        //             below root level
        while($node && $node->getLvl() > 0) {
            $pages = $node->getPagesByLang();
            if(!isset($pages[$targetLang])) {
                $newPage = new \Cx\Model\ContentManager\Page();
                $newPage->copyFrom($pages[$sourceLanguage], false, false);
                $newPage->setLang($targetLang);
                $newPage->setActive(false);
                $newPage->setDisplay(false);

                $this->em->persist($newPage);

                $node = $node->getParent();
            }
            else { //we have a parent in targetLang, tree is ok from here upwards
                $node = null;
            }
        }

        $this->em->persist($page);

        return $page;
    }


    /**
     * Returns true if the page selected by its language, module name (section)
     * and optional cmd parameters exists
     * @param   integer     $lang       The language ID
     * @param   string      $module     The module (aka section) name
     * @param   string      $cmd        The optional cmd parameter value
     * @return  boolean                 True if the page exists, false
     *                                  otherwise
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @since   3.0.0
     * @internal    Required by the Shop module
     */
    public function existsModuleCmd($lang, $module, $cmd=null)
    {
        $crit = array(
            'module' => $module,
            'lang' => $lang,
        );
        if (isset($cmd)) $crit['cmd'] = $cmd;
        return (boolean)$this->findOneBy($crit);
    }

}