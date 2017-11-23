<?php 
	include './includes/config.php';
	include './includes/header.php';
?>

<?php	
	//  daily php code goes here, from the coffee list
	define("TODAY", date('l'));
	
	if (isset($_GET['day'])) {
		$day = $_GET['day'];
	}
	else {
		$day = TODAY;
	}

	
	
	switch($day){
	
		case 'Monday':
			$coffee = 'Plain Milk';
			$pic = 'plain-milk.jpg';
			$color = '#E0DACC';
			$description = ".  Let's face it:  We're not ready for anything stronger than this on a $day.  Sorry!";
		break;

		case 'Tuesday':
			$coffee = 'Peanut Butter Coffee';
			$pic = 'peanut-butter-coffee.jpg';
			$color = '#C88962';
			$description = ".  Why not get your daily allowance of protein at the same time as your daily dose of caffeine?  Call it a two-for-one coffee -- on Two-fer $day, of course!";
		break;

		case 'Wednesday':
			$coffee = 'Tootsie Roll Coffee';
			$pic = 'tootsie-roll-coffee.jpg';
			$color = '#221C14';
			$description = ".  Sorry, but it's $day.  We can't afford actual chocolate until we get paid at the end of the week.  But just you watch us as we melt nine fun-size Tootsie Rolls into every cup of coffee!";
		break;
		
		case 'Thursday':
			$coffee = 'Brown Crayon';
			$pic = 'brown-crayon.jpg';
			$color = '#4C1F0C';
			$description = ", which is so easy to customize to your taste.  If it's too weak, bring it back to us.  We'll just add another crayon!  << Sigh. >>  $day is just one day before payday!";
		break;

		case 'Friday':
			$coffee = 'Fried Egg';  //  Get the details for this from the original.
			$pic = 'fried-egg.jpg';
			$color = '#EA930C';
			$description = ".  $coffee, did you say?  Yes, that's right!  Picture a nice, hot, steaming bowl of coffee, only instead of having whipped cream floating on the top -- how about a freshly-reheated fried egg floating on top?  It's $coffee $day!  Our special treat to you on our payday!";
		break;

		case 'Saturday':
			$coffee = 'Reconstituted Hot Apple Cinders';
			$pic = 'reconstituted-hot-apple-cinders.jpg';
			$color = '#993710';
			$description = ".  It's $day!  Hooray!  Who needs caffeine on a such a bright and happy $day?  But you still want that char-broiled taste?  So try our $coffee!";
		break;

		case 'Sunday':
			$coffee = 'Iced Coffee';
			$pic = 'iced-coffee.jpg';
			$color = '#D49A45';
			$description = ", which is great on a hot $day!";
		break;

		default:
			$coffee = 'Drip';  //  Always provide a default, just in case.
			$pic = 'drip-coffee.jpg';
			$color = '#060604';
			$description = ".  $coffee, $coffee, $coffee.  Ho hum.  What a $coffee.  We're out of everything else today.  That's what $day is always like around here.  Sorry!";

	}

$title = $alt = "Coffee Special for $day: $coffee";


?>

	<div class="modal-open">
		<div class="py-lg-4">
			<img src="images/<?=$pic?>" alt="<?=$alt?>" title="<?=$title?>"/>
		</div>
		<div>
			<p style="white-space:pre-wrap"><span class="text-expanded text-uppercase"><strong><?=$day?>'s</strong></span> daily disappointment at the So Sorry Coffee Company is <strong><?=$coffee?></strong><?=$description?></p>
			<p>Curious about what our daily disappointments are on the other days of the week?&nbsp; Have a look through the links below!</p>
		</div>
	</div>
		
	<hr />
	
    <div class="navbar navbar-expand-lg navbar-light bg-faded py-lg-4">
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav mx-auto">
            <li class="nav-item px-lg-1">
              <a class="nav-link text-uppercase text-expanded" href="?day=<?=TODAY?>"><strong>Today</strong></a>
            </li>
            <li class="nav-item px-lg-1">
              <a class="nav-link text-uppercase text-expanded" href="?day=Monday">Monday</a>
            </li>
            <li class="nav-item px-lg-1">
              <a class="nav-link text-uppercase text-expanded" href="?day=Tuesday">Tuesday</a>
            </li>
            <li class="nav-item px-lg-1">
              <a class="nav-link text-uppercase text-expanded" href="?day=Wednesday">Wednesday</a>
            </li>
            <li class="nav-item px-lg-1">
              <a class="nav-link text-uppercase text-expanded" href="?day=Thursday">Thursday</a>
            </li>
            <li class="nav-item px-lg-1">
              <a class="nav-link text-uppercase text-expanded" href="?day=Friday">Friday</a>
            </li>
            <li class="nav-item px-lg-1">
              <a class="nav-link text-uppercase text-expanded" href="?day=Saturday">Saturday</a>
            </li>
            <li class="nav-item px-lg-1">
              <a class="nav-link text-uppercase text-expanded" href="?day=Sunday">Sunday</a>
            </li>
            <li class="nav-item px-lg-1">
              <a class="nav-link text-uppercase text-expanded" href="?day=Every Holiday">Holiday</a>
            </li>
          </ul>
        </div>
	</div>

	<hr />
	




<?php include './includes/footer.php' ?>