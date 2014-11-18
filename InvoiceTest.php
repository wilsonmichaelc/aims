<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/InvoiceService.php");
$invoiceService = new InvoiceService();

echo $invoiceService->getInvoice(5);

?>