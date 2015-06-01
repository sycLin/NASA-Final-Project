<?php

include("config.php");
session_start();

/* for mailing function */
require './PHPMailer-master/PHPMailerAutoload.php';

/* print the header of the html page */
function print_header() {
	echo "<html><head>";
	echo "<title>webMS: Welcome!</title>";
	echo "<link rel=stylesheet type='text/css' href='welcome.css'>";
	echo "</head>";
}

/* print the body of the html page */
/* LRflag: 0 => L, 1 => R */
/* Errflag: 0 => none, 1 => Err */
function print_body($LRflag, $Errflag) {
	echo "<body><h1 id='header'><span>webMS</span></h1><hr>";
	if($Errflag == 1) {
		if($LRflag == 0) { // login error
			echo "<p class='warning'>Invalid Username and/or Password!</p>";
		} else { // registration error
			echo "<p class='warning'>This username is not available, please choose another one.</p>";
		}
	}
	if($LRflag == 0) { // login page
		print_login_form();
	} else { // register page
		print_register_form();
	}
	echo "</body></html>";
}

/* print the login form */
function print_login_form() {
	echo "<div>";
	echo "<form action='' method='POST'>";
	echo "<label>Username:</label><input type='text' name='username' placeholder='Your Username'><br />";
	echo "<label>Password:</label><input type='password' name='password' placeholder='Your Password'><br />";	
	echo "<label></label><input type='submit' class='button' value='Login'>";
	echo "</form><hr>";
	echo "<span>Not a member yet?&nbsp;";
	echo "<form class='complementary' action='' method='GET'><input type='submit' class='button' name='entrance' value='Register Here'></form>";
	echo "</span>";
	echo "</div>";
}

/* print the register form */
function print_register_form() {
	echo "<div>";
	echo "<form action='' method='POST'>";
	echo "<label>Username:</label><input type='text' name='username' placeholder='Pick a username!'><br />";
	echo "<label>E-mail:</label><input type='text' name='email' placeholder='Must enter a valid email!'><br />";
	/* since we're not gonna email the user his/her password, we have to let them enter their password */
	echo "<label>Password:</label><input type='password' name='password' placeholder='Pick a password!'><br />";
	echo "<label></label><input type='submit' class='button' value='Register'>";
	echo "</form><hr>";
	echo "<span>Already a member?&nbsp;";
	echo "<form class='complementary' action='' method='GET'><input type='submit' class='button' name='entrance' value='Login Here'></form>";
	echo "</span>";
	echo "</div>";
}

/* check login information, returns 1 on success; return 0 when fails. */
function check_login_info($u, $p) {
	global $db;
	// escape the string: for DB security
	$myusername = mysqli_real_escape_string($db, $u);
	$mypassword = mysqli_real_escape_string($db, $p);
	// query the DB
	$query = "SELECT * FROM acct WHERE username='$myusername' and password='$mypassword'";
	$result = mysqli_query($db, $query);
	// fetch info
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC); // actually not used.
	$count = mysqli_num_rows($result);
	if($count == 1) { // login succeeded!
		// set SESSION variables
		$_SESSION['Username'] = $myusername;
		$_SESSION['Password'] = $mypassword;
		return 1; 
	} else { // login failed!
		return 0;
	}
}

/* return 0 if the username isn't in DB; return 1 if already exists. */
function check_username_validity($u) {
	global $db;
	$username_to_check = mysqli_real_escape_string($db, $u);
	$query = "SELECT * FROM acct WHERE username='$username_to_check'";
	$result = mysqli_query($db, $query);
	$count = mysqli_num_rows($result);
	if($count == 0) {
		return 0;
	} else {
		return 1;
	}
}

/* return a randomized string with length specified in the parameter. */
function generateRandomString($length_you_want) {
	$charset = "abcdefghijklmnopqrstuvwxyz0123456789";
	$random_sting = "";
	for($i = 0; $i < $length_you_want; $i = $i + 1) {
		$tmp = rand(0, strlen($charset));
		$random_string .= $charset[$tmp];
	}
	return $random_string;
}


// handler for GET and POST
if($_POST) {
	// gotta handle DATABASE here
	if(isset($_POST['email'])) { // it's REGISTER form
		/*
		echo "<body><h1>it's register form</h1><hr>";
		echo "<p>Sorry, registration not allowed currently.</p></body></html>";
		*/
		// Step 1): check username validity: username must be unique.
		if(check_username_validity($_POST['username']) == 0) {
			// this username is available
			// Step 2): create randomized password for the user.
			// NO! LET THEM ENTER THEIR PASSWORD!
			// $rand_str = generateRandomString(8);
			// Step 3): insert to DB
			$username = $_POST['username'];
			$email = $_POST['email'];
			// $password = $rand_str;
			$password = $_POST['password'];
			$query = "INSERT into acct (username, password, email) values ('$username', '$password', '$email')";
			$dummy = mysqli_query($db, $query);
			// Step 4): send an email
			/* below is PHP built-in mail() function
			$subject = "Welcome to webMS, $username";
			$content = "Hello dear $username\nYour password is initialized as: ".$password."\n";
			$content .= "You can always change your password on our website anytime :)\n";
			$content .= "Thank you! Thanks for your interests in our services!\n";
			$tmp = mail("$email", "$subject", "$content");
			*/
			/* below is PHPMailer
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'supermonster8@gmail.com';
			$mail->Password = 'bostonmit';
			$mail->FromName = "webMS-service";
			$mail->From = "supermonster8@gmail.com";
			$mail->AddAddress("$email", "$username");
			$mail->AddReplyTo('supermonster8@gmail.com', 'no-reply');
			$mail->IsHTML(true);
			$mail->WordWrap = 80;
			$mail->Subject = 'Welcome to webMS, '.$username;
			$content = "Hello dear $username\nYour Password is initialized as: ".$password."\n";
			$content .= "You can always change your password on our website anytime :)\n";
			$content .= "Thank you! Thanks for your interests in our services!\n";
			$mail->Body = $content;
			$tmp = $mail->send();
			*/
			// Step 5): notification and redirection.
			print_header();
			/*
			if($tmp)
				echo "<script language='javascript'>alert('Registration succeeded, and password is sent to your email.');</script>";
			else
				echo "<script language='javascript'>alert('No! The email is not sent!!! QAQ');</script>";
			*/
			echo "<script language='javascript'>alert('Registration Complete! Congrats!');</script>";
			print_body(0, 0);
		} else {
			// this username is used, must choose another one.
			print_header();
			print_body(1, 1); // print error message and the register form again.
		}
	} else { // it's LOGIN form
		/*
		print_header();
		echo "<body><h1>it's login form</h1></body></html>";
		*/
		// check login info:
		//   if yes, header('location: xxxx.php').
		//   if no, print error message and the the form again.
		$tmp = check_login_info($_POST['username'], $_POST['password']);
		if($tmp == 1) { // login succeeded
			header("location: main.php");
		} else { // login failed
			print_header();
			print_body(0, 1);
		}
	}
} else if($_GET) {
	print_header();
	if($_GET["entrance"] == "Register Here") {
		print_body(1, 0); // print register form
	} else {
		print_body(0, 0); // print login form
	}
} else {
	print_header();
	print_body(0, 0);
}

?>


