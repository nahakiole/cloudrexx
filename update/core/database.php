<?php

/**
 * Database access function(s)
 * @copyright    CONTREXX CMS - COMVATION AG
 * @author        Comvation Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  core
 * @version        1.0.0
 */

/**
 * @ignore
 */
require_once UPDATE_LIB.'/adodb/adodb.inc.php';

/**
 * Returns the database object.
 *
 * If none was created before, or if {link $newInstance} is true,
 * creates a new database object first.
 * In case of an error, the reference argument $errorMsg is set
 * to the error message.
 * @author  Comvation Development Team <info@comvation.com>
 * @access  public
 * @version 1.0.0
 * @param   string  $errorMsg       Error message
 * @param   boolean $newInstance    Force new instance
 * @global  array                   Language array
 * @global  array                   Database configuration
 * @global  integer                 ADODB fetch mode
 * @return  boolean                 True on success, false on failure
 */
function getDatabaseObject(&$errorMsg, $newInstance = false)
{
    global $_DBCONFIG, $ADODB_FETCH_MODE, $_CONFIG;
    static $objDatabase;

    if (is_object($objDatabase) && !$newInstance) {
        return $objDatabase;
    } else {
        // open db connection
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

        $objDb = ADONewConnection($_DBCONFIG['dbType']);
        @$objDb->Connect($_DBCONFIG['host'], $_DBCONFIG['user'], $_DBCONFIG['password'], $_DBCONFIG['database']);

        $errorNo = $objDb->ErrorNo();
        if ($errorNo != 0) {
            if ($errorNo == 1049) {
                $errorMsg = 'The database is unavailable';
            } else {
                $errorMsg =  $objDb->ErrorMsg()."<br />";
            }
            unset($objDb);
            return false;
        }

        if (!empty($_CONFIG['timezone'])) {
            if (!$objDb->Execute('SET TIME_ZONE="'.$_CONFIG['timezone'].'"') && array_search($_CONFIG['timezone'], timezone_identifiers_list())) {
                //calculate and set the timezone offset if the mysql timezone tables aren't loaded
                $objDateTimeZone = new DateTimeZone($_CONFIG['timezone']);
                $objDateTime = new DateTime('now', $objDateTimeZone);
                $offset = $objDateTimeZone->getOffset($objDateTime);
                $offsetHours = round(abs($offset)/3600); 
                $offsetMinutes = round((abs($offset)-$offsetHours*3600) / 60); 
                $offsetString = ($offset > 0 ? '+' : '-').($offsetHours < 10 ? '0' : '').$offsetHours.':'.($offsetMinutes < 10 ? '0' : '').$offsetMinutes;
                $objDb->Execute('SET TIME_ZONE="'.$offsetString.'"');
            }
        }
        
        // Disable STRICT_TRANS_TABLES mode:
        $res = $objDb->Execute('SELECT @@sql_mode');
        if ($res->EOF) {
            $errorMsg = 'Database mode error';
            return;
        }
        $sqlModes = explode(',', $res->fields['@@sql_mode']);
        array_walk($sqlModes, 'trim');
        if (($index = array_search('STRICT_TRANS_TABLES', $sqlModes)) !== false) {
            unset($sqlModes[$index]);
        }
        $objDb->Execute('SET sql_mode = \'' . implode(',', $sqlModes) . '\'');

        if (empty($_DBCONFIG['charset']) || $objDb->Execute('SET NAMES '.$_DBCONFIG['charset']) && $objDb) {
            if ($newInstance) {
                return $objDb;
            } else {
                $objDatabase = $objDb;
                return $objDb;
            }
        } else {
            $errorMsg = 'Cannot connect to database server<i>&nbsp;('.$objDb->ErrorMsg().')</i>';
            unset($objDb);
        }
        return false;
    }
}

?>
