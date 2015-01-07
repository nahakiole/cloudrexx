<?php
/**
 * Created by PhpStorm.
 * User: robin
 * Date: 29.09.14
 * Time: 09:47
 */

namespace Cx\Core_Modules\MediaBrowser\Model;

use Cx\Model\Base\EntityBase;

class MediaBrowser extends EntityBase
{
    /**
     * @var Array
     */
    protected $options = array();
    protected $tagName = 'button';

    function __construct()
    {
        $this->getComponentController()->addMediaBrowser($this);

        $this->options = array(
            'data-cx-mb',
            'class' => "button"
        );
    }


    /**
     * @param $options
     */
    function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    function getOption($option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }
        return null;
    }

    function setCallback($callback)
    {
        $this->options['data-cx-Mb-Cb-Js-Modalclosed'] = $callback;
    }

    function getOptionsString()
    {
        $optionsString = "";
        foreach ($this->options as $key => $value) {
            if (is_int($key)) {
                $optionsString .= $value . ' ';
            } else {
                $optionsString .= $key . '="' . $value . '" ';
            }
        }
        return $optionsString;
    }

    function getXHtml($buttonName = "MediaBrowser")
    {
        return '<'.$this->tagName.' ' . $this->getOptionsString() . ' >' . $buttonName . '</'.$this->tagName.' >';
    }
}