<?php

/**
 * BackendTable
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  core
 */

/**
 * BackendTable
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  core
 */
class BackendTable extends HTML_Table {

    public function __construct($attrs = array(), $options = array()) {
        global $_ARRAYLANG;
        
        if ($attrs instanceof \Cx\Core_Modules\Listing\Model\Entity\DataSet) {
            $hasMasterTableHeader = !empty($options['header']);
            // add master table-header-row
            if ($hasMasterTableHeader) {
                $this->addRow(array(0 => $options['header']), null, 'th');
            }
            $first = true;
            $row = 1 + $hasMasterTableHeader;
            foreach ($attrs as $rowname=>$rows) {
                $col = 0;
                $virtual = $rows['virtual'];
                unset($rows['virtual']);
                if (isset($options['multiActions'])) {
                    $this->setCellContents($row, $col, '<input name="select-' . $rowname . '" value="' . $rowname . '" type="checkbox" />', 'TD', '0', false);
                    $col++;
                }
                foreach ($rows as $header=>$data) {
                    $encode = true;
                    if (
                        isset($options['fields']) &&
                        isset($options['fields'][$header]) &&
                        isset($options['fields'][$header]['showOverview']) &&
                        !$options['fields'][$header]['showOverview']
                    ) {
                        continue;
                    }
                    
                    $origHeader = $header;
                    if(isset($options['fields'][$header]['sorting'])) {
                        $sorting = $options['fields'][$header]['sorting'];
                    } else if(isset($options['functions']['sorting'])) {
                        $sorting = $options['functions']['sorting'];
                    }
                    if ($first) {
                        if (isset($options['fields'][$header]['header'])) {
                            $header = $options['fields'][$header]['header'];
                        }
                        if (isset($_ARRAYLANG[$header])) {
                            $header = $_ARRAYLANG[$header];
                        }
                        if (
                            is_array($options['functions']) &&
                            isset($options['functions']['sorting']) &&
                            $options['functions']['sorting'] &&
                            $sorting !== false
                        ) {
                            $order = '';
                            $img = '&uarr;&darr;';
                            if (isset($_GET['order'])) {
                                $supOrder = explode('/', $_GET['order']);
                                if (current($supOrder) == $origHeader) {
                                    $order = '/DESC';
                                    $img = '&darr;';
                                    if (count($supOrder) > 1 && $supOrder[1] == 'DESC') {
                                        $order = '';
                                        $img = '&uarr;';
                                    }
                                }
                            }
                            $header = '<a href="' .  \Env::get('cx')->getRequest()->getUrl() . '&order=' . $origHeader . $order . '" style="white-space: nowrap;">' . $header . ' ' . $img . '</a>';
                        }
                        if ($hasMasterTableHeader) {
                            $this->setCellContents(1, $col, $header, 'td', 0);
                        } else {
                            $this->setCellContents(0, $col, $header, 'th', 0);
                        }
                    }
                    if (
                        isset($options['fields']) &&
                        isset($options['fields'][$origHeader]) &&
                        isset($options['fields'][$origHeader]['table']) &&
                        isset($options['fields'][$origHeader]['table']['parse']) &&
                        is_callable($options['fields'][$origHeader]['table']['parse'])
                    ) {
                        $callback = $options['fields'][$origHeader]['table']['parse'];
                        $data = $callback($data, $rows);
                        $encode = false; // todo: this should be set by callback
                    } else if (is_object($data) && get_class($data) == 'DateTime') {
                        $data = $data->format(ASCMS_DATE_FORMAT);
                    } else if (isset($options['fields'][$origHeader]) && isset($options['fields'][$origHeader]['type']) && $options['fields'][$origHeader]['type'] == '\Country') {
                        $data = \Cx\Core\Country\Controller\Country::getNameById($data);
                        if (empty($data)) {
                            $data = \Cx\Core\Country\Controller\Country::getNameById(204);
                        }
                    } else if (gettype($data) == 'boolean') {
                        $data = '<i>' . ($data ? $_ARRAYLANG['TXT_YES'] : $_ARRAYLANG['TXT_NO']) . '</i>';
                        $encode = false;
                    } else if ($data === null) {
                        $data = '<i>' . $_ARRAYLANG['TXT_CORE_NONE'] . '</i>';
                        $encode = false;
                    } else if (empty($data)) {
                        $data = '<i>(empty)</i>';
                        $encode = false;
                    }
                    $this->setCellContents($row, $col, $data, 'TD', 0, $encode);
                    $col++;
                }
                if ($this->hasRowFunctions($options['functions'], $virtual)) {
                    if ($first) {
                        $header = 'Functions';
                        if (isset($_ARRAYLANG['TXT_FUNCTIONS'])) {
                            $header = $_ARRAYLANG['TXT_FUNCTIONS'];
                        }
                        if ($hasMasterTableHeader) {
                            $this->setCellContents(1, $col, $header, 'td', 0, true);
                        } else {
                            $this->setCellContents(0, $col, $header, 'th', 0, true);
                        }
                    }
                    
                    $this->updateColAttributes($col, array('style' => 'text-align:right;'));
                    if (empty($options['functions']['baseUrl'])) {
                        $options['functions']['baseUrl'] = clone \Env::get('cx')->getRequest()->getUrl();
                    }
                    $this->setCellContents($row, $col, $this->getFunctionsCode($rowname, $rows, $options['functions'], $virtual), 'TD', 0);
                }
                $first = false;
                $row++;
            }
            // adjust colspan of master-table-header-row
            if ($hasMasterTableHeader) {
                $this->setCellAttributes(0, 0, array('colspan' => $col + is_array($options['functions'])));
                $this->updateRowAttributes(1, array('class' => 'row3'), true);
            }
            // add multi-actions
            if (isset($options['multiActions'])) {
                $multiActionsCode = '
                    <img src="images/icons/arrow.gif" width="38" height="22" alt="^" title="^">
                    <a href="#" onclick="jQuery(\'input[type=checkbox]\').prop(\'checked\', true);return false;">' . $_ARRAYLANG['TXT_SELECT_ALL'] . '</a> /
                    <a href="#" onclick="jQuery(\'input[type=checkbox]\').prop(\'checked\', false);return false;">' . $_ARRAYLANG['TXT_DESELECT_ALL'] . '</a>
                    <img alt="-" title="-" src="images/icons/strike.gif">
                ';
                $multiActions = array(''=>$_ARRAYLANG['TXT_SUBMIT_SELECT']);
                foreach ($options['multiActions'] as $actionName=>$actionProperties) {
                    $actionTitle = $actionName;
                    if (isset($actionProperties['title'])) {
                        $actionTitle = $actionProperties['title'];
                    } else if (isset($_ARRAYLANG[$actionName])) {
                        $actionTitle = $_ARRAYLANG[$actionName];
                    }
                    if (isset($actionProperties['jsEvent'])) {
                        $actionName = $actionProperties['jsEvent'];
                    }
                    $multiActions[$actionName] = $actionTitle;
                }
                $select = new \Cx\Core\Html\Model\Entity\DataElement(
                    'cxMultiAction',
                    \Html::getOptions($multiActions),
                    \Cx\Core\Html\Model\Entity\DataElement::TYPE_SELECT
                );
                // this is not a nice place for this code
                // but we should cleanup this complete class and make
                // it base on templates
                $select->setAttribute(
                    'onchange',
                    '
                        var regex = /([a-zA-Z\/]+):([a-zA-Z\/]+)/;
                        var matches = jQuery(this).val().match(regex);
                        if (!matches) {
                            return false;
                        }
                        var checkboxes = jQuery(this).closest("table").find("input[type=checkbox]");
                        var activeRows = [];
                        checkboxes.filter(":checked").each(function(el) {
                            activeRows.push(jQuery(this).val());
                        });
                        cx.trigger(matches[1], matches[2], activeRows);
                        checkboxes.prop("checked", false);
                        jQuery(this).val("");
                    '
                );
                $this->setCellContents($row, 0, $multiActionsCode . $select, 'TD', 0);
                $this->setCellAttributes($row, 0, array('colspan' => $col + is_array($options['functions'])));
            }
            $attrs = array();
        }
        parent::__construct(array_merge($attrs, array('class' => 'adminlist', 'width' => '100%')));
    }

    /**
     * Override from parent. Added contrexx_raw2xhtml support
     * @param type $row
     * @param type $col
     * @param type $contents
     * @param type $type
     * @param type $body
     * @param type $encode
     * @return type 
     */
    function setCellContents($row, $col, $contents, $type = 'TD', $body = 0, $encode = false)
    {
        if ($encode) {
            //1->n relations
            if (is_object($contents) && $contents instanceof \Doctrine\ORM\PersistentCollection) {
                $contents = $contents->toArray();
            }
            if (is_array($contents)) {
                $displayedRelationsLimit = 3;
                if (count($contents) > $displayedRelationsLimit) {
                    $contents = array_slice($contents, 0, $displayedRelationsLimit);
                    $contents[] = '...';
                }
                $contents = implode(', ', $contents);
            }
            //replaces curly brackets, so they get not parsed with the sigma engine
            $contents = preg_replace(array("/{/","/}/"), array("&#123;","&#125;"), contrexx_raw2xhtml($contents), -1);
        }
        $ret = $this->_adjustTbodyCount($body, 'setCellContents');
        if (PEAR::isError($ret)) {
            return $ret;
        }
        $ret = $this->_tbodies[$body]->setCellContents($row, $col, $contents, $type);
        if (PEAR::isError($ret)) {
            return $ret;
        }
    }
    
    protected function hasRowFunctions($functions, $virtual = false) {
        if (!is_array($functions)) {
            return false;
        }
        if ($virtual) {
            return false;
        }
        if (isset($functions['actions']) && is_callable($functions['actions'])) {
            return true;
        }
        if (isset($functions['edit']) && $functions['edit']) {
            return true;
        }
        if (isset($functions['delete']) && $functions['delete']) {
            return true;
        }
        return false;
    }
    
    protected function getFunctionsCode($rowname, $rowData, $functions, $virtual = false ) {
        global $_ARRAYLANG;
        
        $baseUrl = $functions['baseUrl'];
        $code = '<span class="functions">';
        if(!$virtual){
            $editUrl = clone $baseUrl;
            $params = $editUrl->getParamArray();
            $editId = '';
            if (!empty($params['editid'])) {
                $editId = $params['editid'] . ',';
            }
            $editId .= '{' . $functions['vg_increment_number'] . ',' . $rowname . '}';

            if (isset($functions['actions']) && is_callable($functions['actions'])) {
                $code .= $functions['actions']($rowData, $editId);
            }
            
            if (isset($functions['edit']) && $functions['edit']) {
                $editUrl->setParam('editid', $editId);
                $code .= '<a href="' . $editUrl . '" class="edit" title="'.$_ARRAYLANG['TXT_CORE_RECORD_EDIT_TITLE'].'"></a>';
            }
            if (isset($functions['delete']) && $functions['delete']) {
                $deleteUrl = clone $baseUrl;
                $deleteUrl->setParam('deleteid', $rowname);
                $deleteUrl->setParam('vg_increment_number', $functions['vg_increment_number']);
                $deleteUrl.='&csrf='.\Cx\Core\Csrf\Controller\Csrf::code();
                $onclick ='if (confirm(\''.$_ARRAYLANG['TXT_CORE_RECORD_DELETE_CONFIRM'].'\'))'.
                        'window.location.replace(\''.$deleteUrl.'\');';
                $_uri = 'javascript:void(0);';
                $code .= '<a onclick="'.$onclick.'" href="'.$_uri.'" class="delete" title="'.$_ARRAYLANG['TXT_CORE_RECORD_DELETE_TITLE'].'"></a>';
            }
        }
        return $code . '</span>';
    }

    /**
     * Returns an HTML formatted attribute string
     * Use Sigma for parsing
     * @param    array   $attributes
     * @return   string
     * @access   private
     */
    function _getAttrString($attributes)
    {
        $template = new \Cx\Core\Html\Sigma(ASCMS_CORE_PATH.'/Html/View/Template/Generic/');
        $template->loadTemplateFile('Attribute.html');
        
        $strAttr = '';

        if (is_array($attributes)) {
            $charset = HTML_Common::charset();
            foreach ($attributes as $key => $value) {
                $template->setVariable(array(
                    'ATTRIBUTE_NAME' => $key,
                    'ATTRIBUTE_VALUE' => htmlspecialchars($value, ENT_COMPAT, $charset),
                ));
                $template->parse('attribute');
            }
        }
        return $template->get();
    } // end func _getAttrString
    
    /**
     * This is a soft override of Storage's toHtml()
     * in order to use Sigma for parsing
     */
    protected function parseStorage($template, $storage) {
        for ($i = 0; $i < $storage->_rows; $i++) {
            if (isset($storage->_structure[$i]['attr'])) {
                $template->setVariable('TR_ATTRIBUTES', $this->_getAttrString($storage->_structure[$i]['attr']));
            }
            for ($j = 0; $j < $storage->_cols; $j++) {
                $attr     = '';
                $contents = '';
                $type     = 'td';
                if (isset($storage->_structure[$i][$j]) && $storage->_structure[$i][$j] == '__SPANNED__') {
                    continue;
                }
                if (isset($storage->_structure[$i][$j]['type'])) {
                    $type = (strtolower($storage->_structure[$i][$j]['type']) == 'th' ? 'th' : 'td');
                }
                if (isset($storage->_structure[$i][$j]['attr'])) {
                    $attr = $storage->_structure[$i][$j]['attr'];
                }
                if (isset($storage->_structure[$i][$j]['contents'])) {
                    $contents = $storage->_structure[$i][$j]['contents'];
                }
                if (is_object($contents)) {
                    // changes indent and line end settings on nested tables
                    if (is_subclass_of($contents, 'html_common')) {
                        $contents->setTab($tab . $extraTab);
                        $contents->setTabOffset($storage->_tabOffset + 3);
                        $contents->_nestLevel = $storage->_nestLevel + 1;
                        $contents->setLineEnd($storage->_getLineEnd());
                    }
                    if (method_exists($contents, 'toHtml')) {
                        $contents = $contents->toHtml();
                    } elseif (method_exists($contents, 'toString')) {
                        $contents = $contents->toString();
                    }
                }
                if (is_array($contents)) {
                    $contents = implode(', ', $contents);
                }
                if (isset($storage->_autoFill) && $contents === '') {
                    $contents = $storage->_autoFill;
                }
                $template->setVariable(array(
                    'CELL_ATTRIBUTES' => $this->_getAttrString($attr),
                    'CELL_CONTENTS' => $contents,
                ));
                $template->parse('row_' . $type);
            }
            $template->parse('row');
        }
    }

    /**
     * Returns the table structure as HTML
     * Override in order to use Sigma for parsing
     * @access  public
     * @return  string
     */
    function toHtml()
    {
        $this->altRowAttributes(2, array('class' => 'row1'), array('class' => 'row2'), true);
        $strHtml = '';
        $tabs = $this->_getTabs();
        $tab = $this->_getTab();
        $lnEnd = $this->_getLineEnd();
        $tBodyColCounts = array();
        for ($i = 0; $i < $this->_tbodyCount; $i++) {
            $tBodyColCounts[] = $this->_tbodies[$i]->getColCount();
        }
        $tBodyMaxColCount = 0;
        if (count($tBodyColCounts) > 0) {
            $tBodyMaxColCount = max($tBodyColCounts);
        }
        if ($this->_comment) {
            $strHtml .= $tabs . "<!-- $this->_comment -->" . $lnEnd;
        }
        $template = new \Cx\Core\Html\Sigma(ASCMS_CORE_PATH.'/Html/View/Template/Generic/');
        $template->loadTemplateFile('Table.html');
        if ($this->getRowCount() > 0 && $tBodyMaxColCount > 0) {
            $template->setVariable('TABLE_ATTRIBUTES', $this->_getAttrString($this->_attributes));
            if (!empty($this->_caption)) {
                $attr = $this->_caption['attr'];
                $contents = $this->_caption['contents'];
                if (is_array($contents)) {
                    $contents = implode(', ', $contents);
                }
                $template->setVariable(array(
                    'CAPTION_ATTRIBUTES' => $this->_getAttrString($attr),
                    'CAPTION_CONTENTS' => $contents,
                ));
                $template->parse('caption');
            }
            if (!empty($this->_colgroup)) {
                foreach ($this->_colgroup as $g => $col) {
                    $attr = $this->_colgroup[$g]['attr'];
                    $contents = $this->_colgroup[$g]['contents'];
                    $template->setVariable(array(
                        'COLGROUP_ATTRIBUTES' => $this->_getAttrString($attr),
                    ));
                    if (!empty($contents)) {
                        $strHtml .= $lnEnd;
                        if (!is_array($contents)) {
                            $contents = array($contents);
                        }
                        foreach ($contents as $a => $colAttr) {
                            $attr = $this->_parseAttributes($colAttr);
                            $template->setVariable(array(
                                'COL_ATTRIBUTES' => $this->_getAttrString($attr),
                            ));
                            $template->parse('col');
                        }
                    }
                    $template->parse('colgroup');
                }
            }
            if ($this->_useTGroups) {
                $tHeadColCount = 0;
                if ($this->_thead !== null) {
                    $tHeadColCount = $this->_thead->getColCount();
                }
                $tFootColCount = 0;
                if ($this->_tfoot !== null) {
                    $tFootColCount = $this->_tfoot->getColCount();
                }
                $maxColCount = max($tHeadColCount, $tFootColCount, $tBodyMaxColCount);
                if ($this->_thead !== null) {
                    $this->_thead->setColCount($maxColCount);
                    if ($this->_thead->getRowCount() > 0) {
                        $template->setVariable(array(
                            'THEAD_ATTRIBUTES' => $this->_getAttrString($this->_thead->_attributes),
                        ));
                        $this->parseStorage($template, $this->_thead);
                        $template->touchBlock('thead_close');
                    }
                }
                if ($this->_tfoot !== null) {
                    $this->_tfoot->setColCount($maxColCount);
                    if ($this->_tfoot->getRowCount() > 0) {
                        $template->setVariable(array(
                            'TFOOT_ATTRIBUTES' => $this->_getAttrString($this->_tfoot->_attributes),
                        ));
                        $this->parseStorage($template, $this->_tfoot);
                        $template->touchBlock('tfoot_close');
                    }
                }
                for ($i = 0; $i < $this->_tbodyCount; $i++) {
                    $this->_tbodies[$i]->setColCount($maxColCount);
                    if ($this->_tbodies[$i]->getRowCount() > 0) {
                        $template->setVariable(array(
                            'TBODY_ATTRIBUTES' => $this->_getAttrString($this->_tbodies[$i]->_attributes),
                        ));
                        $this->parseStorage($template, $this->_tbodies[$i]);
                        $template->touchBlock('tbody_close');
                    }
                }
            } else {
                for ($i = 0; $i < $this->_tbodyCount; $i++) {
                    $this->parseStorage($template, $this->_tbodies[$i]);
                }
            }
        }
        return $template->get();
    }
}
