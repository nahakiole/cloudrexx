<?php
/**
 * @copyright   Comvation AG 
 * @author Robin Glauser <robin.glauser@comvation.com>
 * @package     contrexx
 */

namespace Cx\Modules\MediaDir\Model\Event;


use Cx\Core\Core\Controller\Cx;
use Cx\Core\Event\Model\Entity\EventListener;
use Cx\Core_Modules\MediaBrowser\Controller\MediaBrowserConfiguration;
use Cx\Core\Model\Model\Entity\MediaType;

class MediaDirEventListener implements EventListener {

    /**
     * @var Cx
     */
    protected $cx;

    function __construct(Cx $cx)
    {
        $this->cx = $cx;
    }

    public function onEvent($eventName, array $eventArgs)
    {
        $this->$eventName(current($eventArgs));
    }

    public function LoadMediaTypes(MediaBrowserConfiguration $mediaBrowserConfiguration)
    {
        global $_ARRAYLANG;
        \Env::get('init')->loadLanguageData('MediaDir');
        $mediaType = new MediaType();
        $mediaType->setName('mediadir');
        $mediaType->setHumanName($_ARRAYLANG['TXT_FILEBROWSER_MEDIADIR']);
        $mediaType->setDirectory(array(
            $this->cx->getWebsiteImagesMediaDirPath(),
            $this->cx->getWebsiteImagesMediaDirWebPath(),
        ));
        $mediaType->setAccessIds(array(153));
        $mediaBrowserConfiguration->addMediaType($mediaType);
    }


}