<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

session_start();

require_once("../config/config.php");
require_once("ProjectAsJSON.php");
require_once("GenerateInvoice.php");

$project = new ProjectAsJSON();
$invoice = new GenerateInvoice();

if( isset($_POST['getProjectAsJSON']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $project->getProjectAsJSON($_POST['getProjectAsJSON']);
	}
}

if( isset($_POST['getProjectsForSelection']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $project->getProjectsForSelection($_POST['getProjectsForSelection'], $_POST['dateFrom'], $_POST['dateTo']);
	}
}

if( isset($_POST['generateInvoice']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $invoice->generateInvoice($_POST['generateInvoice']);
	}
}

if( isset($_POST['getUsersForSelection']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $project->getUsersForSelection($_POST['dateFrom'], $_POST['dateTo']);
	}
}

?>