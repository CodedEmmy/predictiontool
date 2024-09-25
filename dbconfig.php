<?php 
	$dbname = 'crowdtool';
	$dbuser = 'root';
	$dbcode = '';
	
	$dbhost = 'localhost';
	$conn = mysqli_connect($dbhost, $dbuser, $dbcode, $dbname);
	if (!$conn) {
		die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
	}
?>