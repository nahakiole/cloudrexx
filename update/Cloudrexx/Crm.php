<?php

require_once dirname(dirname(dirname(__FILE__))) . '/core/Core/init.php';
init('minimal');
echo crmUpdates();

function crmUpdates() {
    //Update the database changes
    try {
        //update module name 
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."modules` SET `name` = 'Crm' WHERE `id` = 69");
        //update navigation url
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."backend_areas` SET `uri` = 'index.php?cmd=Crm&act=customers' WHERE `area_id` = 191");
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."backend_areas` SET `uri` = 'index.php?cmd=Crm&act=task' WHERE `area_id` = 192");
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."backend_areas` SET `uri` = 'index.php?cmd=Crm&act=deals' WHERE `area_id` = 193");
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."backend_areas` SET `uri` = 'index.php?cmd=Crm&act=settings' WHERE `area_id` = 195");
        //Insert component entry
        \Cx\Lib\UpdateUtil::sql("INSERT INTO `".DBPREFIX."component` (`id`, `name`, `type`) VALUES ('69', 'Crm', 'module')");
        //update module name for email templates
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."core_mail_template` SET `section` = 'Crm' WHERE `section` = 'crm'");
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."core_text` SET `section` = 'Crm' WHERE `section` = 'crm'");
        //update module name for crm core settings
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."core_setting` SET `section` = 'Crm' WHERE `section` = 'crm'");
    } catch (\Cx\Lib\UpdateException $e) {
        return "Error: $e->sql";
    }
    
    //Update script for moving the folder
    $crmImgPath   = ASCMS_DOCUMENT_ROOT . '/images';
    $crmMediaPath = ASCMS_DOCUMENT_ROOT . '/media';
    
    try {
        if (file_exists($crmImgPath . '/crm') && !file_exists($crmImgPath . '/Crm')) {
            \Cx\Lib\FileSystem\FileSystem::makeWritable($crmImgPath . '/crm');
            if (!\Cx\Lib\FileSystem\FileSystem::move($crmImgPath . '/crm', $crmImgPath . '/Crm')) {
                return 'Failed to move the folder from '.$crmImgPath . '/crm to '.$crmImgPath . '/Crm.';
            }
        }
        if (file_exists($crmMediaPath . '/crm') && !file_exists($crmMediaPath . '/Crm')) {
            \Cx\Lib\FileSystem\FileSystem::makeWritable($crmMediaPath . '/crm');
            if (!\Cx\Lib\FileSystem\FileSystem::move($crmMediaPath . '/crm', $crmMediaPath . '/Crm')) {
                return 'Failed to move the folder from '.$crmMediaPath . '/crm to '.$crmMediaPath . '/Crm.';
            }
        }
    } catch (\Cx\Lib\FileSystem\FileSystemException $e) {
        return $e->getMessage();
    }
    
    return 'Crm updated successfully.';
}