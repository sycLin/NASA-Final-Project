<?php

session_start();

function print_header() {
	echo "<html><head><title>webMS: Welcome Back!</title></head>";
}

function print_body() {
	echo "<body>";
	echo "<h1>webMS</h1><hr>";
	echo "<p>Login Succeeded!</p>";
	echo "<p>Hello, ".$_SESSION['Username'];
	echo "</body></html>";
}



print_header();
print_body();


?>
