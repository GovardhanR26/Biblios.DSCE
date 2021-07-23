<?php

date_default_timezone_set("Asia/Calcutta");

	$hostname = "localhost";
	$username = "root";
	$password = "";
	$dbname = "library2";
	
	//connection 
	$link = mysqli_connect($hostname, $username, $password, $dbname);
	
	//check if connected
	if($link === false) {
		die("Connection failed : ".mysqli_connect_error());
	}
	//No need to echo anything
	//echo "Successfully connected. Host info : ".mysqli_get_host_info($link);
?>