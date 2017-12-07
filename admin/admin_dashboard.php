<?php
/**
 * admin_dashboard.php session protected page of links to administrator tool pages
 *
 * Use this file as a landing page after successfully logging in as an administrator.  
 * Be sure this page is not publicly accessible by referencing admin_only_inc.php
 * 
 * @package nmAdmin
 * @author Bill Newman <williamnewman@gmail.com>
 * @version 2.21 2015/12/07
 * @link http://www.newmanix.com/
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @see admin_only_inc.php 
 * @todo none
 */

require '../includes/config.php'; #provides configuration, pathing, error handling, db credentials 
$title = 'Admin Dashboard'; #Fills <title> tag. If left empty will fallback to $config->titleTag in config_inc.php 
//END CONFIG AREA ----------------------------------------------------------
$access = "admin"; #admin or higher level can view this page
include_once INCLUDE_PATH . 'admin_only_inc.php'; #session protected page - level is defined in $access var
get_header(); #header must appear before any HTML is printed by PHP
?>
<h1>Admin Dashboard</h1>
<table border="1" style="border-collapse:collapse" align="center" width="98%" cellpadding="3" cellspacing="3">
	<tr><th>Page</th><th>Purpose</th></tr>
	<?php if($_SESSION['Privilege']=="developer"){
    //place any developer specific links here
    
	}
	if($_SESSION['Privilege']=="superadmin" || $_SESSION['Privilege']=="developer"){ ?>
		<tr>
			<td width="250" align="center"><a href="<?=ADMIN_PATH?>admin_add.php">Add Administrator</a></td>
			<td><b>SuperAdmin Only.</b> Create site administrators, of any level.</td>
		</tr>
	<?php
	}
	?>
	<tr>
			<td width="250" align="center"><a href="<?=ADMIN_PATH?>admin_reset.php">Reset Administrator Password</a></td>
			<td>Reset Admin passwords here.  SuperAdmins can reset the passwords of others.</td>
	</tr>
	<tr>
			<td width="250" align="center"><a href="<?=ADMIN_PATH?>admin_edit.php">Edit Administrator Data</a></td>
			<td>Edit Admin data such as first, last & email here.  SuperAdmins can edit the Privilege levels of others.</td>
	</tr>
	<tr>
        <td align="center" colspan="2"><b><?=$_SESSION['FirstName']?></b> is currently logged in as <b><?=$_SESSION['Privilege']?></b> <a href="<?=ADMIN_PATH?>admin_logout.php" title="Don't forget to Logout!">Logout</a></td>
	</tr>
</table>
<?php
get_footer();
?>
