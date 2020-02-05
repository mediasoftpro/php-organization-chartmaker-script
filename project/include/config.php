<?php
error_reporting(E_ERROR | E_PARSE);
$root = dirname(dirname(__FILE__)) . '/';
define('ROOTPATH', $root);
define('INCLUDE_ROOT', ROOTPATH . "include/");
/* Define upload directory path */
define ('UPLOAD_DIRECTORY_PATH', ROOTPATH . "contents/member/");
define ('UPLOAD_DIRECTORY_PATH2', ROOTPATH . "contents/");

/* Define root domain path */
define('SITE_DOMAIN', 'http://bootstrapkits.com/jchart/'); 
/* Define page caption -> append with each page title */
define('Page_Caption', 'JUGNOON jChart'); // Append with each page title
/* Define site upload directo path for accessing */
define ('SITE_DIRECTORY_PATH', SITE_DOMAIN . "contents/member/");
/* Define Site Name (Can be used for logo) */
define('SITENAME', 'Bootstrap Kit - jChart');
/* Define Logo Url */
define('LOGOURL', 'LOGOURL'); // css path for public pages
/* Define Admin Css Path */
define('ADMINCSSPATH', SITE_DOMAIN . 'css/base/'); // css path for admin control panel
/* Define Public Css Path */
define('THEMEPATH', SITE_DOMAIN . 'css/base/'); // css path for public pages

?>