<?php
/**
 * admin_login.php is entry point (form) page to administrative area
 *
 * Works with admin_validate.php to process administrator login requests.
 * Forwards user to admin_dashboard.php, upon successful login.
 *
 * @package nmAdmin
 * @author Bill Newman <williamnewman@gmail.com>
 * @version 2.21 2015/12/07
 * @link http://www.newmanix.com/
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @see admin_validate.php
 * @see admin_dashboard.php
 * @see admin_logout.php
 * @see admin_only_inc.php     
 * @todo none
 */
 
require '../includes/config.php'; #provides configuration, pathing, error handling, db credentials
$title = 'Admin Login'; #Fills <title> tag
//END CONFIG AREA ----------------------------------------------------------
if(startSession() && isset($_SESSION['red']) && $_SESSION['red'] != 'admin_logout.php')
{//store redirect to get directly back to originating page
	$red = $_SESSION['red'];
}else{//don't redirect to logout page!
	$red = '';
}#required for redirect back to previous page

get_header(); #header must appear before any HTML is printed by PHP
echo '
 <h1>Admin Login</h1>
 <table align="center">
 	  <form action="' . ADMIN_PATH . 'admin_validate.php" method="post">
      <tr>
			<td align="right">Email:</td>
			<td>
				<input type="email" autofocus required size="25" name="em" id="em" />
			</td>
      </tr>
      <tr>
      		<td align="right">Password:</td>
      		<td>
      			<input type="password" size="25" required name="pw" id="pw" />
      		</td>
      </tr>
       
      <tr>
      	<td align="center" colspan="2">
      		<input type="hidden" name="red" value="' . $red . '" />
      		<input type="submit" value="login" />
      	</td>
      </tr>
 </table>
 </form>
 ';
get_footer();

if(isset($_SESSION['red']) && $_SESSION['red'] == 'admin_logout.php')
{#since admin_logout.php uses the session var to pass feedback, kill the session here!
	$_SESSION = array();
	session_destroy();	
}
?>
