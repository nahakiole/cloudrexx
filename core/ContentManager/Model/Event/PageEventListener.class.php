<?php

/**
 * This listener ensures slug consistency on Page objects.
 * On Flushing, all entities are scanned and changed where needed.
 * After persist, the XMLSitemap is rewritten
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  core_contentmanager
 */

namespace Cx\Core\ContentManager\Model\Event;

use \Cx\Core\ContentManager\Model\Entity\Page as Page;
use Doctrine\Common\Util\Debug as DoctrineDebug;

/**
 * PageEventListenerException
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  core_contentmanager
 */
class PageEventListenerException extends \Exception {}

/**
 * PageEventListener
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  core_contentmanager
 */
class PageEventListener implements \Cx\Core\Event\Model\Entity\EventListener {
    
    public function prePersist($eventArgs) {
        $this->setUpdatedByCurrentlyLoggedInUser($eventArgs);
        $this->fixAutoIncrement();
    }
    
    /**
     *
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs $eventArgs 
     */
    public function preUpdate($eventArgs) {
        $this->setUpdatedByCurrentlyLoggedInUser($eventArgs);
    }

    protected function setUpdatedByCurrentlyLoggedInUser($eventArgs) {
        $entity = $eventArgs->getEntity();
        $em     = $eventArgs->getEntityManager();
        $uow    = $em->getUnitOfWork();

        if ($entity instanceof \Cx\Core\ContentManager\Model\Entity\Page) {
            $entity->setUpdatedBy(
                \FWUser::getFWUserObject()->objUser->getUsername()
            );

            if (\Env::get('em')->contains($entity)) {
                $uow->recomputeSingleEntityChangeSet(
                    $em->getClassMetadata('Cx\Core\ContentManager\Model\Entity\Page'),
                    $entity
                );
            } else {
                $uow->computeChangeSet(
                    $em->getClassMetadata('Cx\Core\ContentManager\Model\Entity\Page'),
                    $entity
                );
            }
        }
    }

    public function preRemove($eventArgs) {
        $em      = $eventArgs->getEntityManager();
        $uow     = $em->getUnitOfWork();
        $entity  = $eventArgs->getEntity();
        
        // remove aliases of page
        $aliases = $entity->getAliases();
        if (!empty($aliases)) {
            foreach ($aliases as $alias) {
                $node = $alias->getNode();
                $em->remove($node);
                $uow->computeChangeSet(
                    $em->getClassMetadata('Cx\Core\ContentManager\Model\Entity\Node'),
                    $node
                );
            }
        }
    }

    public function postPersist($eventArgs) {
        $this->writeXmlSitemap($eventArgs);
    }

    public function postUpdate($eventArgs) {
        $this->writeXmlSitemap($eventArgs);
    }

    public function postRemove($eventArgs) {
        $this->writeXmlSitemap($eventArgs);
    }

    protected function writeXmlSitemap($eventArgs) {
        global $_CONFIG;

        $entity = $eventArgs->getEntity();
        if (($entity instanceof \Cx\Core\ContentManager\Model\Entity\Page)
            && ($entity->getType() != \Cx\Core\ContentManager\Model\Entity\Page::TYPE_ALIAS)
            && ($_CONFIG['xmlSitemapStatus'] == 'on')
        ) {
            \Cx\Core\PageTree\XmlSitemapPageTree::write();
        }
    }

    public function onFlush($eventArgs) {
        $em = $eventArgs->getEntityManager();

        $uow = $em->getUnitOfWork();

        $pageRepo = $em->getRepository('Cx\Core\ContentManager\Model\Entity\Page');

        global $objCache;
        if ($objCache) {
            /*
             * Really unneccessary, because we do not use resultcache 
             * @see http://bugs.contrexx.com/contrexx/ticket/2339
             */
            //$objCache->clearCache();
        }
        
        foreach ($uow->getScheduledEntityUpdates() AS $entity) {            
            $this->checkValidPersistingOperation($pageRepo, $entity);
        }
    }

    /**
     * Sanity test for Pages. Prevents user from persisting bogus Pages.
     * This is the case if
     *  - the Page has fallback content. In this case, the Page's content was overwritten with
     *    other data that is not meant to be persisted.
     *  - more than one page has module home without cmd
     * @throws PageEventListenerException
     */
    protected function checkValidPersistingOperation($pageRepo, $page) {
        global $_CORELANG;
        
        if ($page instanceof Page) {
            if ($page->isVirtual()) {
                throw new PageEventListenerException('Tried to persist Page "'.$page->getTitle().'" with id "'.$page->getId().'". This Page is virtual and cannot be stored in the DB.');
            }
            if ($page->getModule() == 'Home'
                    && $page->getCmd() == ''
                    && $page->getType() == \Cx\Core\ContentManager\Model\Entity\Page::TYPE_APPLICATION
            ) {
                $home = $pageRepo->findBy(array(
                    'module' => 'Home',
                    'cmd' => '',
                    'lang' => $page->getLang(),
                    'type' => \Cx\Core\ContentManager\Model\Entity\Page::TYPE_APPLICATION,
                ));
                reset($home);
                if (   count($home) > 1
                    || (   count($home) == 1
                        && current($home)->getId() != $page->getId())
                ) {
                    throw new PageEventListenerException('Tried to persist Page "'.$page->getTitle().'" with id "'.$page->getId().'". Only one page with module "home" and no cmd is allowed.');
                    
                    // the following is not necessary, since a nice error message
                    // is display by javascript.
                    // find the other page to display a better error message:
                    if (current($home)->getId() == $page->getId()) {
                        $home = end($home);
                    } else {
                        $home = current($home);
                    }
                    throw new PageEventListenerException(sprintf($_CORELANG['TXT_CORE_CM_HOME_FAIL'], $home->getId(), $home->getPath()));
                    
                    //SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1-110-Cx\Model\ContentManager\Page' for key 'log_class_unique_version_idx'
                    
                    //'Tried to persist Page "'.$page->getTitle().'" with id "'.$page->getId().'". Only one page with module "home" and no cmd is allowed.');
                }
            }
        }
    }

    /**
     * Fix the auto increment for the content_page table
     * Ticket #1070 in bug tracker
     *
     * The last content page have been deleted and the website was moved to another server, in this case
     * the auto increment does not match the log's last object_id. This will cause a duplicate primary key.
     */
    private function fixAutoIncrement() {
        $database = \Env::get('db');
        $result = $database->Execute("SELECT MAX(CONVERT(`object_id`, UNSIGNED)) AS `oldAutoIncrement`
                                        FROM `" . DBPREFIX . "log_entry`
                                        WHERE `object_class` = 'Cx\\\\Core\\\\ContentManager\\\\Model\\\\Entity\\\\Page'");
        if ($result === false) return;
        $oldAutoIncrement = $result->fields['oldAutoIncrement'] + 1;
        $result = $database->Execute("SHOW TABLE STATUS LIKE '" . DBPREFIX . "content_page'");
        if ($result !== false && $result->fields['Auto_increment'] < $oldAutoIncrement) {
            $result = $database->Execute("ALTER TABLE `" . DBPREFIX . "content_page` AUTO_INCREMENT = " . contrexx_raw2db($oldAutoIncrement));
        }
    }

    public function onEvent($eventName, array $eventArgs) {        
        $this->$eventName(current($eventArgs));
    }
   
     public static function SearchFindContent($search) {
         
        $pageRepo = \Env::get('em')->getRepository('Cx\Core\ContentManager\Model\Entity\Page');
        $result = new \Cx\Core_Modules\Listing\Model\Entity\DataSet($pageRepo->searchResultsForSearchModule($search->getTerm(), \Env::get('cx')->getLicense()));
        $search->appendResult($result);
    }
}