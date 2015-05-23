<?php

session_start();

/* set up some variables */
$machine_list = array(); // this is gonna be a 2D array!
$proc_type_list = array("R", "S", "Z");
$sortedby_list = array("cpu", "memory", "totaltime");
$count_list = array("20", "50", "100", "200", "all");

/* lets get machine information from DB */
$query = "SELECT * FROM machines WHERE username=".$_SESSION['Username'];
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$tmp = array($row['mname'], $row['mhost'], $row['musername'], $row['mpassword']);
	array_push($machine_list, $tmp);
}

function print_header() {
	echo "<html><head><title>webMS: Welcome Back!</title></head>";
}

function print_body() {
	echo "<body>";
	echo "<h1>webMS</h1><hr>";
	// echo "<p>Login Succeeded!</p>";
	echo "<p>Hello, ".$_SESSION['Username'];
	print_settings_form();
	echo "<br />";
	print_process();
	echo "</body></html>";
}

/* settings: which machines? process type? sortedby? count? */
function print_settings_form() {
	echo "<p>Display Settings</p>";
	echo "<form action='' method='get' id='settings'>";
	// print machine options
}

/* print process info according to the settings */
function print_process() {
	;
}



print_header();
print_body();


?>
