<?php
//  common.php:  Stores our favorite procedures for use throughout the website.



function myerror($myFile, $myLine, $errorMsg)
{
    if(defined('DEBUG') && DEBUG)
    {
       echo "Error in file: <b>" . $myFile . "</b> on line: <b>" . $myLine . "</b><br />";
       echo "Error Message: <b>" . $errorMsg . "</b><br />";
       die();
    }else{
        echo "I'm sorry, we have encountered an error.  Would you like to buy some socks?";
        die();
    }
}

/*
 loads header include file from theme folder 

*/


function dbOut($str)
{
	if($str!=""){$str = stripslashes(trim($str));}//strip out slashes entered for SQL safety
	return $str;
} #End dbOut()



function get_header($file=''){
    global $config;
    static $header_loaded = false;
    if($file == ''){//load included file
        if(!$header_loaded)
        {//header loads first time
            $file = 'header.php'; 
            $header_loaded = true;
        }else{
            $file = 'footer.php';
        }
           
    }
    
    $file = $config->physical_path . '/themes/' . $config->theme . '/' . $file;
    
    if(file_exists($file)){
        include $file;
    }else{
        myerror(__FILE__,__LINE__,'include file not found: ' . $file);
    }
    
    
}#end get_header()

function get_footer()
{
    global $config;
    get_header();
}#get_footer() is same function, run second time with footer file


/*
Allows us to send an email that respects the server domain spoofing polices of 
hosts like DH.

$response = safeEmail($to, $subject, $message, $replyTo,'html');

if($response)
{
    echo 'hopefully HTML email sent!<br />';
}else{
   echo 'Trouble with HTML email!<br />'; 
}

*/
function safeEmail($to, $subject, $message, $replyTo = '',$contentType='text')  //  The fourth and fifth parameters assign default values. 
{
    $fromAddress = "Automated Email <noreply@" . $_SERVER["SERVER_NAME"] . ">";

    if(strtolower($contentType)=='html')
    {//change to html format
        $contentType = 'Content-type: text/html; charset=iso-8859-1';
    }else{//default is text
        $contentType = 'Content-type: text/plain; charset=iso-8859-1';
    }
    
    $headers[] = "MIME-Version: 1.0";//optional but more correct
    //$headers[] = "Content-type: text/plain; charset=iso-8859-1";
    $headers[] = $contentType;
    //$headers[] = "From: Sender Name <sender@domain.com>";
    $headers[] = 'From: ' . $fromAddress;
    //$headers[] = "Bcc: JJ Chong <bcc@domain2.com>";
    
    if($replyTo !=''){//only add replyTo if passed
        //$headers[] = "Reply-To: Recipient Name <receiver@domain3.com>";
        $headers[] = 'Reply-To: ' . $replyTo;   
    }
    
    $headers[] = "Subject: {$subject}";
    $headers[] = "X-Mailer: PHP/". phpversion();
    
    //collapse all header data into a string with operating system safe
    //carriage returns - PHP_EOL
    $headers = implode(PHP_EOL,$headers);

    //use mail() command internally and pass back the feedback
    return mail($to, $subject, $message, $headers);

}//end safeEmail()



/**
 * Filters data per MySQL standards before entering database. 
 *
 * Adds slashes and helps prevent SQL injection per MySQL standards.    
 * Function enclosed in 'wrapper' function to add further functionality when 
 * as vulnerabilities emerge.
 *
 * @param string $var data as entered by user
 * @param object $myConn active mysqli DB connection, passed by reference.
 * @return string returns data filtered by MySQL, adding slashes, etc.
 * @see dbIn() 
 * @todo none
 */
function dbIn($var,&$iConn)  //  A wrapper function so that we don't have to type mysqli_real_escape_string() every time, and in case a new version of this gets published in the future.
{
	if(isset($var) && $var != "")
	{
		return mysqli_real_escape_string($iConn,$var);  //  
	}else{
		return "";
	}
	
} #End dbIn()

/**
 * requires POST or GET params or 
 * redirect, etc. back to calling form or 
 * safe page
 *
* <code>
* $params = array('last_name','first_name','email_name');#required fields to register	
* 
* if(!required_params($params))
* {//abort - required fields not sent
*		feedback("Data not entered/updated. (error code #" . createErrorCode(THIS_PAGE,__LINE__) . ")","error");
*		myRedirect(GMAA_PATH . 'index.php');
* 	die();
* }
* </code>
 *
 * @param array names of all POST/GET required fields
 * @return void
 * @see gmaa-input.php
 * @todo none 
 */

function required_params($params) {
	foreach($params as $param) {
		if(!isset($_REQUEST[$param])) {
			return false;
		}
	}
	return true;
}#end required_params()

/**
 * wrapper function for PHP session_start(), to prevent 'session already started' error messages. 
 *
 * To view any session data, sessions must be explicitly started in PHP.  
 * In order to use sessions in a variety of INC files, we'll check to see if a session 
 * exists first, then start the session only when necessary.
 *
 * 
 * @return void
 * @todo none 
 */
function startSession()
{
	//if(!isset($_SESSION)){@session_start();}
	if(isset($_SESSION))
	{
		return true;
	}else{
		@session_start();
	}
	if(isset($_SESSION)){return true;}else{return false;}
} #End startSession()

/**
 * loads a quick user message (flash/heads up) to provide user feedback
 *
 * Uses a Session to store the data until the data is displayed via showFeedback() loaded 
 * inside the bottom of header_inc.php (or elsewhere) 
 *
 * <code>
 * feedback('Flash!  This is an important message!'); #will show up next running of showFeedback()
 * </code>
 * 
 * added version 2.07
 *
 * @param string $msg message to show next time showFeedback() is invoked
 * @return none 
 * @see showFeedback() 
 * @todo none
 */

#flash message is a temporary message sent to the user
#load it here and show it one time when showFeedback() is called
function feedback($msg,$level="warning")
{
	startSession();
	$_SESSION['feedback'] = $msg;
	$_SESSION['feedback-level'] = $level;

}#end feedback()

/**
 * shows a quick user message (flash/heads up) to provide user feedback
 *
 * Uses a Session to store the data until the data is displayed via showFeedback()
 *
 * Related feedback() function used to store message 
 *
 * <code>
 * echo showFeedback(); #will show then clear message stored via feedback()
 * </code>
 * 
 * changed from showFeedback() version 2.10
 *
 * @param none 
 * @return string html & potentially CSS to style feedback
 * @see feedback() 
 * @todo none
 */
function showFeedback()
{
	startSession();//startSession() does not return true in INTL APP!
	
	$myReturn = "";  //init
	if(isset($_SESSION['feedback']) && $_SESSION['feedback'] != "")
	{#show message, clear flash
		$myReturn .= 
			'
			<style type="text/css">
			p.feedback {  /* default style for div */
				border: 1px solid #000;
				margin:auto;
				width:100%;
				text-align:center;
				font-weight: bold;
			}
		
			p.error {
			  color: #000;
			  background-color: #ee5f5b; /* error color */
			}
		
			p.warning {
			  color: #000;
			  background-color: #f89406; /* warning color */
			}
		
			p.notice {
			  color: #000;
			  background-color: #5bc0de; /* notice color */
			}
			
			p.success {
			  color: #000;
			  background-color: #62c462; /* notice color */
			}
			</style>
			';
	
		if(isset($_SESSION['feedback-level'])){$level = $_SESSION['feedback-level'];}else{$level = 'warning';}
		$myReturn .= '<p class="feedback ' . $level . '">'  . $_SESSION['feedback'] . '</p>';
		$_SESSION['feedback'] = ""; #cleared
		$_SESSION['feedback-level'] = "";
		return $myReturn; //data passed back for printing
		
	}
}#end showFeedback()

/**
 * Create an error code out of the file name and line number of our error
 *
 * Will make upper case, strip out the vowels and create an 
 * error of the file name (minus extension & vowels) + "x" + line number of error
 *
 * Will also replace any underscores with "x". This file would be:
 *
 * Example: CNFGxNCx41
 * 
 * The above would be the example for this file, plus an error at line 41
 * This allows a user to report an error that identifies it, without compromising site security
 *
 * @param string $myFile file name provided by PHP error handler
 * @param string $myLine line number of error provided by PHP error handler
 * @return string File name and line number of error disguised vaguely as an error code
 * @see printUserError() 
 * @todo none
 */
function createErrorCode($myFile,$myLine)
{
	$mySlash = strrpos($myFile,"/"); //find position of last slash in path
	$myFile = substr($myFile,$mySlash + 1);  //strip off all but file name
	$myFile = substr($myFile, 0, strripos($myFile, '.'));//remove extension
	$myFile = strtoupper($myFile); //change to upper case
	$vowels = array("A", "E", "I", "O", "U", "Y");  //array of vowels to remove
	$myFile = str_replace($vowels, "", $myFile); //remove vowels
	$myFile = str_replace("_", "x", $myFile); //replace underscore with "x"
	return $myFile . "x" . $myLine;  //CNFGNCx50
}# End createErrorCode()

/**
 * Checks for email pattern using PHP regular expression.  
 *
 * Returns true if matches pattern.  Returns false if it doesn't.   
 * It's advised not to trust any user data that fails this test.
 *
 * @param string $str data as entered by user
 * @return boolean returns true if matches pattern.
 * @todo none
 */
function onlyEmail($myString)
{
  if(preg_match("/^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_\-\.]+\.[a-zA-Z0-9_\-]+$/",$myString))
  {return true;}else{return false;}
}#end onlyEmail()

/**
 * Checks data for alphanumeric characters using PHP regular expression.  
 *
 * Returns true if matches pattern.  Returns false if it doesn't.   
 * It's advised not to trust any user data that fails this test.
 *
 * @param string $str data as entered by user
 * @return boolean returns true if matches pattern.
 * @todo none
 */
function onlyAlphaNum($myString)
{
  if(preg_match("/[^a-zA-Z0-9]/",$myString))
  {return false;}else{return true;} //opposite logic from email?
}#end onlyAlphaNum()

/**
 * Checks data for numeric characters using PHP regular expression.  
 *
 * Returns true if matches pattern.  Returns false if it doesn't.   
 * It's advised not to trust any user data that fails this test.
 *
 * @param string $str data as entered by user
 * @return boolean returns true if matches pattern.
 * @todo none
 */
function onlyNum($myString)
{
  if(preg_match("/[^0-9]/",$myString))
  {return false;}else{return true;} //opposite logic from email?
}#end onlyNum()

/**
 * Checks data for alphanumeric characters using PHP regular expression.  
 *
 * Returns true if matches pattern.  Returns false if it doesn't.   
 * It's advised not to trust any user data that fails this test.
 *
 * @param string $str data as entered by user
 * @return boolean returns true if matches pattern.
 * @todo none
 */
function onlyAlpha($myString)
{
  if(preg_match("/[^a-zA-Z]/",$myString))
  {return false;}else{return true;} //opposite logic from email?  
}#end onlyAlpha()

/**
 * getENUM retrieves an array of all possible choices in a MySQL ENUM
 *
 * Using an ENUM allows us to avoid an extra link-table of limited choices
 *
 * regex version update from: http://barrenfrozenwasteland.com/index.php?q=node/7
 *
 * <code>
 * $privileges = getENUM(PREFIX . 'Admin','Privilege',$iConn); #grab all possible 'Privileges' from ENUM
 * </code>
 */
function getENUM($table,$column,&$iConn)
{
	$sql = "SHOW COLUMNS FROM $table LIKE '$column'";
	$result = @mysqli_query($iConn,$sql) or die(myerror(__FILE__,__LINE__,mysqli_error($iConn)));
	$enum = mysqli_fetch_object($result);
	preg_match_all("/'([\w ]*)'/", $enum->Type, $values);
    @mysqli_free_result($result);
	return $values[1];
}# end getENUM()

/**
 * Creates and pre-loads radio, checkbox & options from passed delineated strings
 *
 * Pass arrays, or strings of data for value, label and database match to the function 
 * and identify if you wish to create a select option, or a set of 
 * radio buttons or checkboxes.
 *
 * Form elements will be 'pre-loaded' with database values ($dbStr) so a 
 * user can change their selection, or see their original choice. 
 * 
 * <code>
 * $valuStr = "1,2,3,4,5";
 * $dbStr = "1,2,5";  
 * $lblStr = "chocolate,bananas,nuts,caramel,butterscotch";
 * $attribs = 'id="blah"'; //attribs added to element
 * echo returnSelect("checkbox","Toppings",$valuStr,$dbStr,$lblStr,",");
 * </code>
 *
 * @param string $elType type of input element created, 'select', 'radio' or 'checkbox'
 * @param string $elName name of element
 * @param string/array $valuArray delimiter separated string of values to choose
 * @param string/array $dbArray delimiter separated string of DB items to match
 * @param string/array $lblArray delimiter separated string of labels to view
 * @param string $char delimiter, default is comma
 * @param string $attribs allows placement of attribute to element, ID, class, JS hook
 * @param string $alignment elements can be placed horizontally or vertically 
 * @return string elements pre-loaded with data
 * @todo none
 */
function returnSelect($elType,$elName,$valuArray,$dbArray,$lblArray,$char=',',$attribs='', $alignment ='horizontal')
{
if(!is_array($valuArray)){$valuArray = explode($char,$valuArray);}//if not array, blow it up!	
if(!is_array($dbArray)){$dbArray = explode($char,$dbArray);}  //db values
if(!is_array($lblArray)){$lblArray = explode($char,$lblArray);}  //labels identify
if($attribs!=''){$attribs = ' ' . $attribs;} //add space before attribs
$myReturn = ''; //init	
$x = 0; $y = 0; $sel = "";//init stuff
   switch($elType)
   {
   case "radio":
   case "checkbox":
        for($x=0;$x<count($valuArray);$x++)
        {
             for($y=0;$y<count($dbArray);$y++)
             {
                   if($valuArray[$x]==$dbArray[$y])
                   {
                        $sel = ' checked="checked"';
                   }
             }//y for
              if($alignment == 'horizontal')
			  {//elements are side by side
				  $myReturn .= '<input type="' . $elType . '" name="' . $elName . '" value="' . $valuArray[$x] . '"' . $sel . $attribs .'>' . $lblArray[$x] . ' &nbsp; &nbsp;&nbsp;' . PHP_EOL; 
			  }else{//stack radio/checkboxes vertically
				$myReturn .= '<input type="' . $elType . '" name="' . $elName . '" value="' . $valuArray[$x] . '"' . $sel . $attribs .'>' . $lblArray[$x] . '<br />' . PHP_EOL;
			  }
		 $sel = '';
        }//x for
        break;
   case "select":
	$myReturn .= '<select name="' . $elName . '"' . $attribs .'>';
        for($x=0;$x<count($valuArray);$x++)
        {
             for($y=0;$y<count($dbArray);$y++)
             {
                   if($valuArray[$x]==$dbArray[$y])
                   {
                       $sel = ' selected="selected"';
                   }
             }//y for
              $myReturn .= '<option value="' . $valuArray[$x] . '"' . $sel . '>' . $lblArray[$x] . '</option>' . PHP_EOL;
	      $sel = '';
        }//x for
        $myReturn .= '</select>';
        break;
   }
   return $myReturn;
}#end returnSelect()