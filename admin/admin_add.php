<?php
/**
 * admin_add.php is a single page web application that adds an administrator 
 * to the admin database table
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
$title = 'Add Administrator'; #Fills <title> tag 
//END CONFIG AREA ----------------------------------------------------------

$access = "superadmin"; #superadmin or above can add new administrators
include_once INCLUDE_PATH . 'admin_only_inc.php'; #session protected page - level is defined in $access var

if (isset($_POST['Email']))
{# if Email is set, check for valid data
	if(!onlyEmail($_POST['Email']))
	{//data must be valid email	
		feedback("Data entered for email is not valid", "error");
		header('Location:' . ADMIN_PATH . THIS_PAGE);
        die;
	}
		
	if(!onlyAlphaNum($_POST['PWord1']))
	{//data must be alphanumeric or punctuation only	
		feedback("Password must contain letters and numbers only.","error");
		header('Location:' . ADMIN_PATH . THIS_PAGE);
        die;
	}
    
     $params = array('FirstName','LastName','PWord1','Email','Privilege');#required fields
    if(!required_params($params))
    {//abort - required fields not sent
        feedback("Data not entered/updated. (error code #" . createErrorCode(THIS_PAGE,__LINE__) . ")","error");
        header('Location:' . ADMIN_PATH . THIS_PAGE);
        die;	    
    }

	$iConn = @mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME) or die(myerror(__FILE__,__LINE__,mysqli_connect_error()));

	$FirstName = dbIn($_POST['FirstName'],$iConn);
    $LastName = dbIn($_POST['LastName'],$iConn);
    $AdminPW = dbIn($_POST['PWord1'],$iConn);
    $Email = strtolower(dbIn($_POST['Email'],$iConn));
    $Privilege = dbIn($_POST['Privilege'],$iConn);

	#sprintf() function allows us to filter data by type while inserting DB values.
	$sql = sprintf("INSERT into " . PREFIX . "Admin (FirstName,LastName,AdminPW,Email,Privilege,DateAdded) VALUES ('%s','%s',SHA('%s'),'%s','%s',NOW())",
            $FirstName,$LastName,$AdminPW,$Email,$Privilege);
    
    # insert is done here
	@mysqli_query($iConn,$sql) or die(myerror(__FILE__,__LINE__,mysqli_error($iConn)));
	
	# feedback success or failure of insert
	if (mysqli_affected_rows($iConn) > 0){
		feedback("Administrator Added!","notice");
	}else{
	 	feedback("Administrator NOT Added!", "error");
	}
	get_header();
	echo '
		<p><h1>Add Administrator</h1></p>
		<p align="center"><a href="' . ADMIN_PATH . THIS_PAGE . '">Add More</a></p>
		<p align="center"><a href="' . ADMIN_PATH . 'admin_dashboard.php">Exit To Admin</a></p>
		';	
	get_footer();
}else{ //show form - provide feedback
	$loadhead .= '
	<script type="text/javascript" src="' . $config->virtual_path . 'includes/util.js"></script>
	<script type="text/javascript">
			function checkForm(thisForm)
			{//check form data for valid info
				if(empty(thisForm.FirstName,"Please Enter Administrator\'s First Name")){return false;}
				if(empty(thisForm.LastName,"Please Enter Administrator\'s Last Name")){return false;}
				
				if(!isEmail(thisForm.Email,"Please enter a valid Email Address")){return false;}
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
	echo '
	<h1>Add New Administrator</h1>
	<p align="center">Be sure to write down the password!!</p>
	<form action="' . ADMIN_PATH . THIS_PAGE . '" method="post" onsubmit="return checkForm(this);">
	<table align="center">
		<tr>
			<td align="right">First Name</td>
			<td>
				<input type="text" autofocus required name="FirstName" />
				<font color="red"><b>*</b></font>
			</td>
		</tr>
		<tr>
			<td align="right">Last Name</td>
			<td>
				<input type="text" required name="LastName" />
				<font color="red"><b>*</b></font>
			</td>
		</tr>
		<tr>
			<td align="right">Email</td>
			<td>
				<input type="email" required name="Email" />
				<font color="red"><b>*</b></font>
			</td>
		</tr>
	   <tr>
	   		<td align="right">Privilege:</td>
	   		<td>
	   	';	

			
            $iConn = @mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME) or die(myerror(__FILE__,__LINE__,mysqli_connect_error()));
            $privileges = getENUM(PREFIX . 'Admin','Privilege',$iConn); #grab all possible 'Privileges' from ENUM
			echo returnSelect("select","Privilege",$privileges,"",$privileges,",");
		echo '
	   		</td>
	   </tr>
	   <tr>
	   		<td align="right">Password</td>
	   		<td>
	   			<input type="password" name="PWord1" />
	   				<font color="red"><b>*</b></font> 
	   				<em>(6-20 alphanumeric chars)</em>
	   		</td>
	   	</tr>
	   <tr>
	   		<td align="right">Re-enter Password</td>
	   		<td>
	   			<input type="password" name="PWord2" />
	   			<font color="red"><b>*</b></font>
	   		</td>
	   </tr>
	   <tr>
	   		<td align="center" colspan="2">
	   			<input type="submit" value="Add-Min!" />
	   			<em>(<font color="red"><b>*</b> required field</font>)</em>
	   		</td>
	   	</tr>
	</table>    
	</form>
	<p align="center"><a href="' . ADMIN_PATH . 'admin_dashboard.php">Exit To Admin Page</a></p>
	';
    
    @mysqli_free_result($result);
    @mysqli_close($iConn);
	
    get_footer();
}

?>
