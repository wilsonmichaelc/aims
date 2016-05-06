<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");
require_once("php/classes/Users.php");

$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
}
if ($_SESSION['isAdmin'] == 0){
	header('Location: index.php');
}else{
	$users = new Users();
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
		<link rel="stylesheet" href="css/adminInvoiceBetaStyle.css" />
		<!--<link rel="stylesheet" href="css/adminRequestStyle.css" /> -->
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
									<span class="fa fa-pencil-square-o"></span>
								</span>
								<ul class="stats">
									<li><a href="adminStats.php">Stats</a></li>
									<li class="current-page-start"><a href="adminInvoice.php">Invoices</a></li>
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

							<header>
								<span class="byline">Generate Invoice</span>
							</header>
							<div>

								<!-- Select a month and year -->
								<span class="label">Select A Date Range:</span>
								<div>
									<span class="fromLabel">From:</span>
									<select name="monthFrom">
										<?php $thisMonth = date('m'); ?>
										<?php for($m=1; $m<=12; $m++): ?>
										<?php $month = date('F', mktime(0,0,0,$m, 1, date('Y'))); ?>
										<option value="<?php echo $m; ?>" <?php if($m==$thisMonth){echo 'selected="true"';} ?>><?php echo $month; ?></option>
										<?php endfor; ?>
									</select>
									<select name="dayFrom">
										<?php for($d=1; $d<=31; $d++): ?>
										<option value="<?php echo $d; ?>"><?php echo $d; ?></option>
										<?php endfor; ?>
									</select>
									<select name="yearFrom">
										<?php $year = date("Y"); ?>
										<?php for($y=$year-2; $y<$year+2; $y++): ?>
										<option value="<?php echo $y; ?>" <?php if($y==$year){echo 'selected="true"';} ?>><?php echo $y; ?></option>
										<?php endfor; ?>
									</select>
									<br /><span class="toLabel">To:</span>
									<select name="monthTo">
										<?php for($m=1; $m<=12; $m++): ?>
										<?php $month = date('F', mktime(0,0,0,$m, 1, date('Y'))); ?>
										<option value="<?php echo $m; ?>" <?php if($m==$thisMonth){echo 'selected="true"';} ?>><?php echo $month; ?></option>
										<?php endfor; ?>
									</select>
									<select name="dayTo">
										<?php for($d=1; $d<=31; $d++): ?>
										<option value="<?php echo $d; ?>" <?php if($d == 31){echo 'selected="true"';} ?>><?php echo $d; ?></option>
										<?php endfor; ?>
									</select>
									<select name="yearTo">
										<?php $year = date("Y"); ?>
										<?php for($y=$year-2; $y<$year+2; $y++): ?>
										<option value="<?php echo $y; ?>" <?php if($y==$year){echo 'selected="true"';} ?>><?php echo $y; ?></option>
										<?php endfor; ?>
									</select>
									<input type="button" name="getUsersForSelection" value="Search" />
								</div>
								<!--<div style="color:red;">DO NOT USE... UNDER CONSTRUCTION</div>-->
								<!-- /Select a month and year -->

								<!-- List users that have done work for the month selected USE AJAX -->
								<div class="userList" style="display: none;">
									<br /><span class="label">Select A User:</span>
									<select name="userId">
									</select>
								</div>

								<!-- When finished searching, projects will be listed here. On change, the project info will be fetched. -->
								<br />
								<div class="projectList" style="display: none;">
									<span class="projects label">Select A Project:</span>
									<select name="projectId">
									</select>
								</div>

								<!-- Display the spinner while searching in case it takes some time -->
								<div id="spinner" style="display: none;">Searching ... </div>

								<!-- -->
								<div class="project"></div>
								<div class="bookings"></div>
								<div class="requests"></div>
								<div class="training"></div>
								<div class="createButton"></div>
								<!--
								<span id="projectId"></span>
								<span id="bookingIds"></span>
								<span id="requestIds"></span>
								<span id="trainingIds"></span>
								-->

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
		<script src="js/adminInvoiceBeta.js"></script>
		<!-- /Scripts -->

	</body>
</html>
