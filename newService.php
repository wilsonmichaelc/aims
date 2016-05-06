<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");
require_once("php/classes/ProjectInfo.php");
require_once("php/classes/NewServiceRequest.php");
require_once("php/classes/MSCServices.php");
require_once("php/libraries/PHPMailer.php");

$login = new Login();
$projectInfo = new ProjectInfo();
$mscServices = new MSCServices();
$newServiceRequest = new NewServiceRequest();


// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
}
$projects = $projectInfo->getActiveProjects($_SESSION['id']);
$analysisServices = $mscServices->getAnalysisServices();
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
									<span class="fa fa-puzzle-piece"></span>
								</span>
								<ul class="stats">
									<li><a href="newProject.php">Project</a></li>
									<li class="current-page-start"><a href="newService.php">Services</a></li>
									<li><a href="newTraining.php">Training</a></li>
									<li><a href="newBooking.php">Bookings</a></li>
								</ul>
							</div>
							<!-- /Inner Menu -->

							<header>
								<h2><a href="#">New Service Request</a></h2>
								<?php if( (count($projects) != 0) && (count($analysisServices) != 0) ): ?>
									<span class="byline">We offer a range of proteomics and small molecule services.</span>
								<?php endif; ?>
								<span class="byline">
									<?php
										// show negative messages
										if ($newServiceRequest->errors) { foreach ($newServiceRequest->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										// show positive messages
										if ($newServiceRequest->messages) { foreach ($newServiceRequest->messages as $message) { echo '<div class="success">' . $message . '</div>'; } }
									?>
								</span>
							</header>

							<!-- Page Content -->
							<div class="page-content-min-height">

								<?php if( count($projects) == 0 ): ?>
										<span class="byline error">It looks like you don't have any projects yet. Click on <a href="newProject.php">HERE</a> to create one.</span>
								<?php elseif( count($analysisServices) == 0 ): ?>
										<span class="byline error">It looks like we aren't offering any services at this time. Please check back soon!</span>
								<?php else: ?>

								<!-- Create a new project -->
								<section class="is-form">
									<form action="newService.php" method="post" id="newServiceRequest" accept-charset="UTF-8">

										<div><strong>Sample Information</strong></div>

										<div class="column-container">
											<p></p>
											Required Information<br>

											<select name="projectId" required>
												<option value="">Choose a project...</option>
												<?php if(count($projects) != 0): ?>
													<?php foreach($projects as $project): ?>
														<option value="<?php echo $project['id']; ?>">
														<?php echo $project['title']; ?>
														</option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>

											<input type="text" class="text" name="label" placeholder="Sample Label" required/>
											<input type="text" class="text" name="concentration" placeholder="Concentration" required/>
											<input type="radio" class="radio" name="state" value="solid" required="true" checked/>Solid
											<input type="radio" class="radio" name="state" value="liquid" required/>Liquid
											<input type="text" class="text" name="composition" placeholder="Buffer Composition" required/>
											<input type="text" class="text" name="digestionEnzyme" placeholder="Digestion Enzyme" required/>
											<input type="text" class="text" name="species" placeholder="Taxonomy (species)" required/>
											<p></p>
											Additional Information<br>
											<input type="text" class="text" name="purification" placeholder="Purification Method" />
											<input type="text" class="text" name="redoxChemicals" placeholder="Reduction & Alkylation Chemicals" />
											<input type="text" class="text" name="molecularWeight" placeholder="Approx. Molecular Weight (Da)" />
											<input type="text" class="text" name="suspectedModifications" placeholder="Suspected Modifications" />
											<input type="text" class="text" name="aaModifications" placeholder="Amino Acid Modifications" />
											<textarea type="text" class="text" name="sequence" placeholder="Sequence"></textarea>
											<textarea type="textarea" class="text" name="comments" placeholder="Comments"></textarea>
										</div>

										<div>
											<strong>Services Requested</strong><p></p>
										</div>

										<div class="column-container" id="allServices">

											<?php foreach($analysisServices as $service): ?>

												<div id="analysisService" class="service-box-border service-box">
													<div id="id" value="<?php echo $service['id']; ?>"></div>
													<input id="service" type="checkbox" name="<?php echo 'msc' . $service['id']; ?>" />&nbsp;<?php echo $service['name']; ?>

													<div style="width: 90px; float: right;">
														<select id="replicates" name="<?php echo $service['id']; ?>_Replicates">
															<option value="1">Replicates?</option>
															<option value="1">none</option>
															<option value="2">duplicate</option>
															<option value="3">triplicate</option>
														</select>
													</div>

													<input id="samples" type="number" min="1" step="1" class="text" name="<?php echo $service['id']; ?>_Samples" placeholder="# Samples" style="width: 100px; float: right;"/>

													<?php if($service['samplePrepId'] != 0): ?>
														<?php $prepService = $mscServices->getPrepService($service['id']); ?>
														<div class="sample-prep">
															<input id="prep" type="checkbox" name="<?php echo $service['id']; ?>_Prep" value="<?php echo $prepService['id']; ?>"/>My samples will also need to be prepped.
														</div>
													<?php else: ?>
														<div class="sample-prep">
														</div>
													<?php endif; ?>

												</div>

											<?php endforeach; ?>
										</div>

										<p></p>
										<div class="column-container" style="display: none;"> <!-- Add: style="display: none;" to remove estimate from this page -->
											<strong>Cost Estimate </strong>
											<input style="width: 150px;" type="text" id="estimate" name="estimate" disabled/>
										</div>

										<p></p>
										<div class="column-container">
											<input type="hidden" name="accountType" value="<?php echo $_SESSION['accountType']; ?>" />
											<input type="submit" name="createServiceRequest" value="Submit" />
										</div>

									</form>
								</section>

								<?php endif; ?>

							</div>
							<!-- End Page Content -->

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
						<li class="current_page_item"><a href="index.php">Home</a></li>
						<li><a href="myProjects.php">My Projects</a></li>
						<li><a href="settings.php">Settings</a></li>
						<?php if($_SESSION['isAdmin'] == 1): ?>
						<li><a href="adminStats.php">Admin</a></li>
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
		<script src="js/newService.js"></script>
		<!-- /Scripts -->

	</body>
</html>
