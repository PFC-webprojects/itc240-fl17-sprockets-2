<?php
/**
 * admin_reset.php allows an administrator to reset (reselect) a password 
 *
 * Because passwords are encrypted via the MySQL encrpyption SHA() method, 
 * we can't recover them, so we instead create new ones.
 *
 * As of v 2.21, requires $nav1 to be name of nav element from config file
 * 
 * @package nmAdmin
 * @author Bill Newman <williamnewman@gmail.com>
 * @version 2.21 2015/12/07
 * @link http://www.newmanix.com/
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

require '../includes/config.php'; #provides configuration, pathing, error handling, db credentials 
$title = 'Edit Administrator'; #Fills <title> tag 
//END CONFIG AREA ----------------------------------------------------------

$access = "admin"; #admin can reset own password, superadmin can reset others
include_once INCLUDE_PATH . 'admin_only_inc.php'; #session protected page - level is defined in $access var

# Read the value of 'action' whether it is passed via $_POST or $_GET with $_REQUEST
if(isset($_REQUEST['act'])){$myAction = (trim($_REQUEST['act']));}else{$myAction = "";}
switch ($myAction) 
{//check for type of process
	case "edit": //2) show password change form
	 	editDisplay($nav1);
	 	break;
	case "update": //3) change password, feedback to user
		updateExecute($nav1);
		break; 
	default: //1)Select Administrator
	 	selectAdmin($nav1);
}


function selectAdmin($nav1='')
{//Select administrator
	
	if($_SESSION["Privilege"] == "admin")
	{#redirect if logged in only as admin
		header('Location:' . ADMIN_PATH . THIS_PAGE . "?act=edit");
        die;
	}

	$loadhead='
	<script type="text/javascript" src="' . $config->virtual_path . 'include/util.js"></script>
	<script type="text/javascript">
			function checkForm(thisForm)
			{//check form data for valid info
				if(empty(thisForm.AdminID,"Please Select an Administrator.")){return false;}
				return true;//if all is passed, submit!
			}
	</script>';
	get_header();
	echo '<h1 align="center">Reset Administrator Password</h1>';
	if($_SESSION["Privilege"] != "admin")
	{# must be greater than admin level to have  choice of selection
		echo '<p align="center">Select an Administrator, to reset their password:</p>';
	}
	echo '<form action="' . ADMIN_PATH . THIS_PAGE . '" method="post" onsubmit="return checkForm(this);">';
	
    $iConn = @mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME) or die(myerror(__FILE__,__LINE__,mysqli_connect_error()));
	
    $sql = "select AdminID,FirstName,LastName,Email,Privilege,LastLogin,NumLogins from " . PREFIX . "Admin";
	
    if($_SESSION["Privilege"] == "admin")
	{# limit access to the individual, if admin level
		$sql .= " where AdminID=" . $_SESSION["AdminID"];
	}
	
    $result = @mysqli_query($iConn,$sql) or die(myerror(__FILE__,__LINE__,mysqli_error($iConn)));
	
    if (mysqli_num_rows($result) > 0)//at least one record!
	{//show results
		echo '
		<form action="' . ADMIN_PATH . THIS_PAGE . '" method="post" onsubmit="return checkForm(this);">
		<table align="center" border="1" style="border-collapse:collapse" cellpadding="3" cellspacing="3">
			<tr>
				<th>AdminID</th>
				<th>Admin</th>
				<th>Email</th>
				<th>Privilege</th>
			</tr>
		';
		
        while ($row = mysqli_fetch_array($result))
		{//dbOut() function is a 'wrapper' designed to strip slashes, etc. of data leaving db
		     echo '
			     <tr>
					<td>
						<input type="radio" required name="AdminID" value="' . dbOut($row['AdminID']) . '">'
							. dbOut($row['AdminID']) . '
					</td>
					<td>' . dbOut($row['FirstName']) . ' ' . dbOut($row['LastName']) . '</td>
					<td>' . dbOut($row['Email']) . '</td>
			     	<td>' . dbOut($row['Privilege']) . '</td>
			     </tr>
		     ';
		}
		echo '
			<input type="hidden" name="act" value="edit" />
			<tr>
				<td align="center" colspan="4">
					<input type="submit" value="Choose Admin!" />
				</td>
			</tr>
		</table>
		</form>
		';	
	}else{//no records
      //put links on page to reset form, exit
      echo '<p align="center"><h3>Currently No Administrators in Database.</h3></p>';
	}
	 echo '<p align="center"><a href="' . ADMIN_PATH . 'admin_dashboard.php">Exit To Admin</a></p>';
	@mysqli_free_result($result); //free resources
    @mysqli_close($iConn);
	get_footer();
}

function editDisplay($nav1='')
{
	if($_SESSION["Privilege"] == "admin")
	{#use session data if logged in as admin only
		$myID = (int)$_SESSION['AdminID'];
	}else{
		if(isset($_POST['AdminID']) && (int)$_POST['AdminID'] > 0)
		{
		 	$myID = (int)$_POST['AdminID']; #Convert to integer, will equate to zero if fails
		}else{
			feedback("AdminID not numeric","error");
			header('Location:' . ADMIN_PATH . THIS_PAGE);
        die;
		}
	}
	$loadhead = '
	<script type="text/javascript" src="' . $config->virtual_path . 'include/util.js"></script>
	<script type="text/javascript">
			function checkForm(thisForm)
			{//check form data for valid info
				if(!isAlphanumeric(thisForm.PWord1,"Only alphanumeric characters are allowed for passwords.")){thisForm.PWord2.value="";return false;}
				if(!correctLength(thisForm.PWord1,6,20,"Password does not meet the following requirements:")){thisForm.PWord2.value="";return false;}
				if(thisForm.PWord1.value != thisForm.PWord2.value)
				{//match password fields
	   			alert("Password fields do not match.");
	   			thisForm.PWord1.value = "";
	   			thisForm.PWord2.value = "";
	   			thisForm.PWord1.focus();
	   			return false;
	   		}
				return true;//if all is passed, submit!
			}
	</script>
	';
	get_header();
	
    $iConn = @mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME) or die(myerror(__FILE__,__LINE__,mysqli_connect_error()));
	
    $sql = sprintf("select AdminID,FirstName,LastName,Email,Privilege from " . PREFIX . "Admin WHERE AdminID=%d",$myID);
	
    $result = @mysqli_query($iConn,$sql) or die(myerror(__FILE__,__LINE__,mysqli_error($iConn)));
	
    if(mysqli_num_rows($result) > 0)//at least one record!
	{//show results
		while ($row = mysqli_fetch_array($result))
		{//dbOut() function is a 'wrapper' designed to strip slashes, etc. of data leaving db
		     $Name = dbOut($row['FirstName']) . ' ' . dbOut($row['LastName']);
		     $Email = dbOut($row['Email']);
		     $Privilege = dbOut($row['Privilege']);
		}
	}else{//no records
      //put links on page to reset form, exit
      echo '
      	<p align="center"><h3>No such administrator.</h3></p>
      	<p align="center"><a href="'  . ADMIN_PATH . 'admin_dashboard.php">Exit To Admin</a></p>
      	';
	}
	echo '
	<h1>Reset Administrator Password</h1>
	<p align="center">
		Admin: <font color="red"><b>' . $Name . '</b></font> 
		Email: <font color="red"><b>' . $Email . '</b></font>
		Privilege: <font color="red"><b>' . $Privilege . '</b></font> 
	</p> 
	<p align="center">Be sure to write down password!!</p>
	<form action="' . ADMIN_PATH . THIS_PAGE . '" method="post" onsubmit="return checkForm(this);">
	<table align="center">
	   <tr>
		   	<td align="right">Password</td>
		   	<td>
		   		<input required type="password" name="PWord1" />
		   		<font color="red"><b>*</b></font> <em>(6-20 alphanumeric chars)</em>
		   	</td>
	   </tr>
	   <tr>
	   		<td align="right">Re-enter Password</td>
	   		<td>
	   			<input required type="password" name="PWord2" />
	   			<font color="red"><b>*</b></font>
	   		</td>
	   </tr>
	   <tr>
	   		<td align="center" colspan="2">
	   			<input type="hidden" name="AdminID" value="' .$myID . '" />
	   			<input type="hidden" name="act" value="update" />
	   			<input type="submit" value="Reset Password!" />
	   			<em>(<font color="red"><b>*</b> required field</font>)</em>
	   		</td>
	   	</tr>
	</table>    
	</form>
	<p align="center"><a href="' . ADMIN_PATH . 'admin_dashboard.php">Exit To Admin</a></p>
	';
	@mysqli_free_result($result); #free resources
    @mysqli_close($iConn);
	get_footer();
}

function updateExecute($nav1='')
{
    $params = array('AdminID','PWord1');#required fields
    if(!required_params($params))
    {//abort - required fields not sent
        feedback("Data not entered/updated. (error code #" . createErrorCode(THIS_PAGE,__LINE__) . ")","error");
        header('Location:' . ADMIN_PATH . THIS_PAGE);
        die;	    
    }

	if(isset($_POST['AdminID']) && (int)$_POST['AdminID'] > 0)
	{
	 	$AdminID = (int)$_POST['AdminID']; #Convert to integer, will equate to zero if fails
	}else{
		feedback("AdminID not numeric","warning");
		header('Location:' . ADMIN_PATH . THIS_PAGE);
        die;
	}
	
	if(!onlyAlphaNum($_POST['PWord1']))
	{//data must be alphanumeric or punctuation only	
		feedback("Data entered for password must be alphanumeric only");
		header('Location:' . ADMIN_PATH . THIS_PAGE);
        die;
	}
	$iConn = @mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME) or die(myerror(__FILE__,__LINE__,mysqli_connect_error())); 
	
    $AdminPW = dbIn($_POST['PWord1'],$iConn);
	
    # SHA() is the MySQL function that encrypts the password
	$sql = sprintf("UPDATE " . PREFIX . "Admin set AdminPW=SHA('%s') WHERE AdminID=%d",$AdminPW,$AdminID);

	@mysqli_query($iConn,$sql) or die(myerror(__FILE__,__LINE__,mysqli_error($iConn)));
	
	 //feedback success or failure of insert
	 if (mysqli_affected_rows($iConn) > 0)
	 {
		 feedback("Password Successfully Reset!","notice");
 	 }else{
		 feedback("Password NOT Reset! (or not changed from original value)");
	 }
    @mysqli_close($iConn);
	get_header();
	echo '
	<p align="center"><h3>Reset Administrator Password</h3></p>
	<p align="center"><a href="' . ADMIN_PATH . THIS_PAGE . '">Reset More</a></p>
	<p align="center"><a href="' . ADMIN_PATH . 'admin_dashboard.php">Exit To Admin</a></p>
	';
	get_footer();
}

