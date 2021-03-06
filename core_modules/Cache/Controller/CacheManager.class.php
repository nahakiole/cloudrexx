<?php
/**
 * Cache
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Comvation Development Team <info@comvation.com>
 * @version     1.0.1
 * @package     contrexx
 * @subpackage  coremodule_cache
 * @todo        Edit PHP DocBlocks!
 */
 namespace Cx\Core_Modules\Cache\Controller;
/**
 * Cache
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Comvation Development Team <info@comvation.com>
 * @version     1.0.1
 * @package     contrexx
 * @subpackage  coremodule_cache
 */
class CacheManager extends \Cx\Core_Modules\Cache\Controller\CacheLib
{
    var $objTpl;
    var $arrSettings = array();

    private $objSettings;

    /**
     * Constructor
     *
     */
    function CacheManager()
    {
        $this->__construct();
    }

    /**
     * PHP5 constructor
     *
     * @global     object    $objTemplate
     * @global    array    $_ARRAYLANG
     */
    function __construct()
    {
        global $objTemplate, $_ARRAYLANG, $objInit;
        
        $this->objTpl = new \Cx\Core\Html\Sigma(ASCMS_CORE_MODULE_PATH . '/Cache/View/Template/Backend');
        $langData = $objInit->loadLanguageData('Cache');
        \Cx\Core\Csrf\Controller\Csrf::add_placeholder($this->objTpl);
        $this->objTpl->setErrorHandling(PEAR_ERROR_DIE);

        $this->arrSettings = $this->getSettings();
        $this->objSettings = new \Cx\Core\Config\Controller\Config();

        if (is_dir(ASCMS_CACHE_PATH)) {
            if (is_writable(ASCMS_CACHE_PATH)) {
                $this->strCachePath = ASCMS_CACHE_PATH . '/';
            } else {
                $objTemplate->SetVariable('CONTENT_STATUS_MESSAGE', $_ARRAYLANG['TXT_CACHE_ERR_NOTWRITABLE'] . ASCMS_CACHE_PATH);
            }
        } else {
            $objTemplate->SetVariable('CONTENT_STATUS_MESSAGE', $_ARRAYLANG['TXT_CACHE_ERR_NOTEXIST'] . ASCMS_CACHE_PATH);
        }
        
        $this->initOPCaching();
        $this->initUserCaching();
        $this->getActivatedCacheEngines();
    }

    /**
     * Creates an array containing all important cache-settings
     *
     * @global     object    $objDatabase
     * @return    array    $arrSettings
     */
    function getSettings() {
        $arrSettings = array();
        \Cx\Core\Setting\Controller\Setting::init('Config', NULL,'Yaml');
        $ymlArray = \Cx\Core\Setting\Controller\Setting::getArray('Config', null);

        foreach ($ymlArray as $key => $ymlValue){
            $arrSettings[$key] = $ymlValue['value'];
        }

        return $arrSettings;
    }

    /**
     * Show settings of the module
     *
     * @global     object    $objTemplate
     * @global     array    $_ARRAYLANG
     */
    function showSettings()
    {
        global $objTemplate, $_ARRAYLANG;

        $this->objTpl->loadTemplateFile('settings.html');
        $this->objTpl->setVariable(array(
            'TXT_CACHE_GENERAL' => $_ARRAYLANG['TXT_SETTINGS_MENU_CACHE'],
            'TXT_CACHE_STATS' => $_ARRAYLANG['TXT_CACHE_STATS'],
            'TXT_CACHE_CONTREXX_CACHING' => $_ARRAYLANG['TXT_CACHE_CONTREXX_CACHING'],
            'TXT_CACHE_USERCACHE' => $_ARRAYLANG['TXT_CACHE_USERCACHE'],
            'TXT_CACHE_OPCACHE' => $_ARRAYLANG['TXT_CACHE_OPCACHE'],
            'TXT_CACHE_PROXYCACHE' => $_ARRAYLANG['TXT_CACHE_PROXYCACHE'],
            'TXT_CACHE_EMPTY' => $_ARRAYLANG['TXT_CACHE_EMPTY'],
            'TXT_CACHE_STATS' => $_ARRAYLANG['TXT_CACHE_STATS'],
            'TXT_CACHE_APC' => $_ARRAYLANG['TXT_CACHE_APC'],
            'TXT_CACHE_ZEND_OPCACHE' => $_ARRAYLANG['TXT_CACHE_ZEND_OPCACHE'],
            'TXT_CACHE_XCACHE' => $_ARRAYLANG['TXT_CACHE_XCACHE'],
            'TXT_CACHE_MEMCACHE' => $_ARRAYLANG['TXT_CACHE_MEMCACHE'],
            'TXT_CACHE_MEMCACHED' => $_ARRAYLANG['TXT_CACHE_MEMCACHED'],
            'TXT_CACHE_FILESYSTEM' => $_ARRAYLANG['TXT_CACHE_FILESYSTEM'],
            'TXT_CACHE_APC_ACTIVE_INFO' => $_ARRAYLANG['TXT_CACHE_APC_ACTIVE_INFO'],
            'TXT_CACHE_APC_CONFIG_INFO' => $_ARRAYLANG['TXT_CACHE_APC_CONFIG_INFO'],
            'TXT_CACHE_ZEND_OPCACHE_ACTIVE_INFO' => $_ARRAYLANG['TXT_CACHE_ZEND_OPCACHE_ACTIVE_INFO'],
            'TXT_CACHE_ZEND_OPCACHE_CONFIG_INFO' => $_ARRAYLANG['TXT_CACHE_ZEND_OPCACHE_CONFIG_INFO'],
            'TXT_CACHE_XCACHE_ACTIVE_INFO' => $_ARRAYLANG['TXT_CACHE_XCACHE_ACTIVE_INFO'],
            'TXT_CACHE_XCACHE_CONFIG_INFO' => $_ARRAYLANG['TXT_CACHE_XCACHE_CONFIG_INFO'],
            'TXT_CACHE_MEMCACHE_ACTIVE_INFO' => $_ARRAYLANG['TXT_CACHE_MEMCACHE_ACTIVE_INFO'],
            'TXT_CACHE_MEMCACHE_CONFIG_INFO' => $_ARRAYLANG['TXT_CACHE_MEMCACHE_CONFIG_INFO'],
            'TXT_CACHE_MEMCACHED_ACTIVE_INFO' => $_ARRAYLANG['TXT_CACHE_MEMCACHED_ACTIVE_INFO'],
            'TXT_CACHE_MEMCACHED_CONFIG_INFO' => $_ARRAYLANG['TXT_CACHE_MEMCACHED_CONFIG_INFO'],
            'TXT_CACHE_ENGINE' => $_ARRAYLANG['TXT_CACHE_ENGINE'],
            'TXT_CACHE_INSTALLATION_STATE' => $_ARRAYLANG['TXT_CACHE_INSTALLATION_STATE'],
            'TXT_CACHE_ACTIVE_STATE' => $_ARRAYLANG['TXT_CACHE_ACTIVE_STATE'],
            'TXT_CACHE_CONFIGURATION_STATE' => $_ARRAYLANG['TXT_CACHE_CONFIGURATION_STATE'],
            'TXT_CACHING' => $_ARRAYLANG['TXT_CACHING'],
            'TXT_SETTINGS_SAVE' => $_ARRAYLANG['TXT_SAVE'],
            'TXT_SETTINGS_ON' => $_ARRAYLANG['TXT_ACTIVATED'],
            'TXT_SETTINGS_OFF' => $_ARRAYLANG['TXT_DEACTIVATED'],
            'TXT_SETTINGS_STATUS' => $_ARRAYLANG['TXT_CACHE_SETTINGS_STATUS'],
            'TXT_SETTINGS_STATUS_HELP' => $_ARRAYLANG['TXT_CACHE_SETTINGS_STATUS_HELP'],
            'TXT_SETTINGS_EXPIRATION' => $_ARRAYLANG['TXT_CACHE_SETTINGS_EXPIRATION'],
            'TXT_SETTINGS_EXPIRATION_HELP' => $_ARRAYLANG['TXT_CACHE_SETTINGS_EXPIRATION_HELP'],
            'TXT_EMPTY_BUTTON' => $_ARRAYLANG['TXT_CACHE_EMPTY'],
            'TXT_EMPTY_DESC' => $_ARRAYLANG['TXT_CACHE_EMPTY_DESC'],
            'TXT_EMPTY_DESC_APC' => $_ARRAYLANG['TXT_CACHE_EMPTY_DESC_FILES_AND_ENRIES'],
            'TXT_EMPTY_DESC_ZEND_OP' => $_ARRAYLANG['TXT_CACHE_EMPTY_DESC_FILES'],
            'TXT_EMPTY_DESC_MEMCACHE' => $_ARRAYLANG['TXT_CACHE_EMPTY_DESC_MEMCACHE'],
            'TXT_EMPTY_DESC_XCACHE' => $_ARRAYLANG['TXT_CACHE_EMPTY_DESC_FILES_AND_ENRIES'],
            'TXT_STATS_FILES' => $_ARRAYLANG['TXT_CACHE_STATS_FILES'],
            'TXT_STATS_FOLDERSIZE' => $_ARRAYLANG['TXT_CACHE_STATS_FOLDERSIZE'],
            'TXT_STATS_CHACHE_SITE_COUNT' => $_ARRAYLANG['TXT_STATS_CHACHE_SITE_COUNT'],
            'TXT_STATS_CHACHE_ENTRIES_COUNT' => $_ARRAYLANG['TXT_STATS_CHACHE_ENTRIES_COUNT'],
            'TXT_STATS_CACHE_SIZE' => $_ARRAYLANG['TXT_STATS_CACHE_SIZE'],
            'TXT_DEACTIVATED' => $_ARRAYLANG['TXT_DEACTIVATED'],
            'TXT_DISPLAY_CONFIGURATION' => $_ARRAYLANG['TXT_DISPLAY_CONFIGURATION'],
            'TXT_HIDE_CONFIGURATION' => $_ARRAYLANG['TXT_HIDE_CONFIGURATION'],
            'TXT_CACHE_VARNISH'    => $_ARRAYLANG['TXT_CACHE_VARNISH'],
            'TXT_CACHE_PROXY_IP'    => $_ARRAYLANG['TXT_CACHE_PROXY_IP'],
            'TXT_CACHE_PROXY_PORT'  => $_ARRAYLANG['TXT_CACHE_PROXY_PORT']
        ));

        if ($this->objSettings->isWritable()) {
            $this->objTpl->parse('cache_submit_button');
        } else {
            $this->objTpl->hideBlock('cache_submit_button');
            $objTemplate->SetVariable('CONTENT_STATUS_MESSAGE', implode("<br />\n", $this->objSettings->strErrMessage));
        }
        
        // parse op cache engines
        $this->parseOPCacheEngines();
        // parse user cache engines
        $this->parseUserCacheEngines();

        $this->parseMemcacheSettings();
        $this->parseMemcachedSettings();
        $this->parseVarnishSettings();

        $intFoldersizePages = 0;
        $intFoldersizeEntries = 0;
        $intFilesPages = 0;
        $intFilesEntries = 0;

        $handleFolder = opendir($this->strCachePath);
        if ($handleFolder) {
            while ($strFile = readdir($handleFolder)) {
                if ($strFile != '.' && $strFile != '..') {
                    if(is_dir($this->strCachePath.'/'.$strFile)){
                        $intFoldersizeEntries += filesize($this->strCachePath . $strFile);
                        ++$intFilesEntries;
                    }elseif($strFile !== '.htaccess'){
                        $intFoldersizePages += filesize($this->strCachePath . $strFile);
                        ++$intFilesPages;
                    }
                }
            }
            $intFoldersizeEntries = filesize($this->strCachePath) - $intFoldersizePages - filesize($this->strCachePath . '.htaccess');
            closedir($handleFolder);
        }
        
        if (   $this->isInstalled(self::CACHE_ENGINE_APC)
            && $this->isConfigured(self::CACHE_ENGINE_APC)
            && (
                $this->opCacheEngine == self::CACHE_ENGINE_APC
                || $this->userCacheEngine == self::CACHE_ENGINE_APC
            )
        ){
            $this->objTpl->touchBlock('apcCachingStats');
            $apcSmaInfo = \apc_sma_info();
            $apcCacheInfo = \apc_cache_info();
        }else{
            $this->objTpl->hideBlock('apcCachingStats');
        }
        if (   $this->isInstalled(self::CACHE_ENGINE_ZEND_OPCACHE)
            && $this->isConfigured(self::CACHE_ENGINE_ZEND_OPCACHE)
            && $this->opCacheEngine == self::CACHE_ENGINE_ZEND_OPCACHE
            && $this->getOpCacheActive()
        ){
            $this->objTpl->touchBlock('zendOpCachingStats');
            $opCacheConfig = \opcache_get_configuration();
            $opCacheStatus = \opcache_get_status();
        }else{
            $this->objTpl->hideBlock('zendOpCachingStats');
        }
        if (   $this->isInstalled(self::CACHE_ENGINE_MEMCACHE)
            && $this->isConfigured(self::CACHE_ENGINE_MEMCACHE)
            && $this->userCacheEngine == self::CACHE_ENGINE_MEMCACHE
            && $this->getUserCacheActive()
        ){
            $this->objTpl->touchBlock('memcacheCachingStats');
            $memcacheStats = $this->memcache->getStats();
        }else{
            $this->objTpl->hideBlock('memcacheCachingStats');
        }
        if (   $this->isInstalled(self::CACHE_ENGINE_XCACHE)
            && $this->isConfigured(self::CACHE_ENGINE_XCACHE)
            && (
                   $this->opCacheEngine == self::CACHE_ENGINE_XCACHE
                || $this->userCacheEngine == self::CACHE_ENGINE_XCACHE
            )
        ){
            $this->objTpl->touchBlock('xCacheCachingStats');
        }else{
            $this->objTpl->hideBlock('xCacheCachingStats');
        }
        if ($this->userCacheEngine == self::CACHE_ENGINE_FILESYSTEM && $this->getUserCacheActive()) {
            $this->objTpl->touchBlock('FileSystemCachingStats');
        } else {
            $this->objTpl->hideBlock('FileSystemCachingStats');
        }
        $apcSizeCount = isset($apcCacheInfo['nhits']) ? $apcCacheInfo['nhits'] : 0;
        $apcEntriesCount = 0;
        if(isset($apcCacheInfo)){
            foreach($apcCacheInfo['cache_list'] as $entity){
                if(false !== strpos($entity['key'], $this->getCachePrefix())){
                    $apcEntriesCount++;
                }
            }
        }
        $apcMaxSizeKb = isset($apcSmaInfo['num_seg']) && isset($apcSmaInfo['seg_size']) ? $apcSmaInfo['num_seg']*$apcSmaInfo['seg_size'] / 1024 : 0;
        $apcSizeKb = isset($apcCacheInfo['mem_size']) ? $apcCacheInfo['mem_size'] / 1024 : 0;
        
        $opcacheSizeCount = !isset($opCacheStatus) || $opCacheStatus == false ? 0 : $opCacheStatus['opcache_statistics']['num_cached_scripts'];
        $opcacheSizeKb = (!isset($opCacheStatus) || $opCacheStatus == false ? 0 : $opCacheStatus['memory_usage']['used_memory']) / (1024 * 1024);
        $opcacheMaxSizeKb = isset($opCacheConfig['directives']['opcache.memory_consumption']) ? $opCacheConfig['directives']['opcache.memory_consumption'] / (1024 * 1024) : 0;
        
        $memcacheEntriesCount = isset($memcacheStats['curr_items']) ? $memcacheStats['curr_items'] : 0;
        $memcacheSizeMb = isset($memcacheStats['bytes']) ? $memcacheStats['bytes'] / (1024 *1024) : 0;
        $memcacheMaxSizeMb = isset($memcacheStats['limit_maxbytes']) ? $memcacheStats['limit_maxbytes'] / (1024 *1024) : 0;
        
        $this->objTpl->setVariable(array(
            'SETTINGS_STATUS_ON' => ($this->arrSettings['cacheEnabled'] == 'on') ? 'checked' : '',
            'SETTINGS_STATUS_OFF' => ($this->arrSettings['cacheEnabled'] == 'off') ? 'checked' : '',
            'SETTINGS_OP_CACHE_STATUS_ON'   => ($this->arrSettings['cacheOpStatus'] == 'on') ? 'checked' : '',
            'SETTINGS_OP_CACHE_STATUS_OFF'  => ($this->arrSettings['cacheOpStatus'] == 'off') ? 'checked' : '',
            'SETTINGS_DB_CACHE_STATUS_ON'   => ($this->arrSettings['cacheDbStatus'] == 'on') ? 'checked' : '',
            'SETTINGS_DB_CACHE_STATUS_OFF'  => ($this->arrSettings['cacheDbStatus'] == 'off') ? 'checked' : '',
            'SETTINGS_VARNISH_CACHE_STATUS_ON'  => ($this->arrSettings['cacheVarnishStatus'] == 'on') ? 'checked' : '',
            'SETTINGS_VARNISH_CACHE_STATUS_OFF' => ($this->arrSettings['cacheVarnishStatus'] == 'off') ? 'checked' : '',            
            'SETTINGS_EXPIRATION' => intval($this->arrSettings['cacheExpiration']),
            'STATS_CONTREXX_FILESYSTEM_CHACHE_PAGES_COUNT' => $intFilesPages,
            'STATS_FOLDERSIZE_PAGES'                => number_format($intFoldersizePages / 1024, 2, '.', '\''),
            'STATS_CONTREXX_FILESYSTEM_CHACHE_ENTRIES_COUNT' => $intFilesEntries,
            'STATS_FOLDERSIZE_ENTRIES'              => number_format($intFoldersizeEntries / 1024, 2, '.', '\''),
            'STATS_APC_CHACHE_SITE_COUNT'           => $apcSizeCount,
            'STATS_APC_CHACHE_ENTRIES_COUNT'        => $apcEntriesCount,
            'STATS_APC_MAX_SIZE'                    => number_format($apcMaxSizeKb, 2, '.', '\''),
            'STATS_APC_SIZE'                        => number_format($apcSizeKb, 2, '.', '\''),
            'STATS_OPCACHE_CHACHE_SITE_COUNT'       => $opcacheSizeCount,
            'STATS_OPCACHE_SIZE'                    => number_format($opcacheSizeKb, 2, '.', '\''),
            'STATS_OPCACHE_MAX_SIZE'                => number_format($opcacheMaxSizeKb, 2, '.', '\''),
            'STATS_MEMCACHE_CHACHE_ENTRIES_COUNT'   => $memcacheEntriesCount,
            'STATS_MEMCACHE_SIZE'                   => number_format($memcacheSizeMb, 2, '.', '\''),
            'STATS_MEMCACHE_MAX_SIZE'               => number_format($memcacheMaxSizeMb, 2, '.', '\''),
        ));

        $objTemplate->setVariable(array(
            'CONTENT_TITLE' => $_ARRAYLANG['TXT_SETTINGS_MENU_CACHE'],
            'ADMIN_CONTENT' => $this->objTpl->get()
        ));
    }

    /**
     * Update settings and write them to the database
     *
     * @global     object    $objDatabase
     * @global     object    $objTemplate
     * @global     array    $_ARRAYLANG
     */
    function updateSettings()
    {
        global $objDatabase, $objTemplate, $_ARRAYLANG, $_CONFIG;

        if (!isset($_POST['frmSettings_Submit'])) {
            return;
        }

        \Cx\Core\Setting\Controller\Setting::init('Config', 'cache','Yaml');
        \Cx\Core\Setting\Controller\Setting::set('cacheEnabled', $_POST['cachingStatus']);
        \Cx\Core\Setting\Controller\Setting::set('cacheExpiration', intval($_POST['cachingExpiration']));
        \Cx\Core\Setting\Controller\Setting::set('cacheUserCache', contrexx_input2db($_POST['usercache']));
        \Cx\Core\Setting\Controller\Setting::set('cacheOPCache', contrexx_input2db($_POST['opcache']));
        \Cx\Core\Setting\Controller\Setting::set('cacheOpStatus', contrexx_input2db($_POST['cacheOpStatus']));
        \Cx\Core\Setting\Controller\Setting::set('cacheOpStatus', contrexx_input2db($_POST['cacheOpStatus']));
        \Cx\Core\Setting\Controller\Setting::set('cacheDbStatus', contrexx_input2db($_POST['cacheDbStatus']));
        \Cx\Core\Setting\Controller\Setting::set('cacheVarnishStatus', contrexx_input2db($_POST['cacheVarnishStatus']));

        if(!empty($_POST['memcacheSettingIp']) || !empty($_POST['memcacheSettingPort'])){
            $settings = json_encode(
                array(
                    'ip'   => !empty($_POST['memcacheSettingIp']) ? contrexx_input2raw($_POST['memcacheSettingIp']) : '127.0.0.1',
                    'port' => !empty($_POST['memcacheSettingPort']) ? intval($_POST['memcacheSettingPort']) : '11211',
                )
            );
            \Cx\Core\Setting\Controller\Setting::set('cacheUserCacheMemcacheConfig', $settings);
        }
        
        if(!empty($_POST['varnishCachingIp']) || !empty($_POST['varnishCachingPort'])){
            $settings = json_encode(
                array(
                    'ip'   => !empty($_POST['varnishCachingIp']) ? contrexx_input2raw($_POST['varnishCachingIp']) : '127.0.0.1',
                    'port' => !empty($_POST['varnishCachingPort']) ? intval($_POST['varnishCachingPort']) : '8080',
                )
            );
            \Cx\Core\Setting\Controller\Setting::set('cacheProxyCacheVarnishConfig', $settings);
        }
        
        \Cx\Core\Setting\Controller\Setting::updateAll();
        $this->arrSettings = $this->getSettings();
        $this->initUserCaching(); // reinit user caches (especially memcache)
        $this->initOPCaching(); // reinit opcaches
        $this->getActivatedCacheEngines();
        $this->clearCache($this->getOpCacheEngine());

        if (!count($this->objSettings->strErrMessage)) {
            $objTemplate->SetVariable('CONTENT_OK_MESSAGE', $_ARRAYLANG['TXT_SETTINGS_UPDATED']);
        } else {
            $objTemplate->SetVariable('CONTENT_STATUS_MESSAGE', implode("<br />\n", $this->objSettings->strErrMessage));
        }
    }
    
    private function parseOPCacheEngines() {
        $cachingEngines = array(
            self::CACHE_ENGINE_APC => array(),
            self::CACHE_ENGINE_ZEND_OPCACHE => array(),
            self::CACHE_ENGINE_XCACHE => array(),
        );
        $this->objTpl->setVariable('CHECKED_OPCACHE_' . strtoupper($this->getOpCacheEngine()), 'checked="checked"');
        if ($this->isInstalled(self::CACHE_ENGINE_APC)) {
            $cachingEngines[self::CACHE_ENGINE_APC]['installed'] = true;
        }
        if ($this->isActive(self::CACHE_ENGINE_APC)) {
            $cachingEngines[self::CACHE_ENGINE_APC]['active'] = true;
        }
        if ($this->isConfigured(self::CACHE_ENGINE_APC)) {
            $cachingEngines[self::CACHE_ENGINE_APC]['configured'] = true;
        }
        
        if ($this->isInstalled(self::CACHE_ENGINE_ZEND_OPCACHE)) {
            $cachingEngines[self::CACHE_ENGINE_ZEND_OPCACHE]['installed'] = true;
        }
        if ($this->isActive(self::CACHE_ENGINE_ZEND_OPCACHE)) {
            $cachingEngines[self::CACHE_ENGINE_ZEND_OPCACHE]['active'] = true;
        }
        if ($this->isConfigured(self::CACHE_ENGINE_ZEND_OPCACHE)) {
            $cachingEngines[self::CACHE_ENGINE_ZEND_OPCACHE]['configured'] = true;
        }
        
        if ($this->isInstalled(self::CACHE_ENGINE_XCACHE)) {
            $cachingEngines[self::CACHE_ENGINE_XCACHE]['installed'] = true;
        }
        if ($this->isActive(self::CACHE_ENGINE_XCACHE)) {
            $cachingEngines[self::CACHE_ENGINE_XCACHE]['active'] = true;
        }
        if ($this->isConfigured(self::CACHE_ENGINE_XCACHE)) {
            $cachingEngines[self::CACHE_ENGINE_XCACHE]['configured'] = true;
        }
        
        foreach ($cachingEngines as $engine => $data) {
            $installationIcon = $activeIcon = $configurationIcon = 'led_red.gif';
            if (isset($data['installed']) && isset($data['active']) && isset($data['configured'])) {
                if ($this->objTpl->blockExists('cache_opcache_' . $engine)) {
                    $this->objTpl->touchBlock('cache_opcache_' . $engine);
                }
            }
            if (isset($data['installed'])) {
                $installationIcon = 'led_green.gif';
            }
            if (isset($data['active'])) {
                $activeIcon = 'led_green.gif';
            }
            if (isset($data['configured'])) {
                $configurationIcon = 'led_green.gif';
            }
            $engine = strtoupper($engine);
            $this->objTpl->setVariable($engine . '_OPCACHE_INSTALLATION_ICON', $installationIcon);
            $this->objTpl->setVariable($engine . '_OPCACHE_ACTIVE_ICON', $activeIcon);
            $this->objTpl->setVariable($engine . '_OPCACHE_CONFIGURATION_ICON', $configurationIcon);
        }
    }
    
    private function parseUserCacheEngines() {
        $cachingEngines = array(
            self::CACHE_ENGINE_APC => array(),
            self::CACHE_ENGINE_MEMCACHE => array(),
            self::CACHE_ENGINE_MEMCACHED => array(),
            self::CACHE_ENGINE_XCACHE => array(),
            self::CACHE_ENGINE_FILESYSTEM => array(),
        );
        $this->objTpl->setVariable('CHECKED_USERCACHE_' . strtoupper($this->getUserCacheEngine()), 'checked="checked"');
        if ($this->isInstalled(self::CACHE_ENGINE_APC, true)) {
            $cachingEngines[self::CACHE_ENGINE_APC]['installed'] = true;
        }
        if ($this->isActive(self::CACHE_ENGINE_APC)) {
            $cachingEngines[self::CACHE_ENGINE_APC]['active'] = true;
        }
        if ($this->isConfigured(self::CACHE_ENGINE_APC, true)) {
            $cachingEngines[self::CACHE_ENGINE_APC]['configured'] = true;
        }
        
        if ($this->isInstalled(self::CACHE_ENGINE_MEMCACHE)) {
            $cachingEngines[self::CACHE_ENGINE_MEMCACHE]['installed'] = true;
        }
        if ($this->isActive(self::CACHE_ENGINE_MEMCACHE)) {
            $cachingEngines[self::CACHE_ENGINE_MEMCACHE]['active'] = true;
        }
        if ($this->isConfigured(self::CACHE_ENGINE_MEMCACHE)) {
            $cachingEngines[self::CACHE_ENGINE_MEMCACHE]['configured'] = true;
        }

        if ($this->isInstalled(self::CACHE_ENGINE_MEMCACHED)) {
            $cachingEngines[self::CACHE_ENGINE_MEMCACHED]['installed'] = true;
        }
        if ($this->isActive(self::CACHE_ENGINE_MEMCACHED)) {
            $cachingEngines[self::CACHE_ENGINE_MEMCACHED]['active'] = true;
        }
        if ($this->isConfigured(self::CACHE_ENGINE_MEMCACHED)) {
            $cachingEngines[self::CACHE_ENGINE_MEMCACHED]['configured'] = true;
        }
        
        if ($this->isInstalled(self::CACHE_ENGINE_XCACHE)) {
            $cachingEngines[self::CACHE_ENGINE_XCACHE]['installed'] = true;
        }
        if ($this->isActive(self::CACHE_ENGINE_XCACHE)) {
            $cachingEngines[self::CACHE_ENGINE_XCACHE]['active'] = true;
        }
        if ($this->isConfigured(self::CACHE_ENGINE_XCACHE, true)) {
            $cachingEngines[self::CACHE_ENGINE_XCACHE]['configured'] = true;
        }
        
        if ($this->isConfigured(self::CACHE_ENGINE_FILESYSTEM)) {
            $cachingEngines[self::CACHE_ENGINE_FILESYSTEM] = array(
                'installed' => true,
                'active' => true,
                'configured' => true
            );
        }
        foreach ($cachingEngines as $engine => $data) {
            $installationIcon = $activeIcon = $configurationIcon = 'led_red.gif';
            if (isset($data['installed']) && isset($data['active']) && isset($data['configured'])) {
                if ($this->objTpl->blockExists('cache_usercache_' . $engine)) {
                    $this->objTpl->touchBlock('cache_usercache_' . $engine);
                }
            }
            if (isset($data['installed'])) {
                $installationIcon = 'led_green.gif';
            }
            if (isset($data['active'])) {
                $activeIcon = 'led_green.gif';
            }
            if (isset($data['configured'])) {
                $configurationIcon = 'led_green.gif';
            }
            $engine = strtoupper($engine);
            $this->objTpl->setVariable($engine . '_USERCACHE_INSTALLATION_ICON', $installationIcon);
            $this->objTpl->setVariable($engine . '_USERCACHE_ACTIVE_ICON', $activeIcon);
            $this->objTpl->setVariable($engine . '_USERCACHE_CONFIGURATION_ICON', $configurationIcon);
        }
    }
    
    protected function parseMemcacheSettings() {
        $configuration = $this->getMemcacheConfiguration();
        $this->objTpl->setVariable('MEMCACHE_USERCACHE_CONFIG_IP', contrexx_raw2xhtml($configuration['ip']));
        $this->objTpl->setVariable('MEMCACHE_USERCACHE_CONFIG_PORT', contrexx_raw2xhtml($configuration['port']));
    }
    
    protected function parseMemcachedSettings() {
        $configuration = $this->getMemcachedConfiguration();
        $this->objTpl->setVariable('MEMCACHED_USERCACHE_CONFIG_IP', contrexx_raw2xhtml($configuration['ip']));
        $this->objTpl->setVariable('MEMCACHED_USERCACHE_CONFIG_PORT', contrexx_raw2xhtml($configuration['port']));
    }
    
    protected function parseVarnishSettings(){
        $configuration = $this->getVarnishConfiguration();
        $this->objTpl->setVariable('VARNISH_PROXYCACHE_CONFIG_IP', contrexx_raw2xhtml($configuration['ip']));
        $this->objTpl->setVariable('VARNISH_PROXYCACHE_CONFIG_PORT', contrexx_raw2xhtml($configuration['port']));
    }

    /**
     * Delete all files in cache-folder
     *
     * @global     object    $objTemplate
     * @global     array    $_ARRAYLANG
     */
    function deleteAllFiles($cacheEngine = null)
    {
        global $_ARRAYLANG, $objTemplate;

        $this->_deleteAllFiles($cacheEngine);

        $objTemplate->SetVariable('CONTENT_OK_MESSAGE', $_ARRAYLANG['TXT_CACHE_FOLDER_EMPTY']);
    }
    
    /**
     * Calls the related Clear Function from Lib and sets an OK-Message
     * @global array $_ARRAYLANG
     * @global object $objTemplate
     * @param string $cacheEngine
     */
    public function forceClearCache($cacheEngine = null){
        
        global $_ARRAYLANG, $objTemplate;
        
        switch ($cacheEngine) {
            case 'cxEntries':
            case 'cxPages':
                $this->deleteAllFiles($cacheEngine);
                break;
            case self::CACHE_ENGINE_APC:
            case 'apc':
                $this->clearCache(self::CACHE_ENGINE_APC);
                break;
            case self::CACHE_ENGINE_ZEND_OPCACHE:
            case 'zendop':
                $this->clearCache(self::CACHE_ENGINE_ZEND_OPCACHE);
                break;
            case self::CACHE_ENGINE_MEMCACHE:
            case 'memcache':
                $this->clearCache(self::CACHE_ENGINE_MEMCACHE);
                break;
            case self::CACHE_ENGINE_XCACHE:
            case 'xcache':
                $this->clearCache(self::CACHE_ENGINE_XCACHE);
                break;
            default:
                $this->clearCache(null);
                break;
        }
        
        $objTemplate->SetVariable('CONTENT_OK_MESSAGE', $_ARRAYLANG['TXT_CACHE_EMPTY_SUCCESS']);
    }


    /**
     * Delete all specific file from cache-folder
     *
     * @global     object    $objDatabase
     */
    function deleteSingleFile($intPageId)
    {
        global $objDatabase;

        $intPageId = intval($intPageId);
        if ( 0 < $intPageId ) {
            $files = glob( $this->strCachePath . '*_' . $intPageId );
            if ( count( $files ) ) {
                foreach ( $files as $file ) {
                    @unlink( $file );
                }
            }
        }
    }
}

?>
