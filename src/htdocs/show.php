<html>

<?php

session_start();

/* variables */
$machine_list = array("linux11", "linux12", "linux13", "linux14", "linux15");
$sortedby_list = array("cpu", "memory", "totaltime");
$count_list = array("20", "50", "100", "200", "all");

/* default settings */
$_SESSION['settings_machine'] = 'linux11';
$_SESSION['settings_sortedby'] = 'cpu';
$_SESSION['settings_count'] = '20';

function print_header() {
	print "<head>";
	print "</head>";
}

function print_body() {
	print "<body>";
	print "<h1>Hello World!</h1>";
	print_SettingsForm();
	echo "<br />";
	print_process();
	print "</body>";
}

function print_SettingsForm() {
	print "<p>Change Settings</p>";
	print "<form action='' method='get' id='settings'>";
	// ----- print machine options ----- //
	print "<label for=''>Machines:</label>";
	print "<select name='machine' form_id='settings'>";
	global $machine_list;
	for($i = 0; $i < count($machine_list); $i = $i + 1) {
		$tmp = $machine_list[$i];
		if($tmp == $_SESSION['settings_machine']) {
			print "<option value='$tmp' selected='selected'>$tmp</option>";
		} else {
			print "<option value='$tmp'>$tmp</option>";
		}
	}
	print "</select>";
	// ----- print sortedby options ----- //
	print "<label for=''>Sorted By:</label>";
	print "<select name='sortedby' form_id='settings'>";
	global $sortedby_list;
	for($i = 0; $i < count($sortedby_list); $i = $i + 1) {
		$tmp = $sortedby_list[$i];
		if($tmp == $_SESSION['settings_sortedby']) {
			print "<option value='$tmp' selected='selected'>$tmp</option>";
		} else {
			print "<option value='$tmp'>$tmp</option>";
		}
	}
	print "</select>";
	// ----- print count options ----- //
	print "<label for=''>Count:</label>";
	print "<select name='count' form_id='settings'>";
	global $count_list;
	for($i = 0; $i < count($count_list); $i = $i + 1) {
		$tmp = $count_list[$i];
		if($tmp == $_SESSION['settings_count']) {
			print "<option value='$tmp' selected='selected'>$tmp</option>";
		} else {
			print "<option value='$tmp'>$tmp</option>'";
		}
	}
	print "</select>";
	// ----- end of settings list, now let's have a submit button ----- //
	print "<input type='submit' value='Go!'>";
	print "</form>";
}

function print_process() {

	// ----- print to check ----- //
	print "now: ";
	print $_SESSION['settings_machine'];
	print $_SESSION['settings_sortedby'];
	print $_SESSION['settings_count']. "<br />";

	// settings changed
	// throw something to CGI script:
	// - $_SESSION['Username']
	// - $_SESSION['Password']
	// - $_SESSION['settings_machine']
	// - $_SESSION['settings_sortedby']
	// - $_SESSION['settings_count']
	$url_request = "http://127.0.0.1/cgi-bin/ssh_connect.cgi?";
	$url_request = $url_request."Username=".$_SESSION['Username'];
	$url_request = $url_request."&&Password=".$_SESSION['Password'];
	$url_request = $url_request."&&settings_machine=".$_SESSION['settings_machine'];
	$url_request = $url_request."&&settings_sortedby=".$_SESSION['settings_sortedby'];
	$url_request = $url_request."&&settings_count=".$_SESSION['settings_count'];
	$data = file_get_contents($url_request, 0);
	echo $data;
}

// ----- handling HTML form actions ----- //
if($_GET) {
	// get settings variables
	$_SESSION['settings_machine'] = $_GET['machine'];
	$_SESSION['settings_sortedby'] = $_GET['sortedby'];
	$_SESSION['settings_count'] = $_GET['count'];
	print_header();
	print_body();
} else {
	print_header();
	print_body();
}


?>


</html>
