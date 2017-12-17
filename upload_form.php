<?php
/**
 * upload_form.php takes id number from view page, and creates form for uploading a new image. 
 * 
 * This page requires a loaded querystring, with an id number of an item passed to it. Form  
 * will submit image to be uploaded, and ID to upload_image.php, which processes the upload.
 * 
 * @package nmUpload
 * @author Bill Newman <williamnewman@gmail.com>
 * @version 2.1 2015/12/07
 * @link http://www.newmanix.com/
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @see upload_execute.php
 * @todo Identify the return page more specifically - don't rely on referer
 */
 
require 'includes/config.php';

$title = "Image Upload";

/**
 * You can declare the allowable file size here.
 * 100000 is 100K, for example.
 */
$sizeBytes = 100000; # bytes max file size

/**
 * If true, will create thumbnail in same upload directory
 * 
 * Pass as a string, so can be placed in hidden form field 
 */
$createThumb = "TRUE";  

/**
 * Declared width of thumbnail
 * Height calculated from there
 */
$thumbWidth = 50;

/**
 * Declared suffix of thumbnail.  
 * if use '_thumb', and image prefix is 'm', file name would be: m1_thumb.jpg
 */
$thumbSuffix = "_thumb"; 

/**
 * Folder for upload.
 */
$uploadFolder = "uploads/"; # Physical path added to uploadFolder info in upload_execute.php

/**
 * unique prefix to add to your image name.
 */
$imagePrefix = "sponsor";

/**
 * image extension - currently only supporting .jpg - see upload_execute.php
 */
$extension = ".jpg";

//END CONFIG AREA ---------------------------------------------------------- 

/**
 * Session protects including page
 * Only administrators are allowed to view this page
 * Level is defined in constant, ACCESS, above
 * @see admin_only_inc.php 
 */ 
require_once INCLUDE_PATH . 'admin_only_inc.php';
#the loadHead variable allows us to add JS or CSS into the <head> tag inside header_inc.php
$config->loadhead = 
'
<script language="JavaScript">
function checkForm(thisForm)
{
	if(thisForm.FileToUpload.value=="")
	{
		alert("Please select a file to upload");
		thisForm.FileToUpload.focus();
		return false;	
	}else{
		document.getElementById("mySubmit").disabled=true;
		document.getElementById("mySubmit").value="Uploading, please wait...";
		return true;	
	}
}
</script> 
';

get_header();
?>
<h3 align="center"><?=$title?></h3>

<?php
# check variable of item passed in on querystring
if(isset($_REQUEST['id'])){$myID = (int)$_REQUEST['id'];}else{$myID = 0;}

#over-ride info sent from form, if sent - uses $_REQUEST instead of $_GET or $_POST
if(isset($_REQUEST['imagePrefix'])){$imagePrefix = $_REQUEST['imagePrefix'];}
if(isset($_REQUEST['uploadFolder'])){$uploadFolder = $_REQUEST['uploadFolder'];}
if(isset($_REQUEST['extension'])){$extension = $_REQUEST['extension'];}
if(isset($_REQUEST['createThumb'])){$createThumb = $_REQUEST['createThumb'];}
if(isset($_REQUEST['thumbWidth'])){$thumbWidth = $_REQUEST['thumbWidth'];}
if(isset($_REQUEST['thumbSuffix'])){$thumbSuffix = $_REQUEST['thumbSuffix'];}
if(isset($_REQUEST['sizeBytes'])){$sizeBytes = $_REQUEST['sizeBytes'];}
$size = $sizeBytes/1000; # divide by 1000, use KB 

if($myID > 0)
{//show table
?>
<p align="center">
<table border="1" align="center" width="50%" style="border-collapse:collapse">
	<tr>
		<td align="center">OLD IMAGE:</td>
		<td align="center"><img src="<?php echo $config->virtual_path . '/' . $uploadFolder . $imagePrefix . $myID . $extension; ?>" /></td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<form name="myForm" action="<?php echo $config->virtual_path; ?>/upload_execute.php" method="post" enctype="multipart/form-data" onsubmit="return checkForm(this);">
				Browse an Image to Upload: <i>(file must be <?php echo $size; ?>KB or less.)</i><br />
				<input type="file" name="FileToUpload" id="FileToUpload" /><br />
				<input type="hidden" name="myID" value="<?php echo $myID; ?>" />
				<input type="hidden" name="imagePrefix" value="<?php echo $imagePrefix; ?>" />
				<input type="hidden" name="uploadFolder" value="<?php echo $uploadFolder; ?>" />
				<input type="hidden" name="extension" value="<?php echo $extension; ?>" />
				<input type="hidden" name="createThumb" value="<?php echo $createThumb; ?>" />
				<input type="hidden" name="thumbWidth" value="<?php echo $thumbWidth; ?>" />
				<input type="hidden" name="thumbSuffix" value="<?php echo $thumbSuffix; ?>" />
				<input type="hidden" name="sizeBytes" value="<?php echo $sizeBytes; ?>" />
				<input type="hidden" name="returnPage" value="<?php echo $_SERVER["HTTP_REFERER"]; ?>" />
				<br />
				<input type="Submit" value="Upload File" id="mySubmit" />
			</form>
			<p align="center"><a href="javascript:history.go(-1)">EXIT WITHOUT UPLOAD</a></p>
		</td>
	</tr>
</table>
</p>
<?php
}else{
	echo '<p align="center"><h4>No Such Image</h4></p>
		<p align="center"><a href="javascript:history.go(-1)">EXIT</a></p>';	
}

get_footer();
?>
