<?php 
	include './includes/config.php';
	include './includes/header.php';
?>

	
<?php  //  main
	if (isset($_POST["message"])) {
		$notification_e_mail 	= "Peter.Caliandro@seattlecolleges.edu";
		$organization			= "ITC 240 " . $config->banner;
		$headers				= "From: noreply@pfc-webprojects.zone"
									. PHP_EOL . "X-Mailer: PHP/" . phpversion()
									. PHP_EOL . "MIME-Version: 1.0"
									. PHP_EOL . "Content-type: text/html; charset=iso-8859-1";
		
		$first_name 			= clean_post("firstname");
		$last_name 				= clean_post("lastname");
		$confirmation_e_mail 	= clean_post("email");
		$message 				= clean_post("message");
		
		if (send_notification($first_name, $organization, $last_name, $confirmation_e_mail, $message, $headers, $notification_e_mail) && 
				send_confirmation($first_name, $organization, $confirmation_e_mail, $message, $headers)) {
			show_confirmation($first_name, $last_name, $confirmation_e_mail, $message);
		}
		else {
			show_form($first_name, $last_name, $confirmation_e_mail, $message, true);
		}
	}
	else {
		show_form("", "", "", "", false);
	}
?>



<?php  // functions
	
	function send_confirmation($first_name, $organization, $confirmation_e_mail, $message, $headers) {
		$subject = "Confirmation of Form Submission at " . $organization;
	
		$text = '
			<html> 
				<body>
					<h3>Thank you' . ($first_name == "" ? "" : ", ") . $first_name . '.&nbsp; Your message below has been sent.&nbsp; We will reply to your
						message shortly.</h3>
					<div style="margin:2em 2em 3em 2em">
						<p style="white-space:pre-wrap">' . $message . '</p>
					</div>
					<h3>' . $organization . '</h3>
				</body>
			</html>
		';
		
		return mail($confirmation_e_mail, $subject, $text, $headers);
	}
	
	function send_notification($first_name, $organization, $last_name, $confirmation_e_mail, $message, $headers, $notification_e_mail) {
		$subject = "Notification of Form Submission for " . $organization;
		
		$text = '
			<html>
				<body>
					<h3>
						' . ($first_name == "" ? "" : $first_name . " ") .
						($last_name  == "" ? "" : $last_name . " ") .
						($first_name == "" && $last_name == "" ? "" : "at ") . $confirmation_e_mail . ' writes:
					</h3>
					<div style="margin:2em 2em 3em 2em">
						<p style="white-space:pre-wrap">' . $message . '</p>
					</div>
				</body>
			</html>
		';
		
		$headers .= PHP_EOL . "Reply-To: " . $confirmation_e_mail;
		
		return mail($notification_e_mail, $subject, $text, $headers);
	}

	function show_confirmation($first_name, $last_name, $confirmation_e_mail, $message) {
		show_page_heading("E-Mail Confirmation" .
			($first_name == "" && $last_name == "" ? "" : " for ") . 
			($first_name == "" ? "" : $first_name . " ") .
			($last_name == "" ? "" : $last_name . " "));

		echo '
			<div class="form-group col-lg-12">
				<p class="text-heading">Thank you.&nbsp; Your message below has been sent.&nbsp; We will reply to you at ' . $confirmation_e_mail . ' shortly.</p>
				<p class="text-heading"></p>
				<p class="text-heading" style="white-space:pre-wrap">' . $message . '</p>
				<p class="text-heading"></p>
			</div> <!-- form-group col-lg-12 -->

			<div class="form-group col-lg-12">
				<a class="btn btn-secondary" href="">Back</a> <!-- class="btn btn-secondary" makes an anchor look just like a button or an input of type="submit"! -->
			</div> <!-- form-group col-lg-12 -->
		';
	}
	
	function show_form($first_name, $last_name, $confirmation_e_mail, $message, $previous_attempt_failed) {
		show_page_heading("Contact Form");

		echo '
			<form action="" method="post">

				<div class="row">
				
					<div class="form-group col-lg-4">
						<label class="text-heading">First Name</label>
						<input type="text" name="firstname" class="form-control" value="' . $first_name . '" autofocus required />
					</div> <!-- form-group col-lg-4 -->
						
					<div class="form-group col-lg-4">
						<label class="text-heading">Last Name</label>
						<input type="text" name="lastname" class="form-control" value="' . $last_name . '" required />
					</div> <!-- form-group col-lg-4 -->

					<div class="form-group col-lg-4">
						<label class="text-heading">E-Mail Address</label>
						<input type="email" name="email" class="form-control" value="' . $confirmation_e_mail . '" required />
					</div> <!-- form-group col-lg-4 -->

					<div class="form-group col-lg-12">
						<label class="text-heading">Your Message</label>
						<textarea name="message" class="form-control" required>' . $message . '</textarea>
					</div> <!-- form-group col-lg-12 -->

					<div class="form-group col-lg-4">
						<input type="submit" class="btn btn-secondary" value="Send Message" />
					</div> <!-- form-group col-lg-4 -->
		';

		if ($previous_attempt_failed) {
			echo '
					<div class="form-group col-lg-8">
						<p class="text-heading" style="color:red">Sorry, an error occurred.&nbsp; Please review your information and submit the form again.</p>
					</div> <!-- form-group col-lg-8 -->
			';
		}
		
		echo '
				</div> <!-- row -->
				
			</form>
		';
	}

	
	function show_page_heading($page_heading){
		echo '
			<hr class="divider">
			<h2 class="text-center text-lg text-uppercase my-0">' . $page_heading . '</h2>
			<hr class="divider">
		';
	}
	
	function clean_post($key) {
		if (isset($_POST[$key])) {
			return strip_tags(trim($_POST[$key]));
		}
		else {
			return "";
		}
	}
	
?>



<?php include './includes/footer.php' ?>
