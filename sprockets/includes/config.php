<?php  //  Make sure that PHP is the very first character in the file.  Otherwise just one whitespace character will get interpreted as HTML, causing the header to get sent right away.
	/*
	config.php

	Stores configuation information for our web application

	*/

	
	//  Eliminates a common error in PHP:  "Header already sent".  The file will go into a buffer; the header will not get sent right away.  Place it first thing after the PHP tag.
	ob_start();

	define('DEBUG', true); #we want to see all errors

	define("SKI_IMAGES_FOLDER", "./ski_images/");
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

	
	//START NEW THEME STUFF
	$sub_folder = 'sprockets';//change to 'widgets' or 'sprockets' etc.

	//add subfolder, in this case 'fidgets' if not loaded to root:
	$config->physical_path = $_SERVER["DOCUMENT_ROOT"] . '/' . $sub_folder;
	$config->virtual_path = 'http://' . $_SERVER["HTTP_HOST"] . '/' . $sub_folder;
	$config->theme = 'BusinessCasual';//sub folder to themes

	//END NEW THEME STUFF


	
	switch(THIS_PAGE) {
		case 'index.php':
			$config->pageID = $config->title = 'Home Page';
			break;
		case 'ski-areas_list.php':
		case 'ski-areas_view.php':
			$config->pageID = $config->title = 'Ski Areas Page';
			break;
		case 'daily.php':
			$config->title = 'Daily Disappointments Page';
			$config->pageID = $config->title . ' for the So Sorry Coffee Company';
			break;
		case 'blog.php':
			$config->pageID = $config->title = 'Blog Page';
			break;
		case 'contact.php':
			$config->pageID = $config->title = 'Contact Page';
			break;
	}
	$config->title = $config->banner . ': ' . $config->title;  //  Start the page name with the site name.

	
	//START NEW THEME STUFF
	//creates theme virtual path for theme assets, JS, CSS, images
	$config->theme_virtual = $config->virtual_path . '/themes/' . $config->theme . '/';
	//END NEW THEME STUFF
	
?>