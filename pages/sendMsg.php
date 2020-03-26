<?php
require_once('../pdoconfig.php');

// Setup link to database
$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}
// Insert message
session_start();
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, "INSERT INTO IM (SenderID, ReceiverID, IMText) VALUES (?,?,?)");
mysqli_stmt_bind_param($stmt, "iis", $_SESSION["userid"], $_SESSION["chatpartner"], $_GET["text"]);
mysqli_stmt_execute($stmt);
/*
$fileName = $_SESSION["userid"] . ':' . $_GET["receiverid"];
$file = fopen($fileName);
$fwrite($file, $_GET["text"] . '\n', 'a');
$fclose($file);
 */
?>