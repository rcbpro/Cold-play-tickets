<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require "wp-ex-admin-dbfunctions.php";
$adminDbFunc = new AdminDbExtraFunctions();

switch($_GET['action']){
	case "parentEvents":
		echo JSON_encode($adminDbFunc->loadAllParentEvents($_GET['current']));
	break;
}