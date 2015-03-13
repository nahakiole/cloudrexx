<?php

/**
 * 
 */

namespace Cx\Core\Html\Controller;

/**
 * 
 */
class FormGenerator {
    protected $form = null;
    
    public function __construct($entity, $actionUrl = null, $entityClass = '', $options = array()) {
        // Remove the virtual element from array
        unset($entity['virtual']);
        if (empty($entityClass) && is_object($entity)) {
            $entityClass = get_class($entity);
        }
        \JS::registerCSS(\Env::get('cx')->getCoreFolderName() . '/Html/View/Style/Backend.css');
        $this->form = new \Cx\Core\Html\Model\Entity\FormElement($actionUrl);
        $this->form->setAttribute('id', 'form-X');
        $this->form->setAttribute('class', 'cx-ui');
        $em = \Env::get('em');
        $title = new \Cx\Core\Html\Model\Entity\HtmlElement('legend');
        $title->addChild(new \Cx\Core\Html\Model\Entity\TextElement($entityClass));
        $this->form->addChild($title);
        // @todo replace this by auto-find editid
        if (isset($_REQUEST['editid'])) {
            $editIdField = new \Cx\Core\Html\Model\Entity\DataElement('editid', contrexx_input2raw($_REQUEST['editid']), 'input');
            $editIdField->setAttribute('type', 'hidden');
            $this->form->addChild($editIdField);   
        }
        // foreach entity field
        /*$metadata = $em->getClassMetadata(get_class($entity));
        foreach ($metadata->getColumnNames() as $field) {
            $type = $metadata->fieldMappings[$field]['type'];//*/
        foreach ($entity as $field=>$value) {
            $type = null;
            
            if (!empty($options[$field]['type'])) {
                $type = $options[$field]['type'];
            }

            if (is_object($value)) {
                if ($value instanceof \Cx\Model\Base\EntityBase) {
                    $type = 'Cx\Model\Base\EntityBase';
                } elseif ($value instanceof \Doctrine\Common\Collections\Collection) {
                    continue;
                } else {
                    $type = get_class($value);
                }
            }//*/
            $length = 0;
            /*if (isset($metadata->fieldMappings[$field]['length'])) {
                $length = $metadata->fieldMappings[$field]['length'];
            }*/
            //if (is_array($entity) && isset($entity[$field])) {
                $value = $entity[$field];
            /*} else {
                $value = $metadata->getFieldValue($entity, $field);
            }*/
            //$this->addFieldsForMetadata($metadata->fieldMappings[$field], $value);
            /*$label = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
            $label->setAttribute('for', 'formX_' . $field);
            $label->addChild(new \Cx\Core\Html\Model\Entity\TextElement($field));
            $this->form->addChild($label);*/
            $fieldOptions = array();
            if (isset($options['fields']) && isset($options['fields'][$field])) {
                $fieldOptions = $options['fields'][$field];
            }
            /*$element = $this->getDataElement($field, $type, $length, $value, $fieldOptions);
            $element->setAttribute('id', 'form-X-' . $field);*/
            $dataElement = $this->getDataElement($field, $type, $length, $value, $fieldOptions);
            if (empty($dataElement)) {
                continue;
            }
            $dataElement->setAttribute('id', 'form-X-' . $field);
            $this->form->addChild(static::getDataElementGroup($field, $dataElement, $fieldOptions));
        }
        if (isset($options['cancelUrl'])) {
            $this->form->cancelUrl =$options['cancelUrl'];
        }
    }
    
    public static function getDataElementGroup($field, $dataElement, $fieldOptions = array()) {
        $group = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $group->setAttribute('class', 'group');
        $label = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
        $label->setAttribute('for', 'form-X-' . $field);
        $fieldHeader = $field;
        if (isset($fieldOptions['formtext'])){
            $fieldHeader = FormGenerator::getFormLabel($fieldOptions, 'formtext');
        } else if (isset($fieldOptions['header'])) {
            $fieldHeader = FormGenerator::getFormLabel($fieldOptions, 'header');
        }
        $label->addChild(new \Cx\Core\Html\Model\Entity\TextElement($fieldHeader));
        $group->addChild($label);
        $controls = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $controls->setAttribute('class', 'controls');
        $controls->addChild($dataElement);
        $group->addChild($controls);
        return $group;
    }
    
    public function getDataElement($name, $type, $length, $value, $options) {
        global $_ARRAYLANG;
        if (!empty($options['type'])) {
            $type = $options['type'];
        }
        if (isset($options['formfield']) && is_callable($options['formfield'])) {
            $formFieldGenerator = $options['formfield'];
            $formField = $formFieldGenerator($name, $type, $length, $value, $options);
            if (is_a($formField, 'Cx\Core\Html\Model\Entity\HtmlElement')) {
                return $formField;
            }
        }
        switch ($type) {
            case 'bool':
            case 'boolean':
                // yes/no checkboxes
                $fieldset = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
                $inputYes = new \Cx\Core\Html\Model\Entity\DataElement($name, 'yes');
                $inputYes->setAttribute('type', 'radio');
                $inputYes->setAttribute('value', '1');
                $inputYes->setAttribute('id', 'form-X-' . $name . '_yes');
                $fieldset->addChild($inputYes);
                $labelYes = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
                $labelYes->setAttribute('for', 'form-X-' . $name . '_yes');
                $labelYes->addChild(new \Cx\Core\Html\Model\Entity\TextElement($_ARRAYLANG['TXT_YES']));
                $fieldset->addChild($labelYes);
                $inputNo = new \Cx\Core\Html\Model\Entity\DataElement($name, 'no');
                $inputNo->setAttribute('id', 'form-X-' . $name . '_no');
                $inputNo->setAttribute('type', 'radio');
                $inputNo->setAttribute('value', '0');
                $fieldset->addChild($inputNo);
                $labelNo = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
                $labelNo->setAttribute('for', 'form-X-' . $name . '_no');
                $labelNo->addChild(new \Cx\Core\Html\Model\Entity\TextElement($_ARRAYLANG['TXT_NO']));
                $fieldset->addChild($labelNo);
                if ($value) {
                    $inputYes->setAttribute('checked');
                } else {
                    $inputNo->setAttribute('checked');
                }
                return $fieldset;
                break;
            case 'int':
            case 'integer':
                // input field with type number
                $inputNumber = new \Cx\Core\Html\Model\Entity\DataElement(
                    $name,
                    $value,
                    \Cx\Core\Html\Model\Entity\DataElement::TYPE_INPUT,
                    new \Cx\Core\Validate\Model\Entity\RegexValidator(
                        '/-?[0-9]*/'
                    )
                );
                $inputNumber->setAttribute('type', 'number');
                return $inputNumber;
                break;
            case 'Cx\Model\Base\EntityBase':
                $entityClass = get_class($value);
                $entities = \Env::get('em')->getRepository($entityClass)->findAll();
                $primaryKeyName = \Env::get('em')->getClassMetadata($entityClass)->getSingleIdentifierFieldName();
                $selected = \Env::get('em')->getClassMetadata($entityClass)->getFieldValue($value, $primaryKeyName);
                foreach ($entities as $entity) {
                    $arrEntities[\Env::get('em')->getClassMetadata($entityClass)->getFieldValue($entity, $primaryKeyName)] = $entity;
                }
                $select = new \Cx\Core\Html\Model\Entity\DataElement(
                    $name,
                    \Html::getOptions($arrEntities, $selected),
                    \Cx\Core\Html\Model\Entity\DataElement::TYPE_SELECT
                );
                return $select;
                break;
            case 'Country':
                // this is for customizing only:
                $data = \Cx\Core\Country\Controller\Country::getNameById($value);
                if (empty($data)) {
                    $value = 204;
                }
                $options = \Cx\Core\Country\Controller\Country::getMenuoptions($value, false);
                $select = new \Cx\Core\Html\Model\Entity\DataElement(
                    $name,
                    $options,
                    \Cx\Core\Html\Model\Entity\DataElement::TYPE_SELECT
                );
                return $select;
                break;
            case 'DateTime':
            case 'datetime':
                // input field with type text and class datepicker
                if ($value instanceof \DateTime) {
                    $value = $value->format(ASCMS_DATE_FORMAT);
                }
                if (is_null($value)) {
                    $value = '';
                }
                $input = new \Cx\Core\Html\Model\Entity\DataElement($name, $value);
                $input->setAttribute('type', 'text');
                $input->setAttribute('class', 'datepicker');
                if (isset($options['readonly']) && $options['readonly']) {
                    $input->setAttribute('disabled');
                }
                \DateTimeTools::addDatepickerJs();
                \JS::registerCode('
                        cx.jQuery(function() {
                          cx.jQuery(".datepicker").datetimepicker();
                        });
                        ');
                return $input;
                break;
            case 'text':
                // textarea
                $textarea = new \Cx\Core\Html\Model\Entity\HtmlElement('textarea');
                $textarea->setAttribute('name', $name);
                if (isset($options['readonly']) && $options['readonly']) {
                    $textarea->setAttribute('disabled');
                }
                $textarea->addChild(new \Cx\Core\Html\Model\Entity\TextElement($value));
                return $textarea;
                break;
            case 'phone':
                // input field with type phone
                $input = new \Cx\Core\Html\Model\Entity\DataElement($name, $value);
                $input->setAttribute('type', 'phone');
                if (isset($options['readonly']) && $options['readonly']) {
                    $input->setAttribute('disabled');
                }
                return $input;
                break;
            case 'mail':
                // input field with type mail
                $emailValidator = new \Cx\Core\Validate\Model\Entity\EmailValidator();
                $input = new \Cx\Core\Html\Model\Entity\DataElement($name, $value, 'input', $emailValidator);
                $input->setAttribute('onkeyup', $emailValidator->getJavaScriptCode());
                $input->setAttribute('type', 'mail');
                if (isset($options['readonly']) && $options['readonly']) {
                    $input->setAttribute('disabled');
                }
                return $input;
                break;
            case 'uploader':
                \JS::registerCode('
                    function javascript_callback_function(data) {
                        if(data.type=="file") {
                            if(data.data[0].datainfo.extension=="Jpg"||data.data[0].datainfo.extension=="Gif"){
                                cx.jQuery("#'.$name.'").val(data.data[0].datainfo.filepath);
                            }
                        }
                        //console.log(data.data[0].datainfo.extension);
                    }
                ');
                $mediaBrowser = new \Cx\Core_Modules\MediaBrowser\Model\MediaBrowser();
                $mediaBrowser->setOptions(array('type' => 'button'));
                $mediaBrowser->setCallback('javascript_callback_function');
                $mediaBrowser->setOptions(
                    array(
                        'data-cx-mb-views' => 'filebrowser,uploader',
                        'id' => 'page_target_browse',
                        'cxMbStartview' => 'MediaBrowserList'
                    )
                );
                $mb = $mediaBrowser->getXHtml(
                    $_ARRAYLANG['TXT_CORE_CM_BROWSE']
                );
                
                $input = new \Cx\Core\Html\Model\Entity\DataElement($name, $value);
                $input->setAttribute('type', 'text');
                $input->setAttribute('id', $name);
                
                $div = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
                $div->addChild($input);
                $div->addChild(new \Cx\Core\Html\Model\Entity\TextElement($mb));
                
                return $div;
                break;
            case 'sourcecode':
                //set mode
                $mode = 'html';
                if(isset($options['mode'])) {
                    switch($options['mode']) {
                        case 'js':
                            $mode = 'javascript';
                        break;
                        case 'yml':
                        case 'yaml':
                            $mode = 'yaml';
                        break;
                    }
                }
                
                //define textarea
                $textarea = new \Cx\Core\Html\Model\Entity\HtmlElement('textarea');
                $textarea->setAttribute('name', $name);
                $textarea->setAttribute('id', $name);
                $textarea->setAttribute('style', 'display:none;');
                $textarea->addChild(new \Cx\Core\Html\Model\Entity\TextElement($value));
                
                //define pre
                $pre = new \Cx\Core\Html\Model\Entity\HtmlElement('pre');
                $pre->setAttribute('id','editor-'.$name);
                $pre->addChild(new \Cx\Core\Html\Model\Entity\TextElement(contrexx_raw2xhtml($value)));
                
                //set readonly if necessary
                $readonly = '';
                if (isset($options['readonly']) && $options['readonly']) {
                    $readonly = 'editor.setReadOnly(true);';
                    $textarea->setAttribute('disabled');
                }
                
                //create div and add all stuff
                $div = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
                $div->addChild($textarea);
                $div->addChild($pre);
                
                //register js
                $jsCode = <<<CODE
var editor;                        
\$J(function(){
if (\$J("#editor-$name").length) {
    editor = ace.edit("editor-$name");
    editor.getSession().setMode("ace/mode/$mode");
    editor.setShowPrintMargin(false);
    editor.focus();
    editor.gotoLine(1);
    $readonly
}

\$J('form').submit(function(){
    \$J('#$name').val(editor.getSession().getValue());
});

});
CODE;
                \JS::activate('ace');            
                \JS::registerCode($jsCode);
                
                return $div;
                break;
            case 'string':
            default:
                // convert NULL to empty string
                if (is_null($value)) {
                    $value = '';
                }
                // input field with type text
                $input = new \Cx\Core\Html\Model\Entity\DataElement($name, $value);
                $input->setAttribute('type', 'text');
                if (isset($options['readonly']) && $options['readonly']) {
                    $input->setAttribute('disabled');
                }
                return $input;
                break;
        }
    }
    
    public function getForm() {
        return $this->form;
    }
    
    public function isValid() {
        return $this->form->isValid();
    }
    
    public function getData() {
        return $this->form->getData();
    }
    
    public function render() {
        return $this->form->render();
    }
    
    public function __toString() {
        return $this->render();
    }
    
    protected static function getFormLabel($fieldOptions, $key) {
        global $_ARRAYLANG;
        
        if (isset($_ARRAYLANG[$fieldOptions[$key]])) {
            $fieldHeader = $_ARRAYLANG[$fieldOptions[$key]];
        } else {
            $fieldHeader = $fieldOptions[$key];
        }
        return $fieldHeader;
    }
}
