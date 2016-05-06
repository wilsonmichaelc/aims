<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");
require_once("php/classes/InstrumentInfo.php");
require_once("php/classes/InstrumentUpdate.php");

$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
}
if ($_SESSION['isAdmin'] == 0){
	header('Location: index.php');
}else{
	$instrumentInfo = new InstrumentInfo();
	$instrumentUpdate = new InstrumentUpdate();
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
									<span class="fa fa-wrench"></span> 
								</span>
								<ul class="stats">
									<li><a href="adminStats.php">Stats</a></li>
									<li><a href="adminInvoice.php">Invoices</a></li>
									<li><a href="adminServices.php">Services</a></li>
									<li class="current-page-start"><a href="adminInstruments.php">Instruments</a></li>
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
							
							<!-- Add Instrument -->
								<header>
									<span class="byline">Add an Instrument</span>
									<span class="byline">
									<?php 
										// show negative messages
										if ($instrumentUpdate->errors) { foreach ($instrumentUpdate->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										// show positive messages
										if ($instrumentUpdate->messages) { foreach ($instrumentUpdate->messages as $messages) { echo '<div class="success">' . $messages . '</div>'; } }
									?>
								</span>
								</header>
								
								<div class="newInstrument">
									<form action="adminInstruments.php" method="post">
										<input type="text" name="name" placeholder="Name" required="true"/>
										<input type="text" name="model" placeholder="Model" required="true"/>
										<input type="text" name="asset" placeholder="Asset Num" required="true"/>
										<input type="text" name="location" placeholder="Room#" required="true" />
										<select name="accuracy" required="true">
											<option value="">Accuracy</option>
											<option value="low">Low</option>
											<option value="high">High</option>
										</select>
										<select name="minBookableUnit" required="true">
											<option value="">Min Unit</option>
											<option value="15">15 min</option>
											<option value="30">30 min</option>
											<option value="60">60 min</option>
										</select>
										
										<input type="text" name="color" class="color {adjust:false,hash:true}" placeholder="Color" required="true"/>
										<input type="submit" name="addInstrument" value="Create" />
										<br>
										Bookable?
										<input type="radio" name="bookable" value="1" required="true">Yes
										<input type="radio" name="bookable" value="0" required="true">No								
									</form>
								</div><br>
							<!-- End Add Instrument -->
							
							<!-- Modify Instrument -->
								<header>
									<span class="byline">Modify an Instrument</span>
								</header>
								<?php $instruments = $instrumentInfo->getInstruments(); ?>
								<?php foreach($instruments as $instrument): ?>
								<div>
									<form method="post" action="javascript:void(0)" class="updateInstrument" id="id<?php echo $instrument['id']; ?>">
										<input type="hidden" name="id" value="<?php echo $instrument['id']; ?>" />
										<input type="text" name="name" required="true" value="<?php echo $instrument['name']; ?>" />
										<input type="text" name="model" required="true" value="<?php echo $instrument['model']; ?>" />
										<input type="text" name="asset" required="true" value="<?php echo $instrument['assetNumber']; ?>" />
										<input type="text" name="location" required="true" value="<?php echo $instrument['location']; ?>" />
										<select name="accuracy">
											<option value="low" <?php if($instrument['accuracy'] == 'low'){echo 'selected="true"';} ?>>Low</option>
											<option value="high" <?php if($instrument['accuracy'] == 'high'){echo 'selected="true"';} ?>>High</option>
										</select>
										<select name="minBookableUnit">
											<option value="15" <?php if($instrument['minBookableUnit'] == '15'){echo 'selected="true"';} ?>>15 min</option>
											<option value="30" <?php if($instrument['minBookableUnit'] == '30'){echo 'selected="true"';} ?>>30 min</option>
											<option value="60" <?php if($instrument['minBookableUnit'] == '60'){echo 'selected="true"';} ?>>60 min</option>
										</select>
										<input type="text" name="color" class="color {adjust:false,hash:true}" value="<?php echo $instrument['color']; ?>" required="true"/>
										<span id="id<?php echo $instrument['id']; ?>"></span>
										<br>
										Bookable?
										<input type="radio" name="bookable" value="1" required="true" <?php if($instrument['bookable'] == 1){echo 'checked="true"';} ?>>Yes
										<input type="radio" name="bookable" value="0" required="true" <?php if($instrument['bookable'] == 0){echo 'checked="true"';} ?>>No								
									</form>
								</div><br />
								<?php endforeach; ?>
							<!-- End Modify Instrument -->
							
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
		<script src="js/jscolor/jscolor.js"></script>
		<script src="js/messageHandler.js"></script>
		<script src="js/adminInstruments.js"></script>
		
		<!-- /Scripts -->

	</body>
</html>