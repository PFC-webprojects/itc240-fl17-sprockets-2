<?php 
	include './includes/config.php';
	get_header();
?>

<?php
	if (isset($_GET['id'])  &&  0 < (int) $_GET['id']) {
		$id  =  (int) $_GET['id'];
	}
	else {
		header("Location:" . LIST_PAGE);
	}

	$sql = "SELECT * FROM SkiAreas WHERE SkiAreaID = " . $id . ";";
	
	$iConn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die(myerror(__FILE__, __LINE__, mysqli_connect_error()));
	$skiAreas = mysqli_query($iConn, $sql) or die(myerror(__FILE__, __LINE__, mysqli_error($iConn)));  //  $skiAreas gets stored in the web server, not the database server

	if (0 < mysqli_num_rows($skiAreas))	{
		$skiArea = mysqli_fetch_assoc($skiAreas);

		if (($latitudeAbsolute = $skiArea['Latitude'])  <  0) {
			$latitudeAbsolute *= -1;
			$latitudeSuffix = "° S";
		}
		else {
			$latitudeSuffix = "° N";
		}

		$anchorOpenExternal = $anchorCloseExternal = '';
		if ($skiArea['Website'] !== NULL) {
			$anchorOpenExternal   =  '<a href="' . $skiArea['Website'] . '" target="_blank">';
			$anchorCloseExternal  =  '</a>';
		}
		
		$skiAreaName = $skiArea['SkiAreaName'];
		if ($skiArea['SkiAreaNameArticle']) {
			$skiAreaName  =  'the ' . $skiAreaName;
		}

		if ($skiArea['TopElevation'] !== NULL) {
			$topElevation  =  ' and rises up to ' . $skiArea['TopElevation'] . ' m.';
		}
		else {
			$topElevation  =  '.';
		}
			
		echo '
			<h3>Ski Areas of the World:&nbsp; ' . $anchorOpenExternal . $skiArea['SkiAreaName'] . $anchorCloseExternal . '</h3>
			<p>Located at ' . $latitudeAbsolute . $latitudeSuffix . ' in the ' . $skiArea['Region'] . ' ' . $skiArea['CountryPreposition'] . ' ' . $skiArea['Country'] . ', ' . $skiAreaName . ' starts at ' . $skiArea['BaseElevation'] . ' m above sea level' . $topElevation . '<p>
			<hr />
			<img class="img-fluid" src="' . SKI_IMAGES_FOLDER . $skiArea['Photo'] . '" alt="' . $skiArea['SkiAreaName'] . '" title="' . $skiArea['SkiAreaName'] . '" />
		';

		if (($article = $skiArea['Article'])  !==  NULL) {
			echo '
				<hr />
				<div class="navbar">
					<p>Curious?&nbsp; Find out more at <a href="' . $article . '" target="_blank">' . $article . '</a> .</p>
				</div>
			';
		}

		echo '
			<hr />
			<div>
				<p>And now for an image from one of our sponsors!</p>
				<p><img src="uploads/sponsor' . $id . '.jpg" alt="Sponsor Image" title="Sponsor Image" /></p>
			</div>
		';

		
		if(startSession() && isset($_SESSION["AdminID"])) {  # only admins can see 'peek a boo' link:
			echo '<p align="center"><a href="' . $config->virtual_path . '/upload_form.php?' . $_SERVER['QUERY_STRING'] . '">UPLOAD NEW IMAGE</a></p>';
			/*
			# if you wish to overwrite any of these options on the view page, 
			# you may uncomment this area, and provide different parameters:						
			echo '<div align="center"><a href="' . VIRTUAL_PATH . 'upload_form.php?' . $_SERVER['QUERY_STRING']; 
			echo '&imagePrefix=customer';
			echo '&uploadFolder=upload/';
			echo '&extension=.jpg';
			echo '&createThumb=TRUE';
			echo '&thumbWidth=50';
			echo '&thumbSuffix=_thumb';
			echo '&sizeBytes=100000';
			echo '">UPLOAD IMAGE</a></div>';
			*/						
		}
		
		if(isset($_GET['msg'])) {  # msg on querystring implies we're back from uploading new image
			$msgSeconds = (int)$_GET['msg'];
			$currSeconds = time();
			if(($msgSeconds + 2)> $currSeconds)	{  //link only visible once, due to time comparison of qstring data to current timestamp
				echo '<p align="center"><script type="text/javascript">';
				echo 'document.write("<form><input type=button value=\'IMAGE UPLOADED! CLICK TO REFRESH PAGE!\' onClick=history.go()></form>")</scr';
				echo 'ipt></p>';
			}
		}
		
	}
	else {  //no records
		echo '<h3>What!&nbsp; No ski areas?&nbsp; There must be a mistake!!</h3>';
	}
?>

	<hr />
	<p><a href="ski-areas_list.php">Back</a></p>
	
<?php
	@mysqli_free_result($result); #releases web server memory.  The @ symbol tells PHP to squelch warnings.
	@mysqli_close($iConn); #close connection to database
?>

<?php get_footer() ?>
	