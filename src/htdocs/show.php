<html>

<?php

function print_header() {
	print "<head>";
	print "</head>";
}

function print_body() {
	print "<body>";
	print "<h1>Hello World!</h1>";
	print_LoginForm();
	echo "<br />";
	echo "<br /> <br /><br /><br />";
/*	$data = file_get_contents("http://127.0.0.1/cgi-bin/ssh_connect.cgi", 0);
	echo $data;
	echo "<br /> <br /><br /><br />";
*/
	print "</body>";
}

function print_LoginForm() {
	print "<p>Please enter your CSIE account.</p>";
	print "<form action='/cgi-bin/ssh_connect.cgi' method='post'>";
	print "Username(student ID): <input type='text' name='id'><br />";
	print "Password: <input type='password' name='password'><br />";
	print "<input type='submit' value='Show!'>";
}

print_header();
print_body();


?>


</html>
