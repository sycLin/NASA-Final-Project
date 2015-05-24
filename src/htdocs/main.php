<?php

session_start();

/* set up some variables */
$machine_list = array(); // this is gonna be a 2D array!
$proc_type_list = array("all", "R", "S", "Z");
$sortedby_list = array("cpu", "memory", "totaltime");
$count_list = array("20", "50", "100", "200", "all");

/* lets get machine information from DB */
$query = "SELECT * FROM machines WHERE username=".$_SESSION['Username'];
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$tmp = array($row['mname'], $row['mhost'], $row['musername'], $row['mpassword']);
	array_push($machine_list, $tmp);
}

/* set up some variables for settings */
$_SESSION['current_machine'] = $machine_list[0];
$_SESSION['current_proc_type'] = $proc_type_list[0];
$_SESSION['current_sortedby'] = $sortedby_list[0];
$_SESSION['current_count'] = $count_list[0];

function print_header() {
	echo "<html><head><title>webMS: Welcome Back!</title></head>";
}

function print_body() {
	echo "<body>";
	echo "<h1>webMS</h1><hr>";
	// echo "<p>Login Succeeded!</p>";
	echo "<p>Hello, ".$_SESSION['Username']."<hr>";
	print_settings_form();
	echo "<hr>";
	global $machine_list;
	if(count($machine_list) == 0) { // this user has no machines that are setup.
		echo "<p>Sorry, You don't have any machines to be moniotored!</p>";
		echo "<p>Please go to the settings section to create one</p>";
	} else {
		print_process();
	}
	echo "</body></html>";
}

/* settings: which machines? process type? sortedby? count? */
function print_settings_form() {
	echo "<p>Filter Settings</p>";
	echo "<form action='' method='get' id='settings'>";
	// ----- print machine options ----- //
	echo "<label for=''>Machines:</label>";
	echo "<select name='machine' form_id='settings'>";
	global $machine_list;
	for($i = 0; $i < count($machine_list); $i = $i + 1) {
		$tmp = $machine_list[$i];
		if($tmp == $_SESSION['current_machine']) {
			echo "<option value='$tmp' selected='selected'>$tmp</option>";
		} else {
			echo "<option value='$tmp'>$tmp</option>";
		}
	}
	echo "</select>";
	// ----- print process type options ----- //
	echo "<label for=''>Process Type:</label>";
	echo "<select name='proc_type' form_id='settings'>";
	global $proc_type_list;
	for($i = 0; $i < count($proc_type_list); $i = $i + 1) {
		$tmp = $proc_type_list[$i];
		if($tmp == $_SESSION['current_proc_type']) {
			echo "<option value='$tmp' selected='selected'>$tmp</option>";
		} else {
			echo "<option value='$tmp'>$tmp</option>";
		}
	}
	echo "</select>";
	// ----- print sortedby options ----- //
	echo "<label for=''>Sorted By:</label>";
	echo "<select name='sortedby' form_id='settings'>";
	global $sortedby_list;
	for($i = 0; $i < count($sortedby_list); $i = $i + 1) {
		$tmp = $sortedby_list[$i];
		if($tmp == $_SESSION['current_sortedby']) {
			echo "<option value='$tmp' selected='selected'>$tmp</option>";
		} else {
			echo "<option value='$tmp'>$tmp</option>";
		}
	}
	echo "</select>";
	// ----- print count options ----- //
	echo "<label for=''>Count:</label>";
	echo "<select name='count' form_id='settings'>";
	global $count_list;
	for($i = 0; $i < count($count_list); $i = $i + 1) {
		$tmp = $count_list[$i];
		if($tmp == $_SESSION['current_count']) {
			echo "<option value='$tmp' selected='selected'>$tmp</option>";
		} else {
			echo "<option value='$tmp'>$tmp</option>'";
		}
	}
	echo "</select>";
	// ----- end of settings list, now let's have a submit button ----- //
	echo "<input type='submit' value='Go!'>";
	echo "</form>";
}

/* print process info according to the settings */
function print_process() {
	// lets test the variables
	echo "hello ".$_SESSION['current_machine']." ".$_SESSION['current_proc_type']." ".$_SESSION['current_sortedby']." ".$_SESSION['current_count'];
}


/* when the settings are changed */
if($_GET) {
	// set up settings variables
	$_SESSION['current_machine'] = $_GET['machine'];
	$_SESSION['current_proc_type'] = $_GET['proc_type'];
	$_SESSION['current_sortedby'] = $_GET['sortedby'];
	$_SESSION['current_count'] = $_GET['count'];
	print_header();
	print_body();
} else {
	print_header();
	print_body();
}

?>
