<?php

class InstallerCx {

    /**
     * The webserver's DocumentRoot path.
     * Formerly known as ASCMS_PATH.
     * @var string
     * @access protected
     */
    protected $basePath = null;

    /**
     * The absolute path to the Code Base of the Contrexx installation.
     * Formerly known as ASCMS_DOCUMENT_ROOT.
     * @var string
     * @access protected
     */
    protected $codeBaseDocumentRootPath = null;

    /**
     * The absolute path to the data repository of the Contrexx installation.
     * Formerly known as ASCMS_INSTANCE_DOCUMENT_ROOT.
     * @var string
     * @access protected
     */
    protected $websiteDocumentRootPath = null;

    /**
     * The absolute path to the website's data repository.
     * Formerly known as ASCMS_INSTANCE_PATH.
     * @var string
     * @access protected
     */
    protected $websitePath = null;

    /**
     * The offset path from the website's data repository to the
     * location of the Contrexx installation if it is run in a subdirectory.
     * Formerly known as ASCMS_INSTANCE_OFFSET.
     * @var string
     * @access protected
     */
    protected $websiteOffsetPath = null;

    /**
     * System mode
     * @var string Mode as string
     * @access protected
     */
    protected $mode = 'minimal';

    /**
     * Initializes the InstallerCx class
     * This includes all class variables which are needed for the installer.
     * @param string $basePath webserver's DocumentRoot path - if it includes /installer it will be cutted out
     * @access public
     */
    public function __construct($basePath) {
        $this->basePath = str_replace('/installer', '', $basePath);
        $this->codeBaseDocumentRootPath = $this->basePath;
        $this->websiteDocumentRootPath = $this->basePath;
        $this->websitePath = $this->basePath;
        $this->websiteOffsetPath = '';
    }

    /**
     * Return the absolute path to the Code Base of the Contrexx installation.
     * Formerly known as ASCMS_DOCUMENT_ROOT.
     * @return string
     * @access public
     */
    public function getCodeBaseDocumentRootPath() {
        return $this->codeBaseDocumentRootPath;
    }

    /**
     * Return the absolute path to the data repository of the Contrexx installation.
     * Formerly known as ASCMS_INSTANCE_DOCUMENT_ROOT.
     * @return string
     * @access public
     */
    public function getWebsiteDocumentRootPath() {
        return $this->websiteDocumentRootPath;
    }

    /**
     * Return the offset path from the website's data repository to the
     * location of the Contrexx installation if it is run in a subdirectory.
     * Formerly known as ASCMS_INSTANCE_OFFSET.
     * @return string
     * @access public
     */
    public function getWebsiteOffsetPath()
    {
        return $this->websiteOffsetPath;
    }

    /**
     * Return the absolute path to the website's data repository.
     * Formerly known as ASCMS_INSTANCE_PATH.
     * @return string
     * @access public
     */
    public function getWebsitePath() {
        return $this->websitePath;
    }

    /**
     * Returns the mode this instance of Cx is in
     * @return string at the moment it always returns the standard value
     * @access public
     */
    public function getMode() {
        return $this->mode;
    }
}
