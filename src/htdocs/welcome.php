<?php

include("config.php");
session_start();


/* print the header of the html page */
function print_header() {
	echo "<html><head><title>webMS: Welcome!</title></head>";
}

/* print the body of the html page */
/* LRflag: 0 => L, 1 => R */
/* Errflag: 0 => none, 1 => Err */
function print_body($LRflag, $Errflag) {
	echo "<body><h1>webMS</h1><hr>";
	if($Errflag == 1) {
		echo "<p>Invalid Username and/or Password!</p>";
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
	echo "<form action='' method='POST'>";
	echo "<label>Username:</label><input type='text' name='username'><br />";
	echo "<label>Password:</label><input type='password' name='password'><br />";	
	echo "<input type='submit' value='LogIn'>";
	echo "</form><hr>";
	echo "<p>Not a member yet?</p>";
	echo "<form action='' method='GET'><input type='submit' name='entrance' value='Register Here'></form>";
}

/* print the register form */
function print_register_form() {
	echo "<form action='' method='POST'>";
	echo "<label>Username:</label><input type='text' name='username'><br />";
	echo "<label>E-mail:</label><input type='text' name='email'><br />";
	echo "<input type='submit' value='Register'>";
	echo "</form><hr>";
	echo "<p>Already a member?</p>";
	echo "<form action='' method='GET'><input type='submit' name='entrance' value='LogIn Here'></form>";
}

// check login information, returns 1 on success; return 0 when fails.
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

// handler for GET and POST
if($_POST) {
	// gotta handle DATABASE here
	if(isset($_POST['email'])) { // it's REGISTER form
		print_header();
		echo "<body><h1>it's register form</h1><hr>";
		echo "<p>Sorry, registration not allowed currently.</p></body></html>";
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


