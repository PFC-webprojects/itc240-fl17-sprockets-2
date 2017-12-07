<?php
/**
 * admin_logout.php destroys session so administrators can logout
 *
 * Clears session data, forwards user to admin login page upon successful logout  
 * 
 * @package nmAdmin
 * @author Bill Newman <williamnewman@gmail.com>
 * @version 2.21 2015/12/07
 * @link http://www.newmanix.com/
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @see admin_login.php
 * @todo none
 */

require '../includes/config.php'; #provides configuration, pathing, error handling, db credentials

startSession(); //wrapper for session_start()
$_SESSION = array();# Setting a session to an empty array safely clears all data

//session_destroy();# can't destroy session as will disable feedback - instead do it on login form!
feedback("Logout Successful!", "notice");
$_SESSION['red'] = THIS_PAGE;
header('Location:' . ADMIN_PATH . 'admin_login.php'); # redirect for successful logout
die;
?>
