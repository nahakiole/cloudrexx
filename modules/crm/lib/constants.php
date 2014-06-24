<?php
/**
 * Define constants
 *
 * @category   Constants
 * @package    contrexx
 * @subpackage module_crm
 * @author     SoftSolutions4U Development Team <info@softsolutions4u.com>
 * @copyright  2012 and CONTREXX CMS - COMVATION AG
 * @license    trial license
 * @link       www.contrexx.com
 */

define('CRM_MODULE_LIB_PATH', ASCMS_MODULE_PATH.'/crm/lib/');
define('CRM_ACCESS_PROFILE_IMG_WEB_PATH', ASCMS_PATH_OFFSET.ASCMS_IMAGES_FOLDER.'/crm/profile');
define('CRM_ACCESS_PROFILE_IMG_PATH',     ASCMS_DOCUMENT_ROOT.ASCMS_IMAGES_FOLDER.'/crm/profile');
define('CRM_ACCESS_OTHER_IMG_WEB_PATH', ASCMS_PATH_OFFSET.ASCMS_IMAGES_FOLDER.'/crm');
define('CRM_ACCESS_OTHER_IMG_PATH',     ASCMS_DOCUMENT_ROOT.ASCMS_IMAGES_FOLDER.'/crm');
define('CRM_MEDIA_PATH', ASCMS_MEDIA_PATH.'/crm/');

define('CRM_EVENT_ON_USER_ACCOUNT_CREATED', 'crm_user_account_created');
define('CRM_EVENT_ON_TASK_CREATED', 'crm_task_assigned');
define('CRM_EVENT_ON_ACCOUNT_UPDATED', 'crm_notify_staff_on_contact_added');