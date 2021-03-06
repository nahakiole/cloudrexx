<?php 

 /** 
 * @copyright   CONTREXX CMS - COMVATION AG 
 * @author      Comvation Development Team <info@comvation.com>
 * @access      public 
 * @package     contrexx
 * @subpackage  coremodule_cache
 */ 
global $_ARRAYLANG; 
$_ARRAYLANG['TXT_CACHE_ERR_NOTWRITABLE'] = 'De gekozen buffermap is niet beschrijfbaar! Wijzig de permissies naar 777 van:';
$_ARRAYLANG['TXT_CACHE_ERR_NOTEXIST'] = 'Map voor het geheugensysteem bestaat niet! Controleer het pad:';
$_ARRAYLANG['TXT_SETTINGS_MENU_CACHE'] = 'Buffer';
$_ARRAYLANG['TXT_CACHE_STATS'] = 'Statistics';
$_ARRAYLANG['TXT_CACHE_CONTREXX_CACHING'] = 'Contrexx caching';
$_ARRAYLANG['TXT_CACHE_USERCACHE'] = 'Database cache engine';
$_ARRAYLANG['TXT_CACHE_OPCACHE'] = 'Program code cache engine';
$_ARRAYLANG['TXT_CACHE_PROXYCACHE'] = 'Proxy cache';
$_ARRAYLANG['TXT_CACHE_EMPTY'] = 'Buffer legen';
$_ARRAYLANG['TXT_CACHE_APC'] = 'APC';
$_ARRAYLANG['TXT_CACHE_ZEND_OPCACHE'] = 'Zend OPCache';
$_ARRAYLANG['TXT_CACHE_XCACHE'] = 'xCache';
$_ARRAYLANG['TXT_CACHE_MEMCACHE'] = 'Memcache';
$_ARRAYLANG['TXT_CACHE_MEMCACHED'] = 'Memcached';
$_ARRAYLANG['TXT_CACHE_FILESYSTEM'] = 'FileSystem';
$_ARRAYLANG['TXT_CACHE_APC_ACTIVE_INFO'] = 'APC is active, as soon as the php directive "apc.enabled" has been set to "On".';
$_ARRAYLANG['TXT_CACHE_APC_CONFIG_INFO'] = 'If you want to use apc as a database cache engine, you have to set the php directive "apc.serializer" to "php".';
$_ARRAYLANG['TXT_CACHE_ZEND_OPCACHE_ACTIVE_INFO'] = 'Zend OPCache is active, as soon as the php directive "opcache.enable" has been set to "On".';
$_ARRAYLANG['TXT_CACHE_ZEND_OPCACHE_CONFIG_INFO'] = 'If you want to use the zend opcache as cache engine, the php directives "opcache.save_comments" and "opcache.load_comments" has to be set to "On".';
$_ARRAYLANG['TXT_CACHE_XCACHE_ACTIVE_INFO'] = 'xCache is active, as soon as the php directive "xcache.cacher" has been set to "On".';
$_ARRAYLANG['TXT_CACHE_XCACHE_CONFIG_INFO'] = 'If you want to use xCache as a database cache engine, you have to set the php directive "xcache.var_size" to a value bigger than 0. For the program code cache the php directive "xcache.size" has to be bigger than 0.';
$_ARRAYLANG['TXT_CACHE_MEMCACHE_ACTIVE_INFO'] = 'Memcache is active, as soon as the Memcache server is running and the configuration is correct.';
$_ARRAYLANG['TXT_CACHE_MEMCACHE_CONFIG_INFO'] = 'If you want to use Memcache, the configuration (IP address and port number) has to be correct.';
$_ARRAYLANG['TXT_CACHE_MEMCACHED_ACTIVE_INFO'] = 'Memcached is active, as soon as the Memcached server is running and the configuration is correct.';
$_ARRAYLANG['TXT_CACHE_MEMCACHED_CONFIG_INFO'] = 'If you want to use Memcached, the configuration (IP address and port number) has to be correct.';
$_ARRAYLANG['TXT_CACHE_ENGINE'] = 'Engine';
$_ARRAYLANG['TXT_CACHE_INSTALLATION_STATE'] = 'Installed';
$_ARRAYLANG['TXT_CACHE_ACTIVE_STATE'] = 'Active';
$_ARRAYLANG['TXT_CACHE_CONFIGURATION_STATE'] = 'Configured';
$_ARRAYLANG['TXT_SAVE'] = 'Opslaan';
$_ARRAYLANG['TXT_ACTIVATED'] = 'Geactiveerd';
$_ARRAYLANG['TXT_DEACTIVATED'] = 'Gedeactiveerd';
$_ARRAYLANG['TXT_CACHE_SETTINGS_STATUS'] = 'Geheugensysteem';
$_ARRAYLANG['TXT_CACHE_SETTINGS_STATUS_HELP'] = 'Status van het buffersysteem: (aan | uit)';
$_ARRAYLANG['TXT_CACHE_SETTINGS_EXPIRATION'] = 'Expiratie';
$_ARRAYLANG['TXT_CACHE_SETTINGS_EXPIRATION_HELP'] = 'Na deze periode (gemeten in seconden) worden pagina"s in het geheugen aangemaakt.';
$_ARRAYLANG['TXT_CACHE_EMPTY_DESC'] = 'Door op de knop te drukken kunnen alle gebufferde bestanden worden verwijderd. De buffer wordt weer opnieuw aangemaakt voor gebruik.';
$_ARRAYLANG['TXT_CACHE_EMPTY_DESC_FILES_AND_ENRIES'] = 'With a click on the button, you can remove the current cache content. The cached files and entries will be recreated while viewing the pages.';
$_ARRAYLANG['TXT_CACHE_EMPTY_DESC_FILES'] = 'With a click on the button, you can remove the current cache content. The cached files will be recreated while viewing the pages.';
$_ARRAYLANG['TXT_CACHE_EMPTY_DESC_MEMCACHE'] = 'With a click on the button, you can mark the current cache content as outdated. The cached entries will be updated while viewing the pages.';
$_ARRAYLANG['TXT_CACHE_STATS_FILES'] = 'Pagina"s in buffer';
$_ARRAYLANG['TXT_CACHE_STATS_FOLDERSIZE'] = 'Map grootte';
$_ARRAYLANG['TXT_STATS_CHACHE_SITE_COUNT'] = 'Cached Files';
$_ARRAYLANG['TXT_STATS_CHACHE_ENTRIES_COUNT'] = 'Cached Databaseentries';
$_ARRAYLANG['TXT_STATS_CACHE_SIZE'] = 'Ammount of stored Data';
$_ARRAYLANG['TXT_DISPLAY_CONFIGURATION'] = 'Konfiguration einblenden';
$_ARRAYLANG['TXT_HIDE_CONFIGURATION'] = 'Konfiguration ausblenden';
$_ARRAYLANG['TXT_CACHE_VARNISH'] = 'Varnish';
$_ARRAYLANG['TXT_CACHE_PROXY_IP'] = 'Proxy IP-Address';
$_ARRAYLANG['TXT_CACHE_PROXY_PORT'] = 'Proxy Port';
$_ARRAYLANG['TXT_SETTINGS_UPDATED'] = 'Instellingen zijn gewijzigd!';
$_ARRAYLANG['TXT_CACHE_FOLDER_EMPTY'] = 'De geheugenfolder is leeggemaakt.';
$_ARRAYLANG['TXT_CACHE_EMPTY_SUCCESS'] = 'Cache has been emptied.';
