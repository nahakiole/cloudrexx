<?php

require_once dirname(dirname(dirname(__FILE__))) . '/core/Core/init.php';
init('minimal');
echo directoryUpdates();

function directoryUpdates() {
    //Update the database changes
    try {
        //update module name 
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."modules` SET `name` = 'Directory' WHERE `contrexx_modules`.`id` = 12");
        //update navigation url
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."backend_areas` SET `uri` = 'index.php?cmd=Directory' WHERE `contrexx_backend_areas`.`area_id` = 59");
        //Insert component entry
        \Cx\Lib\UpdateUtil::sql("INSERT INTO `".DBPREFIX."component` (`id`, `name`, `type`) VALUES ('12', 'Directory', 'module')");
        //update module name for frontend pages
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."content_page` SET `module` = 'Directory' WHERE `module` = 'directory'");
    } catch (\Cx\Lib\UpdateException $e) {
        return "Error: $e->sql";
    }
    
    //Update script for moving the folder
    $sourcePath      = ASCMS_DOCUMENT_ROOT . '/media/directory';
    $destinationPath = ASCMS_DOCUMENT_ROOT . '/media/Directory';
    
    try {
        if (file_exists($sourcePath) && !file_exists($destinationPath)) {
            \Cx\Lib\FileSystem\FileSystem::makeWritable($sourcePath);
            if (!\Cx\Lib\FileSystem\FileSystem::move($sourcePath, $destinationPath)) {
                return 'Failed to move the folder from '.$sourcePath.' to '.$destinationPath;
            }
        }
    } catch (\Cx\Lib\FileSystem\FileSystemException $e) {
        return $e->getMessage();
    }
    
    return 'Directory is updated successfully.';
}