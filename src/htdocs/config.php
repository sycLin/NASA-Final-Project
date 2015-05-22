<?php

// mysqli_connect(host, username, password, database)
$db = mysqli_connect("127.0.0.1", "root", "ji394vup aj4", "webMS");

if(mysqli_connect_errno($db)) {
	echo "FAILED TO CONNECT TO MySQL: " . mysqli_connect_error();
}

?>
