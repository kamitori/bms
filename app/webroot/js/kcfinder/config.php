<?php

/** This file is part of KCFinder project
  *
  *      @desc Base configuration file
  *   @package KCFinder
  *   @version 2.51
  *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
  * @copyright 2010, 2011 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  */

// IMPORTANT!!! Do not remove uncommented settings in this file even if
// you are using session configuration.
// See http://kcfinder.sunhater.com/install for setting descriptions
if (!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);

if (!defined('ROOT'))
    define('ROOT', dirname(dirname(dirname(__FILE__))));
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    $upload_path = 'http://localhost/jobtraq/upload/';
}else if ($_SERVER['SERVER_NAME'] == 'jobtraq.anvy.net') {
    $upload_path = 'http://jobtraq.anvy.net/upload';

} else if ($_SERVER['SERVER_NAME'] == 'jt.local.com') {
    $upload_path = 'http://jt.local.com/Dropbox/Anvydigital/anvy-jobtraq/upload';

} else if ($_SERVER['SERVER_NAME'] == 'jt.com') { // BaoNam ==================================
    $upload_path = 'http://jt.com/upload';

} else if ($_SERVER['SERVER_NAME'] == 'bms.com') {
    $upload_path = 'http://bms.com/upload';

}else if ($_SERVER['SERVER_NAME'] == 'jt.anvy.net') {
    $upload_path = 'http://jt.anvy.net/upload';
}else if ($_SERVER['SERVER_NAME'] == 'jobtraq.anvyonline.com') {
    $upload_path = 'http://jobtraq.anvyonline.com/upload';
}else if ($_SERVER['SERVER_NAME'] == 'bms.anvyonline.com') {
    $upload_path = 'http://jt.banhmisub.com/upload';
}else if ($_SERVER['SERVER_NAME'] == '192.168.0.201') {
    $upload_path = 'http://192.168.0.201/upload';
}

$_CONFIG = array(

    'disabled' => false,
    'denyZipDownload' => false,
    'denyUpdateCheck' => true,
    'denyExtensionRename' => true,

    'theme' => "dark",

    'uploadURL' => $upload_path,
    'uploadDir' => ROOT.DS.'upload',

    'dirPerms' => 0755,
    'filePerms' => 0644,

    'access' => array(

        'files' => array(
            'upload' => true,
            // 'delete' => true,
            'copy' => true,
            // 'move' => true,
            'rename' => true
        ),

        'dirs' => array(
            // 'create' => true,
            // 'delete' => true,
            // 'rename' => true
            'create' => false,
            'delete' => false,
            'rename' => false
        )
    ),

    'deniedExts' => "exe com msi bat php phps phtml php3 php4 cgi js pl",

    'types' => array(

        // CKEditor & FCKEditor types
        // 'files'   =>  "",
        // 'flash'   =>  "swf",
        'email_template'  =>  "*img",

        // TinyMCE types
        // 'file'    =>  "",
        // 'media'   =>  "swf flv avi mpg mpeg qt mov wmv asf rm",
        // 'image'   =>  "*img",
    ),

    'filenameChangeChars' => array(
        ' ' => "_",
        ':' => "."
    ),

    'dirnameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'mime_magic' => "",

    'maxImageWidth' => 0,
    'maxImageHeight' => 0,

    'thumbWidth' => 100,
    'thumbHeight' => 100,

    'thumbsDir' => "thumbs",

    'jpegQuality' => 90,

    'cookieDomain' => "",
    'cookiePath' => "",
    'cookiePrefix' => 'KCFINDER_',

    // THE FOLLOWING SETTINGS CANNOT BE OVERRIDED WITH SESSION CONFIGURATION
    '_check4htaccess' => true,
    //'_tinyMCEPath' => "/tiny_mce",

    '_sessionVar' => &$_SESSION['KCFINDER'],
    //'_sessionLifetime' => 30,
    //'_sessionDir' => "/full/directory/path",

    //'_sessionDomain' => ".mysite.com",
    //'_sessionPath' => "/my/path",
);

?>