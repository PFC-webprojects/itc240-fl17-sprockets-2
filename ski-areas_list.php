<?php 
	require 'includes/config.php'; #provides configuration, pathing, error handling, db credentials 
	require 'includes/Pager.php'; #allows pagination 

	$config->loadhead .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';
	get_header();
?>

<h3>Ski Areas of the World</h3>
<p>Why not combine your love of skiing with your taste for something exotic?&nbsp; Ski where you never thought possible!&nbsp; Yes, these are all lift-served ski areas, no helicopter or hiking necessary.&nbsp; Check out some of these links below.&nbsp; Adventure awaits!</p>
<hr />

<?php	
	$prev = '<i class="fa fa-arrow-left" aria-hidden="true"></i>';
	$next = '<i class="fa fa-arrow-right" aria-hidden="true"></i>';


	$sql = "SELECT SkiAreaID, SkiAreaName, NationalFlag, Country FROM SkiAreas ORDER BY SkiAreaName Asc";

	$iConn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die(myerror(__FILE__, __LINE__, mysqli_connect_error()));

	$myPager = new Pager(8,'',$prev,$next,'');
	$sql = $myPager->loadSQL($sql,$iConn);  #load SQL, pass in existing connection, add offset

	$skiAreas = mysqli_query($iConn, $sql) or die(myerror(__FILE__, __LINE__, mysqli_error($iConn)));  //  $skiAreas gets stored in the web server, not the database server

	if (mysqli_num_rows($skiAreas) > 0)  //  at least one record!
	{//show results
		echo '<p>' . $myPager->showTotal() . ' ' . ($myPager->showTotal() == 1 ? 'ski area' : 'ski areas') . ' found</p>';

		echo $myPager->showNAV('<p align="center">', '</p>
			<br />');  //  show pager if enough records 

	
		echo '<table class="table-sm table-striped">
			';
		$tableRowNumber = 0;
		while ($skiArea = mysqli_fetch_assoc($skiAreas))
		{	
			$title 					  =  'National Flag of ' . $skiArea['Country'];
			$anchorOpen				  =  '<a href = "' . VIEW_PAGE . "?id=" . $skiArea['SkiAreaID'] . '">';
			$anchorClose			  =  '</a>';

			$skiAreaNameCell          =  '<td>' . $anchorOpen . $skiArea['SkiAreaName'] . $anchorClose . '</td>
				';
			$skiAreaNationalFlagCell  =  '<td>' . $anchorOpen . '<img src="';
			$skiAreaNationalFlagCell .=  SKI_IMAGES_FOLDER . $skiArea['NationalFlag'] . '" alt="' . $title . '" title="' . $title . '" />' . $anchorClose . '</td>
				';

			$emptyCell				  =  '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
			$skiAreaThumbnailCell	  =  '<td>' . $anchorOpen . '<img src="' . $config->virtual_path . '/uploads/sponsor' . dbOut($skiArea['SkiAreaID']) . '_thumb.jpg" alt="Sponsor Image" title="Sponsor Image" />' . $anchorClose . '</td>
				';
				
			echo '<tr>
				';
			if ($tableRowNumber++ % 2  ===  0) {
				echo $skiAreaNationalFlagCell;
				echo $skiAreaNameCell;
			}
			else {
				echo $skiAreaNameCell;
				echo $skiAreaNationalFlagCell;
			}
			
			echo $emptyCell;
			echo $skiAreaThumbnailCell;
			echo '</tr>
				';
		}
		echo '</table>
			';

		echo $myPager->showNAV('<br />
			<p align="center">', '</p>');  //  show pager if enough records 
			
	}else{//no records
		echo '<h3>What!&nbsp; No ski areas?&nbsp; There must be a mistake!!</h3>';
	}

	@mysqli_free_result($result); #releases web server memory.  The @ symbol tells PHP to squelch warnings.
	@mysqli_close($iConn); #close connection to database
?>


<?php get_footer() ?>
	