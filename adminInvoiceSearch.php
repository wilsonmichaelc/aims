<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");

$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
}
if ($_SESSION['isAdmin'] == 0){
	header('Location: index.php');
}else{
	//$invoiceSearch = new invoiceSearch();
}
?>
<!DOCTYPE HTML>
<!--
	Striped 2.5 by HTML5 Up!
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>Analytical Instrument Management System</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="keywords" content="Mass Spectrometry Center, Instrument Management, Mass Spec, Goodlett" />
		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400italic,700|Open+Sans+Condensed:300,700" rel="stylesheet" />

		<noscript>
			<link rel="stylesheet" href="css/skel-noscript.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
			<link rel="stylesheet" href="css/style-wide.css" />
		</noscript>
		<!--[if lte IE 9]><link rel="stylesheet" href="css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><link rel="stylesheet" href="css/ie8.css" /><![endif]-->
		<!--[if lte IE 7]><link rel="stylesheet" href="css/ie7.css" /><![endif]-->
	</head>

	<body class="left-sidebar menu">

		<!-- Wrapper -->
		<div id="wrapper">

			<!-- Content -->
			<div id="content">
			
				<!-- Inner Content -->
				<div id="content-inner">
			
						<!-- Post -->
						<article class="is-post is-post-excerpt">

							<!-- Inner Menu -->
							<div class="info menu">
								<span class="date">
									<span class="fa fa-bar-chart-o"></span> 
								</span>
								<ul class="stats">
									<li><a href="adminStats.php">Stats</a></li>
									<li><a href="adminInvoice.php">Invoices</a></li>
									<li><a href="adminServices.php">Services</a></li>
									<li><a href="adminInstruments.php">Instruments</a></li>
									<li><a href="adminUsers.php">Users</a></li>
									<li><a href="adminTraining.php">Training</a></li>
									<li><a href="adminFAQ.php">FAQ</a></li>
									<li><a href="adminAccountTypes.php">Accounts</a></li>
									<li><a href="adminBookingRates.php">Rates</a></li>
									<li><a href="adminInstrumentAccess.php">Access</a></li>
									<li class="current-page-start"><a href="adminInvoiceSearch.php">Invoice-Search</a></li>
								</ul>
							</div>
							<!-- /Inner Menu -->
         
							<div class="page-content-min-height">
								<span class="byline">Search Invoice History</span>
								<div>
									<form action="javascript:void(0)" method="post" class="searchByInvoiceNumber">
										<input style="width: 200px; float: left;" type="number" name="invoiceNumber" placeholder="Invoice Number" required/>
										<input style="width: 60px; float: left;" type="submit" name="searchByInvoiceNumber" value="Search" />
									</form>
								</div>

							</div>

							<div class="project"></div>
							<br>
							<div class="service"></div>
							<br>
							<div class="booking"></div>
							<br>
							<div class="training"></div>
							
						</article>
						<!-- End Post -->

				</div>
				<!-- /Inner Content -->
				
			</div>
			<!-- /Content -->
				
			<!-- Sidebar -->
			<div id="sidebar">
			
				<!-- Logo -->
				<div id="logo">
					<h1>AIMS</h1>
				</div>
				<!-- /Logo -->
		
				<!-- Logout -->
				<section>
					<div class="inner">
						Welcome <?php echo $_SESSION['first']; ?>!&nbsp;&nbsp;&nbsp;
						<strong><a href="index.php?logout">Logout</a></strong>
					</div>
				</section>
				<!-- /Logout -->
				
				<!-- Nav -->
				<nav id="nav">
					<ul>
						<li><a href="index.php">Home</a></li>
						<li><a href="myProjects.php">My Projects</a></li>
						<li><a href="settings.php">Settings</a></li>
						<?php if($_SESSION['isAdmin'] == 1): ?>
						<li class="current_page_item"><a href="adminStats.php">Admin</a></li>
						<?php endif; ?>
						<li><a href="help.php">Help</a></li>
						<li><a href="faq.php">FAQ</a></li>
					</ul>
				</nav>
				<!-- /Nav -->

				<!-- Search -->
					<?php include("php/includes/search.php"); ?>
				<!-- /Search -->
		
				<!-- Text -->
				<section class="is-text-style1">
					<div class="inner">
						<?php echo $login->getSideBarMessage(); ?>
					</div>
				</section>
				<!-- /Text -->


				<!-- Copyright -->
				<div id="copyright">
					<p>
						&copy; 2014 Mass Spectrometry Center.<br />
						Maintainer: <a href="mailto:<?php echo MAINTAINER_EMAIL; ?>"><?php echo MAINTAINER_NAME; ?></a>
						Aesthetics: <a href="http://html5up.net/">HTML5 UP</a>
					</p>
				</div>
				<!-- /Copyright -->

			</div>
			<!-- /Sidebar -->

		</div>
		<!-- /Wrapper -->
		
		<!-- Scripts -->
		<script src="js/jquery.min.js"></script>
		<script src="js/jquery.formatCurrency-1.4.0.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-panels.min.js"></script>
		<script src="js/init.js"></script>
		<script>
			$('form.searchByInvoiceNumber').on('submit', function(e){

				//Clear any existing data...
				$('div.project').html('');
				$('div.service').html('');
				$('div.booking').html('');
				$('div.training').html('');


				var form = $('form.searchByInvoiceNumber');
				$.ajax(
				{
					type: 'post',
					url: 'php/classes/ajax_InvoiceSearch.php',
					dataType: 'json',
					data: form.serialize(),
					success: function(response)
					{
						if(response == false){
							$('div.project').html('<br><br><p>No Results Found.</p>');
						}else{
							var html = '<br><br>';

								html += '<div>';
								html += '	<div style="font-size: 30px; font-weight: bold;">' + response.first + ' ' + response.last + '</div>';
								html += '</div>';

								html += '<br>';
								html += '<div style="font-size: 20px; float: left; margin-left:20px; width: 200px;">Project (' + response.projectId + ')</div>';
								html += '<div style="font-size: 20px; float: left; margin-left: 100px;">' + response.title + '</div>';

								html += '<br><br>';
								html += '<div style="font-size: 20px; float: left; margin-left: 20px; width: 200px;">Primary Investigator:</div>';
								html += '<div style="font-size: 20px; float: left; margin-left: 100px;">' + response.primaryInvestigator + '</div>';

								html += '<br><br>';
								html += '<div style="font-size: 20px; float: left; margin-left: 20px; width: 200px;">Address:</div>';
								html += '<div style="font-size: 20px; float: left; margin-left: 100px;">' + response.addressOne + '</div>';
								html += '<br><div style="font-size: 20px; float: left; margin-left: 320px;">' + response.addressTwo + '</div>';
								html += '<br><div style="font-size: 20px; float: left; margin-left: 320px;">' + response.city + ', ' + response.state + ' ' + response.zip + '</div>';

								html += '<br><br>';
								html += '<div style="font-size: 20px; float: left; margin-left: 20px; width: 200px;">Billing:</div>';
								html += '<div style="font-size: 20px; float: left; margin-left: 100px;">PCBU: ' + response.pcbu + '</div>';
								html += '<br><div style="font-size: 20px; float: left; margin-left: 320px;">Project ID: ' + response.pid + '</div>';
								html += '<br><div style="font-size: 20px; float: left; margin-left: 320px;">Department ID: ' + response.did + '</div>';
								html += '<br><div style="font-size: 20px; float: left; margin-left: 320px;">PO#: ' + response.po + '</div>';

								html += '<br><br>';
								html += '<div style="font-size: 20px; float: left; margin-left: 20px; width: 200px;">Total Cost: </div>';
								html += '<div id="total" style="font-size: 20px; float: left; margin-left: 100px;">$'+response.total+'</div>';

								$('div.project').append(html);
								var jsonString = jQuery.parseJSON(response.jsonString);

								$.each(jsonString.serviceRequests, function(i, item) {
								    $.ajax(
									{
										type: 'post',
										url: 'php/classes/ajax_InvoiceSearch.php',
										dataType: 'json',
										data: { type: 'service', id: item },
										success: function(response)
										{

											if(response != false){

												var html = '<br>';
												html += '<div style="font-size: 20px; float: left; margin-left: 20px; width: 1200px;">Service: ';
												html += '	<span style="margin-left: 20px; width: 300px;">' + response.name + '</span>';
												html += '	<span style="margin-left: 30px; width: 200px;">' + response.samples + ' samples</span>';
												if(response.prep = 1){
													html += '	<span style="margin-left: 30px; width: 200px;">w/ Sample Prep</span>';
												}else{
													html += '	<span style="margin-left: 30px; width: 200px;">w/o Sample Prep</span>';
												}
												if(response.replicates > 1){
													html += '	<span style="margin-left: 30px; width: 200px;">' + response.replicates + ' Replicates</span>';
												}
												html += '</div>';
												$('div.service').append(html);
											}
										}
									})
								})

								$.each(jsonString.bookings, function(i, item){
								    $.ajax(
									{
										type: 'post',
										url: 'php/classes/ajax_InvoiceSearch.php',
										dataType: 'json',
										data: { type: 'booking', id: item },
										success: function(response)
										{
											if(response != false){

												var df = response.dateFrom.split('-');
												var tf = response.timeFrom.split(':');
												var dt = response.dateTo.split('-');
												var tt = response.timeTo.split(':');
												var dFrom = new Date(df[0], df[1]-1, df[2], tf[0], tf[1], 0, 0);
												var dTo = new Date(dt[0], dt[1]-1, dt[2], tt[0], tt[1], 0, 0);
												var hrs = Math.abs(dTo.getTime() - dFrom.getTime()) / 36e5;

												var html = '<br>';
												html += '<div style="font-size: 20px; float: left; margin-left: 20px; width: 1200px;">Booking: ';
												html += '	<span style="margin-left: 20px; width: 300px;">' + response.name + '</span>';
												html += '	<span style="margin-left: 20px; width: 600px;">';
												html += 	response.dateFrom + ' @ ' + response.timeFrom;
												html += '  --  ';
												html += 	response.dateTo + ' @ ' + response.timeTo;
												html += '	</span>';
												html += '	<span style="margin-left: 30px; width: 200px;">' + hrs + ' hours</span>';
												html += '</div>';
												$('div.booking').append(html);
											}
										}
									})
								})

								$.each(jsonString.trainings, function(i, item) {
									$.ajax(
									{
										type: 'post',
										url: 'php/classes/ajax_InvoiceSearch.php',
										dataType: 'json',
										data: { type: 'training', id: item },
										success: function(response)
										{
											if(response != false){

												var df = response.dateFrom.split('-');
												var tf = response.timeFrom.split(':');
												var dt = response.dateTo.split('-');
												var tt = response.timeTo.split(':');
												var dFrom = new Date(df[0], df[1]-1, df[2], tf[0], tf[1], 0, 0);
												var dTo = new Date(dt[0], dt[1]-1, dt[2], tt[0], tt[1], 0, 0);
												var hrs = Math.abs(dTo.getTime() - dFrom.getTime()) / 36e5;

												var html = '<br>';
												html += '<div style="font-size: 20px; float: left; margin-left: 20px; width: 1200px;">Training: ';
												html += '	<span style="margin-left: 20px; width: 300px;">' + response.name + '</span>';
												html += '	<span style="margin-left: 20px; width: 600px;">';
												html += 	response.dateFrom + ' @ ' + response.timeFrom;
												html += '  --  ';
												html += 	response.dateTo + ' @ ' + response.timeTo;
												html += '	</span>';
												html += '	<span style="margin-left: 30px; width: 200px;">' + hrs + ' hours</span>';
												html += '</div>';
												$('div.training').append(html);
											}
										}
									})
								})

						}
					}

				});
			});
		
		</script>
		<!-- /Scripts -->

	</body>
</html>