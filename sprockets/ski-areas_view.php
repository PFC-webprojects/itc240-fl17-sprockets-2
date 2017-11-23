<?php 
	include './includes/config.php';
	include './includes/header.php';
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
			
		
	}
	else {  //no records
		echo '<h3>What!&nbsp; No ski areas?&nbsp; There must be a mistake!!</h3>';
	}
?>

	
<?php
	@mysqli_free_result($result); #releases web server memory.  The @ symbol tells PHP to squelch warnings.
	@mysqli_close($iConn); #close connection to database
?>

<?php include './includes/footer.php' ?>
	