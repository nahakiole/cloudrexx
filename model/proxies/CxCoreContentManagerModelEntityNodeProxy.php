<?php

namespace Cx\Model\Proxies;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class CxCoreContentManagerModelEntityNodeProxy extends \Cx\Core\ContentManager\Model\Entity\Node implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    private function _load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    
    public function getUniqueIdentifier()
    {
        $this->_load();
        return parent::getUniqueIdentifier();
    }

    public function setId($id)
    {
        $this->_load();
        return parent::setId($id);
    }

    public function getId()
    {
        $this->_load();
        return parent::getId();
    }

    public function setLft($lft)
    {
        $this->_load();
        return parent::setLft($lft);
    }

    public function getLft()
    {
        $this->_load();
        return parent::getLft();
    }

    public function setRgt($rgt)
    {
        $this->_load();
        return parent::setRgt($rgt);
    }

    public function getRgt()
    {
        $this->_load();
        return parent::getRgt();
    }

    public function setLvl($lvl)
    {
        $this->_load();
        return parent::setLvl($lvl);
    }

    public function getLvl()
    {
        $this->_load();
        return parent::getLvl();
    }

    public function addChildren(\Cx\Core\ContentManager\Model\Entity\Node $children)
    {
        $this->_load();
        return parent::addChildren($children);
    }

    public function addParsedChild(\Cx\Core\ContentManager\Model\Entity\Node $child)
    {
        $this->_load();
        return parent::addParsedChild($child);
    }

    public function getChildren($lang = NULL)
    {
        $this->_load();
        return parent::getChildren($lang);
    }

    public function addPage(\Cx\Core\ContentManager\Model\Entity\Page $page)
    {
        $this->_load();
        return parent::addPage($page);
    }

    public function getPages($inactive_langs = false, $aliases = false)
    {
        $this->_load();
        return parent::getPages($inactive_langs, $aliases);
    }

    public function getPagesByLang($inactive_langs = false)
    {
        $this->_load();
        return parent::getPagesByLang($inactive_langs);
    }

    public function getPage($lang)
    {
        $this->_load();
        return parent::getPage($lang);
    }

    public function setParent(\Cx\Core\ContentManager\Model\Entity\Node $parent)
    {
        $this->_load();
        return parent::setParent($parent);
    }

    public function getParent()
    {
        $this->_load();
        return parent::getParent();
    }

    public function validate()
    {
        $this->_load();
        return parent::validate();
    }

    public function hasAccessByUserId($frontend = true)
    {
        $this->_load();
        return parent::hasAccessByUserId($frontend);
    }

    public function translatePage($activate, $targetLang)
    {
        $this->_load();
        return parent::translatePage($activate, $targetLang);
    }

    public function copy($recursive = false, \Cx\Core\ContentManager\Model\Entity\Node $newParent = NULL, $persist = true)
    {
        $this->_load();
        return parent::copy($recursive, $newParent, $persist);
    }

    public function serialize()
    {
        $this->_load();
        return parent::serialize();
    }

    public function unserialize($data)
    {
        $this->_load();
        return parent::unserialize($data);
    }

    public function __get($name)
    {
        $this->_load();
        return parent::__get($name);
    }

    public function getComponentController()
    {
        $this->_load();
        return parent::getComponentController();
    }

    public function setVirtual($virtual)
    {
        $this->_load();
        return parent::setVirtual($virtual);
    }

    public function isVirtual()
    {
        $this->_load();
        return parent::isVirtual();
    }

    public function __toString()
    {
        $this->_load();
        return parent::__toString();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'lft', 'rgt', 'lvl', 'children', 'pages', 'parent');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}