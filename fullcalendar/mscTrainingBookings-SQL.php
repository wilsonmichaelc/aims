<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("../php/config/config.php");
require_once("../php/classes/TrainingInfo.php");

$trainingInfo = new TrainingInfo();

$events = null;

if(isset($_POST['start'])){
	$start = date( 'Y-m-d', $_POST['start']);
	$end = date( 'Y-m-d', $_POST['end']);
	$events = $trainingInfo->getBookedTraining($start, $end);
}elseif(isset($_GET['start'])) {
	$start = date( 'Y-m-d', $_GET['start']);
	$end = date( 'Y-m-d', $_GET['end']);
	$events = $trainingInfo->getBookedTraining($start, $end);
}

if(count($events) == 0){
	echo '';
}else{
	echo $events;
}
?>
