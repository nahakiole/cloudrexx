<?php
/**
 * Listing controller
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  coremodule_listing
 */

namespace Cx\Core_Modules\Listing\Controller;

/**
 * Listing exception
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  coremodule_listing
 */
class ListingException extends \Exception {}

/**
 * Creates rendered lists (paging, filtering, sorting)
 * @author ritt0r <drissg@gmail.com>
 * @package     contrexx
 * @subpackage  coremodule_listing
 */
class ListingController {
    /**
     * Turn paging off
     * @var int
     */
    const PAGING_OFF = 0;
    /**
     * No AJAX, traditional page requests
     * @var int
     */
    const PAGING_NON_AJAX = 1;
    /**
     * Paged HTML is transferred via AJAX
     * @var int
     */
    const PAGING_HTML_AJAX = 2;
    /**
     * Only data is transferred via AJAX
     * @var int
     */
    const PAGING_DATA_AJAX = 3;
    /**
     * Paging is done client side, no additional data is transferred after page load
     * @var unknown
     */
    const PAGING_CLIENT_ONLY = 4;
    const SORTING_OFF = 8;
    const SORTING_NON_AJAX = 9;
    const SORTING_HTML_AJAX = 10;
    const SORTING_DATA_AJAX = 11;
    const SORTING_CLIENT_ONLY = 12;
    const FILTERING_OFF = 16;
    const FILTERING_NON_AJAX = 17;
    const FILTERING_HTML_AJAX = 18;
    const FILTERING_DATA_AJAX = 19;
    const FILTERING_CLIENT_ONLY = 20;
    
    /**
     * How many lists are there for this request
     * @var int
     */
    protected static $listNumber = 0;
    
    /**
     * Entity class name
     * @var String
     */
    protected $entityClass = null;
    
    /**
     * Callback function to get data
     * @var Callable
     */
    protected $callback = null;
    
    /**
     * Offset to start from
     * @var int
     */
    protected $offset = 0;
    
    /**
     * How many results are returned
     * @var int
     */
    protected $count = 0;
    
    /**
     * Order by array($field=>asc/desc)
     * @var Array
     */
    protected $order = array();
    
    /**
     * Criteria the result must match
     * @var Array
     */
    protected $criteria = array();
    
    
    private $paging;
    /**
     * Handles a list
     * @param mixed $entities Entity class name as string or callback function
     * @param array $crit (optional) Doctrine style criteria array to use
     * @param array $options (Unused)
     */
    public function __construct($entities, $crit = array(), $options = array()) {
        if (isset($options['paging'])) {
            $this->paging = $options['paging'];
        }
        if (isset($options['order'])) {
            $this->order = $options['order'];
        }
        // init handlers (filtering, paging and sorting)
        $this->handlers[] = new FilteringController();
        if (!empty($options['sorting'])) {
            $this->handlers[] = new SortingController();
        }
        $this->handlers[] = new PagingController();
        
        if (is_callable($entities)) {
            \DBG::msg('Init ListingController using callback function');
            $this->callback = $entities;
        } else if ($entities instanceof \Cx\Core_Modules\Listing\Model\Entity\DataSet) {
            \DBG::msg('Init ListingController using DataSet');
            $this->entityClass = $entities;
        } else {
            \DBG::msg('Init ListingController using entity class');
            $this->entityClass = $entities;
        }
        $this->criteria = $crit;
        
        // todo: allow multiple listing controllers per page request
        $this->args = contrexx_input2raw($_GET);
    }
    
    /**
     * Initializes listing for the given object
     * @param Cx\Core_Modules\Listing\Model\Listable $listableObject
     * @param int $mode (optional) A combination of the paging, sorting and filtering modes above (use |)
     * @returm Cx\Core_Modules\Listing\Model\DataSet Parsed data
     */
    public function getData() {
        $params = array(
            'offset'    => $this->offset,
            'count'     => $this->count,
            'order'     => $this->order,
            'criteria'  => $this->criteria,
        );
        foreach ($this->handlers as $handler) {
            $params = $handler->handle($params, $this->args);
        }
        $this->offset   = $params['offset'];
        $this->count    = $params['count'];
        $this->order    = $params['order'];
        $this->criteria = $params['criteria'];
        
        // handle ajax requests
        if (false /* ajax request for this listing */) {
            $jd = new \Cx\Core\Json\JsonData();
            $jd->json(array(
                'filtering' => $this->getAjaxFilteringData(),
                'sorting' => $this->getAjaxSortingData(),
                'paging' => $this->getAjaxPagingData(),
            ), true);
            // JsonData->json() does call die() itself
        }
        
        if ($this->entityClass instanceof \Cx\Core_Modules\Listing\Model\Entity\DataSet) {
            //$data = new \Cx\Core_Modules\Listing\Model\Entity\DataSet();
            $data = $this->entityClass;
            
            $data = $data->sort($this->order);
            
            // add sorting and filtering
            $data = $data->limit($this->count, $this->offset);
            return $data;
        }
        
        // If a callback was specified, use it:
        $qb = \Env::get('em')->createQueryBuilder();
        $qb->select('e')->from($this->entityClass, 'e');
        $query = $qb->getQuery();
        if (is_callable($this->callback)) {
            $callable = $this->callback;
            $query = $callable($this->offset, $this->count, $this->order, $this->criteria);
            if (!($query instanceof \Doctrine\ORM\Query)) {
                return $query;
            }
        }
        
        if (!class_exists($this->entityClass)) {
            //throw new ListingException('No such entity "' . $this->entityClass . '"');
        }
        
        // build query
        // TODO: check if entity class is managed
         //$qb = new \Doctrine\ORM\QueryBuilder();
        $query->setFirstResult($this->offset);
        $query->setMaxResults($this->count);
        /*foreach ($this->order as $field=>$order) {
            $query->orderBy($field, $order);
        }
        foreach ($this->criteria as $crit=>$param) {
            $query->addWhere($crit);
            if ($param) {
                $query->addParameter($param[0], $param[1]);
            }
        }
        var_dump($query->getDQL());*/
        $entities = $query->getResult();
        
        // @todo: check if entities should be encapsulated in a class
        $data = new \Cx\Core_Modules\Listing\Model\Entity\DataSet($entities);
        
        // return calculated data
        return $data;
    }
    
    /**
     * @todo: implement, this is just a draft!
     */
    public function toHtml() {
        return $this->getPagingControl();
    }
    
    public function __toString() {
        return $this->toHtml();
    }
    
    /**
     * Calculates the paging
     * @throws PagingException when paging type is unknown (see class constants)
     * @return mixed Array for type DATA_AJAX, HTML as string otherwise
     * @todo NON_AJAX mode
     */
    protected function getPaging() {
        switch ($this->listableObject->getType()) {
            case DATA_AJAX:
                return $this->listableObject->getData($this->offset, $this->count);
                break;
            case HTML_AJAX:
                $html = $this->listableObject->preRender($this->offset, $this->count);
                for ($i = $this->offset; $i < ($this->offset + $this->count); $i++) {
                    $html .= $this->listableObject->renderEntry($i);
                }
                $html .= $this->listableObject->postRender($this->offset, $this->count);
                return $html;
                break;
            case NON_AJAX:
                break;
            default:
                throw new PagingException('Unknown paging type "' . $this->listableObject->getType() . '"');
                break;
        }
    }
    
    /**
     * This renders the template for paging control element
     * @todo templating!
     * @todo show only a certain number of pages
     * @todo move to pagingcontroller
     */
    protected function getPagingControl() {
        $html = '';
        if(!$this->paging || $this->entityClass->size() <= $this->count){
            return $html;    
        }
        $numberOfPages = ceil($this->entityClass->size() / $this->count);
        $activePageNumber = ceil(($this->offset + 1) / $this->count);
        
        /*echo 'Number of entries: ' . count($this->entityClass->toArray()) . '<br />';
        echo 'Entries per page: ' . $this->count . '<br />';
        echo 'Number of pages: ' . $numberOfPages . '<br />';
        echo 'Active page: ' . $activePageNumber . '<br />';*/
        
        
        
        if ($this->offset) {
            // render goto start
            $url = clone \Env::get('cx')->getRequest()->getUrl();
            $url->setParam('pos', 0);
            $html .= '<a href="' . $url . '">&lt;&lt;</a>&nbsp;';

            // render goto previous
            $pagePos = ($activePageNumber - 2) * $this->count;
            if ($pagePos < 0) {
                $pagePos = 0;
            }
            $url = clone \Env::get('cx')->getRequest()->getUrl();
            $url->setParam('pos', $pagePos);
            $html .= '<a href="' . $url . '">&lt;</a>&nbsp;';
        } else {
            $html .= '&lt;&lt;&nbsp;&lt;&nbsp;';
        }
        
        for ($pageNumber = 1; $pageNumber <= $numberOfPages; $pageNumber++) {
            if ($pageNumber == $activePageNumber) {
                // render page without link
                $html .= $pageNumber . '&nbsp;';
                continue;
            }
            // render page with link
            $pagePos = ($pageNumber - 1) * $this->count;
            $url = clone \Env::get('cx')->getRequest()->getUrl();
            $url->setParam('pos', $pagePos);
            $html .= '<a href="' . $url . '">' . $pageNumber . '</a>&nbsp;';
        }
        
        if ($this->offset + $this->count < $this->entityClass->size()) {
            // render goto next
            $pagePos = ($activePageNumber - 0) * $this->count;
            if ($pagePos < 0) {
                $pagePos = 0;
            }
            $url = clone \Env::get('cx')->getRequest()->getUrl();
            $url->setParam('pos', $pagePos);
            $html .= '<a href="' . $url . '">&gt;</a>&nbsp;';
            
            // render goto last page
            $url = clone \Env::get('cx')->getRequest()->getUrl();
            $url->setParam('pos', ($numberOfPages - 1) * $this->count);
            $html .= '<a href="' . $url . '">&gt;&gt;</a>';
        } else {
            $html .= '&gt;&nbsp;&gt;&gt;';
        }
        if($this->offset + $this->count > $this->entityClass->size()){
            $to =  $this->entityClass->size();
        }else{
            $to  = $this->offset + $this->count;
        }
        // entry x-y out of n
        $html .= '&nbsp;Einträge ' . ($this->offset+1). ' - ' . $to . ' von ' . $this->entityClass->size();
        return $html;
    }
}
