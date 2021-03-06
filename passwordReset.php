<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/libraries/password_compatibility_library.php");
require_once("php/libraries/PHPMailer.php");
require_once("php/config/config.php");
require_once("php/classes/Login.php");
$login = new Login();

if ($login->isUserLoggedIn() == true) {
    header('Location: index.php');
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

	<body class="left-sidebar">

		<!-- Wrapper -->
		<div id="wrapper">

			<!-- Content -->
			<div id="content">
			
				<!-- Inner Content -->
				<div id="content-inner">
			
						<!-- Post -->
						<article class="is-post is-post-excerpt">

							<!-- Inner Menu -->
							<div class="info">
								<span class="date">
									<span class="fa fa-terminal"></span> 
								</span>
							</div>
							<!-- /Inner Menu -->
							
							<header>
								<h2><a href="#">Password Reset</a></h2>
								<span class="byline">1. Enter your username and click submit</span>
								<span class="byline">2. Check your email for the password reset link</span>
								<span class="byline">3. Click the link and change your password</span>
								<span class="byline">4. Login using your new password</span>
								<span class="byline">
									<?php 
										if ($login->errors) { foreach ($login->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										if ($login->messages) { foreach ($login->messages as $message) { echo '<div class="success">' . $message . '</div>'; } }
									?>
								</span>
							</header>

							<!-- Page Content -->
							<div class="page-content-min-height">
								<div class="column-container">
									<?php if($login->passwordResetLinkIsValid() == true): ?>
									    <!--
									      the user just came to our page by the URL provided in the password-reset-mail and all data is valid
									      so we show the type-your-new-password form
									    -->
									    <form method="post" action="passwordReset.php">
										    <input type='hidden' name='username' value='<?php echo $login->getUsername(); ?>' />
										    <input type='hidden' name='passwordResetHash' value='<?php echo $login->getPasswordResetHash(); ?>' />
										    
										    <label for="reset_input_password_new">New password (min. 6 characters)</label>
										    <input id="reset_input_password_new" class="reset_input" type="password" name="newPassword" pattern=".{6,}" required autocomplete="off" />  
										    <br>
										    <label for="reset_input_password_repeat">Repeat new password</label>
										    <input id="reset_input_password_repeat" class="reset_input" type="password" name="newPasswordRepeat" pattern=".{6,}" required autocomplete="off" />
										    <br>
										    <input type="submit"  name="submitNewPassword" value="Submit new password" />
										</form>

									
								    <?php elseif($login->passwordResetWasSuccessful() == true): ?>
									    <!-- 
									       the user has just successfully entered a new password
									       so we show the index page = the login page
									    -->
									    <form action="login.php" method="post" name="loginform" accept-charset="UTF-8" autocomplete="off">
											<input type="text" name="username" placeholder="Username" required/><br />
											<input type="password" name="password" placeholder="Password" required/><br />	
											<input type="submit" name="login" value="Login"/>		
										</form>
									
									<?php else: ?>
									    <!-- 
									      no data from a password-reset-mail has been provided, so we simply show the request-a-password-reset form
									    -->
									   	<form action="passwordReset.php" method="post" accept-charset="UTF-8" autocomplete="off">
											<input type="text" name="username" placeholder="Username" required/><br />
											<input type="submit" name="requestPasswordReset" value="Submit"/>		
										</form>
									<?php endif; ?>
								</div>
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
		
				<!-- Nav -->
				<nav id="nav">
					<ul>
						<li class="current_page_item"><a href="login.php">Login</a></li>
						<li><a href="register.php">Register</a></li>
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
		<script type="text/javascript">
		    $(document).ready(function() {
		        $("body").css("display", "none");
		        $("body").fadeIn(1000);
		    });
		</script>
		<!-- /Scripts -->

	</body>
</html>