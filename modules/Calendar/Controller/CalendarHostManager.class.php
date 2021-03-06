<?php
/**
 * Calendar 
 * 
 * @package    contrexx
 * @subpackage module_calendar
 * @author     Comvation <info@comvation.com>
 * @copyright  CONTREXX CMS - COMVATION AG
 * @version    1.00
 */

namespace Cx\Modules\Calendar\Controller;
/**
 * Calendar Class Host Manager
 * 
 * @package    contrexx
 * @subpackage module_calendar
 * @author     Comvation <info@comvation.com>
 * @copyright  CONTREXX CMS - COMVATION AG
 * @version    1.00
 */
class CalendarHostManager extends \Cx\Modules\Calendar\Controller\CalendarLibrary 
{
    /**
     * Host list
     *
     * @access public
     * @var array
     */
    public $hostList = array();
    
    /**
     * Category id
     *
     * @access private
     * @var integer
     */
    private $catId;
    
    /**
     * only confirmes
     *
     * @access private
     * @var boolean
     */
    private $onlyConfirmed;
    
    /**
     * only Active
     *
     * @access private
     * @var boolean
     */
    private $onlyActive;
    
    /**
     * Host manager constructor
     * 
     * @param integer $catId         Category Id
     * @param boolean $onlyConfirmed condition to get only confirmed
     * @param boolean $onlyActive    condition to get only active
     */
    function __construct($catId=null,$onlyConfirmed=false,$onlyActive=false){
    	$this->catId = intval($catId);
        $this->onlyConfirmed = intval($onlyConfirmed);
        $this->onlyActive = intval($onlyActive);  
    }
    
    /**
     * Initialize the host list
     * 
     * @return null
     */
    function getHostList() {
        global $objDatabase,$_ARRAYLANG,$_LANGID;
        
        if($this->catId != null) {                                                              
            $catId_where = "AND cat_id = '".$this->catId."' ";  
        } else {                                          
            $catId_where = "";   
        }  
        
        if($this->onlyActive) {                                                              
            $active_where = "AND status = '1' ";  
        } else {                                          
            $active_where = "";   
        }  
        
        $query = "SELECT id
                    FROM ".DBPREFIX."module_".$this->moduleTablePrefix."_host
                   WHERE id != 0 ".$catId_where." ".$active_where."
                ORDER BY status DESC";
        
        $objResult = $objDatabase->Execute($query);

        if ($objResult !== false) {
            while (!$objResult->EOF) {
                $objHost = new \Cx\Modules\Calendar\Controller\CalendarHost(intval($objResult->fields['id']));
                $this->hostList[] = $objHost;
                $objResult->MoveNext();
            }
        }
    }
    
    /**
     * Sets the host list place holders to the template
     * 
     * @param object $objTpl Template object
     * 
     * @return null
     */
    function showHostList($objTpl) {
        global $_ARRAYLANG;
        
        $i=0;
        foreach ($this->hostList as $key => $objHost) {
        	$objCategory = new \Cx\Modules\Calendar\Controller\CalendarCategory(intval($objHost->catId));
                
        	$objTpl->setVariable(array(
                $this->moduleLangVar.'_HOST_ROW'         => $i%2==0 ? 'row1' : 'row2',
                $this->moduleLangVar.'_HOST_ID'          => $objHost->id,
                $this->moduleLangVar.'_HOST_LED'         => $objHost->status==0 ? 'red' : 'green',
                $this->moduleLangVar.'_HOST_STATUS'      => $objHost->status==0 ? $_ARRAYLANG['TXT_CALENDAR_INACTIVE'] : $_ARRAYLANG['TXT_CALENDAR_ACTIVE'],
                $this->moduleLangVar.'_HOST_TITLE'       => $objHost->title,
                $this->moduleLangVar.'_HOST_URI'         => $objHost->uri,
                $this->moduleLangVar.'_HOST_CATEGORY'    => $objCategory->name,
                $this->moduleLangVar.'_HOST_KEY'         => $objHost->key,
            ));
            
            $i++;
            $objTpl->parse('hostList');
        }
    
        if(count($this->hostList) == 0) {
            $objTpl->hideBlock('hostList');
            
            $objTpl->setVariable(array(
                'TXT_'.$this->moduleLangVar.'_NO_HOSTS_FOUND' => $_ARRAYLANG['TXT_CALENDAR_NO_HOSTS_FOUND'],
            ));
                
            $objTpl->parse('emptyHostList');
        }
    }
    
    /**
     * Sets the host place holder to the template
     * 
     * @param object  $objTpl Html Template object
     * @param integer $hostId Host id
     * 
     * @return null
     */
    function showHost($objTpl, $hostId) {
        $objHost = new \Cx\Modules\Calendar\Controller\CalendarHost(intval($hostId));
        $this->hostList[$hostId] = $objHost;
        
        $objTpl->setVariable(array(
            $this->moduleLangVar.'_HOST_ID'          => $objHost->id,
            $this->moduleLangVar.'_HOST_STATUS'      => $objHost->status==0 ? '' : 'checked="checked"',
            $this->moduleLangVar.'_HOST_TITLE'       => $objHost->title,
            $this->moduleLangVar.'_HOST_URI'         => $objHost->uri,
            $this->moduleLangVar.'_HOST_KEY'         => $objHost->key,
        ));
    }
}