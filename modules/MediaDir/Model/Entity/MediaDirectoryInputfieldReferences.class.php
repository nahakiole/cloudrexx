<?php

/**
 * Media Directory Inputfield References Class
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Comvation Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  module_mediadir
 * @todo        Edit PHP DocBlocks!
 */
namespace Cx\Modules\MediaDir\Model\Entity;

/**
 * Media Directory Inputfield References Class
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  module_mediadir
 */
class MediaDirectoryInputfieldReferences extends \Cx\Modules\MediaDir\Controller\MediaDirectoryLibrary
{
    public $arrPlaceholders = array('TXT_MEDIADIR_INPUTFIELD_NAME','MEDIADIR_INPUTFIELD_VALUE');



    /**
     * Constructor
     */
    function __construct($name)
    {
        parent::__construct('.', $name);
        parent::getFrontendLanguages();
        parent::getSettings();
    }



    function getInputfield($intView, $arrInputfield, $intEntryId=null)
    {
        global $objDatabase, $_LANGID, $objInit, $_ARRAYLANG, $_CORELANG;

        $intId = intval($arrInputfield['id']);

        switch ($intView) {
            default:
            case 1:
                //modify (add/edit) View
                if(isset($intEntryId) && $intEntryId != 0) {
                    $objInputfieldValue = $objDatabase->Execute("
                        SELECT
                            `value`,
                            `lang_id`
                        FROM
                            ".DBPREFIX."module_".$this->moduleTablePrefix."_rel_entry_inputfields
                        WHERE
                            field_id=".$intId."
                        AND
                            entry_id=".$intEntryId."
                    ");
                    if ($objInputfieldValue !== false) {
                        while (!$objInputfieldValue->EOF) {
                            $strValue = htmlspecialchars($objInputfieldValue->fields['value'], ENT_QUOTES, CONTREXX_CHARSET);
                            $arrParents = array();
                            $arrParents = explode("||", $strValue);  
                            
                            foreach($arrParents as $intKey => $strChildes) {
                                $arrChildes = array();  
                                $arrChildes = explode("##", $strChildes);                        
                                
                                $arrValue[intval($objInputfieldValue->fields['lang_id'])][$intKey]['title'] = $arrChildes[0];  
                                $arrValue[intval($objInputfieldValue->fields['lang_id'])][$intKey]['desc'] = $arrChildes[1];  
                            } 
                                                                                                                 
                            $objInputfieldValue->MoveNext();
                        }     
                        $arrValue[0] = $arrValue[$_LANGID];
                        $intNumElements = count($arrParents);
                    }    
                } else {
                    $arrValue = null;
                    $intNumElements = 0;
                }
                
                $arrInfoValue = array();
                
                if(!empty($arrInputfield['info'][0])){
                	$arrInfoValue[0] = 'title="'.$arrInputfield['info'][0].'"';
	                foreach($arrInputfield['info'] as $intLangKey => $strInfoValue) {
	                	$strInfoClass = 'mediadirInputfieldHint';
	                    $arrInfoValue[$intLangKey] = empty($strInfoValue) ? 'title="'.$arrInputfield['info'][0].'"' : 'title="'.$strInfoValue.'"';
	                }
                } else {
                	$arrInfoValue = null;
                    $strInfoClass = '';
                }
                   
                $intNextElementId = $intNumElements;                   
                
                $strBlankElement .= $objInit->mode == 'backend' ? '<fieldset style="border: 1px solid #0A50A1; width: 402px; margin-bottom: 10px; position: relative;"  id="'.$this->moduleNameLC.'ReferencesElement_'.$intId.'_ELEMENT-KEY">' : '<fieldset id="'.$this->moduleNameLC.'ReferencesElement_'.$intId.'_ELEMENT-KEY" style=" margin-bottom: 10px; position: relative;">'; 
                $strBlankElement .= $objInit->mode == 'backend' ? '<img src="../core/Core/View/Media/icons/delete.gif" onclick="referencesDeleteElement_'.$intId.'(ELEMENT-KEY);" style="cursor: pointer; position: absolute; top: -7px; right: 10px; z-index: 1000;"/>' : '<img src="../core/Core/View/Media/icons/delete.gif" onclick="referencesDeleteElement_'.$intId.'(ELEMENT-KEY);" style="cursor: pointer; position: absolute; top: -7px; right: 10px; z-index: 1000;"/>';   
                $strBlankElement .= $objInit->mode == 'backend' ? '<legend style="color: #0A50A1;">'.$arrInputfield['name'][0].' #ELEMENT-NR</legend>' : '<legend>'.$arrInputfield['name'][0].' #ELEMENT-NR</legend>';          
                $strBlankElement .= '<div id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_ELEMENT-KEY_Minimized" style="display: block;">';
                $strBlankElement .= $objInit->mode == 'backend' ? '<input type="text" name="'.$this->moduleNameLC.'Inputfield['.$intId.'][0][ELEMENT-KEY][title]" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_ELEMENT-KEY_0_title" value="'.$_ARRAYLANG['TXT_MEDIADIR_TITLE'].'" style="width: 300px" onfocus="this.select();" />' : '<input type="text" name="'.$this->moduleNameLC.'Inputfield['.$intId.'][0][ELEMENT-KEY][title]" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_ELEMENT-KEY_0_title" value="'.$_ARRAYLANG['TXT_MEDIADIR_TITLE'].'" onfocus="this.select();" />'; 
                $strBlankElement .= '<br />';   

                if($this->arrSettings['settingsFrontendUseMultilang'] == 1 || $objInit->mode == 'backend') {  
                    $strBlankElement .= $objInit->mode == 'backend' ? '<textarea name="'.$this->moduleNameLC.'Inputfield['.$intId.'][0][ELEMENT-KEY][desc]" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_ELEMENT-KEY_0_desc" style="width: 300px; height: 60px;" onfocus="this.select();">'.$_CORELANG['TXT_CORE_SETTING_NAME'].'</textarea>&nbsp;<a href="javascript:ExpandMinimizeMultiple('.$intId.', ELEMENT-KEY);">'.$_ARRAYLANG['TXT_MEDIADIR_MORE'].'&nbsp;&raquo;</a>' : '<textarea name="'.$this->moduleNameLC.'Inputfield['.$intId.'][0][ELEMENT-KEY][desc]" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_ELEMENT-KEY_0_desc" onfocus="this.select();">'.$_CORELANG['TXT_CORE_SETTING_NAME'].'</textarea>&nbsp;<a href="javascript:ExpandMinimizeMultiple('.$intId.', ELEMENT-KEY);">'.$_ARRAYLANG['TXT_MEDIADIR_MORE'].'&nbsp;&raquo;</a>';  
                    $strBlankElement .= '</div>';  
                    
                    $strBlankElement .= '<div id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_ELEMENT-KEY_Expanded" style="display: none;">';
                    foreach ($this->arrFrontendLanguages as $key => $arrLang) {
                        $intLangId = $arrLang['id'];

                        if(($key+1) == count($this->arrFrontendLanguages)) {
                            $minimize = "&nbsp;<a href=\"javascript:ExpandMinimizeMultiple(".$intId.", ELEMENT-KEY);\">&laquo;&nbsp;".$_ARRAYLANG['TXT_MEDIADIR_MINIMIZE']."</a>";
                        } else {
                            $minimize = "";
                        }
                                                                                    
                        $strBlankElement .= $objInit->mode == 'backend' ? '<input type="text" name="'.$this->moduleNameLC.'Inputfield['.$intId.']['.$intLangId.'][ELEMENT-KEY][title]" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_ELEMENT-KEY_'.$intLangId.'_title" style="width: 279px; margin-bottom: 2px; padding-left: 21px; background: #ffffff url('. \Env::get('cx')->getCodeBaseOffsetPath() . \Env::get('cx')->getCoreFolderName().'/Country/View/Media/Flag/flag_'.$arrLang['lang'].'.gif) no-repeat 3px 3px;" onfocus="this.select();" />&nbsp;'.$arrLang['name'].'<br /><textarea name="'.$this->moduleNameLC.'Inputfield['.$intId.']['.$intLangId.'][ELEMENT-KEY][desc]" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_ELEMENT-KEY_'.$intLangId.'_desc"  style="height: 60px; width: 279px; margin-bottom: 2px; padding-left: 21px; background: #ffffff url('. \Env::get('cx')->getCodeBaseOffsetPath() . \Env::get('cx')->getCoreFolderName().'/Country/View/Media/Flag/flag_'.$arrLang['lang'].'.gif) no-repeat 3px 3px;" onfocus="this.select();" /></textarea>'.$minimize.'<br /><br />' : '<input type="text" name="'.$this->moduleNameLC.'Inputfield['.$intId.']['.$intLangId.'][ELEMENT-KEY][title]" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_ELEMENT-KEY_'.$intLangId.'_title" onfocus="this.select();" />&nbsp;'.$arrLang['name'].'<br /><textarea name="'.$this->moduleNameLC.'Inputfield['.$intId.']['.$intLangId.'][ELEMENT-KEY][desc]" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_ELEMENT-KEY_'.$intLangId.'_desc" onfocus="this.select();" /></textarea>'.$minimize.'<br /><br />'; 
                    } 
                    $strBlankElement .= '</div>';   
                } else {
                    $strBlankElement .= '<textarea name="'.$this->moduleNameLC.'Inputfield['.$intId.'][0][ELEMENT-KEY][desc]" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_ELEMENT-KEY_0_desc" onfocus="this.select();">'.$_CORELANG['TXT_CORE_SETTING_NAME'].'</textarea>&nbsp;<a href="javascript:ExpandMinimizeMultiple('.$intId.', ELEMENT-KEY);">';  
                    $strBlankElement .= '</div>';  
                }
                
                $strBlankElement .= '</fieldset>';    
                
                $strElementSetId =  $this->moduleNameLC.'ReferencesElements_'.$intId; 
                $strElementId =  $this->moduleNameLC.'ReferencesElement_'.$intId.'_'; 
                
                $strInputfield = <<< EOF
<script type="text/javascript">
/* <![CDATA[ */                      
  
var nextReferenceId = $intNextElementId;
var blankReference = '$strBlankElement';     

function referencesAddElement_$intId(){                                           
    
    var blankReferenceReplace1 = blankReference.replace(/ELEMENT-KEY/g, nextReferenceId);    
    var blankReferenceReplace2 = blankReferenceReplace1.replace(/ELEMENT-NR/g, nextReferenceId+1);    
    
    \$J('#$strElementSetId').append(blankReferenceReplace2);     
    \$J('#$strElementId' + nextReferenceId).css('display', 'none');
    \$J('#$strElementId' + nextReferenceId).fadeIn("fast");               
    
    nextReferenceId = nextReferenceId + 1; 
}

function referencesDeleteElement_$intId(key){
    \$J('#$strElementId'+key).fadeOut("fast", function(){ \$J('#$strElementId'+key).remove();}); 
}

/* ]]> */
</script>

EOF;
                $strInputfield .= '<div class="'.$this->moduleNameLC.'GroupMultilang">';
                $strInputfield .= '<div id="'.$this->moduleNameLC.'ReferencesElements_'.$intId.'">';
                
                if($objInit->mode == 'backend') {
                    for($intKey = 0; $intKey < $intNumElements; $intKey++) {
                        $intNummer = $intKey+1;  
                          
                        $strInputfield .= '<fieldset id="'.$this->moduleNameLC.'ReferencesElement_'.$intId.'_'.$intKey.'" style="border: 1px solid #0A50A1; width: 402px; margin-bottom: 10px; position: relative;">';
                        $strInputfield .= '<img src="../core/Core/View/Media/icons/delete.gif" onclick="referencesDeleteElement_'.$intId.'('.$intKey.');" style="cursor: pointer; position: absolute; top: -7px; right: 10px; z-index: 1000;"/>'; 
                        $strInputfield .= '<legend style="color: #0A50A1;">'.$arrInputfield['name'][0].' #'.$intNummer.'</legend>';    
                        $strInputfield .= '<div id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_Minimized" style="display: block;">';
                        $strInputfield .= '<input type="text" name="'.$this->moduleNameLC.'Inputfield['.$intId.'][0]['.$intKey.'][\'title\']" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_0_title" value="'.$arrValue[0][$intKey]['title'].'" style="width: 300px" onfocus="this.select();" />'; 
                        $strInputfield .= '<br />'; 
                        $strInputfield .= '<textarea name="'.$this->moduleNameLC.'Inputfield['.$intId.'][0]['.$intKey.'][\'desc\']" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_0_title" style="width: 300px; height: 60px;" onfocus="this.select();" />'.$arrValue[0][$intKey]['desc'].'</textarea>&nbsp;<a href="javascript:ExpandMinimizeMultiple(\''.$intId.'\', \''.$intKey.'\');">'.$_ARRAYLANG['TXT_MEDIADIR_MORE'].'&nbsp;&raquo;</a>';
                        $strInputfield .= '</div>';  

                        $strInputfield .= '<div id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_Expanded" style="display: none;">';
                        foreach ($this->arrFrontendLanguages as $key => $arrLang) {
                            $intLangId = $arrLang['id'];

                            if(($key+1) == count($this->arrFrontendLanguages)) {
                                $minimize = "&nbsp;<a href=\"javascript:ExpandMinimizeMultiple('".$intId."', '".$intKey."');\">&laquo;&nbsp;".$_ARRAYLANG['TXT_MEDIADIR_MINIMIZE']."</a>";
                            } else {
                                $minimize = "";
                            }

                            $strInputfield .= '<input type="text" name="'.$this->moduleNameLC.'Inputfield['.$intId.']['.$intLangId.']['.$intKey.'][\'title\']" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_'.$intLangId.'_title" value="'.$arrValue[$intLangId][$intKey]['title'].'" style="width: 279px; margin-bottom: 2px; padding-left: 21px; background: #ffffff url(\''. \Env::get('cx')->getCodeBaseOffsetPath() . \Env::get('cx')->getCoreFolderName().'/Country/View/Media/Flag/flag_'.$arrLang['lang'].'.gif\') no-repeat 3px 3px;" onfocus="this.select();" />&nbsp;'.$arrLang['name'].'<br /><textarea name="'.$this->moduleNameLC.'Inputfield['.$intId.']['.$intLangId.']['.$intKey.'][\'desc\']" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_'.$intLangId.'_desc"  style="height: 60px; width: 279px; margin-bottom: 2px; padding-left: 21px; background: #ffffff url(\''. \Env::get('cx')->getCodeBaseOffsetPath() . \Env::get('cx')->getCoreFolderName().'/Country/View/Media/Flag/flag_'.$arrLang['lang'].'.gif\') no-repeat 3px 3px;" onfocus="this.select();" />'.$arrValue[$intLangId][$intKey]['desc'].'</textarea>'.$minimize.'<br /><br />'; 
                        } 
                        
                        $strInputfield .= '<input type="hidden" name="'.$this->moduleNameLC.'Inputfield['.$intId.'][old]['.$intKey.'][\'title\']" value="'.$arrValue[0][$intKey]['title'].'" />';  
                        $strInputfield .= '<input type="hidden" name="'.$this->moduleNameLC.'Inputfield['.$intId.'][old]['.$intKey.'][\'desc\']" value="'.$arrValue[0][$intKey]['desc'].'" />';  
                        $strInputfield .= '</div>'; 
                        $strInputfield .= '</fieldset>';      
                    }
                } else {                                                              
	                for($intKey = 0; $intKey < $intNumElements; $intKey++) {
                        $intNummer = $intKey+1;  
                          
                        $strInputfield .= '<fieldset id="'.$this->moduleNameLC.'ReferencesElement_'.$intId.'_'.$intKey.'" style=" margin-bottom: 10px; position: relative;">';
                        $strInputfield .= '<img src="../core/Core/View/Media/icons/delete.gif" onclick="referencesDeleteElement_'.$intId.'('.$intKey.');" style="cursor: pointer; position: absolute; top: -7px; right: 10px; z-index: 1000;"/>'; 
                        $strInputfield .= '<legend>'.$arrInputfield['name'][0].' #'.$intNummer.'</legend>';  
                        $strInputfield .= '<div id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_Minimized" style="display: block;">';
                        $strInputfield .= '<input type="text" name="'.$this->moduleNameLC.'Inputfield['.$intId.'][0]['.$intKey.'][\'title\']" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_0_title" value="'.$arrValue[0][$intKey]['title'].'" onfocus="this.select();" />'; 
                        $strInputfield .= '<br />';          

                        if($this->arrSettings['settingsFrontendUseMultilang'] == 1) {
                            $strInputfield .= '<textarea name="'.$this->moduleNameLC.'Inputfield['.$intId.'][0]['.$intKey.'][\'desc\']" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_0_title" onfocus="this.select();" />'.$arrValue[0][$intKey]['desc'].'</textarea>&nbsp;<a href="javascript:ExpandMinimizeMultiple(\''.$intId.'\', \''.$intKey.'\');">'.$_ARRAYLANG['TXT_MEDIADIR_MORE'].'&nbsp;&raquo;</a>';  
                            $strInputfield .= '</div>';  
                         
                            $strInputfield .= '<div id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_Expanded" style="display: none;">';
                            foreach ($this->arrFrontendLanguages as $key => $arrLang) {
                                $intLangId = $arrLang['id'];

                                if(($key+1) == count($this->arrFrontendLanguages)) {
                                    $minimize = "&nbsp;<a href=\"javascript:ExpandMinimizeMultiple('".$intId."', '".$intKey."');\">&laquo;&nbsp;".$_ARRAYLANG['TXT_MEDIADIR_MINIMIZE']."</a>";
                                } else {
                                    $minimize = "";
                                }

                                $strInputfield .= '<input type="text" name="'.$this->moduleNameLC.'Inputfield['.$intId.']['.$intLangId.']['.$intKey.'][\'title\']" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_'.$intLangId.'_title" value="'.$arrValue[$intLangId][$intKey]['title'].'" onfocus="this.select();" />&nbsp;'.$arrLang['name'].'<br /><textarea name="'.$this->moduleNameLC.'Inputfield['.$intId.']['.$intLangId.']['.$intKey.'][\'desc\']" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_'.$intLangId.'_desc" />'.$arrValue[$intLangId][$intKey]['desc'].'</textarea>'.$minimize.'<br /><br />'; 
                            } 
                            
                            $strInputfield .= '</div>';   
                        } else {
                            $strInputfield .= '<textarea name="'.$this->moduleNameLC.'Inputfield['.$intId.'][0]['.$intKey.'][\'desc\']" id="'.$this->moduleNameLC.'Inputfield_'.$intId.'_'.$intKey.'_0_title" onfocus="this.select();" />'.$arrValue[0][$intKey]['desc'].'</textarea>';  
                            $strInputfield .= '</div>';  
                        }
                        
                        $strInputfield .= '<input type="hidden" name="'.$this->moduleNameLC.'Inputfield['.$intId.'][old]['.$intKey.'][\'title\']" value="'.$arrValue[0][$intKey]['title'].'" />';  
                        $strInputfield .= '<input type="hidden" name="'.$this->moduleNameLC.'Inputfield['.$intId.'][old]['.$intKey.'][\'desc\']" value="'.$arrValue[0][$intKey]['desc'].'" />'; 
                        $strInputfield .= '</fieldset>';   
                    } 
                }       
                    
                $strInputfield .= '</div>';  
                $strInputfield .= '<input type="button" value="'.$arrInputfield['name'][0].' '.$_ARRAYLANG['TXT_MEDIADIR_ADD'].'" onclick="referencesAddElement_'.$intId.'();" />'; 
                $strInputfield .= '</div>';                                                                                                                                       
                
                return $strInputfield;

                break;  
            case 2:
                //search View  
                break;
        }
    }



    function saveInputfield($intInputfieldId, $arrValue, $intLangId) 
    {         
        $arrValues = array();
        
        foreach($arrValue as $intKey => $arrValuesTmp) {
            $arrValues[] = join("##", $arrValuesTmp);
        }
                 
        $strValue = contrexx_strip_tags(contrexx_input2raw(join("||", $arrValues)));
        return $strValue;
    }


    function deleteContent($intEntryId, $intIputfieldId)
    {
        global $objDatabase;

        $objDeleteInputfield = $objDatabase->Execute("DELETE FROM ".DBPREFIX."module_".$this->moduleTablePrefix."_rel_entry_inputfields WHERE `entry_id`='".intval($intEntryId)."' AND  `field_id`='".intval($intIputfieldId)."'");

        if($objDeleteInputfield !== false) {
            return true;
        } else {
            return false;
        }
    }



    function getContent($intEntryId, $arrInputfield, $arrTranslationStatus)
    {
        global $objDatabase, $_LANGID;

        $intId = intval($arrInputfield['id']);
        $objEntryDefaultLang = $objDatabase->Execute("SELECT `lang_id` FROM ".DBPREFIX."module_".$this->moduleTablePrefix."_entries WHERE id=".intval($intEntryId)." LIMIT 1");
        $intEntryDefaultLang = intval($objEntryDefaultLang->fields['lang_id']);
        
        if($this->arrSettings['settingsTranslationStatus'] == 1) {
	        if(in_array($_LANGID, $arrTranslationStatus)) {
	        	$intLangId = $_LANGID;
	        } else {
	        	$intLangId = $intEntryDefaultLang;
	        }
        } else {
        	$intLangId = $_LANGID;
        }
        
        $objInputfieldValue = $objDatabase->Execute("
            SELECT
                `value`
            FROM
                ".DBPREFIX."module_".$this->moduleTablePrefix."_rel_entry_inputfields
            WHERE
                field_id=".$intId."
            AND
                entry_id=".intval($intEntryId)."
            AND
                lang_id=".$intLangId."
            LIMIT 1
        ");
        
        if(empty($objInputfieldValue->fields['value'])) {
        	$objInputfieldValue = $objDatabase->Execute("
	            SELECT
	                `value`
	            FROM
	                ".DBPREFIX."module_".$this->moduleTablePrefix."_rel_entry_inputfields
	            WHERE
	                field_id=".$intId."
	            AND
	                entry_id=".intval($intEntryId)."
	            AND
	                lang_id=".intval($intEntryDefaultLang)."
	            LIMIT 1
	        ");
        }
        
        $strValue = strip_tags(htmlspecialchars($objInputfieldValue->fields['value'], ENT_QUOTES, CONTREXX_CHARSET));
        

        if(!empty($strValue)) {
            $arrParents = array();
            $arrParents = explode("||", $strValue); 
            $strValue = null;      
            
            foreach($arrParents as $intKey => $strChildes) {
                $arrChildes = array();  
                $arrChildes = explode("##", $strChildes);    
                
                $strTitle = '<span class="'.$this->moduleNameLC.'ReferenceTitle">'.$arrChildes[0].'</span>';
                $strDesc = '<span class="'.$this->moduleNameLC.'ReferenceDescription">'.$arrChildes[1].'</span>';   
                
                $strValue .= '<div class="'.$this->moduleNameLC.'Reference">'.$strTitle.$strDesc.'</div>';
            }   
            
            $arrContent['TXT_'.$this->moduleLangVar.'_INPUTFIELD_NAME'] = htmlspecialchars($arrInputfield['name'][0], ENT_QUOTES, CONTREXX_CHARSET);
            $arrContent[$this->moduleLangVar.'_INPUTFIELD_VALUE'] = $strValue;
        } else {
            $arrContent = null;
        }

        return $arrContent;
    }


    function getJavascriptCheck()
    {
    	$fieldName = $this->moduleNameLC."Inputfield_";
        $strJavascriptCheck = <<<EOF

            case 'references':  
                break;

EOF;
        return $strJavascriptCheck;
    }
    
    
    function getFormOnSubmit($intInputfieldId)
    {
        return null;
    }
}
