<?php
/**
 * upload_execute.php takes ID number & image file sent via upload_form.php, and 
 * replaces image associated with the current ID number. 
 * 
 * This page requires a loaded querystring, with an id number of an item passed to it. Form  
 * will submit image to be uploaded, and ID to uploadImage.php, which processes the upload.
 * The checkFileType() function in the common_inc.php is used to validate file extensions.
 *
 * All default settings below can be over-written by a submitting form.
 *
 * Global Variable $_FILES is used in PHP 4.x
 * $_FILES['upload']['size'] ==> Returns the Size of the File in Bytes.
 * $_FILES['upload']['tmp_name'] ==> Returns the Temporary Name of the File.
 * $_FILES['upload']['name'] ==> Returns the Actual Name of the File.
 * $_FILES['upload']['type'] ==> Returns the Type of the File.
 *
 * @package nmUpload
 * @author Bill Newman <williamnewman@gmail.com>
 * @version 2.1 2015/12/07
 * @link http://www.newmanix.com/
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @see checkFileType()
 * @see upload_form.php 
 * @todo expand to add .gif support
 * @todo allow inc file to add DB updates, file names, sizes, etc.
 * @todo check if determining correctly if writeable or not.  Might be checking for writeable via PHP, not public
 */
 


/**
 * Default setting: comma separated string of acceptable file types.
 * Works with checkFileType() function in common_inc.php
 * Examples of (image) file types below:
 *
 *<code>
 * $file_types = "image/pjpeg,image/jpeg,image/gif,image/x-png";
 *</code>
 *
 * To add to acceptable file types, attempt to upload invalid file types, 
 * then make a note of the extension, and add it to the list.
 *
 * NOTE: These are only default values, and represent the WIDEST variety of files that
 * may be uploaded.  The form may make it's own limitations via the $_POST['extension']
 * variable.
 *
 * @see checkFileType()
 * @see common_inc.php 
 */
$file_types = "image/pjpeg,image/jpeg"; # currently we only accepts jpegs!

//END CONFIG AREA ---------------------------------------------------------- 

require 'includes/config.php';
/**
 * Session protects including page
 * Only administrators are allowed to view this page
 * Level is defined in constant, ACCESS, above
 * @see admin_only_inc.php 
 */ 
require_once INCLUDE_PATH . 'admin_only_inc.php';

# check variable of item passed in - if invalid data, forcibly redirect back to view or index page 
if(isset($_POST['myID']) && (int)$_POST['myID'] > 0){#proper data must be on querystring
	 $myID = (int)$_POST['myID']; #Convert to integer, will equate to zero if fails
}else{
	if(!isset($redirect)){$redirect = $config->virtual_path . "/index.php";}
	header('Location:' . $redirect);
    die;
}

# retrieve over-rides from upload_form.php page, if applicable - otherwise safe defaults are provided
if(isset($_POST['sizeBytes'])){$sizeBytes = (int)$_POST['sizeBytes'];}else{$sizeBytes = "100000";}
if(isset($_POST['imagePrefix'])){$imagePrefix = $_POST['imagePrefix'];}else{$imagePrefix = "";} 
if(isset($_POST['uploadFolder'])){$uploadFolder = $_POST['uploadFolder'];}else{$uploadFolder = "uploads/";}
if(isset($_POST['createThumb'])){$createThumb = $_POST['createThumb'];}else{$createThumb = "FALSE";}
if(isset($_POST['thumbWidth'])){$thumbWidth = $_POST['thumbWidth'];}else{$thumbWidth = "50";}
if(isset($_POST['thumbSuffix'])){$thumbSuffix = $_POST['thumbSuffix'];}else{$thumbSuffix = "_thumb";}

/**
 * Indicate exact path to file upload here. 
 * Folder must have write capability, either 0775, or 0777
 * You may not want this to be your actual images directory. Note the path must include a last "/".
 *
 * PHYSICAL PATH is path to root space of server, and set in config_inc.php
 */
$uploadFolder = $config->physical_path . '/' . $uploadFolder;


# determine acceptable extensions for file upload - convert to formats used by PHP upload
if(!empty($_POST['extension']))
{# if not passed by form, use defaults
	$aExtensions = explode(",",$_POST['extension']);
	if(is_array($aExtensions))
	{#comma separated set of extensions
		$fileString = "";
		for($x = 0; $x < count($aExtensions); $x++)
		{
		    $file_types = extension2fileType($aExtensions[$x]);
		    if($fileString == ""){$fileString .= extension2fileType($aExtensions[$x]);}else{$file_types .= "," . extension2fileType($aExtensions[$x]);}
		    $extension = $aExtensions[$x];
		}	
	}else{#only one extension sent
		$file_types = extension2fileType($aExtensions);
		$extension = $_POST['extension']; //single extension (non-array) attach to doc	
	}
} 

$FileName = $imagePrefix . $myID . $extension;  //create name of file to be uploaded

$aErrors = array(); //init.  If error message is loaded (array length > 0) errors occurred!

//identify the page where the admin will return once upload is complete
if(isset($_POST['returnPage'])){$returnPage = $_POST['returnPage'];}else{$returnPage = $config->virtual_path . '/index.php';}

//check if the directory exist or not.
if (!is_dir("$uploadFolder")){$aErrors[] = "The directory <b>" . $uploadFolder . "</b> doesn't exist.";}

//check if the directory is writable.
if (count($aErrors)==0 && !is_writeable("$uploadFolder"))
{
   $aErrors[] = "Unable to write to directory: <b>" . $uploadFolder . "</b>. Change directory permissions: try 0755";
}
//not determining correctly if writeable or not.  Might be checking for writeable via PHP, not public

//Check first if a file has been sent via HTTP POST. Returns false otherwise.
if (count($aErrors)==0 && is_uploaded_file($_FILES['FileToUpload']['tmp_name']))
{
	$size = $_FILES['FileToUpload']['size'];  //Get the Size of the File
	if (count($aErrors)==0 && $size > $sizeBytes) //Make sure that $size is acceptable
	{
		$aErrors[] = "The File you tried to upload is <b>" . $size . "</b>K. Maximum file size: <b>" . $sizeBytes . "</b>K. Please upload a smaller file.";
	}

	if (count($aErrors)==0 && !checkFileType($file_types,$_FILES['FileToUpload']['type'])) 
	{//Make sure file is of allowable file types
		$aErrors[] = "File you tried to upload is <b>" . $_FILES['FileToUpload']['type'] . "</b>. This file type is not currently allowed.";
	}

	//move_filetoupload_file('filename','destination') Moves file to directory
	if (count($aErrors)==0 && move_uploaded_file($_FILES['FileToUpload']['tmp_name'],$uploadFolder.$FileName)){
		if($createThumb == "TRUE")
		{//create thumbnail in same folder, add thumbSuffix
			$tempImage = ImageCreateFromJPEG($uploadFolder.$FileName);  //copy to temporary image
			$width=ImageSx($tempImage); // Original picture width
			$height=ImageSy($tempImage); // Original picture height
			$thumbHeight = floor( $height * ( $thumbWidth / $width ) ); // calculate proper thumbnail height
			$newimage=imagecreatetruecolor($thumbWidth,$thumbHeight); //create new blank image
			imageCopyResampled($newimage,$tempImage,0,0,0,0,$thumbWidth,$thumbHeight,$width,$height);  //copy to thumb
			ImageJpeg($newimage,$uploadFolder . $imagePrefix . $myID . $thumbSuffix . $extension);  //create thumbnail in same directory
		}
		
		//append timestamp to qstring to print temporary reload() message
		$mySeconds = time();
		
		//redirect to item page so new file can be seen
		feedback("Image Uploaded Successfully!<br /> (If you see old image, click button below pic)","notice");
        
        header('Location:' . $returnPage . "&msg=" . $mySeconds);
        die;

	}else{
		//Print error
		$aErrors[] = "Unable to upload file.";
	}
}else{
	$aErrors[] = "File not sent via POST.";	
}

if(count($aErrors)>0)
{
	get_header();
	echo '<h2 align="center">Upload Error</h2>';
	echo '<p align="center">Please view the following upload error(s)</p><ol>';
	for($x = 0; $x < count($aErrors); $x++)
	{
	    echo "<li>" . $aErrors[$x] . "</li>";
	}
	echo '</ol><p align="center"><a href="' . $returnPage . '">BACK</a></p>';
	get_footer();
}

/**
 * Checks a comma separated string of acceptable file types set by 
 * an administrator in upload pages (upload_execute.php, upload_form.php) 
 * to be certain an accepted file type is currently being uploaded.
 *
 * PHP upload file types can be derived by using extension2fileType() function.
 *
 *<code>
 * $file_types = "image/pjpeg,image/jpeg,image/gif,image/x-png";
 * $validFileType = checkFileType($file_types,$_FILES['FileToUpload']['type']);
 *</code>
 *
 * To add to acceptable file types, attempt to upload invalid file types, 
 * then make a note of the extension, and add it to the list.
 *
 * Added with nmUpload Package
 *
 * @see upload_execute.php 
 * @see upload_form.php
 * @uses extension2fileType   
 *  
 * @param str $file_types comma separated string of acceptable files to upload
 * @param str $currentFileType file type of the current file being uploaded 
 * @return boolean true if passes filetype test
 */
function checkFileType($file_types,$currentFileType)
{
	$aFiles = explode(",",$file_types); //create aFiles file type array:
	for($x=0; $x < count($aFiles); $x++)
	{
		if($aFiles[$x]==$currentFileType){return true;}
	}
	return false; // no match found, return false!
}#end checkFileType()

/**
 * Converts natural file extension into file type required for 
 * PHP upload.
 *
 * Works with checkFileType() function to identify valid file types, as 
 * declared by administrators
 *
 *<code>
 * $fileType = extension2fileType(".jpg");
 *</code>
 *
 * To add to acceptable file type, attempt to upload INVALID file types, 
 * then make a note of the file_type/extension, and add it to the switch below.
 *
 * Added with nmUpload Package
 *
 * @see upload_execute.php 
 * @see upload_form.php 
 *  
 * @param string $extension natural extension of a file
 * @return string PHP upload file type of the current file
 */
function extension2fileType($extension)
{
	$file_types = ""; #init
	switch(strtolower($extension))
	{
		case ".jpg":
		case ".jpeg":
			$file_types = "image/pjpeg,image/jpeg"; 
			break;
		case ".gif":
			$file_types = "image/gif"; 
			break;
		case ".png":
			$file_types = "image/x-png"; 
			break;
		case ".doc":
			$file_types = "application/msword"; 
			break;
		case ".zip":
			$file_types = "application/x-zip-compressed"; 
			break;
		case ".pdf":
			$file_types = "application/pdf"; 
			break;
		case ".xls":
			$file_types = "application/vnd.ms-excel"; 
			break;
		case ".mp3":
			$file_types = "audio/mpeg"; 
			break;
		case ".txt":
			$file_types = "text/plain"; 
			break;	
		case ".htm":
		case ".html":
			$file_types = "text/html"; 
			break;
		case ".wma":
			$file_types = "audio/x-ms-wma"; 
			break;											
		default:
			$file_types = "image/pjpeg,image/jpeg"; //default to .jpg	
	}
	return $file_types; # return matching file type!
}#end extension2fileType()

