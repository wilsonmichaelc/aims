<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

session_start();

require_once("../config/config.php");
require_once("InvoiceSearch.php");

$search = new InvoiceSearch();

if( isset($_POST['invoiceNumber']) ){
    if($_SESSION['isAdmin'] == 1){
        echo $search->searchByInvoiceId($_POST['invoiceNumber']);
    }
}

if( isset($_POST['type']) ){
    if($_SESSION['isAdmin'] == 1){
    	if($_POST['type'] == 'service'){
    		echo $search->getServiceById($_POST['id']);
    	}
    	if($_POST['type'] == 'booking'){
    		echo $search->getBookingById($_POST['id']);
    	}
    	if($_POST['type'] == 'training'){
    		echo $search->getTrainingById($_POST['id']);
    	}
    }
}

?>