<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");
require_once("php/classes/InstrumentInfo.php");
require_once("php/classes/Users.php");

$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
}
if ($_SESSION['isAdmin'] == 0){
	header('Location: index.php');
}
$instrumentInfo = new InstrumentInfo();
$userInfo = new Users();
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
									<li class="current-page-start"><a href="adminStats.php">Stats</a></li>
									<li><a href="adminInvoice.php">Invoices</a></li>
									<li><a href="adminServices.php">Services</a></li>
									<li><a href="adminInstruments.php">Instruments</a></li>
									<li><a href="adminUsers.php">Users</a></li>
									<li><a href="adminTraining.php">Training</a></li>
									<li><a href="adminFAQ.php">FAQ</a></li>
									<li><a href="adminAccountTypes.php">Accounts</a></li>
									<li><a href="adminBookingRates.php">Rates</a></li>
									<li><a href="adminInstrumentAccess.php">Access</a></li>
									<li><a href="adminInvoiceSearch.php">Invoice-Search</a></li>
								</ul>
							</div>
							<!-- /Inner Menu -->
							<?php 
				            	date_default_timezone_set('America/New_York');
				            	$year = date("Y");
				            	$low = 2013;
				            ?>
				            
							<div class="page-content-min-height">
							<span class="byline">Instrument Usage</span>
								Range: 
								<form name="InstrumentByMonth" method="post" action="javascript:void(0)" style="display: inline;">
									<select name="startYear" style="width: 50px;">
										<option value="0">none</option>
										<?php 
					                		for($y=$low; $y<=$year; $y++){
						                		echo '<option value="' . $y . '"';
						                		if($y == $year){echo ' selected="true"';}
						                		echo '>' . $y . '</option>';
					                		}
					                	?>
									</select>
									<select name="endYear" style="width: 50px;">
										<option value="0">none</option>
										<?php 
					                		for($y=$low; $y<=$year; $y++){
						                		echo '<option value="' . $y . '"';
						                		if($y == $year){echo ' selected="true"';}
						                		echo '>' . $y . '</option>';
					                		}
					                	?>
									</select>
									AND
									<select name="instrument" style="max-width: 150px;">
										<option value="null">Instrument</option>
										<?php 
											$instruments = $instrumentInfo->getInstruments();
											foreach($instruments as $instrument){
												echo '<option value="' . $instrument['id'] . '">' . $instrument['name'] . '</option>' ;
											}
										?>
									</select>
									OR
									<select name="user" style="max-width: 150px;">
										<option value="null">User</option>
										<?php 
											$users = $userInfo->getAllUsers();
											foreach($users as $user){
												echo '<option value="' . $user['id'] . '">' . $user['first'] . ' ' . $user['last'] . '</option>' ;
											}
										?>
									</select>
									<select name="month" style="max-width: 100px; display: none;">
										<option value="null">Month</option>
										<option value="01">January</option>
										<option value="02">February</option>
										<option value="03">March</option>
										<option value="04">April</option>
										<option value="05">May</option>
										<option value="06">June</option>
										<option value="07">July</option>
										<option value="08">August</option>
										<option value="09">September</option>
										<option value="10">October</option>
										<option value="11">November</option>
										<option value="12">December</option>
									</select>
									<input type="submit" value="Update" style="width: 75px;"/>
								</form>
								
								<p><br></p>
								
								<canvas id="InstrumentByMonth" width="700" height="200"></canvas>
								<div style="width: 700px;" class="InstrumentByMonth legend"></div>
							</div>
							
							<div style="border-top: 1px solid gray; margin-bottom: 40px; margin-top: 20px; width: 700px;"></div>
							
							<div class="page-content-min-height">
							<span class="byline">Service Requests</span>
							
								Select a range or leave blank for current year. 
								<form name="ServiceRequestsByMonth" method="post" action="javascript:void(0)" style="display: inline;">
									<select name="startYear" style="width: 50px;">
										<option value="">none</option>
										<?php 
					                		for($y=$low; $y<=$year; $y++){
						                		echo '<option value="' . $y . '"';
						                		if($y == $year){echo ' selected="true"';}
						                		echo '>' . $y . '</option>';
					                		}
					                	?>
									</select>
									<select name="endYear" style="width: 50px;">
										<option value="">none</option>
										<?php 
					                		for($y=$low; $y<=$year; $y++){
						                		echo '<option value="' . $y . '"';
						                		if($y == $year){echo ' selected="true"';}
						                		echo '>' . $y . '</option>';
					                		}
					                	?>
									</select>
									<input type="submit" value="Update" style="width: 75px;"/>
								</form>
								
								<p><br></p>
								
								<canvas id="ServiceRequestsByMonth" width="700" height="200"></canvas>
								<div style="width: 700px;" class="ServiceRequestsByMonth legend"></div>
							</div>
							
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
		<script src="js/skel.min.js"></script>
		<script src="js/skel-panels.min.js"></script>
		<script src="js/init.js"></script>
		<script src="js/Chart.js"></script>
		<script src="js/ajaxAdminStats.js"></script>
		<!-- /Scripts -->

	</body>
</html>