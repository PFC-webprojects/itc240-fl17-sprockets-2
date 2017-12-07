<?php  //  Make sure that PHP is the very first character in the file.  Otherwise just one whitespace character will get interpreted as HTML, causing the header to get sent right away.
	/*
	config.php

	Stores configuation information for our web application

	*/

	
	//  Eliminates a common error in PHP:  "Header already sent".  The file will go into a buffer; the header will not get sent right away.  Place it first thing after the PHP tag.
	ob_start();

	define('SECURE', false); #force secure, https, for all site pages
	define('PREFIX', 'sprockets_fl17_'); #Adds uniqueness to your DB table names.  Limits hackability, naming collisions
	header("Cache-Control: no-cache");header("Expires: -1");#Helps stop browser & proxy caching

	
	define('DEBUG', true); #we want to see all errors

	define("SKI_IMAGES_FOLDER", "./ski_images/");
	define("COFFEE_IMAGES_FOLDER", "./coffee_images/");
	define("VIEW_PAGE", "ski-areas_view.php");
	define("LIST_PAGE", "ski-areas_list.php");

	
	include 'credentials.php';  //  Stores database logon credentials
	include 'common.php';  //  Stores favorite procedures for use throughout the website
	
	//  Prevents date errors
	date_default_timezone_set('America/Los_Angeles');
	

	//  Create default page identifier.
	define('THIS_PAGE', basename($_SERVER['PHP_SELF']));  //  Creates a constant (with global scope).

	
	
	
	//  Create config object.
	$config = new stdClass; //  An object variable.  stdClass is actually a struct, rather than a true class with inheritance and all.
	//  Set website defaults.
	$config->title = THIS_PAGE;  //  By default, in case we forget it in the switch below, at least this page will be named by its file name.
	$config->banner = 'Sprockets';
	$config->pageID ='';  //  Default value, in case we forget it in the switch below.
	$config->loadhead = '';//place items in <head> element

	
	//START NEW THEME STUFF
// ********************************************************************************************************************************************************************
	$sub_folder = 'sprockets';  //  'sprockets' for the published edition.  'itc240/sprockets_development' for the most recent development edition.
// ********************************************************************************************************************************************************************

	//add subfolder, in this case 'fidgets' if not loaded to root:
	$config->physical_path = $_SERVER["DOCUMENT_ROOT"] . '/' . $sub_folder;	//  $config->physical_path = $_SERVER["DOCUMENT_ROOT"] . '/' . $sub_folder;
	$config->virtual_path = 'http://' . $_SERVER["HTTP_HOST"] . '/' . $sub_folder;
	$config->theme = 'BusinessCasual';//sub folder to themes
	
	define('ADMIN_PATH', $config->virtual_path . '/admin/'); # Could change to sub folder
	define('INCLUDE_PATH', $config->physical_path . '/includes/');

	//force secure website
	if (SECURE && $_SERVER['SERVER_PORT'] != 443) {#force HTTPS (Force an application to be secure.)
	header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
}
	//END NEW THEME STUFF

	
	
	for ($i = 0; $i < 4; $i++) {  //  Reset all items in the page header's navigation menu to inactive.  One item will be activated in the switch below.  This is PHP that controls whether a CSS class gets applied or not.
		$config->navItem[$i] = '';
	}
	
	switch(THIS_PAGE) {
		case 'index.php':
			$config->pageID = $config->title = 'Home Page';
			$config->navItem[0] = 'active ';
			break;
		case 'ski-areas_list.php':
		case 'ski-areas_view.php':
			$config->pageID = $config->title = 'Ski Areas Page';
			$config->navItem[1] = 'active ';
			break;
		case 'daily.php':
			$config->title = 'Daily Disappointments Page';
			$config->pageID = $config->title . ' for the So Sorry Coffee Company';
			$config->navItem[2] = 'active ';
			break;
		case 'contact.php':
			$config->pageID = $config->title = 'Contact Page';
			$config->navItem[3] = 'active ';
			break;
	}
	$config->title = $config->banner . ': ' . $config->title;  //  Start the page name with the site name.

	
	//START NEW THEME STUFF
	//creates theme virtual path for theme assets, JS, CSS, images
	$config->theme_virtual = $config->virtual_path . '/themes/' . $config->theme . '/';
	//END NEW THEME STUFF
	
	
	/*
	 * adminWidget allows clients to get to admin page from anywhere
	 * code will show/hide based on logged in status
	*/
	/*
	 * adminWidget allows clients to get to admin page from anywhere
	 * code will show/hide based on logged in status
	*/
	if (startSession() && isset($_SESSION['AdminID'])) {  #add admin logged in info to sidebar or nav
		$config->adminWidget = '
			<a href="' . ADMIN_PATH . 'admin_dashboard.php">ADMIN</a> 
			<a href="' . ADMIN_PATH . 'admin_logout.php">LOGOUT</a>
		';
	}
	else {//show login (YOU MAY WANT TO SET TO EMPTY STRING FOR SECURITY)
		$config->adminWidget = '
			<a  href="' . ADMIN_PATH . 'admin_login.php">LOGIN</a>
		';
	}

?>