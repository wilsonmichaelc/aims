<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");
require_once("php/classes/Users.php");
require_once("php/classes/TrainingModules.php");
require_once("php/classes/NewTrainingModule.php");
require_once("php/classes/TrainingModuleUpdate.php");

$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
}
if ($_SESSION['isAdmin'] == 0){
	header('Location: index.php');
}else{
	$user = new Users();
	$trainingModules = new TrainingModules();
	$newTrainingModule = new NewTrainingModule();
	$updateTrainingModule = new TrainingModuleUpdate();
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
		<link rel="stylesheet" href="css/adminTraining.css" />
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
									<span class="fa fa-book"></span> 
								</span>
								<ul class="stats">
									<li><a href="adminStats.php">Stats</a></li>
									<li><a href="adminInvoice.php">Invoices</a></li>
									<li><a href="adminServices.php">Services</a></li>
									<li><a href="adminInstruments.php">Instruments</a></li>
									<li><a href="adminUsers.php">Users</a></li>
									<li class="current-page-start"><a href="adminTraining.php">Training</a></li>
									<li><a href="adminFAQ.php">FAQ</a></li>
									<li><a href="adminAccountTypes.php">Accounts</a></li>
									<li><a href="adminBookingRates.php">Rates</a></li>
									<li><a href="adminInstrumentAccess.php">Access</a></li>
									<li><a href="adminInvoiceSearch.php">Invoice-Search</a></li>
								</ul>
							</div>
							<!-- /Inner Menu -->
							
							<!-- New Training Modules -->
							<span class="byline">New Training Modules</span>
							<span class="byline">
									<?php 
										// show negative messages
										if ($newTrainingModule->errors) { foreach ($newTrainingModule->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										// show positive messages
										if ($newTrainingModule->messages) { foreach ($newTrainingModule->messages as $messages) { echo '<div class="success">' . $messages . '</div>'; } }
									?>
								</span>
							
							<div class="newTrainingModule">
								<div class="toggleMe">
									<form method="post" action="adminTraining.php" enctype="multipart/form-data" id="createTrainingModule">
										<div class="moduleInfo">
											<b>General Info</b>
											<input type="button" name="status" value="active" />
											<br><br>
											<input type="text" name="name" placeholder="Name" required/>
											<input type="text" name="contact" placeholder="Contact Person" required/>
											<input type="text" name="contactEmail" placeholder="Contact Email" required/>
											<input type="hidden" name="statusVal" value="1" />
											
										</div>
										
										<div class="trainingDocuments">
											<b>Training Documents</b><br />
											<input type="file" name="files[]" id="fileUpload" multiple />
											<div id="documentList"></div>
										</div>
										
										<div class="trainingQuestions" id="trainingQuestions">
											<b>Training Questions</b><br />
										</div>
										
										<div>
											<input type="button" name="addMCQuestion" value="Add Multiple Choice" />
											<input type="button" name="addTFQuestion" value="Add True/False " />
											<input type="hidden" name="totalNumberOfQuestions" value="" />
											<input type="submit" name="createTrainingModule" value="Save"/>
										</div>
									</form>
								</div>
							</div>
							<!-- End New Training Modules -->
							
							
							<!-- Manage Training Modules -->
							
							<span class="byline">Manage Training Module</span>
							<span class="byline">
									<?php 
										// show negative messages
										if ($trainingModules->errors) { foreach ($trainingModules->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										// show positive messages
										if ($trainingModules->messages) { foreach ($trainingModules->messages as $messages) { echo '<div class="success">' . $messages . '</div>'; } }
									?>
								</span>
							<?php $modules = $trainingModules->getTrainingModules(); ?>
							<?php foreach($modules as $module): ?>
								<?php $questions = $trainingModules->getTrainingQuestions($module['id']); ?>
								<?php $documents = $trainingModules->getTrainingDocuments($module['id']); ?>
								<div class="existingTrainingModule">
									<form method="post" action="adminTraining.php" enctype="multipart/form-data" class="updateTrainingModule">
									
										<b><span class="toggle">[+]</span><?php echo $module['name']; ?></b>
										<span class="response"></span><img class="spinner" src="images/ajax-loader.gif">
										<input type="button" name="status" mid="<?php echo $module['id']; ?>" value="<?php if($module['status'] == 1){echo 'active';}else{echo 'inactive';} ?>" />
										
										<!-- Toggle Me Section -->
										<div class="toggleMe">
											<div class="moduleInfo">
												<input type="hidden" name="moduleId" value="<?php echo $module['id']; ?>" />
												<input type="text" name="name" value="<?php echo $module['name']; ?>" required/>
												<input type="text" name="contact" value="<?php echo $module['contact']; ?>" required/>
												<input type="text" name="contactEmail" value="<?php echo $module['contactEmail']; ?>" required/>
												<input type="hidden" name="statusVal" value="<?php echo $module['status']; ?>" />
												
											</div>
											
											<div class="trainingDocuments">
												<input type="file" name="files[]" id="fileUpload" multiple />
												<div id="existingDocumentList"></div>
												<div id="existingDocuments">
													<?php if(sizeof($documents) > 0): ?>
														<?php foreach($documents as $document): ?>
															<div class="document" id="doc_<?php echo $document['id']; ?>">
																<a href="<?php echo $document['documentPath'] . $document['documentName']; ?>"><?php echo $document['documentName']; ?></a>
																<input type="button" name="removeDocument" doc="<?php echo $document['id']; ?>" value="Delete" />
															</div>
														<?php endforeach; ?>
													<?php else: ?>
														<span>
															This module does not have any documents attached.
														</span><br>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="trainingQuestions" id="trainingQuestions">
												<?php foreach($questions as $question): ?>
													<?php if($question['correctAnswer'] == 't' || $question['correctAnswer'] == 'f'): ?>
														<div id="qId_<?php echo $question['id']; ?>">
															Q:<input type="text" id="question" name="tf_question_<?php echo $question['id']; ?>" value="<?php echo $question['question']; ?>"/>
															<input type="button" name="remove" value="Remove" />
															<br /><input type="radio" name="tf_correct_<?php echo $question['id']; ?>" value="t" <?php if($question['correctAnswer'] == 't'){echo 'checked="true"';} ?>/>True
															<br /><input type="radio" name="tf_correct_<?php echo $question['id']; ?>" value="f" <?php if($question['correctAnswer'] == 'f'){echo 'checked="true"';} ?>/>False
														</div>
													<?php else: ?>
														<?php $correctAnswers = explode(',', $question['correctAnswer']); ?>
														<div id="qId_<?php echo $question['id']; ?>">
															Q:<input type="text" id="question" qid="<?php echo $question['id']; ?>" name="mc_question_<?php echo $question['id']; ?>" value="<?php echo $question['question']; ?>"/>
															<input type="button" name="remove" value="Remove" />
															<?php $answers = $trainingModules->getTrainingAnswers($question['id']); ?>
															<?php foreach($answers as $answer): ?>
																<br />
																<input 
																	type="checkbox" 
																	name="<?php echo $answer['letter']; ?>_correct_<?php echo $question['id']; ?>" 
																	value="<?php echo $answer['letter']; ?>" <?php if(in_array($answer['letter'], $correctAnswers)){echo 'checked="true"';} ?>
																/>
																
																<span id="indent">a)</span>
																<input type="text" id="answer" name="<?php echo $answer['letter']; ?>_answer_<?php echo $answer['id']; ?>" value="<?php echo $answer['answer']; ?>"/>
															<?php endforeach; ?>
														</div>
													<?php endif; ?>
												<?php endforeach; ?>
											</div>
											
											<div>
												<input type="button" modId="<?php echo $module['id']; ?>" name="addMCQuestion" value="Add Multiple Choice" />
												<input type="button" modId="<?php echo $module['id']; ?>" name="addTFQuestion" value="Add True/False " />
												<input type="submit" name="updateTrainingModule" value="Update"/>
											</div>
										</div><!-- //End Toggle Me -->
									</form>
								</div>
							<?php endforeach; ?>

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
		<script src="js/messageHandler.js"></script>
		<script src="js/adminTraining.js"></script>
		<!-- /Scripts -->

	</body>
</html>