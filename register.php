<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/libraries/password_compatibility_library.php");
require_once("php/libraries/PHPMailer.php");
require_once("php/config/config.php");
require_once("php/classes/Registration.php");
require_once("php/classes/AccountTypes.php");
//require_once("php/classes/Login.php");

//$login = new Login();
$registration = new Registration();
$accountTypes = new AccountTypes();

if (!$registration->registration_successful && !$registration->verification_successful):
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
							<div class="info">
								<span class="date">
									<span class="fa fa-clipboard"></span> 
								</span>
							</div>
							<!-- /Inner Menu -->
							
							<header>
								<h2><a href="#">Register</a></h2>
								<span class="byline">Instrument Management System</span>
								<span class="byline">
									<?php 
										if ($registration->errors) { foreach ($registration->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										if ($registration->messages) { foreach ($registration->messages as $message) { echo '<div class="success">' . $message . '</div>'; } }
									?>
								</span>
							</header>

							<!-- Page Content -->
							<div class="page-content-min-height">
								<div class="column-container">
									<form action="register.php" method="post" accept-charset="UTF-8">
										
										<div class="label">First</div>
										<div class="input"><input type="text" name="first" placeholder="First Name..." value="<?php if(isset($_POST['first'])){echo $_POST['first'];} ?>" required/></div>
										
										<div class="label">Last</div>		
										<div class="input"><input type="text" name="last" placeholder="Last Name..." value="<?php if(isset($_POST['last'])){echo $_POST['last'];} ?>" required/></div>
										
										<div class="label">Email</div>
										<div class="input"><input type="email" name="email" placeholder="Email Address..." value="<?php if(isset($_POST['email'])){echo $_POST['email'];} ?>" required/></div>
										
										<div class="label">Institution</div>
										<div class="input"><input type="text" name="institution" placeholder="University or Comany Name..." value="<?php if(isset($_POST['institution'])){echo $_POST['institution'];} ?>" required/></div>
										
										<div class="label">Username</div>
										<div class="input"><input type="text" pattern="[a-zA-Z0-9_]{2,64}" name="username" placeholder="Preferred Username... (_ is ok)" value="<?php if(isset($_POST['username'])){echo $_POST['username'];} ?>" required/></div>
										
										<div class="label">Password</div>
										<div class="input"><input type="password" name="newPassword" pattern=".{6,}" autocomplete="off" placeholder="Password" required/></div>
										
										<div class="label">Confirm Password</div>
										<div class="input"><input type="password" name="newPasswordRepeat" pattern=".{6,}" autocomplete="off" placeholder="Confirm Password" required/></div>
										
										<div class="label">Account</div>
										<div class="input">
											<select name="accountType">
												<?php $accounts = $accountTypes->getAccountTypes(); ?>
												<?php foreach($accounts as $account): ?>
													<option value="<?php echo $account['id']; ?>" <?php if(isset($_POST['accountType']) && $_POST['accountType'] == $account['id']){ echo 'selected';} ?>><?php echo $account['name']; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										
										<!-- generate and display a captcha and write the captcha string into session -->
									    <div class="label captcha">Robot Filter</div>
									    <div class="input captcha">
									   		<img src="./php/tools/showCaptcha.php" /><br/>
									   		<input type="text" name="captcha" required /><br/><br/>
									    </div>
									    
									    <div class="label"></div>
										<div class="input"><input type="checkbox" name="termsAndConditions" autocomplete="off" required/> I agree to the <a href="eula.php" target="_blank">terms and conditions</a>.</div>
										
										<div class="label"></div>
										<div class="input"><input type="submit" name="register" value="Register"/></div>
									
									</form>
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
						<li><a href="login.php">Login</a></li>
						<li class="current_page_item"><a href="register.php">Register</a></li>
					</ul>
				</nav>
				<!-- /Nav -->

				<!-- Search -->
					<?php include("php/includes/search.php"); ?>
				<!-- /Search -->
		
				<!-- Text -->
				<section class="is-text-style1">
					<div class="inner">
						<?php echo $registration->getSideBarMessage(); ?>
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
<?php endif; ?>
