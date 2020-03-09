<!DOCTYPE html>
<html lang="en">
<head>
	<title>Kent C++ Supplement</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="./css/index_CSS.css">
	<link href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" rel="stylesheet"/> 
	<link href="https://fonts.googleapis.com/css?family=VT323&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Courier+Prime&display=swap" rel="stylesheet">

</head>
<body>
	<?php
	session_start();
	if ($_SESSION["loggedin"]) {
		echo "Logging out...<br>";
		session_destroy();
		header("Location: https://www.kentcpp.com");
	}
	else {
		echo "User is already logged out.<br>";
		header("Location: https://www.kentcpp.com");
	}
	?>
</body>
