<?php

include("config.php");
session_start();


/* "VIEW" variable -> default to be "status". */
/* status: display process information (default) */
/* userlist: dispaly online user information */
/* log: display the killed processes (IGNORED! FOR NOW!) */
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
// for (proc) filter
$_SESSION['current_machine'] = $machine_list[0];
$_SESSION['current_proc_type'] = $proc_type_list[0];
$_SESSION['current_sortedby'] = $sortedby_list[0];
$_SESSION['current_count'] = $count_list[0];
// for user filter
$_SESSION['current_showip'] = "1";
$_SESSION['current_showlogintime'] = "1";
$_SESSION['current_showidletime'] = "1";
$_SESSION['current_showcommand'] = "1";


function print_header() {
	echo "<html><head>";
	echo "<title>webMS: Welcome Back!</title>";
	echo "<link rel=stylesheet type='text/css' href='main.css'>";
	echo "</head>";
}

function print_body() {
	echo "<body>";
	echo "<h1 id='header'><span>webMS</span></h1><hr>";
	// echo "<p>Login Succeeded!</p>";
	echo "<p id='User'><span class='circles-loader'>Loading&#8230;</span>&nbsp;&nbsp;Hello, ".$_SESSION['Username']."</p><hr>";
	print_menu();
	echo "<hr id='HR'>";
	// print different things according to different views: STATUS, LOG, SETTINGS, LOGOUT
	if(!isset($_SESSION['view']) || $_SESSION['view'] == "status") {
		if(isset($_GET['kill_process'])) { // the user wants to kill a process
			// kill the process
			$pid = $_GET['pid'];
			$tmp = kill_process($pid);
			if($tmp == 0) { // failed to kill the process $pid
				echo "<p class='warning'>Error: process not killed. (pid=$pid)</p>";
			} else if($tmp == 1) { // succeeded killing the process $pid
				echo "<p class='warning'>The process has been killed successfully. (pid=$pid)</p>";
			} else { // shoudn't be here!
				;
			}
			echo "<hr>";
			unset($_GET['kill_process']);
		}
		print_filter_form();
		echo "<hr>";
		print_killer_form();
		echo "<hr>";
		global $machine_list;
		if(count($machine_list) == 0) { // this user has no machines that are setup.
			echo "<p class='warning'>Sorry, You don't have any machines to be moniotored!<br />";
			echo "Please go to the settings section to create one</p>";
		} else {
			print_process();
		}
	} else if($_SESSION['view'] == "userlist") {
		// echo "<p class='warning'>This part is still under construction. Hank!!</p>";
		print_user_filter_form();
		echo "<hr>";
		print_user_list();
	} else if($_SESSION['view'] == "log") {
		echo "<p class='warning'>A Ha! You're now viewing LOG, but there's nothing to show you currently :P</p>";
	} else if($_SESSION['view'] == "settings") {
		if(isset($_GET['change_password'])) { // user wants to change his/her password
			// ----- print change password form ----- //
			print_change_password_form();
		} else if(isset($_GET['edit_machine'])) { // user wants to update machines
			$cmd = $_GET['edit_machine'];
			if($cmd[0] == 'U') { // you wanna update information of a certain machine
				// ----- print update machine form ----- //
				print_update_machine_form();
			} else if($cmd[0] == 'A') { // you wanna add a machine
				// ----- print add machine form ----- //
				print_add_machine_form();
			} else if($cmd[0] == 'D') { // you wanna delete a machine
				// ----- print delete machine form ----- //
				print_delete_machine_form();
			}
		} else if(isset($_SESSION['changepassword'])) { // user just tried to change his/her password
			if($_SESSION['changepassword'] == 0) { // he/she failed
				echo "<p class='warning'>Failed to change your password.</p>";
			} else if($_SESSION['changepassword'] == 1) { // he/she succeeded
				echo "<p class='warning'>Password Updated Successfully.</p>";
			}
			print_profile_settings();
			echo "<hr>";
			print_machine_settings();
			$_SESSION['changepassword'] = NULL;
		} else if(isset($_SESSION['updatemachine'])) { //user just tried to update a machine
			if($_SESSION['updatemachine'] == 0) { // he/she failed
				echo "<p class='warning'>Failed to update the machine.</p>";
			} else if($_SESSION['updatemachine'] == 1) { // he/she succeeded
				echo "<p class='warning'>Machine Updated Successfully.</p>";
			}
			print_profile_settings();
			echo "<hr>";
			print_machine_settings();
			$_SESSION['updatemachine'] = NULL;
		} else if(isset($_SESSION['addmachine'])) { // user just tried to add a machine
			if($_SESSION['addmachine'] == 0) { // he/she failed
				echo "<p class='warning'>Failed to add a machine.</p>";
			} else if($_SESSION['addmachine'] == 1) { // he/she succeeded
				echo "<p class='warning'>New Machine Added Successfully.</p>";
			}
			print_profile_settings();
			echo "<hr>";
			print_machine_settings();
			$_SESSION['addmachine'] = NULL;
		} else if(isset($_SESSION['deletemachine'])) { // user just deleted a machine
			if($_SESSION['deletemachine'] == 0) { // he/she failed
				echo "<p class='warning'>Failed to delete the machine.</p>";
			} else if($_SESSION['deletemachine'] == 1) { // he/she succeeded
				echo "<p class='warning'>Machine deleted successfully.</p>";
			}
			print_profile_settings();
			echo "<hr>";
			print_machine_settings();
			$_SESSION['deletemachine'] = NULL;
		} else { // user is under normal SETTINGS view
			print_profile_settings();
			echo "<hr>";
			print_machine_settings();
		}
	} else if($_SESSION['view'] == "logout") {
		session_destroy();
		header("location: welcome.php");
	} else { // if you're here, ...., you're dying...
		echo "<p class='warning'>hi baby, there's something seriously wrong! Please contact me: b01902044@ntu.edu.tw</p>";
	}
	echo "</body></html>";
}

/* VIEW: "status", "userlist", "log", "settings", "logout" */
function print_menu() {
	echo "<div id='menu'>";
	echo "<form action='' method='get'>";
	// print status button
	echo "<input type='submit' ";
	if($_SESSION['view'] == "status")
		echo "class='active_button' ";
	else
		echo "class='button' ";
	echo "name='changeview' value='Status'>";
	// print userlist button
	echo "<input type='submit' ";
	if($_SESSION['view'] == "userlist")
		echo "class='active_button' ";
	else
		echo "class='button' ";
	echo "name='changeview' value='User List'>";
/*	echo "<input type='submit' class='button' name='changeview' value='Log'>"; */
	// print settings button
	echo "<input type='submit' ";
	if($_SESSION['view'] == "settings")
		echo "class='active_button' ";
	else
		echo "class='button' ";
	echo "name='changeview' value='Settings'>";
	// print logout button
	echo "<input type='submit' ";
	if($_SESSION['view'] == "logout")
		echo "class='active_button' ";
	else
		echo "class='button' ";
	echo "name='changeview' value='Logout'>";
	echo "</form>";
	echo "</div>";
}

/* settings: which machines? process type? sortedby? count? */
function print_filter_form() {
	echo "<div id='filter'>";
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
	echo "<label for=''>Type:</label>";
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
	echo "<label for=''>Sort:</label>";
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
	echo "<input type='submit' class='button' value='Go!'>";
	echo "</form>";
	echo "</div>";
}

/* under "status" view, for users to kill processes */
function print_killer_form() {
	echo "<div id='killer'>";
	echo "<p>Killer</p>";
	echo "<form action='' method='get'>";
	echo "<input type='hidden' name='machine' value='".$_SESSION['current_machine']."'>";
	echo "<label for=''>PID:</label><input type='text' name='pid'>";
	echo "<input type='submit' class='button' name='kill_process' value='Kill It!'>";
	echo "</form>";
	echo "</div>";
}

/* need to be tuned for supporting Mac OSX. */
/* kill a certain process with given pid. Return 0 if fail; return 1 if success. */
function kill_process($pid) {
	// get the variables needed for CGI request
	global $db;
	$u = $_SESSION['Username'];
	$mn = $_SESSION['current_machine'];
	$query = "SELECT * FROM machines WHERE username='$u' and mname='$mn'";
	$result = mysqli_query($db, $query);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$mh = $row['mhost'];
	$mu = $row['musername'];
	$mp = $row['mpassword'];
	// form CGI request
	$cgi_request = "http://127.0.0.1/cgi-bin/ssh_kill.cgi?";
	$cgi_request .= "Host=$mh&&Username=$mu&&Password=$mp&&Pid=$pid";
	$data = file_get_contents($cgi_request, 0);
	for($i = 0; $i < strlen($data)-2; $i = $i + 1) {
		if($data[$i] == "S" && $data[$i+1] == "u" && $data[$i+2] == "c")
			return 1;
	}
	return 0;
}

/* print process info according to the settings */
function print_process() {
	// lets test the variables
	// echo "hello current => ".$_SESSION['current_machine']." ".$_SESSION['current_proc_type']." ".$_SESSION['current_sortedby']." ".$_SESSION['current_count'];
	// get machine information from DB
	global $db;
	$query = "SELECT * FROM machines WHERE username='".$_SESSION['Username']."' and mname='".$_SESSION['current_machine']."'";
	$result = mysqli_query($db, $query);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	// get machine: host, u, p, and os.
	$mh = $row['mhost'];
	$mu = $row['musername'];
	$mp = $row['mpassword'];
	$mos = $row['mos'];
	// form the CGI request
	if($mos == "Linux") {
		$cgi_request = "http://127.0.0.1/cgi-bin/ssh_connect.cgi?";
		$cgi_request .= "Host=".$mh;
		$cgi_request .= "&&Username=".$mu;
		$cgi_request .= "&&Password=".$mp;
		$cgi_request .= "&&settings_proc_type=".$_SESSION['current_proc_type'];
		$cgi_request .= "&&settings_sortedby=".$_SESSION['current_sortedby'];
		$cgi_request .= "&&settings_count=".$_SESSION['current_count'];
	} else if($mos == "MacOSX") {
		$cgi_request = "http://127.0.0.1/cgi-bin/ssh_maconnect.cgi?";
		$cgi_request .= "Host=".$mh;
		$cgi_request .= "&&Username=".$mu;
		$cgi_request .= "&&Password=".$mp;
		$cgi_request .= "&&settings_proc_type=".$_SESSION['current_proc_type'];
		$cgi_request .= "&&settings_sortedby=".$_SESSION['current_sortedby'];
		$cgi_request .= "&&settings_count=".$_SESSION['current_count'];
	} else {
		echo "<p class='warning'>Shouldn't be here!!!</p>";
	}
	// echo "<p class='warning'>You're requesting: $cgi_request</p>";
	$data = file_get_contents($cgi_request, 0);
	echo $data;
}

/* hasn't been adjusted for supporting Mac OSX */
/* filter for displaying online user info */
function print_user_filter_form() {
	echo "<div id='userfilter'>";
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
	echo "<br />";
	// ----- print ip options ----- //
	/*
	echo "<label for=''>Show IP?</label>";
	echo "<select name='showip' form_id='settings'>";
	echo "<option value='1' selected='selected'>Yes</option>";
	echo "<option value='0'>No</option>";
	echo "</select>";
	*/
	echo "<label for=''>Show: </label>";
	echo "<input type='checkbox' name='showip' value='1' checked>";
	echo "<label for=''>IP. </label>";
	// ----- print login (login time) options ----- //
	/*
	echo "<label for=''>Show LoginTime?</label>";
	echo "<select name='showlogintime' form_id='settings'>";
	echo "<option value='1' selected='selected'>Yes</option>";
	echo "<option value='0'>No</option>";
	echo "</select>";
	*/
	echo "<input type='checkbox' name='showlogintime' value='1' checked>";
	echo "<label for=''>Login Time. </label>";
	// ----- print idle (idle time) options ----- //
	/*
	echo "<label for=''>Show IdleTime?</label>";
	echo "<select name='showidletime' form_id='settings'>";
	echo "<option value='1' selected='selected'>Yes</option>";
	echo "<option value='0'>No</option>";
	echo "</select>";
	*/
	echo "<input type='checkbox' name='showidletime' value='1' checked>";
	echo "<label for=''>Idle Time. </label>";
	// ----- print what (command) options ----- //
	/*
	echo "<label for=''>Show Command?</label>";
	echo "<select name='showcommand' form_id='settings'>";
	echo "<option value='1' selected='selected'>Yes</option>";
	echo "<option value='0'>No</option>";
	echo "</select>";
	*/
	echo "<input type='checkbox' name='showcommand' value='1' checked>";
	echo "<label for=''>Command. </label>";
	// ----- end of settings list, now let's have a submit button ----- //
	echo "<br />";
	echo "<input type='submit' class='button' value='Go!'>";
	echo "</form>";
	echo "</div>";
}

/* hasn't been adjusted for supporting Mac OSX */
/* print the online user list */
function print_user_list() {
	// lets test the variables
	// echo "hello current => ".$_SESSION['current_machine']." ".$_SESSION['current_proc_type']." ".$_SESSION['current_sortedby']." ".$_SESSION['current_count'];
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
	$cgi_request = "http://127.0.0.1/cgi-bin/ssh_userlist2.cgi?";
	$cgi_request .= "Host=".$mh;
	$cgi_request .= "&&Username=".$mu;
	$cgi_request .= "&&Password=".$mp;
	$cgi_request .= "&&settings_count=".$_SESSION['current_count'];
	$cgi_request .= "&&settings_ip=".$_SESSION['current_showip'];
	$cgi_request .= "&&settings_login=".$_SESSION['current_showlogintime'];
	$cgi_request .= "&&settings_idle=".$_SESSION['current_showidletime'];
	$cgi_request .= "&&settings_what=".$_SESSION['current_showcommand'];
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
	echo "<div id='profile'>";
	echo "<h2>My Profile</h2>";
	echo "<table border=1>";
	echo "<td class='title'>Username</td><td class='content'>".$u."</td><tr>";
	echo "<td class='title'>Password</td><td class='content'>my password is ... you think I'm an idiot?</td><tr>";
	echo "<td class='title'>Email</td><td class='content'>".$em."</td><tr>";
	echo "</table>";
	// print button for changing password
	echo "<form action='' method='get'><input type='submit' class='button' name='change_password' value='Change Password'></form>";
	echo "</div>";
}

/* for "settings" VIEW */
function print_machine_settings() {
	// get machine information from DB
	global $db;
	$query = "SELECT * FROM machines WHERE username='".$_SESSION['Username']."'";
	$result = mysqli_query($db, $query);
	// print machine info: name, host, username, password, update-button.
	echo "<div id='machines'>";
	echo "<h2>My Machines</h2>";
	echo "<table border=1>";
	echo "<td class='title'>Name</td><td class='title'>Host</td><td class='title'>Username</td><td class='title'>OS</td><td class='title'>Edit</td><td class='title'>Delete</td>";
	echo "<tr>";
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$mn = $row['mname'];
		$mh = $row['mhost'];
		$mu = $row['musername'];
		$mp = $row['mpassword'];
		$mos = $row['mos'];
		echo "<td class='content'>".$mn."</td><td class='content'>".$mh."</td><td class='content'>".$mu."</td><td class='content'>".$mos."</td>";
		echo "<td class='content'><form action='' method='get'><input type='submit' class='button' name='edit_machine' value='Update $mn'></form></td>";
		echo "<td class='contnet'><form action='' method='get'><input type='submit' class='button' name='edit_machine' value='Delete $mn'></form></td>";
		echo "<tr>";
	}
	echo "</table>";
	// print button for adding machines
	echo "<form action='' method='get'>";
	echo "<input type='submit' class='button' name='edit_machine' value='Add Machine'>";
	echo "</form>";
	echo "</div>";
}

/* print the form for users to change their password */
function print_change_password_form() {
	echo "<div id='chpw'>";
	echo "<h2>Change ".$_SESSION['Username']."'s Password</h2>";
	echo "<form action='' method='POST'>";
	echo "<label for=''>Old Password:</label><input type='password' name='old_password'><br />";
	echo "<label for=''>New Password:</label><input type='password' name='new_password'><br />";
	echo "<input type='submit' class='button' name='change_password_submit' value='Confirm'>";
	echo "</form>";
	echo "</div>";
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

/* print the form for users to update one of their machines */
function print_update_machine_form() {
	// get machine name from $_GET['edit_machine']
	$str = $_GET['edit_machine'];
	$tmp = strtok($str, " ");
	$tmp = strtok(" ");
	$mn = $tmp;
	echo "<div id='chmc'>";
	echo "<h2>Update ".$mn."'s Settings</h2>";
	echo "<form action='' method='POST'>";
	echo "<input type='hidden' name='mname' value='$mn'>";
	echo "<label for=''>Host Name:</label><input type='text' name='mhost'><br />";
	echo "<label for=''>Username:</label><input type='text' name='musername'><br />";
	echo "<label for=''>Password:</label><input type='password' name='mpassword'><br />";
	echo "<label for=''>Operating System:</label>";
	echo "<input type='radio' name='mos' value='Linux'checked>Linux";
	echo "<input type='radio' name='mos' value='MacOSX'>Mac OSX";
	echo "<br />";
	echo "<input type='submit' class='button' name='update_machine' value='Update'>";
	echo "</form>";
	echo "</div>";
}

/* print the form for users to add a new machine */
function print_add_machine_form() {
	echo "<div id='addmc'>";
	echo "<h2>Add a new machine</h2>";
	echo "<form action='' method='POST'>";
	echo "<label for=''>Machine Name:</label><input type='text' name='mname'><br />";
	echo "<label for=''>Host Name:</label><input type='text' name='mhost'><br />";
	echo "<label for=''>Username:</label><input type='text' name='musername'><br />";
	echo "<label for=''>Password:</label><input type='password' name='mpassword'><br />";
	echo "<label for=''>Operating System:</label>";
	echo "<input type='radio' name='mos' value='Linux' checked>Linux";
	echo "<input type='radio' name='mos' value='MacOSX'>Mac OSX<br />";
	echo "<input type='submit' class='button' name='add_machine' value='Add'>";
	echo "</form>";
	echo "</div>";
}

/* print the form for users to confirm deleting a machine */
function print_delete_machine_form() {
	// get machine name from $_GET['edit_machine']
	$str = $_GET['edit_machine'];
	$tmp = strtok($str, " ");
	$tmp = strtok(" ");
	$mn = $tmp;
	echo "<div id='demc'>";
	echo "<h2>Delete this machine: $mn?</h2>";
	echo "<form action='' method='POST'>";
	echo "<input type='hidden' name='mname' value='$mn'>";
	echo "<input type='submit' class='button' name='delete_machine' value='Confirm'>";
	echo "</form>";
	echo "</div>";
}

/* update a machine in DB. Return 0 if fail; return 1 if success. */
function update_machine() {
	unset($_GET['edit_machine']);
	global $db;
	$u = $_SESSION['Username'];
	$mn = mysqli_real_escape_string($db, $_POST['mname']);
	$mh = mysqli_real_escape_string($db, $_POST['mhost']);
	$mu = mysqli_real_escape_string($db, $_POST['musername']);
	$mp = mysqli_real_escape_string($db, $_POST['mpassword']);
	$mos = mysqli_real_escape_string($db, $_POST['mos']);
	$query = "UPDATE webMS.machines SET mhost='$mh', musername='$mu', mpassword='$mp', mos='$mos' WHERE machines.username='$u' and machines.mname='$mn'";
	$dummy = mysqli_query($db, $query);
	if($dummy == False)
		return 0;
	return 1;
}

/* add a machine into DB. Return 0 if fail; return 1 if success. */
function add_machine() {
	unset($_GET['edit_machine']);
	global $db;
	$u = $_SESSION['Username'];
	$mn = mysqli_real_escape_string($db, $_POST['mname']);
	$mh = mysqli_real_escape_string($db, $_POST['mhost']);
	$mu = mysqli_real_escape_string($db, $_POST['musername']);
	$mp = mysqli_real_escape_string($db, $_POST['mpassword']);
	$mos = mysqli_real_escape_string($db, $_POST['mos']);
	$query = "INSERT into machines (mname, mhost, musername, mpassword, mos, username) VALUES ('$mn', '$mh', '$mu', '$mp', '$mos', '$u')";
	$dummy = mysqli_query($db, $query);
	if($dummy == False)
		return 0;
	return 1;
}

/* delete a machine from DB. Return 0 if fail; return 1 if success. */
function delete_machine() {
	unset($_GET['edit_machine']);
	global $db;
	$u = $_SESSION['Username'];
	$mn = $_POST['mname'];
	$query = "DELETE FROM machines WHERE username='$u' and mname='$mn'";
	$dummy = mysqli_query($db, $query);
	if($dummy == False)
		return 0;
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
	} else if(isset($_POST['update_machine'])) {
		$tmp = update_machine();
		if($tmp == 1) { // updated successfully
			$_SESSION['view'] = "settings";
			$_SESSION['updatemachine'] = 1;
			print_header();
			print_body();
		} else if($tmp == 0) { // fail to update machine
			$_SESSION['view'] = "settings";
			$_SESSION['updatemachine'] = 0;
			print_header();
			print_body();
		} else { // shouldn't be here!
			;
		}
	} else if(isset($_POST['add_machine'])) {
		$tmp = add_machine();
		if($tmp == 1) { // added successfully
			$_SESSION['view'] = "settings";
			$_SESSION['addmachine'] = 1;
			print_header();
			print_body();
		} else if($tmp == 0) { // fail to add machine
			$_SESSION['view'] = "settings";
			$_SESSION['addmachine'] = "0";
			print_header();
			print_body();
		} else { // shouldn't be here!
			;
		}
	} else if(isset($_POST['delete_machine'])) {
		$tmp = delete_machine();
		if($tmp == 1) { // deleted successfully
			$_SESSION['view'] = "settings";
			$_SESSION['deletemachine'] = 1;
			print_header();
			print_body();
		} else if($tmp == 0) { // fail to delete machine
			$_SESSION['view'] = "settings";
			$_SESSION['deletemachine'] = 0;
			print_header();
			print_body();
		} else  { // shoudn't be here!
			;
		}
	}
} else if($_GET) {
	if(isset($_GET['changeview'])) { // the user is changing view
		if($_GET['changeview'] == "Status") { // display process information
			$_SESSION['view'] = "status";
		} else if($_GET['changeview'] == "User List") { // display online user list
			$_SESSION['view'] = "userlist";
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
	} else if(isset($_GET['showip'])) { // the user is under the USERLIST view and change the settings
		$_SESSION['current_machine'] = $_GET['machine'];
		$_SESSION['current_showip'] = $_GET['showip'];
		$_SESSION['current_showlogintime'] = $_GET['showlogintime'];
		$_SESSION['current_showidletime'] = $_GET['showidletime'];
		$_SESSION['current_showcommand'] = $_GET['showcommand'];
		$_SESSION['current_count'] = $_GET['count'];
		$_SESSION['view'] = "userlist";
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
	} else if(isset($_GET['kill_process'])) { // the user is under the STATUS view and wanna kill a process
		$_SESSION['view'] = "status";
		$_SESSION['current_machine'] = $_GET['machine'];
		print_header();
		print_body();
	}
} else { // default
	print_header();
	print_body();
}

?>

