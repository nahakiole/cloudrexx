<?php

/**
 * SocialNetworks
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  lib_framework
 */

/**
 * Creates Buttons etc. to integrate Social Networks.
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Severin Räz <severin.raez@comvation.com>
 * @version     $Id:  Exp $
 * @package     contrexx
 * @subpackage  lib_framework
 */
class SocialNetworks
{
    /**
     * The URL we want to link to / create buttons for etc.
     * @access protected
     * @var string
     */
    protected $url;

    /**
     * Constructor.
     * @access public
     * @param String $url the url as one would pass it to SocialNetworks::setUrl.
     * @see SocialNetworks::setUrl
     */
    public function __construct($url = '')
    {
        $this->setUrl($url);
    }

    /**
     * Set the URL.
     * @access public
     * @see SocialNetworks::$url
     * @param String $url the URL
     */
    public function setURL($url)
    {
        //remove leading http:// if present
        if(substr($url,0,7) == "http://")
        {
            $url = substr($url,7);
        }

        $this->url = $url;
    }

    /**
     * Get a Facebook "Like"-Button.
     * @access public
     * @see SocialNetworks::setUrl
     * @return String XHTML-Code representing the Button (an Iframe)
     */
    public function getFacebookLikeButton()
    {
        $xhtml = '<iframe
                      src="http://www.facebook.com/plugins/like.php?href='.$this->url.'&amp;layout=standard&amp;show_faces=true&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=80"
                      scrolling="no" frameborder="0"
                      style="border:none; overflow:hidden; width:450px; height:80px;" allowTransparency="true">
                   </iframe>';
        return $xhtml;
    }

    /**
     * Get a Facebook "Share"-Button. 
     * This is not the way to do it, facebook suggests using the "Like"-Button (you can write a comment there too).
     * @param String $type 'icon', 'button' or 'icon_link' (defaults to 'icon')
     * @access public
     * @see SocialNetworks::setUrl
     * @return String XHTML-Code representing the Button (an <a>-Tag)
     */
    public function getFacebookShareButton($type='icon')
    {
	$xhtml = '<a name="fb_share"
                    share_url="'.$this->url.'"
                    type="'.$type.'"
                    >
                  </a> 
                  <script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" 
                    type="text/javascript">
                  </script>';
	return $xhtml;
    }

    /**
     * Gets an Array of all Placeholders with their respective Content for the URL set.
     * This is intended to use with PEAR's SigmaTemplate::setVariable.
     * @access public
     * @see SocialNetworks::setUrl
     * @return Array {<placeholder_name> => <placeholder_content>}
     */
    public function getPlaceholderContentArray()
    {
        $arr = array();
        $arr['SN_FACEBOOK_LIKE'] = $this->getFacebookLikeButton();
        $arr['SN_FACEBOOK_SHARE'] = $this->getFacebookShareButton();

        return $arr;
    }
}
