<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	
	require_once("php/config/config.php");
	require_once("php/classes/Login.php");
	$login = new Login();
	
	if ($login->isUserLoggedIn() == false) {
	    header('Location: login.php');
	}
	if ($_SESSION['isAdmin'] == 0){
		header('Location: index.php');
	}else{
		require_once("php/classes/ServicesOffered.php");
		$servicesOffered = new ServicesOffered();
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
		<link rel="stylesheet" href="css/adminServices.css" />
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
									<li><a href="adminStats.php">Stats</a></li>
									<li><a href="adminInvoice.php">Invoices</a></li>
									<li class="current-page-start"><a href="adminServices.php">Services</a></li>
									<li><a href="adminInstruments.php">Instruments</a></li>
									<li><a href="adminUsers.php">Users</a></li>
									<li><a href="adminTraining.php">Training</a></li>
									<li><a href="adminFAQ.php">FAQ</a></li>
									<li><a href="adminAccountTypes.php">Accounts</a></li>
								</ul>
							</div>
							<!-- /Inner Menu -->
							
							
														
							<!-- Create New Service -->
							<span class="byline">Create or Edit Services Offered</span><p></p>
							<span class="byline">
									<?php 
										// show negative messages
										if ($servicesOffered->errors) { foreach ($servicesOffered->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										// show positive messages
										if ($servicesOffered->messages) { foreach ($servicesOffered->messages as $messages) { echo '<div class="success">' . $messages . '</div>'; } }
									?>
								</span>
							
							
							<form action="adminServices.php" method="post">
								
								<span style="font-weight: bold; font-size: 14pt;">Service:</span>
								<input id="serviceName" type="text" name="name" placeholder="Service Name" required="true"/>
	
								<table class="services">
									<tr>
										<td></td>
										<td>UMB</td>
										<td>Collaborator</td>
										<td>Affiliate</td>
										<td>Member</td>
										<td>Non-Profit</td>
										<td>For-Profit</td>
									</tr>
									<tr>
										<td>Regular</td>
										<td><input type="text" maxlength="4" size="4" required="true" name="memberRegular" value="<?php if( isset($_POST['memberRegular']) && !empty($_POST['memberRegular']) ){echo $_POST['memberRegular'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" required="true" name="collaboratorRegular" value="<?php if( isset($_POST['collaboratorRegular']) && !empty($_POST['collaboratorRegular']) ){echo $_POST['collaboratorRegular'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" required="true" name="umbRegular" value="<?php if( isset($_POST['umbRegular']) && !empty($_POST['umbRegular']) ){echo $_POST['umbRegular'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" required="true" name="affiliateRegular" value="<?php if( isset($_POST['affiliateRegular']) && !empty($_POST['affiliateRegular']) ){echo $_POST['affiliateRegular'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" required="true" name="nonProfitRegular" value="<?php if( isset($_POST['nonProfitRegular']) && !empty($_POST['nonProfitRegular']) ){echo $_POST['nonProfitRegular'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" required="true" name="forProfitRegular" value="<?php if( isset($_POST['forProfitRegular']) && !empty($_POST['forProfitRegular']) ){echo $_POST['forProfitRegular'];} ?>" /></td>
									</tr>
									<tr>
										<td>Discount</td>
										<td><input type="text" maxlength="4" size="4" required="true" name="memberDiscount" value="<?php if( isset($_POST['memberDiscount']) && !empty($_POST['memberDiscount']) ){echo $_POST['memberDiscount'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" required="true" name="collaboratorDiscount" value="<?php if( isset($_POST['collaboratorDiscount']) && !empty($_POST['collaboratorDiscount']) ){echo $_POST['collaboratorDiscount'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" required="true" name="umbDiscount" value="<?php if( isset($_POST['umbDiscount']) && !empty($_POST['umbDiscount']) ){echo $_POST['umbDiscount'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" required="true" name="affiliateDiscount" value="<?php if( isset($_POST['affiliateDiscount']) && !empty($_POST['affiliateDiscount']) ){echo $_POST['affiliateDiscount'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" required="true" name="nonProfitDiscount" value="<?php if( isset($_POST['nonProfitDiscount']) && !empty($_POST['nonProfitDiscount']) ){echo $_POST['nonProfitDiscount'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" required="true" name="forProfitDiscount" value="<?php if( isset($_POST['forProfitDiscount']) && !empty($_POST['forProfitDiscount']) ){echo $_POST['forProfitDiscount'];} ?>" /></td>
									</tr>
									<tr>
										<td>Cutoff</td>
										<td><input type="text" maxlength="2" size="2" required="true" name="memberCutoff" value="<?php if( isset($_POST['memberCutoff']) && !empty($_POST['memberCutoff']) ){echo $_POST['memberCutoff'];} ?>" /></td>
										<td><input type="text" maxlength="2" size="2" required="true" name="collaboratorCutoff" value="<?php if( isset($_POST['collaboratorCutoff']) && !empty($_POST['collaboratorCutoff']) ){echo $_POST['collaboratorCutoff'];} ?>" /></td>				
										<td><input type="text" maxlength="2" size="2" required="true" name="umbCutoff" value="<?php if( isset($_POST['umbCutoff']) && !empty($_POST['umbCutoff']) ){echo $_POST['umbCutoff'];} ?>" /></td>
										<td><input type="text" maxlength="2" size="2" required="true" name="affiliateCutoff" value="<?php if( isset($_POST['affiliateCutoff']) && !empty($_POST['affiliateCutoff']) ){echo $_POST['affiliateCutoff'];} ?>" /></td>
										<td><input type="text" maxlength="2" size="2" required="true" name="nonProfitCutoff" value="<?php if( isset($_POST['nonProfitCutoff']) && !empty($_POST['nonProfitCutoff']) ){echo $_POST['nonProfitCutoff'];} ?>" /></td>
										<td><input type="text" maxlength="2" size="2" required="true" name="forProfitCutoff" value="<?php if( isset($_POST['forProfitCutoff']) && !empty($_POST['forProfitCutoff']) ){echo $_POST['forProfitCutoff'];} ?>" /></td>
									</tr>
								</table>
								
								<div>
									<input type="checkbox" name="prepCheckbox" value="prepCheckbox">
									This service needs a sample preparation option.
								</div>
								<table id="prepCheckbox" class="services prep" style="display: none;">
									<tr>
										<td>Regular</td>
										<td><input type="text" maxlength="4" size="4" name="p_memberRegular" value="<?php if( isset($_POST['p_memberRegular']) && !empty($_POST['p_memberRegular']) ){echo $_POST['p_memberRegular'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" name="p_collaboratorRegular" value="<?php if( isset($_POST['p_collaboratorRegular']) && !empty($_POST['p_collaboratorRegular']) ){echo $_POST['p_collaboratorRegular'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" name="p_umbRegular" value="<?php if( isset($_POST['p_umbRegular']) && !empty($_POST['p_umbRegular']) ){echo $_POST['p_umbRegular'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" name="p_affiliateRegular" value="<?php if( isset($_POST['p_affiliateRegular']) && !empty($_POST['p_affiliateRegular']) ){echo $_POST['p_affiliateRegular'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" name="p_nonProfitRegular" value="<?php if( isset($_POST['p_nonProfitRegular']) && !empty($_POST['p_nonProfitRegular']) ){echo $_POST['p_nonProfitRegular'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" name="p_forProfitRegular" value="<?php if( isset($_POST['p_forProfitRegular']) && !empty($_POST['p_forProfitRegular']) ){echo $_POST['p_forProfitRegular'];} ?>" /></td>
									</tr>
									<tr>
										<td>Discount</td>
										<td><input type="text" maxlength="4" size="4" name="p_memberDiscount" value="<?php if( isset($_POST['p_memberDiscount']) && !empty($_POST['p_memberDiscount']) ){echo $_POST['p_memberDiscount'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" name="p_collaboratorDiscount" value="<?php if( isset($_POST['p_collaboratorDiscount']) && !empty($_POST['p_collaboratorDiscount']) ){echo $_POST['p_collaboratorDiscount'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" name="p_umbDiscount" value="<?php if( isset($_POST['p_umbDiscount']) && !empty($_POST['p_umbDiscount']) ){echo $_POST['p_umbDiscount'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" name="p_affiliateDiscount" value="<?php if( isset($_POST['p_affiliateDiscount']) && !empty($_POST['p_affiliateDiscount']) ){echo $_POST['p_affiliateDiscount'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" name="p_nonProfitDiscount" value="<?php if( isset($_POST['p_nonProfitDiscount']) && !empty($_POST['p_nonProfitDiscount']) ){echo $_POST['p_nonProfitDiscount'];} ?>" /></td>
										<td><input type="text" maxlength="4" size="4" name="p_forProfitDiscount" value="<?php if( isset($_POST['p_forProfitDiscount']) && !empty($_POST['p_forProfitDiscount']) ){echo $_POST['p_forProfitDiscount'];} ?>" /></td>
									</tr>
									<tr>
										<td>Cutoff</td>
										<td><input type="text" maxlength="2" size="2" name="p_memberCutoff" value="<?php if( isset($_POST['p_memberCutoff']) && !empty($_POST['p_memberCutoff']) ){echo $_POST['p_memberCutoff'];} ?>" /></td>
										<td><input type="text" maxlength="2" size="2" name="p_collaboratorCutoff" value="<?php if( isset($_POST['p_collaboratorCutoff']) && !empty($_POST['p_collaboratorCutoff']) ){echo $_POST['p_collaboratorCutoff'];} ?>" /></td>				
										<td><input type="text" maxlength="2" size="2" name="p_umbCutoff" value="<?php if( isset($_POST['p_umbCutoff']) && !empty($_POST['p_umbCutoff']) ){echo $_POST['p_umbCutoff'];} ?>" /></td>
										<td><input type="text" maxlength="2" size="2" name="p_affiliateCutoff" value="<?php if( isset($_POST['p_affiliateCutoff']) && !empty($_POST['p_affiliateCutoff']) ){echo $_POST['p_affiliateCutoff'];} ?>" /></td>
										<td><input type="text" maxlength="2" size="2" name="p_nonProfitCutoff" value="<?php if( isset($_POST['p_nonProfitCutoff']) && !empty($_POST['p_nonProfitCutoff']) ){echo $_POST['p_nonProfitCutoff'];} ?>" /></td>
										<td><input type="text" maxlength="2" size="2" name="p_forProfitCutoff" value="<?php if( isset($_POST['p_forProfitCutoff']) && !empty($_POST['p_forProfitCutoff']) ){echo $_POST['p_forProfitCutoff'];} ?>" /></td>
									</tr>
								</table>
								
								<input type="submit" name="newService" value="Create Service" />
	
							</form>
							<!-- End Create New Service -->
							
							<!-- Manage Services -->
							<span class="byline">Manage Existing Services<p></p></span>
								<?php $services = $servicesOffered->getServicesOffered(); ?>
								<?php foreach($services as $service): ?>
									<form action="javascript:void(0)" method="post" class="updateService" id="id<?php echo $service['id']; ?>">
									
										<input type="hidden" name="updateService" value="1" />
										<input type="hidden" name="serviceId" value="<?php echo $service['id']; ?>" />
										<b style="font-size: 14pt;">Service: 
										<input type="text" name="name" value="<?php echo $service['name']; ?>" required="true"/>
										</b><span class="updateServiceResponse"></span>
			
										<table class="services">
											<tr>
												<td></td>
												<td>UMB</td>
												<td>Collaborator</td>
												<td>Affiliate</td>
												<td>Member</td>
												<td>Non-Profit</td>
												<td>For-Profit</td>
											</tr>
											<tr>
												<td>Regular</td>
												<td><input type="text" maxlength="4" size="4" name="memberRegular" value="<?php echo $service['memberRegular']; ?>" /></td>
												<td><input type="text" maxlength="4" size="4" name="collaboratorRegular" value="<?php echo $service['collaboratorRegular']; ?>" /></td>
												<td><input type="text" maxlength="4" size="4" name="umbRegular" value="<?php echo $service['umbRegular']; ?>" /></td>
												<td><input type="text" maxlength="4" size="4" name="affiliateRegular" value="<?php echo $service['affiliateRegular']; ?>" /></td>
												<td><input type="text" maxlength="4" size="4" name="nonProfitRegular" value="<?php echo $service['nonProfitRegular']; ?>" /></td>
												<td><input type="text" maxlength="4" size="4" name="forProfitRegular" value="<?php echo $service['forProfitRegular']; ?>" /></td>
											</tr>
											<tr>
												<td>Discount</td>
												<td><input type="text" maxlength="4" size="4" name="memberDiscount" value="<?php echo $service['memberDiscount']; ?>" /></td>
												<td><input type="text" maxlength="4" size="4" name="collaboratorDiscount" value="<?php echo $service['collaboratorDiscount']; ?>" /></td>
												<td><input type="text" maxlength="4" size="4" name="umbDiscount" value="<?php echo $service['umbDiscount']; ?>" /></td>
												<td><input type="text" maxlength="4" size="4" name="affiliateDiscount" value="<?php echo $service['affiliateDiscount']; ?>" /></td>
												<td><input type="text" maxlength="4" size="4" name="nonProfitDiscount" value="<?php echo $service['nonProfitDiscount']; ?>" /></td>
												<td><input type="text" maxlength="4" size="4" name="forProfitDiscount" value="<?php echo $service['forProfitDiscount']; ?>" /></td>
											</tr>
											<tr>
												<td>Cutoff</td>
												<td><input type="text" maxlength="2" size="2" name="memberCutoff" value="<?php echo $service['memberCutoff']; ?>" /></td>
												<td><input type="text" maxlength="2" size="2" name="collaboratorCutoff" value="<?php echo $service['collaboratorCutoff']; ?>" /></td>				
												<td><input type="text" maxlength="2" size="2" name="umbCutoff" value="<?php echo $service['umbCutoff']; ?>" /></td>
												<td><input type="text" maxlength="2" size="2" name="affiliateCutoff" value="<?php echo $service['affiliateCutoff']; ?>" /></td>
												<td><input type="text" maxlength="2" size="2" name="nonProfitCutoff" value="<?php echo $service['nonProfitCutoff']; ?>" /></td>
												<td><input type="text" maxlength="2" size="2" name="forProfitCutoff" value="<?php echo $service['forProfitCutoff']; ?>" /></td>
											</tr>
										</table>
										
										<?php if($service['samplePrepId'] != null && !empty($service['samplePrepId'])): ?>
											
											<?php $prep = $servicesOffered->getPrepService($service['samplePrepId']); ?>
											<input type="hidden" name="prepId" value="<?php echo $service['samplePrepId']; ?>" />
											<table class="services prep">
												<tr>
													<td colspan="7">This service also offers a sample preparation option...</td>
												</tr>
												<tr>
													<td>Regular</td>
													<td><input type="text" maxlength="4" size="4" name="p_memberRegular" value="<?php echo $prep['memberRegular']; ?>" /></td>
													<td><input type="text" maxlength="4" size="4" name="p_collaboratorRegular" value="<?php echo $prep['collaboratorRegular']; ?>" /></td>
													<td><input type="text" maxlength="4" size="4" name="p_umbRegular" value="<?php echo $prep['umbRegular']; ?>" /></td>
													<td><input type="text" maxlength="4" size="4" name="p_affiliateRegular" value="<?php echo $prep['affiliateRegular']; ?>" /></td>
													<td><input type="text" maxlength="4" size="4" name="p_nonProfitRegular" value="<?php echo $prep['nonProfitRegular']; ?>" /></td>
													<td><input type="text" maxlength="4" size="4" name="p_forProfitRegular" value="<?php echo $prep['forProfitRegular']; ?>" /></td>
												</tr>
												<tr>
													<td>Discount</td>
													<td><input type="text" maxlength="4" size="4" name="p_memberDiscount" value="<?php echo $prep['memberDiscount']; ?>" /></td>
													<td><input type="text" maxlength="4" size="4" name="p_collaboratorDiscount" value="<?php echo $prep['collaboratorDiscount']; ?>" /></td>
													<td><input type="text" maxlength="4" size="4" name="p_umbDiscount" value="<?php echo $prep['umbDiscount']; ?>" /></td>
													<td><input type="text" maxlength="4" size="4" name="p_affiliateDiscount" value="<?php echo $prep['affiliateDiscount']; ?>" /></td>
													<td><input type="text" maxlength="4" size="4" name="p_nonProfitDiscount" value="<?php echo $prep['nonProfitDiscount']; ?>" /></td>
													<td><input type="text" maxlength="4" size="4" name="p_forProfitDiscount" value="<?php echo $prep['forProfitDiscount']; ?>" /></td>
												</tr>
												<tr>
													<td>Cutoff</td>
													<td><input type="text" maxlength="2" size="2" name="p_memberCutoff" value="<?php echo $prep['memberCutoff']; ?>" /></td>
													<td><input type="text" maxlength="2" size="2" name="p_collaboratorCutoff" value="<?php echo $prep['collaboratorCutoff']; ?>" /></td>				
													<td><input type="text" maxlength="2" size="2" name="p_umbCutoff" value="<?php echo $prep['umbCutoff']; ?>" /></td>
													<td><input type="text" maxlength="2" size="2" name="p_affiliateCutoff" value="<?php echo $prep['affiliateCutoff']; ?>" /></td>
													<td><input type="text" maxlength="2" size="2" name="p_nonProfitCutoff" value="<?php echo $prep['nonProfitCutoff']; ?>" /></td>
													<td><input type="text" maxlength="2" size="2" name="p_forProfitCutoff" value="<?php echo $prep['forProfitCutoff']; ?>" /></td>
												</tr>
											</table>
											
										
										<?php endif; ?>
										<!-- <input type="submit" name="updateService" value="Update" /> -->
			
									</form>
									
									<div style="border-bottom: 1px solid #999; margin-bottom: 20px; max-width: 600px"></div>
								<?php endforeach; ?>
							<!-- End Manage Services -->
							
							
							<!-- End Content -->
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
						Maintainer: <a href="mailto:mwilson@rx.umaryland.edu">Michael Wilson</a>
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
		<script src="js/adminServices.js"></script>
		<!-- /Scripts -->

	</body>
</html>