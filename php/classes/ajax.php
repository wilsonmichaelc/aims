<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

session_start();

require_once("../config/config.php");

require_once("Users.php");
require_once("Stats.php");
require_once("TrainingModules.php");
require_once("TrainingInfo.php");
require_once("TrainingModuleUpdate.php");
require_once("InstrumentInfo.php");
require_once("InstrumentUpdate.php");
require_once("EstimateCalculator.php");
require_once("ServicesOffered.php");
require_once("BookingInfo.php");
require_once("BookingUpdate.php");
require_once("ModifyBooking.php");
require_once("ServiceRequestInfo.php");
require_once("ServiceRequestUpdate.php");
require_once("MetaData.php");

require_once("FAQ.php");

require_once("InvoiceBooking.php");
require_once("InvoiceService.php");
require_once("InvoiceTraining.php");

require_once("ProjectInfo.php");
require_once("ProjectUpdate.php");

require_once("ConferenceRoomInfo.php");
require_once("ConferenceRoomUpdate.php");

$users = new Users();
$stats = new Stats();
$trainingModules = new TrainingModules();
$instrumentInfo = new InstrumentInfo();
$instrumentUpdate = new InstrumentUpdate();
$estimateCalculator = new EstimateCalculator();
$servicesOffered = new ServicesOffered();
$bookingInfo = new BookingInfo();
$bookingUpdate = new BookingUpdate();
$modifyBooking = new ModifyBooking();
$serviceRequestInfo = new ServiceRequestInfo();
$serviceRequestUpdate = new ServiceRequestUpdate();
$trainingInfo = new TrainingInfo();
$trainingUpdate = new TrainingModuleUpdate();
$meta = new MetaData();
$faq = new FAQ();
$invoiceBooking = new InvoiceBooking();
$invoiceService = new InvoiceService();
$invoiceTraining = new InvoiceTraining();
$projectInfo = new ProjectInfo();
$projectUpdate = new ProjectUpdate();
$conferenceRoomInfo = new ConferenceRoomInfo();
$conferenceUpdate = new ConferenceRoomUpdate();


if(isset($_POST['request']) && !empty($_POST['request'])){
	if($_SESSION['isAdmin'] == 1){
		echo $users->jsonGetUserInfo($_POST['request']);
	}
}



// Chart.js Graphs
if(isset($_POST['InstrumentByMonth']) && !empty($_POST['InstrumentByMonth'])){
	echo $stats->getInstrumentByMonth($_POST['startYear'], $_POST['endYear'], $_POST['instrument'], $_POST['user'], $_POST['month']);
}

if(isset($_POST['ServiceRequestsByMonth']) && !empty($_POST['ServiceRequestsByMonth'])){
	echo $stats->getServiceRequestsByMonth($_POST['startYear'], $_POST['endYear']);
}

if(isset($_POST['trainingPassed']) && !empty($_POST['training'])){
	if($_SESSION['isAdmin'] == 1){
		echo $trainingModules->jsonGetTrainingModulesPassed($_POST['training']);
	}
}

if(isset($_POST['access']) && !empty($_POST['access'])){
	if($_SESSION['isAdmin'] == 1){
		echo $instrumentInfo->jsonGetInstrumentAccess($_POST['access']);
	}
}

if(isset($_POST['conferenceAccess']) && !empty($_POST['conferenceAccess'])){
	if($_SESSION['isAdmin'] == 1){
		echo $conferenceRoomInfo->jsonGetConferenceAccess($_POST['conferenceAccess']);
	}
}

if(isset($_POST['projects']) && !empty($_POST['projects'])){
	if($_SESSION['isAdmin'] == 1){
		echo $projectInfo->jsonGetUserProjects($_POST['projects']);
	}
}

if(isset($_POST['getServiceRequests']) && !empty($_POST['getServiceRequests'])){
	if($_SESSION['isAdmin'] == 1){
		echo $serviceRequestInfo->jsonGetUserServiceRequests($_POST['getServiceRequests']);
	}
}

if(isset($_POST['getServiceById']) && !empty($_POST['getServiceById'])){
	if($_SESSION['isAdmin'] == 1){
		echo $serviceRequestInfo->jsonGetServiceName($_POST['getServiceById']);
	}
}

if( isset($_POST['updateUserAccountType']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $users->updateUserAccountType($_POST['accountType'], $_POST['updateUserAccountType']);
	}
}

if( isset($_POST['updateUser']) ){
	if($_SESSION['id'] == $_POST['updateUser'] || $_SESSION['isAdmin'] == 1){
		echo $users->updateUser($_POST['first'], $_POST['last'], $_POST['email'], $_POST['institution'], $_POST['updateUser']);
	}
}

if( isset($_POST['updateInstrumentAccess']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $instrumentUpdate->updateInstrumentAccess($_POST['instrumentId'], $_POST['userId'], $_POST['accessStatus']);
	}
}

if( isset($_POST['updateConferenceAccess']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $conferenceUpdate->updateConferenceAccess($_POST['conferenceId'], $_POST['userId'], $_POST['accessStatus']);
	}
}

if( isset($_POST['updateTrainingStatus']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $trainingModules->updateStatus($_POST['updateTrainingStatus'], $_POST['userId'], $_POST['status']);
	}
}

if( isset($_POST['updateInstrument']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $instrumentUpdate->updateInstrument($_POST['id'], $_POST['name'], $_POST['model'], $_POST['asset'], $_POST['accuracy'], $_POST['minBookableUnit'], $_POST['color'], $_POST['bookable'], $_POST['location']);
	}
}

if( isset($_POST['updateUserProject']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $projectUpdate->updateProject($_POST['updateUserProject'], $_POST['primaryInvestigator'], $_POST['addressOne'], $_POST['addressTwo'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['phone'], $_POST['fax'], $_POST['status'],  $_POST['abstract'], $_POST['purchaseOrder'], $_POST['projectCostingBusinessUnit'], $_POST['projectId'], $_POST['departmentId'], $_POST['pmntId']);
	}
}

if( isset($_POST['updateProject']) ){
		echo $projectUpdate->updateProjectByUser($_POST['updateProject'], $_POST['title'], $_POST['addressOne'], $_POST['addressTwo'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['phone'], $_POST['fax'], $_POST['abstract']);
}

if( isset($_POST['updateUserBooking']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $bookingUpdate->updateBooking($_POST['updateUserBooking'], $_POST['dateFrom'], $_POST['dateTo'], $_POST['timeFrom'], $_POST['timeTo']);
	}
}

if( isset($_POST['updateUserTrainingBooking']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $bookingUpdate->updateTrainingBooking($_POST['updateUserTrainingBooking'], $_POST['dateFrom'], $_POST['dateTo'], $_POST['timeFrom'], $_POST['timeTo']);
	}
}

if( isset($_POST['updateServiceRequest']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $serviceRequestUpdate->updateServiceRequest($_POST['updateServiceRequest'], $_POST['label'], $_POST['concentration'], $_POST['state'], $_POST['composition'], $_POST['digestionEnzyme'], $_POST['purification'], $_POST['redoxChemicals'], $_POST['molecularWeight'], $_POST['suspectedModifications'], $_POST['aaModifications'], $_POST['species'], $_POST['sequence'], $_POST['comments'], $_POST['status']);
	}
}

if( isset($_POST['addServiceToRequest'] ) ){
	if( $_SESSION['isAdmin'] == 1 ){
		echo $serviceRequestUpdate->addServiceToRequest($_POST['addServiceToRequest'], $_POST['service'], $_POST['samples'], $_POST['replicates'], $_POST['prep']);
	}
}

if( isset($_POST['deleteServiceFromRequest'] ) ){
	if( $_SESSION['isAdmin'] == 1 ){
		echo $serviceRequestUpdate->deleteServiceFromRequest($_POST['deleteServiceFromRequest']);
	}
}

if( isset($_POST['updateSelectedService']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $serviceRequestUpdate->updateSelectedService($_POST['updateSelectedService'], $_POST['samples'], $_POST['replicates'], $_POST['prep']);
	}
}

if( isset($_POST['updateModuleStatus']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $trainingUpdate->updateStatus($_POST['updateModuleStatus'], $_POST['newStatus']);
	}
}

if( isset($_POST['archiveBooking']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $bookingUpdate->archiveBooking($_POST['archiveBooking']);
	}
}

if( isset($_POST['unArchiveBooking']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $bookingUpdate->unArchiveBooking($_POST['unArchiveBooking']);
	}
}

if( isset($_POST['updateService']) )
{
	$returnValue = '';
	if($_SESSION['isAdmin'] == 1)
	{
		$result = $servicesOffered->ajaxUpdateAnalysisService(
			$_POST['serviceId'], 
			$_POST['memberRegular'],$_POST['memberDiscount'],$_POST['memberCutoff'],
    		$_POST['collaboratorRegular'],$_POST['collaboratorDiscount'],$_POST['collaboratorCutoff'],
    		$_POST['umbRegular'],$_POST['umbDiscount'],$_POST['umbCutoff'],
    		$_POST['affiliateRegular'],$_POST['affiliateDiscount'],$_POST['affiliateCutoff'],
    		$_POST['nonProfitRegular'],$_POST['nonProfitDiscount'],$_POST['nonProfitCutoff'],
    		$_POST['forProfitRegular'],$_POST['forProfitDiscount'],$_POST['forProfitCutoff']
    	);
		if($result && isset($_POST['prepId']))
		{
			$result = $servicesOffered->ajaxUpdatePrepService(
				$_POST['serviceId'],
				$_POST['p_memberRegular'],$_POST['p_memberDiscount'],$_POST['p_memberCutoff'],
	    		$_POST['p_collaboratorRegular'],$_POST['p_collaboratorDiscount'],$_POST['p_collaboratorCutoff'],
	    		$_POST['p_umbRegular'],$_POST['p_umbDiscount'],$_POST['p_umbCutoff'],
	    		$_POST['p_affiliateRegular'],$_POST['p_affiliateDiscount'],$_POST['p_affiliateCutoff'],
	    		$_POST['p_nonProfitRegular'],$_POST['p_nonProfitDiscount'],$_POST['p_nonProfitCutoff'],
	    		$_POST['p_forProfitRegular'],$_POST['p_forProfitDiscount'],$_POST['p_forProfitCutoff']
			);
			$returnValue .= ' -- UpdatePrep: ' . $result;
		}
		if($result){
			$result = $servicesOffered->updateServiceName($_POST['serviceId'], $_POST['name']);
		}
		
		if($result){
			echo true;
		}else{
			echo false;
		}
    		
	}

}

if( isset($_POST['bookingEstimate']) ){
	echo $estimateCalculator->getBookingEstimate($_POST['accountType'], $_POST['instrument'], $_POST['hours']);
}

if( isset($_POST['serviceEstimate']) ){
	echo $estimateCalculator->getServiceEstimate($_POST['json'], $_POST['accountType']);
}

if( isset($_POST['trainingEstimate']) ){
	echo $estimateCalculator->getTrainingEstimate($_POST['accountType'], $_POST['hours']);
}

/*
*	
*	Ajax Requests for loading booking data by date range
*	
*/
if( isset($_POST['booking']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $bookingInfo->getBookingRange($_POST['dateFrom'], $_POST['dateTo'], $_POST['order'], $_POST['user']);
	}
}

if( isset($_POST['booking_metadata']) ){
	if($_SESSION['isAdmin'] == 1){
		$name = $meta->getUserName($_POST['userId']);
		$project = $meta->getProjectTitle($_POST['projectId']);
		$instrument = $meta->getInstrumentName($_POST['instrumentId']);
		$accountType = $meta->getUserType($_POST['userId']);
		$estimate = $meta->getBookingEstimate($accountType['id'], $_POST['instrumentId'], $_POST['hours']);
		echo $name . ',' . $project['title'] . ',' . $instrument['name'] . ',' . $accountType['name'] . ',$' . $estimate;
	}
}

/*
*	
*	Ajax Requests for loading fee-for-service data by date range
*	
*/
if( isset($_POST['serviceRequests']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $serviceRequestInfo->getServiceRange($_POST['dateFrom'], $_POST['dateTo'], $_POST['order'], $_POST['user']);
	}
}

if( isset($_POST['serviceRequests_metadata']) ){
	if($_SESSION['isAdmin'] == 1){
		$name = $meta->getUserName($_POST['userId']);
		$project = $meta->getProjectTitle($_POST['projectId']);
		$accountType = $meta->getUserType($_POST['userId']);
		$estimate = $estimateCalculator->ajaxGetServiceEstimate($_POST['requestId'], $accountType['id']);
		echo $name . ',' . $project['title'] . ',' . $accountType['name'] . ',$' . $estimate;
	}
}

if( isset($_POST['servicesSelected_metadata']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $serviceRequestInfo->ajaxGetServicesSelected($_POST['requestId']);
	}
}

if( isset($_POST['serviceName_metadata']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $meta->getServiceName($_POST['serviceName_metadata']);
	}
}

/*
*	
*	Ajax Requests for loading training data by date range
*	
*/
if( isset($_POST['trainingRequests']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $trainingInfo->getTrainingRange($_POST['dateFrom'], $_POST['dateTo'], $_POST['trainingRequests']);
	}
}

if( isset($_POST['training_metadata']) ){
	if($_SESSION['isAdmin'] == 1){
		$name = $meta->getUserName($_POST['userId']);
		$project = $meta->getProjectTitle($_POST['projectId']);
		$instrument = $meta->getInstrumentName($_POST['instrumentId']);
		$accountType = $meta->getUserType($_POST['userId']);
		echo $name . ',' . $project['title'] . ',' . $instrument['name'] . ',' . $accountType['name'];
	}
}

/*
*	
*	Ajax Request to cancel booking. Booking will only be canceled with 24hr lead time.
*	
*/
if( isset($_POST['cancelBooking']) ){
	echo $modifyBooking->cancelBooking($_POST['bookingId']);
}

if( isset($_POST['cancelTrainingBooking']) ){
	echo $modifyBooking->cancelTrainingBooking($_POST['trainingId']);
}

if(isset($_POST['getBase64Logo'])){
	echo $meta->getLogo($_POST['getBase64Logo']);
}

if( isset($_POST['getBookingInvoice']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $invoiceBooking->getInvoice($_POST['getBookingInvoice']);
	}
}

if( isset($_POST['getServiceInvoice']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $invoiceService->getInvoice($_POST['getServiceInvoice']);
	}
}

if( isset($_POST['getTrainingInvoice']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $invoiceTraining->getInvoice($_POST['getTrainingInvoice']);
	}
}

if( isset($_POST['getUserBookings']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $bookingInfo->getUserBookings($_POST['getUserBookings']);
	}
}

if( isset($_POST['getUserTrainingBookings']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $trainingInfo->getUserTrainingBookings($_POST['getUserTrainingBookings']);
	}
}

if( isset($_POST['jsonGetInstrumentInfo']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $instrumentInfo->jsonGetInstrumentInfo($_POST['jsonGetInstrumentInfo']);
	}
}

if( isset($_POST['jsonGetPmntInfo']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $meta->jsonGetPmntInfo($_POST['jsonGetPmntInfo']);
	}
}

if( isset($_POST['removeTrainingDocument']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $trainingUpdate->removeDocument($_POST['removeTrainingDocument']);
	}
}

if( isset($_POST['removeQuestion']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $trainingUpdate->removeQuestion($_POST['removeQuestion']);
	}
}

if( isset($_POST['createBlankMCQ']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $trainingUpdate->createBlankMCQ($_POST['createBlankMCQ']);
	}
}

if( isset($_POST['createBlankTFQ']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $trainingUpdate->createBlankTFQ($_POST['createBlankTFQ']);
	}
}

if( isset($_POST['calculateHours']) ){
	echo $meta->calculateHours($_POST['dateFrom'], $_POST['timeFrom'], $_POST['dateTo'], $_POST['timeTo']);
}

if( isset($_POST['updateFaq']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $faq->updateFaq($_POST['id'], $_POST['question'], $_POST['answer']);
	}
}

if( isset($_POST['deleteFaq']) ){
	if($_SESSION['isAdmin'] == 1){
		echo $faq->deleteFaq($_POST['id']);
	}
}


?>