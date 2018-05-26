<?php
require __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/includes/bootstrap.php";
$db = new database(); //database to use

//$request will be an assoc array

//Response array
$response = array();
$response["success"] = false;



echo json_encode($response);