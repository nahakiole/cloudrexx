<?php 

 /** 
 * @copyright   CONTREXX CMS - COMVATION AG 
 * @author      Comvation Development Team <info@comvation.com>
 * @access      public 
 * @package     contrexx
 * @subpackage  coremodule_cache
 */ 
global $_ARRAYLANG; 
$_ARRAYLANG['TXT_CACHE_ERR_NOTWRITABLE'] = 'Das gewählte Caching-Verzeichnis ist nicht beschreibbar. Setzen Sie die Berechtigung 777 auf folgendes Verzeichnis: ';
$_ARRAYLANG['TXT_CACHE_ERR_NOTEXIST'] = 'Das Caching-Verzeichnis existiert nicht. Bitte überprüfen Sie folgenden Ordner:';
$_ARRAYLANG['TXT_SETTINGS_MENU_CACHE'] = 'Caching';
$_ARRAYLANG['TXT_CACHE_STATS'] = 'Statistiken';
$_ARRAYLANG['TXT_CACHE_CONTREXX_CACHING'] = 'Contrexx-Caching';
$_ARRAYLANG['TXT_CACHE_USERCACHE'] = 'Datenbank Cache Systeme';
$_ARRAYLANG['TXT_CACHE_OPCACHE'] = 'Programmcode Cache Systeme';
$_ARRAYLANG['TXT_CACHE_PROXYCACHE'] = 'Proxy Cache';
$_ARRAYLANG['TXT_CACHE_EMPTY'] = 'Cache leeren';
$_ARRAYLANG['TXT_CACHE_APC'] = 'APC';
$_ARRAYLANG['TXT_CACHE_ZEND_OPCACHE'] = 'Zend OPCache';
$_ARRAYLANG['TXT_CACHE_XCACHE'] = 'xCache';
$_ARRAYLANG['TXT_CACHE_MEMCACHE'] = 'Memcache';
$_ARRAYLANG['TXT_CACHE_MEMCACHED'] = 'Memcached';
$_ARRAYLANG['TXT_CACHE_FILESYSTEM'] = 'FileSystem';
$_ARRAYLANG['TXT_CACHE_APC_ACTIVE_INFO'] = 'APC ist aktiviert, sobald die PHP Direktive "apc.enabled" auf "On" gesetzt ist.';
$_ARRAYLANG['TXT_CACHE_APC_CONFIG_INFO'] = 'Wenn Sie APC als Datenbank Cache System einsetzen möchten, muss die PHP Direktive "apc.serializer" auf "php" gesetzt sein.';
$_ARRAYLANG['TXT_CACHE_ZEND_OPCACHE_ACTIVE_INFO'] = 'Zend OPCache ist aktiviert, sobald die PHP Direktive "opcache.enable" auf "On" gesetzt ist.';
$_ARRAYLANG['TXT_CACHE_ZEND_OPCACHE_CONFIG_INFO'] = 'Um Zend OPCache zu verwenden, müssen die PHP Direktiven "opcache.save_comments" und "opcache.load_comments" auf "On" gesetzt sein.';
$_ARRAYLANG['TXT_CACHE_XCACHE_ACTIVE_INFO'] = 'xCache ist aktiviert, sobald die PHP Direktive "xcache.cacher" auf "On" gesetzt ist.';
$_ARRAYLANG['TXT_CACHE_XCACHE_CONFIG_INFO'] = 'Um xCache als Datenbank Cache System zu verwenden, muss die PHP Direktive "xcache.var_size" grösser 0 sein. Für den Programmcode Cache muss "xcache.size" grösser 0 sein.';
$_ARRAYLANG['TXT_CACHE_MEMCACHE_ACTIVE_INFO'] = 'Memcache ist aktiviert, sobald der Memcache Server läuft und die Konfigurationen korrekt gesetzt sind.';
$_ARRAYLANG['TXT_CACHE_MEMCACHE_CONFIG_INFO'] = 'Um Memcache zu verwenden, muss die Konfiguration (IP-Adresse und Port) korrekt konfiguriert sein.';
$_ARRAYLANG['TXT_CACHE_MEMCACHE_ACTIVE_INFO'] = 'Memcached ist aktiviert, sobald der Memcached Server läuft und die Konfigurationen korrekt gesetzt sind.';
$_ARRAYLANG['TXT_CACHE_MEMCACHE_CONFIG_INFO'] = 'Um Memcached zu verwenden, muss die Konfiguration (IP-Adresse und Port) korrekt konfiguriert sein.';
$_ARRAYLANG['TXT_CACHE_ENGINE'] = 'System';
$_ARRAYLANG['TXT_CACHE_INSTALLATION_STATE'] = 'Installiert';
$_ARRAYLANG['TXT_CACHE_ACTIVE_STATE'] = 'Aktiv';
$_ARRAYLANG['TXT_CACHE_CONFIGURATION_STATE'] = 'Konfiguriert';
$_ARRAYLANG['TXT_SAVE'] = 'Speichern';
$_ARRAYLANG['TXT_ACTIVATED'] = 'Aktiviert';
$_ARRAYLANG['TXT_DEACTIVATED'] = 'Deaktiviert';
$_ARRAYLANG['TXT_CACHE_SETTINGS_STATUS'] = 'Cache-System';
$_ARRAYLANG['TXT_CACHE_SETTINGS_STATUS_HELP'] = 'Aktueller Status des Caching-Systems - Status: (on | off)';
$_ARRAYLANG['TXT_CACHE_SETTINGS_EXPIRATION'] = 'Vorhaltezeit';
$_ARRAYLANG['TXT_CACHE_SETTINGS_EXPIRATION_HELP'] = 'Nach Ablauf dieser Zeitdauer (Angabe in Sekunden) werden zwischengespeicherte Seiten neu erzeugt.';
$_ARRAYLANG['TXT_CACHE_EMPTY_DESC'] = 'Über den Button können Sie den aktuellen Inhalt des Caching-Ordners leeren. Der Seitencache baut sich anschliessend bei Aufrufen der Seite jeweils wieder neu auf.';
$_ARRAYLANG['TXT_CACHE_EMPTY_DESC_FILES_AND_ENRIES'] = 'Über den Button können Sie den aktuellen Inhalt des Caches leeren. Der Cache der Dateien und Einträge baut sich anschliessend bei Aufrufen der Seite jeweils wieder neu auf.';
$_ARRAYLANG['TXT_CACHE_EMPTY_DESC_FILES'] = 'Über den Button können Sie den aktuellen Inhalt des Caches leeren. Der Cache der Dateien baut sich anschliessend bei Aufrufen der Seite jeweils wieder neu auf.';
$_ARRAYLANG['TXT_CACHE_EMPTY_DESC_MEMCACHE'] = 'Über den Button können Sie alle aktuell gecachten Einträge als veraltet markieren. Diese werden beim nächsten Aufruf der ensprechenden Site erneuert.';
$_ARRAYLANG['TXT_CACHE_STATS_FILES'] = 'Gecachte Seiten';
$_ARRAYLANG['TXT_CACHE_STATS_FOLDERSIZE'] = 'Ordnergrösse';
$_ARRAYLANG['TXT_STATS_CHACHE_SITE_COUNT'] = 'Gecachte Dateien';
$_ARRAYLANG['TXT_STATS_CHACHE_ENTRIES_COUNT'] = 'Gecachte Datenbankeinträge';
$_ARRAYLANG['TXT_STATS_CACHE_SIZE'] = 'Datenmenge der gespeicherten Daten';
$_ARRAYLANG['TXT_DISPLAY_CONFIGURATION'] = 'Konfiguration einblenden';
$_ARRAYLANG['TXT_HIDE_CONFIGURATION'] = 'Konfiguration ausblenden';
$_ARRAYLANG['TXT_CACHE_VARNISH'] = 'Varnish';
$_ARRAYLANG['TXT_CACHE_PROXY_IP'] = 'Proxy IP-Adresse';
$_ARRAYLANG['TXT_CACHE_PROXY_PORT'] = 'Proxy Port';
$_ARRAYLANG['TXT_SETTINGS_UPDATED'] = 'Die Einstellungen wurden gespeichert.';
$_ARRAYLANG['TXT_CACHE_FOLDER_EMPTY'] = 'Cache-Ordner wurde geleert.';
$_ARRAYLANG['TXT_CACHE_EMPTY_SUCCESS'] = 'Der Cache wurde erfolgreich geleert';
