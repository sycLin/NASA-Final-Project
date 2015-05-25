<?php

include("config.php");
session_start();

/* "VIEW" variable -> default to be "status". */
/* status: display process information (default) */
/* log: display the killed processes */
/* settings: display settings */
/* logout: the user wants to logout */
$_SESSION['view'] = NULL;


/* set up some variables */
$machine_list = array(); // this is gonna be a 2D array!
$proc_type_list = array("all", "R", "S", "Z");
$sortedby_list = array("cpu", "memory", "totaltime");
$count_list = array("10", "20", "50", "100", "200", "all");

/* lets get machine information from DB */
$query = "SELECT * FROM machines WHERE username='".$_SESSION['Username']."'";
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	array_push($machine_list, $row['mname']);
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
	echo "<p>Hello, ".$_SESSION['Username']."</p><hr>";
	print_menu();
	echo "<hr>";
	// print different things according to different views: STATUS, LOG, SETTINGS, LOGOUT
	if(!isset($_SESSION['view']) || $_SESSION['view'] == "status") {
		print_filter_form();
		echo "<hr>";
		global $machine_list;
		if(count($machine_list) == 0) { // this user has no machines that are setup.
			echo "<p>Sorry, You don't have any machines to be moniotored!</p>";
			echo "<p>Please go to the settings section to create one</p>";
		} else {
			print_process();
		}
	} else if($_SESSION['view'] == "log") {
		echo "<p>A Ha! You're now viewing LOG, but there's nothing to show you currently :P</p>";
	} else if($_SESSION['view'] == "settings") {
		if(isset($_GET['change_password'])) { // user wants to change his/her password
			echo "You want to change password?";
			// ----- print change password form ----- //
			print_change_password_form();
		} else if(isset($_GET['edit_machine'])) { // user wants to update machines
			echo "You want to change machines?";
			// ----- print change machine form ----- //
			$cmd = $_GET['edit_machine'];
			if($cmd[0] == 'U') { // you wanna update information of a certain machine
				;
			} else if($cmd[0] == 'A') { // you wanna add a machine
				;
			}
		} else if(isset($_SESSION['changepassword'])) { // user just tried to change his/her password
			if($_SESSION['changepassword'] == 0) { // he/she failed
				echo "<p>Failed to change your password.</p>";
			} else if($_SESSION['changepassword'] == 1) { // he/she succeeded
				echo "<p>Password Updated Successfully.</p>";
			}
			print_profile_settings();
			echo "<hr>";
			print_machine_settings();
			$_SESSION['changepassword'] = NULL;
		} else { // user is under normal SETTINGS view
			print_profile_settings();
			echo "<hr>";
			print_machine_settings();
		}
	} else if($_SESSION['view'] == "logout") {
		session_destroy();
		header("location: welcome.php");
	} else { // if you're here, ...., you're dying...
		echo "<p>hi baby, there's something seriously wrong! Please contact me: b01902044@ntu.edu.tw</p>";
	}
	echo "</body></html>";
}

/* VIEW: "status", "log", "settings", "logout" */
function print_menu() {
	echo "<form action='' method='get'>";
	echo "<input type='submit' name='changeview' value='Status'>";
	echo "<input type='submit' name='changeview' value='Log'>";
	echo "<input type='submit' name='changeview' value='Settings'>";
	echo "<input type='submit' name='changeview' value='Logout'>";
	echo "</form>";
}

/* settings: which machines? process type? sortedby? count? */
function print_filter_form() {
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
	echo "hello current => ".$_SESSION['current_machine']." ".$_SESSION['current_proc_type']." ".$_SESSION['current_sortedby']." ".$_SESSION['current_count'];
	// get machine information from DB
	global $db;
	$query = "SELECT * FROM machines WHERE username='".$_SESSION['Username']."' and mname='".$_SESSION['current_machine']."'";
	$result = mysqli_query($db, $query);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	// get machine: host, u, p
	$mh = $row['mhost'];
	$mu = $row['musername'];
	$mp = $row['mpassword'];
	// form the CGI request
	$cgi_request = "http://127.0.0.1/cgi-bin/ssh_connect.cgi?";
	$cgi_request .= "Host=".$mh;
	$cgi_request .= "&&Username=".$mu;
	$cgi_request .= "&&Password=".$mp;
	$cgi_request .= "&&settings_proc_type=".$_SESSION['current_proc_type'];
	$cgi_request .= "&&settings_sortedby=".$_SESSION['current_sortedby'];
	$cgi_request .= "&&settings_count=".$_SESSION['current_count'];
	$data = file_get_contents($cgi_request, 0);
	echo $data;
}

/* for "settings" VIEW */
function print_profile_settings() {
	// get profile from DB
	global $db;
	$query = "SELECT * FROM acct WHERE username='".$_SESSION['Username']."'";
	$result = mysqli_query($db, $query);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if($row['username'] != $_SESSION['Username'])
		echo "<script language='javascript'>alert('No! It's Wrong!');</script>";
	$u = $row['username'];
	$p = $row['password'];
	$em = $row['email'];
	// print profile: username, password, email.
	echo "<h2>My Profile</h2>";
	echo "<table border=1>";
	echo "<td>Username</td><td>".$u."</td><tr>";
	echo "<td>Password</td><td>you think I'm gonna display it here? you think I'm an idiot?</td><tr>";
	echo "<td>Email</td><td>".$em."</td><tr>";
	echo "</table>";
	// print button for changing password
	echo "<form action='' method='get'><input type='submit' name='change_password' value='Change Password'></form>";
}

/* for "settings" VIEW */
function print_machine_settings() {
	// get machine information from DB
	global $db;
	$query = "SELECT * FROM machines WHERE username='".$_SESSION['Username']."'";
	$result = mysqli_query($db, $query);
	// print machine info: name, host, username, password, update-button.
	echo "<h2>My Machines</h2>";
	echo "<table border=1>";
	echo "<td>Name</td><td>Host</td><td>Username</td><td>Password</td><td>Edit</td>";
	echo "<tr>";
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$mn = $row['mname'];
		$mh = $row['mhost'];
		$mu = $row['musername'];
		$mp = $row['mpassword'];
		echo "<td>".$mn."</td><td>".$mh."</td><td>".$mu."</td><td>".$mp."</td>";
		echo "<td><form action='' method='get'><input type='submit' name='edit_machine' value='Update $mn'></form>";
		echo "<tr>";
	}
	echo "</table>";
	// print button for adding machines
	echo "<form action='' method='get'>";
	echo "<input type='submit' name='edit_machine' value='Add Machine'>";
	echo "</form>";
}

/* print the form for users to change their password */
function print_change_password_form() {
	echo "<h2>Change ".$_SESSION['Username']."'s Password</h2>";
	echo "<form action='' method='POST'>";
	echo "<label for=''>Old Password</label><input type='password' name='old_password'><br />";
	echo "<label for=''>New Password</label><input type='password' name='new_password'><br />";
	echo "<input type='submit' name='change_password_submit' value='Confirm'>";
	echo "</form>";
}

/* update user's password in DB. Return 0 if fail; return 1 if success */
function change_password() {
	global $db;
	unset($_GET['change_password']);
	$u = $_SESSION['Username'];
	$op = $_POST['old_password'];
	$np = $_POST['new_password'];
	$op = mysqli_real_escape_string($db, $op);
	$np = mysqli_real_escape_string($db, $np);
	// first we check the correctness of $op
	$query = "SELECT * FROM acct WHERE username='$u'";
	$result = mysqli_query($db, $query);
	$count = mysqli_num_rows($result);
	if($count != 1) // FAIL: no such user? or username not unique?
		return 0;
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if($row['password'] != $op) // FAIL: old password not correct
		return 0;
	// now we can update the user's password
	$query = "UPDATE webMS.acct SET password='$np' WHERE acct.username='$u'";
	$dummy = mysqli_query($db, $query);
	return 1;
}


/* when the settings are changed */
if($_POST) {
	if(isset($_POST['change_password_submit'])) {
		$tmp = change_password();
		if($tmp == 1) { // changed successfully.
			$_SESSION['view'] = "settings";
			$_SESSION['changepassword'] = 1;
			print_header();
			print_body();
		} else if($tmp == 0) { // fail to change password.
			$_SESSION['view'] = "settings";
			$_SESSION['changepassword'] = 0;
			print_header();
			print_body();
		} else { // shouldn't be here!
			;
		}
	}
} else if($_GET) {
	if(isset($_GET['changeview'])) { // the user is changing view
		if($_GET['changeview'] == "Status") { // display process information
			$_SESSION['view'] = "status";
		} else if($_GET['changeview'] == "Log") { // display killed processes
			$_SESSION['view'] = "log";
		} else if($_GET['changeview'] == "Settings") { // change account settings
			$_SESSION['view'] = "settings";
		} else if($_GET['changeview'] == "Logout"){ // the user wants to log out
			$_SESSION['view'] = "logout";
		} else {
			echo "Shouldn't Be HERE!";
		}
		print_header();
		print_body();
	} else if(isset($_GET['proc_type'])) { // the user is under the STATUS view and change the settings
		$_SESSION['current_machine'] = $_GET['machine'];
		$_SESSION['current_proc_type'] = $_GET['proc_type'];
		$_SESSION['current_sortedby'] = $_GET['sortedby'];
		$_SESSION['current_count'] = $_GET['count'];
		print_header();
		print_body();
	} else if(isset($_GET['change_password'])) { // the user is under the SETTINGS view and wanna update password
		$_SESSION['view'] = "settings";
		print_header();
		print_body();
	} else if(isset($_GET['edit_machine'])) { // the user is under the SETTINGS view and wanna update machine
		$_SESSION['view'] = "settings";
		print_header();
		print_body();
	}
} else { // default
	print_header();
	print_body();
}

?>

