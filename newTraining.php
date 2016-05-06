<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");
require_once("php/classes/TrainingModules.php");
require_once("php/classes/TrainingInfo.php");
require_once("php/classes/ProjectInfo.php");
require_once("php/classes/QuizCalculator.php");
require_once("php/libraries/PHPMailer.php");

$login = new Login();
$trainingModules = new TrainingModules();
$trainingInfo = new TrainingInfo();
$projectInfo = new ProjectInfo();
$quizCalculator = new QuizCalculator();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
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

		<link rel="stylesheet" href="css/newTraining.css" />
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
									<li><a href="newProject.php">Project</a></li>
									<li><a href="newService.php">Services</a></li>
									<li class="current-page-start"><a href="newTraining.php">Training</a></li>
									<li><a href="newBooking.php">Bookings</a></li>
								</ul>
							</div>
							<!-- /Inner Menu -->
							
							<header>
								<h2><a href="#">Instrument Training</a></h2>
								<span class="byline">If you want to run your own samples, start here!</span>
								<span class="byline">
									<?php 
										// show negative messages
										if ($trainingModules->errors) { foreach ($trainingModules->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										if ($quizCalculator->errors) { foreach ($quizCalculator->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										// show positive messages
										if ($trainingModules->messages) { foreach ($trainingModules->messages as $messages) { echo '<div class="success">' . $messages . '</div>'; } }
										if ($quizCalculator->messages) { foreach ($quizCalculator->messages as $messages) { echo '<div class="success">' . $messages . '</div>'; } }
									?>
								</span>
							</header>

							<!-- Page Content -->
							<div class="page-content-min-height">
								<?php $modules = $trainingModules->getActiveTrainingModules(); ?>
								
								<?php if(sizeof($modules) > 0): ?>
									<p>1. Start by reading up on the instrument you would like to use.</p>
									<p>2. Then, pass the quiz. </p>
									<p>3. Finally, click "Request Training" and wait for us to contact you.</p>
								<?php else: ?>
									<p>There are no training modules available at this time. Please check back with us soon.</p>
								<?php endif; ?>
								<!-- Modules -->
									
									<?php foreach($modules as $module): ?>
										
										<div class="service-box-border">
									
											<!-- Title and buttons -->
											
												<span class="inline-block column-container-wide">
												
													<div style="display: inline;" class="title" mod_id="'<?php echo $module['id']; ?>"><?php echo $module['name']; ?></div>
													
													<div style="display: inline; min-width: 310px;">
														
														<div class="status f-right" style="display: inline; min-width: 15px;">
															<?php if($trainingModules->hasPassedTraining($_SESSION['id'], $module['id'])): ?>
																<span style="color: green;">&#x2713;</span>
															<?php else: ?>
																<span style="color: red;">&#x2717;</span>
															<?php endif; ?>
														</div>
														
														<button id="id<?php echo $module['id'] ?>" class="training button f-right" type="button">Hands On Training</button>
														
														<div class="status f-right" style="display: inline; min-width: 15px;">
															<?php if($trainingModules->hasPassedQuiz($_SESSION['id'], $module['id'])): ?>
																<span style="color: green;">&#x2713;</span>
															<?php else: ?>
																<span style="color: red;">&#x2717;</span>
															<?php endif; ?>
														</div>
														
														<button id="id<?php echo $module['id'] ?>" class="quiz button f-right" type="button">Quiz</button>

														<button id="id<?php echo $module['id'] ?>" class="material button f-right" type="button">Study Material</button>
														
													</div>
												</span>
												
											<!-- /Title and buttons -->
											
											
											<!-- Study Material -->
											
												<span id="id<?php echo $module['id'] ?>" class="material hidden column-container-wide">
													<?php $documents = $trainingModules->getTrainingDocuments($module['id']); ?>
													<?php if( count($documents) > 0 ): ?>
														<div>
														<?php foreach($documents as $document): ?>
															<a target="_blank" href="<?php echo $document['documentPath'] . $document['documentName']; ?>"><?php echo $document['documentName']; ?></a><br>
														<?php endforeach; ?>
														</div>
													<?php else: ?>
														<div>This module does not appear to have any documents attached...</div>
													<?php endif; ?>
												</span>
												
											<!-- /Study Material -->
											
											<!-- Quiz Material -->
											
												<span id="id<?php echo $module['id'] ?>" class="quiz hidden column-container-wide">
													
													<?php if($trainingModules->hasPassedQuiz($_SESSION['id'], $module['id'])): ?>
														<div>You have already completed this quiz.</div>
													<?php else: ?>
														<?php $questions = $trainingModules->getTrainingQuestions($module['id']); ?>
														<?php if( count($questions) > 0 ): ?>
															<form action="newTraining.php" method="post">
																
																<input type="hidden" name="moduleId" value="<?php echo $module['id']; ?>" />
																
																<?php foreach($questions as $question): ?>
																	<div class="">
																		<input type="hidden" name="questionId" value="<?php echo $question['id']; ?>" /><?php echo $question['question']; ?>
		
																		<?php if($question['correctAnswer'] == 't' || $question['correctAnswer'] == 'f'): ?>
																			<br><input type="radio" name="<?php echo $question['id']; ?>" value="t" required/>True
																			<br><input type="radio" name="<?php echo $question['id']; ?>" value="f" required/>False
																		<?php else: ?>
																			<?php $answers = $trainingModules->getTrainingAnswers($question['id']); ?>
																			<?php foreach($answers as $answer): ?>
																				<br><input type="radio" name="<?php echo $question['id']; ?>" value="<?php echo $answer['letter']; ?>" required/>
																				<?php echo $answer['letter'] . ') ' . $answer['answer']; ?>										
																			<?php endforeach; ?>
																		<?php endif; ?>
																	</div>
																<?php endforeach; ?>
																<input type="submit" name="quiz" value="Submit" />
	
															</form>
															<p></p>
														<?php else: ?>
															<div>This module does not appear to have a quiz.</div>
														<?php endif; ?>
													<?php endif; ?>
													
												</span>
												
											<!-- /Quiz Material -->
											
											
											<!-- Hands on training-->
												<?php $trainingRequest = $trainingInfo->getTrainingRequest($module['id'], $_SESSION['id']); ?>
												<span id="id<?php echo $module['id']; ?>" class="training hidden column-container-wide">
													<div>
													<?php if(!$trainingModules->hasPassedQuiz($_SESSION['id'], $module['id']) && (count($questions) > 0)): ?>
														Please complete the quiz first.
													<?php elseif( !empty($trainingRequest) ): ?>
														<?php foreach($trainingRequest as $tReq): ?>
														
															<b>Requested:</b> <?php echo $tReq['createdAt']; ?>
															<?php if( !empty($tReq['bookingId']) ): ?>
																<?php $booking = $trainingInfo->getBooking($tReq['bookingId']); ?>
																<b>&nbsp;&nbsp;-&nbsp;&nbsp;Scheduled:</b> <?php echo $booking['dateFrom'] . ' ' . $booking['timeFrom'] . '  -  ' . $booking['dateTo'] . ' ' . $booking['timeTo']; ?>
															<?php else: ?>
																<b>&nbsp;&nbsp;-&nbsp;&nbsp;Scheduled:</b>
																<span>Pending...
																	<form method="post" action="newTraining.php" class="cancelTrainingForm">
																		<input type="hidden" name="userId" value="<?php echo $_SESSION['id']; ?>" />
																		<input type="hidden" name="trainingId" value="<?php echo $tReq['id']; ?>" />
																		<input class="cancelTraining" type="submit" name="cancelTraining" value="Cancel" />
																	</form>
																</span>
															<?php endif; ?>
															
														<?php endforeach; ?>
													<?php endif; ?>
													
													<?php if($trainingModules->hasPassedQuiz($_SESSION['id'], $module['id']) || (count($questions) == 0) ): ?>
														<form method="post" action="newTraining.php">
															<input type="hidden" name="moduleId" value="<?php echo $module['id']; ?>" />
															<input type="hidden" name="userId" value="<?php echo $_SESSION['id']; ?>" />
															<div>Please choose a project to associate with this training session.</div>
															<select name="projectId" required="true" style="width: 200px;">
																<?php $projects = $projectInfo->getActiveProjects($_SESSION['id']); ?>
																<?php foreach($projects as $project): ?>
																	<option value="<?php echo $project['id']; ?>"><?php echo $project['title']; ?></option>
																<?php endforeach; ?>
															</select>
															<input style="width: 130px;" type="submit" name="requestTraining" value="Request Training" />
														</form>
													<?php endif; ?>
													</div>
												</span>
												
											<!-- /Hands on training-->
											
										</div>
										<br>
									<?php endforeach; ?>

								<!-- /Modules -->
								
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

		<script>
			$(document).ready(function(){
				$('button.material').click(function(){
					var id = $(this).attr('id');
					if($('span.material#' + id).css('display') == 'none'){
						$('span.quiz#' + id).hide('medium');
						$('span.training#' + id).hide('medium');
						$('span.material#' + id).show('medium');
					}else{
						$('span.material#' + id).hide('medium');
					}
				});
				$('button.quiz').click(function(){
					var id = $(this).attr('id');
					if($('span.quiz#' + id).css('display') == 'none'){
						$('span.material#' + id).hide('medium');
						$('span.training#' + id).hide('medium');
						$('span.quiz#' + id).show('medium');
					}else{
						$('span.quiz#' + id).hide('medium');
					}
				});
				$('button.training').click(function(){
					var id = $(this).attr('id');
					if($('span.training#' + id).css('display') == 'none'){
						$('span.quiz#' + id).hide('medium');
						$('span.material#' + id).hide('medium');
						$('span.training#' + id).show('medium');
					}else{
						$('span.training#' + id).hide('medium');
					}
				});
			});
			
			$(document).on('click', '.cancelTraining', function(){
				var userId = $('input[name="userId"]').val();
				
			});
		</script>
		<!-- /Scripts -->

	</body>
</html>