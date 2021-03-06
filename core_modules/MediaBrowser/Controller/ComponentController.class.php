<?php

/**
 * Class ComponentController
 *
 * @copyright   CONTREXX CMS - Comvation AG Thun
 * @author      Tobias Schmoker <tobias.schmoker@comvation.com>
 *              Robin Glauser <robin.glauser@comvation.com>
 * @package     contrexx
 * @subpackage  coremodule_mediabrowser
 * @version     1.0.0
 */

namespace Cx\Core_Modules\MediaBrowser\Controller;

use Cx\Core\ContentManager\Model\Entity\Page;
use Cx\Core\Core\Model\Entity\SystemComponentController;
use Cx\Core\Html\Sigma;
use Cx\Core_Modules\MediaBrowser\Model\Event\MediaBrowserEventListener;
use Cx\Core_Modules\MediaBrowser\Model\Entity\MediaBrowser;
use Cx\Core_Modules\Uploader\Controller\UploaderConfiguration;
use Cx\Lib\FileSystem\FileSystemException;

/**
 * Class ComponentController
 *
 * @copyright   CONTREXX CMS - Comvation AG Thun
 * @author      Tobias Schmoker <tobias.schmoker@comvation.com>
 *              Robin Glauser <robin.glauser@comvation.com>
 * @version     1.0.0
 */
class ComponentController extends
    SystemComponentController
{

    /**
     * List with initialised mediabrowser instances.
     * @var array
     */
    protected $mediaBrowserInstances = array();

    /**
     * {@inheritdoc }
     */
    public function getControllerClasses() {
        // Return an empty array here to let the component handler know that there
        // does not exist a backend, nor a frontend controller of this component.
        return array('Backend');
    }

    /**
     * Register a mediabrowser instance
     * @param MediaBrowser $mediaBrowser
     */
    public function addMediaBrowser(MediaBrowser $mediaBrowser) {
        $this->mediaBrowserInstances[] = $mediaBrowser;
    }

    /**
     * {@inheritdoc }
     */
    public function getControllersAccessableByJson() {
        return array(
            'JsonMediaBrowser',
        );
    }


    /**
     * {@inheritdoc }
     */
    public function preContentParse(Page $page) {
        $this->cx->getEvents()->addEventListener(
            'mediasource.load', new MediaBrowserEventListener($this->cx)
        );
    }

    /**
     * @param Sigma $template
     */
    public function preFinalize(Sigma $template) {
        if (count($this->mediaBrowserInstances) == 0) {
            return;
        }
        else {
            global $_ARRAYLANG;
            /**
             * @var $init \InitCMS
             */
            $init = \Env::get('init');
            $init->loadLanguageData('MediaBrowser');
            foreach ($_ARRAYLANG as $key => $value) {
                if (preg_match("/TXT_FILEBROWSER_[A-Za-z0-9]+/", $key)) {
                    \ContrexxJavascript::getInstance()->setVariable(
                        $key, $value, 'mediabrowser'
                    );
                }
            }

            $thumbnailsTemplate = new Sigma();
            $thumbnailsTemplate->loadTemplateFile(
                $this->cx->getCoreModuleFolderName()
                . '/MediaBrowser/View/Template/Thumbnails.html'
            );
            $thumbnailsTemplate->setVariable(
                'TXT_FILEBROWSER_THUMBNAIL_ORIGINAL_SIZE', sprintf(
                    $_ARRAYLANG['TXT_FILEBROWSER_THUMBNAIL_ORIGINAL_SIZE']
                )
            );
            foreach (
                UploaderConfiguration::getInstance()->getThumbnails() as
                $thumbnail
            ) {
                $thumbnailsTemplate->setVariable(
                    array(
                        'THUMBNAIL_NAME' => sprintf(
                            $_ARRAYLANG[
                            'TXT_FILEBROWSER_THUMBNAIL_' . strtoupper(
                                $thumbnail['name']
                            ) . '_SIZE'], $thumbnail['size']
                        ),
                        'THUMBNAIL_ID' => $thumbnail['id'],
                        'THUMBNAIL_SIZE' => $thumbnail['size']
                    )
                );
                $thumbnailsTemplate->parse('thumbnails');
            }

            \ContrexxJavascript::getInstance()->setVariable(
                'thumbnails_template', $thumbnailsTemplate->get(),
                'mediabrowser'
            );

            \JS::activate('mediabrowser');
            \JS::registerJS('core_modules/MediaBrowser/View/Script/mediabrowser.js');
        }
    }


}
