<?php

require_once dirname(dirname(dirname(__FILE__))) . '/core/Core/init.php';
init('minimal');
echo blogUpdates();

function blogUpdates() {
    //Update the database changes
    try {
        //update module name 
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."modules` SET `name` = 'Blog' WHERE `id` = 47");
        //update navigation url
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."backend_areas` SET `uri` = 'index.php?cmd=Blog' WHERE `area_id` = 119");
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."backend_areas` SET `uri` = 'index.php?cmd=Blog&act=manageEntry' WHERE `area_id` = 120");
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."backend_areas` SET `uri` = 'index.php?cmd=Blog&act=addEntry' WHERE `area_id` = 121");
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."backend_areas` SET `uri` = 'index.php?cmd=Blog&act=manageCategory' WHERE `area_id` = 122");
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."backend_areas` SET `uri` = 'index.php?cmd=Blog&act=addCategory' WHERE `area_id` = 123");
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."backend_areas` SET `uri` = 'index.php?cmd=Blog&act=settings' WHERE `area_id` = 124");
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."backend_areas` SET `uri` = 'index.php?cmd=Blog&act=networks' WHERE `area_id` = 125");
        //Insert component entry
        \Cx\Lib\UpdateUtil::sql("INSERT INTO `".DBPREFIX."component` (`id`, `name`, `type`) VALUES ('47', 'Blog', 'module')");
        //update module name for frontend pages
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."content_page` SET `module` = 'Blog' WHERE `module` = 'blog'");
        //following queries for changing the path from images/blog into images/Blog
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."module_blog_messages_lang` 
                                        SET `image` = REPLACE(`image`, 'images/blog', 'images/Blog')
                                        WHERE `image` LIKE ('".ASCMS_PATH_OFFSET."/images/blog%')");
        \Cx\Lib\UpdateUtil::sql("UPDATE `".DBPREFIX."module_blog_networks` 
                                        SET `icon` = REPLACE(`icon`, 'images/blog', 'images/Blog')
                                        WHERE `icon` LIKE ('".ASCMS_PATH_OFFSET."/images/blog%')");
    } catch (\Cx\Lib\UpdateException $e) {
        return "Error: $e->sql";
    }
    
    //Update script for moving the folder
    $blogImgPath = ASCMS_DOCUMENT_ROOT . '/images';
    
    try {
        if (file_exists($blogImgPath . '/blog') && !file_exists($blogImgPath . '/Blog')) {
            \Cx\Lib\FileSystem\FileSystem::makeWritable($blogImgPath . '/blog');
            if (!\Cx\Lib\FileSystem\FileSystem::move($blogImgPath . '/blog', $blogImgPath . '/Blog')) {
                return 'Failed to move the folder from '.$blogImgPath . '/blog to '.$blogImgPath . '/Blog.';
            }
        }
    } catch (\Cx\Lib\FileSystem\FileSystemException $e) {
        return $e->getMessage();
    }
    
    return 'Blog updated successfully.';
}