<?php

session_start();

function print_header() {
	print "<html><head><title>test.php</title></head>";
}

function print_body() {
	print "<body><h1>Testing</h1>";
	print_login_form();
	print "</body></html>";
}

function print_login_form() {
	print "<p>Please log in</p>";
	print "<form method='post'>";
	print "Username: <input type='text' name='username'>";
	print "<br />";
	print "Password: <input type='password' name='password'>";
	print "<input type='submit' value='Log In'>";
	print "</form>";
}

if($_POST) {
	print_header();
	print_body();
	// register session variables
	$_SESSION['Username'] = $_POST['username'];
	$_SESSION['Password'] = $_POST['password'];
	/*
	print "<script language='javascript'>";
	print "function t(){alert('test');} t();";
	print "</script>";
	*/
	header('location: show.php');
} else {
	print_header();
	print_body();
}


?>
