<?php

/**
 * Docsys RSS XML Feed
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Comvation Development Team <info@comvation.com>
 * @access      public
 * @version     1.0.0
 * @package     contrexx
 * @subpackage  module_jobs
 * @todo        Edit PHP DocBlocks!
 */

namespace Cx\Modules\Jobs\Controller;

/**
 * Docsys RSS XML Feed
 *
 * Produces the RSS XML Feedfile of the latest jobs entries
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Comvation Development Team <info@comvation.com>
 * @access      public
 * @version     1.0.0
 * @package     contrexx
 * @subpackage  module_jobs
 */
class RssFeed
{
    var $xmlType;
    var $filePath;
    var $fileName = array();
    var $limit;
    var $langId;

    var $channelTitle;
    var $channelLink;
    var $channelDescription;
    var $channelLanguage;
    var $channelCopyright;
    var $channelGenerator;
    var $channelWebMaster;
    var $itemLink;


    /**
    * Constructor
    *
    * @global    array        $_CONFIG
    * @global    string        $objDatabase
    */
    function __construct()
    {
        global $_CONFIG, $objInit, $objDatabase;

        $this->langId=$objInit->userFrontendLangId;

        $query = "SELECT lang FROM ".DBPREFIX."languages WHERE id='$this->langId'";
        $objResult = $objDatabase->Execute($query);

        $this->xmlType = "headlines";
        $this->filePath = \Env::get('cx')->getWebsiteFeedPath();
        $this->channelLink = ASCMS_PROTOCOL."://".$_SERVER['SERVER_NAME'];
        $this->channelCopyright = ASCMS_PROTOCOL."://".$_SERVER['SERVER_NAME'];
        $this->channelGenerator = $_CONFIG['coreCmsName'];
        $this->channelWebMaster = $_CONFIG['coreAdminEmail'];
        $this->channelLanguage  = $objResult->fields['lang'];
        $this->itemLink = ASCMS_PROTOCOL."://".$_SERVER['SERVER_NAME']."/index.php?section=Jobs&amp;cmd=details&amp;id=";
        $this->fileName[1] = 'jobs_headlines_'.$objResult->fields['lang'].'.xml';
        $this->fileName[2] = 'jobs_'.$objResult->fields['lang'].'.xml';

        $this->limit=20;
        if($this->limit<1 OR $this->limit>100){
            $this->limit=10;
        }
    }


    /**
    * checks the file and folder permissions
    *
    * @return   boolean
    */
    function checkPermissions()
    {
        if(is_writeable($this->filePath) AND is_dir($this->filePath)){
            return true;
        }
        else{  
            return false;
        }
    }

    /**
    * createXML: parse out the XML
    *
    * @global   array         $_LANGID
    * @global    string        $objDatabase
    */
    function createXML()
    {
        global $_LANGID, $objDatabase;

        if ($this->checkPermissions()){
            $xmlOutput = "";
            $xmlOutput .= "<?xml version=\"1.0\" encoding=\"".CONTREXX_CHARSET."\"?>\n";
            $xmlOutput .= "<rss version=\"2.0\">\n";
            $xmlOutput .= "<channel>\n";
            $xmlOutput .= "<title>".$this->channelTitle."</title>\n";
            $xmlOutput .= "<description>".$this->channelDescription."</description>\n";
            $xmlOutput .= "<link>".$this->channelLink."</link>\n";
            $xmlOutput .= "<copyright>".$this->channelCopyright."</copyright>\n";
            $xmlOutput .= "<webMaster>".$this->channelWebMaster."</webMaster>\n";
            $xmlOutput .= "<generator>".$this->channelGenerator."</generator>\n";
            $xmlOutput .= "<lastBuildDate>".date('r',time())."</lastBuildDate>\n";
            $xmlOutput .= "<language>".$this->channelLanguage."</language>\n";

//            $query = "SELECT n.id AS docId,
//                               n.date,
//                               n.title,
//                               n.text,
//                               n.workloc,
//                               u.firstname,
//                               u.lastname,
//                               n.lang,
//                               n.userid,
//                               u.id
//                        FROM ".DBPREFIX."module_jobs AS n,
//                             ".DBPREFIX."access_users AS u
//                        WHERE n.userid = u.id AND n.lang = ".$_LANGID."
//                        ORDER BY n.id DESC";
            
            $query = "SELECT n.id AS docId,
                               n.date,
                               n.title,
                               n.text,
                               n.workloc,
                               n.lang,
                               n.userid,
                               u.id
                        FROM ".DBPREFIX."module_jobs AS n,
                             ".DBPREFIX."access_users AS u
                        WHERE n.userid = u.id AND n.lang = ".$_LANGID."
                        ORDER BY n.id DESC";
            $objResult = $objDatabase->SelectLimit($query, $this->limit);

            while (!$objResult->EOF) {
                $xmlOutput .= "<item>\n";
                $xmlOutput .= "<title>".strip_tags(stripslashes($objResult->fields['title']))."</title>\n";

                if ($this->xmlType == 'fulltext'){
                    $xmlOutput .= "<description>".htmlspecialchars(stripslashes($objResult->fields['text']), ENT_QUOTES, CONTREXX_CHARSET)."</description>\n";
                    //$xmlOutput .= "<description>".$objResult->fields['text')."</description>";
                    $xmlOutput .= "<source>".htmlspecialchars(stripslashes($objResult->fields['workloc']), ENT_QUOTES, CONTREXX_CHARSET)."</source>\n";
                }

                $xmlOutput .= "<pubDate>".date('r',time())."</pubDate>\n";
                $xmlOutput .= "<link>".$this->itemLink.$objResult->fields['docId']."</link>\n";
                $xmlOutput .= "</item>\n";
                $objResult->MoveNext();
            }
            $xmlOutput .= "</channel>\n";
            $xmlOutput .= "</rss>";

            if ($this->xmlType == 'fulltext'){
                $fileHandle = fopen($this->filePath."/".$this->fileName[2],"w+");
            }else {
                $fileHandle = fopen($this->filePath."/".$this->fileName[1],"w+");
            }
            if($fileHandle){
                @fwrite($fileHandle,$xmlOutput);
                @fclose($fileHandle);
            }
        }
    }
}
?>