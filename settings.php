<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/libraries/password_compatibility_library.php");
require_once("php/config/config.php");
require_once("php/classes/Login.php");
require_once("php/classes/Users.php");

$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
}else{
	$user = new Users();
	$thisUser = $user->getUser($_SESSION['id']);
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

							<header>
								<h2><a href="#">Settings</a></h2>
								<span id="messages" class="byline">
									<?php 
										// show negative messages
										if ($login->errors) { foreach ($login->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										// show positive messages
										if ($login->messages) { foreach ($login->messages as $messages) { echo '<div class="success">' . $messages . '</div>'; } }
									?>
								</span>
							</header>
							
							<div class="info">
								<span class="date">
									<span class="fa fa-cogs"></span> 
								</span>
							</div>

							<!-- Page Content -->
							<section class="is-form">
								<div class="page-content-min-height column-container">
								
									<span class="byline">Change Password</span>
									<form method="post" action="settings.php">
									    <div style="width: 200px;">Old Password</div>
									    <input type="password" name="oldPassword" required autocomplete="off" style="width: 250px;"/>
									    <br>
									    <div>New password (min. 6 characters)</div>
									    <input type="password" name="newPassword" pattern=".{6,}" required autocomplete="off" style="width: 250px;"/>  
									    <br>
									    <div>Repeat new password</div>
									    <input type="password" name="newPasswordRepeat" pattern=".{6,}" required autocomplete="off" style="width: 250px;"/>
									    <br>
									    <input type="submit"  name="editPassword" value="Change Password" style="width: 250px;"/>
									</form>
									
									<span class="byline">User Details</span>
									<form method="post" action="javascript:void(0)" class="updateUser">
									    <div style="width: 200px;">Name</div>
									    <input type="text" name="first" value="<?php echo $thisUser['first']; ?>" required style="width: 150px;"/>
									    <input type="text" name="last" value="<?php echo $thisUser['last']; ?>" required style="width: 150px;"/>
									    <br>
									    <div style="width: 200px;">Email</div>
									    <input type="text" name="email" value="<?php echo $thisUser['email']; ?>" required style="width: 300px;"/>  
									    <br>
									    <div style="width: 200px;">Institution</div>
									    <input type="text" name="institution" value="<?php echo $thisUser['institution']; ?>" required style="width: 300px;"/>
									    <input type="hidden" name="updateUser" value="<?php echo $thisUser['id']; ?>" />
									    <br>
									    <input type="submit"  name="updateUser" value="Update" style="width: 300px;"/>
									</form>
											
								</div>
							</section>
							
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
						<li class="current_page_item"><a href="settings.php">Settings</a></li>
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
						Maintainer: <a href="mailto:mwilson@rx.umaryland.edu">Michael Wilson</a>
						Aesthetics: <a href="http://html5up.net/">HTML5 UP</a>
					</p>
				</div>
				<!-- /Copyright -->

			</div>
			<!-- /Sidebar -->

		</div>
		<!-- /Wrapper -->

		<script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-panels.min.js"></script>
		<script src="js/init.js"></script>
		<script src="js/settings.js"></script>
		
	</body>
</html>