<?php
/**
 * Domain Entity
 *
 * A entity that represents a domain.
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Thomas Däppen <thomas.daeppen@comvation.com>
 * @package     contrexx
 * @subpackage  core_model
 */

namespace Cx\Core\Net\Model\Entity;

/**
 * Domain Entity
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Thomas Däppen <thomas.daeppen@comvation.com>
 * @package     contrexx
 * @subpackage  core_model
 */
class DomainException extends \Exception {};

/**
 * Domain Entity
 *
 * A entity that represents a domain.
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Thomas Däppen <thomas.daeppen@comvation.com>
 * @package     contrexx
 * @subpackage  core_model
 */
class Domain extends \Cx\Core\Model\Model\Entity\YamlEntity {
    /**
     * Primary identifier of the domain
     * @var integer
     */
    protected $id;

    /**
     * Domain name of the domain
     * @var string
     */
    protected $name;

    /**
     * Constructor to initialize a new domain.
     * @param   string  $name   Domain name of new domain
     */
    public function __construct($name) {
        $this->setName($name);
    }

    /**
     * Set primary identifier of domain
     * @param   integer $id Primary identifiert for domain
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Return primary identifier of domain
     * @return  integer Primary identifier of domain
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set a domain name of domain
     * @param   string $name    Domain name to set the domain to
     */
    public function setName($name) {
        $this->name = \Cx\Core\Net\Controller\ComponentController::convertIdnToAsciiFormat($name);
    }

    /**
     * Return the domain name of domain
     * @return  string Domain name of domain
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Returns the top-level-domain of the Domain
     * @return string the top-level-domain of the Domain
     */
    public function getTld() {
        $parts = $this->getParts();
        return $parts[0];
    }
    
    /**
     * Returns the domain parts as an array where the tld is listed in index 0, sld in index 1 etc.
     * @return array the domain parts as an array
     */
    public function getParts() {
        $parts = array_reverse(explode('.', $this->getName()));
        return $parts;
    }
    
    /**
     * Return the domain name with the following schema <idn notation> (<punycode notation>)
     * Attention. Returns the punycode notation only when needed
     * @return  string Domain name of domain
     */
    public function getNameWithPunycode() {
        $domainName = \Cx\Core\Net\Controller\ComponentController::convertIdnToUtf8Format($this->name);
        if($domainName!=$this->name) {
            $domainName.= ' (' . $this->name . ')';
        }
        return $domainName;
    }

}

