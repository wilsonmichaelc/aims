<?php
class NewTrainingModule
{
	
    private $db_connection = null;    // database connection   
    public  $errors                   		= array(); // collection of error messages
    public  $messages                 		= array(); // collection of success / neutral messages
    
    public function __construct()
    {
    	if(isset($_POST['createTrainingModule'])){
    	
    		try{
    		
	    		$questions = array();
	    		
	    		for($i=1; $i<=$_POST['totalNumberOfQuestions']; $i++){
	    			
	    			$mcname = 'mc_question_' . $i;
	    			$tfname = 'tf_question_' . $i;
	    			
	    			$correctAnswers = array();
	    			$possibleAnswers = array();
	    			$question = array();
	    			
		    		if(isset($_POST[$mcname])){
		    		
			    		// Capture this multiple choice question
			    		$possibleAnswers = array( 'a' => $_POST['a_answer_'.$i], 'b' => $_POST['b_answer_'.$i], 'c' => $_POST['c_answer_'.$i], 'd' => $_POST['d_answer_'.$i] );
			    		
			    		if(isset($_POST['a_correct_'.$i])){ array_push($correctAnswers, $_POST['a_correct_'.$i]); }
			    		if(isset($_POST['b_correct_'.$i])){ array_push($correctAnswers, $_POST['b_correct_'.$i]); }
			    		if(isset($_POST['c_correct_'.$i])){ array_push($correctAnswers, $_POST['c_correct_'.$i]); }
			    		if(isset($_POST['d_correct_'.$i])){ array_push($correctAnswers, $_POST['d_correct_'.$i]); }
			    		
			    		$question = array( 'question' => $_POST[$mcname], 'possibleAnswers' => $possibleAnswers, 'correctAnswers' => $correctAnswers);
			    		array_push($questions, $question);
			    		
		    		}else if(isset($_POST[$tfname])){
			    		// Capture this true/false question
			    		$possibleAnswers = array( /*'t' => 'True', 'f' => 'False'*/ );
			    		
			    		array_push($correctAnswers, $_POST['tf_correct_'.$i]);
			    		
			    		$question = array( 'question' => $_POST[$tfname], 'possibleAnswers' => $possibleAnswers, 'correctAnswers' => $correctAnswers );
			    		array_push($questions, $question);
		    		}
	    		}
				//var_dump($questions);
		    	$this->createNewModule($_POST['name'], $_POST['contact'], $_POST['contactEmail'], $_POST['statusVal'], $_FILES, $questions);
	    	
	    	}catch(Exception $ex){
		    	header("Location: " . $_SERVER['REQUEST_URI'] . '?error');
	    	}
    	}
    	if(isset($_GET['success'])){
	        $this->messages[] = "Record Updated Successfully!";
        }

    }

    /**
     * Checks if database connection is opened and open it if not
     */
    private function databaseConnection()
    {
        // connection already opened
        if ($this->db_connection != null) {
            return true;
        } else {
            // create a database connection, using the constants from config/config.php
            try {
                $this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME, DB_USER, DB_PASS);
                $this->db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db_connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                return true;

            // If an error is catched, database connection failed
            } catch (PDOException $e) {
                $this->errors[] = "Database connection problem.";
                return false;
            }
        }
    }
    
    private function createNewModule($name, $contact, $email, $status, $files, $questions)
    {
        
        if ($this->databaseConnection()) {
        
	        try{
	        	
	       		$this->db_connection->beginTransaction();
	
	        	$query = $this->db_connection->prepare('INSERT INTO trainingModules (name, contact, contactEmail, status) VALUES(:name, :contact, :contactEmail, :status)');
	        	$query->bindValue(':name', $name, PDO::PARAM_STR);
	        	$query->bindValue(':contact', $contact, PDO::PARAM_STR);
	        	$query->bindValue(':contactEmail', $email, PDO::PARAM_STR);
	        	$query->bindValue(':status', $status, PDO::PARAM_INT);
				$query->execute();
				$moduleId = $this->db_connection->lastInsertId();
	        	
	        	foreach($questions as $question)
	        	{    
				    $correctAnswers = '';
				    
			    	$query = $this->db_connection->prepare('INSERT INTO trainingQuestions (moduleId, question, correctAnswer) VALUES(:moduleId, :question, :correctAnswers)');
		        	$query->bindValue(':moduleId', $moduleId, PDO::PARAM_INT);
		        	$query->bindValue(':question', $question['question'], PDO::PARAM_STR);
		        	$query->bindValue(':correctAnswers', implode(',', $question['correctAnswers']), PDO::PARAM_STR);
					$query->execute();
					$qid = $this->db_connection->lastInsertId();
					
					foreach($question['possibleAnswers'] as $key => $value){
						$query = $this->db_connection->prepare('INSERT INTO trainingAnswers (questionId, letter, answer) VALUES(:questionId, :letter, :answer)');
						$query->bindValue(':questionId', $qid, PDO::PARAM_INT);
						$query->bindValue(':letter', $key, PDO::PARAM_STR);
						$query->bindValue(':answer', $value, PDO::PARAM_STR);
						$query->execute();
					}

		    	}
		    	
		    	if(isset($files)){
			    	$dir = 'files/module_' . $moduleId;
			    	$path = $dir . '/';
			    	
			    	if (!file_exists($dir)) {
					    mkdir($dir, 0777, true);
					}
			    	
		            for($i=0; $i<sizeOf($files['files']['name']); $i++) 
		            {
		                
		                if($files['files']['name'][$i] != ''){
			                $fullPath = $path . $files['files']['name'][$i];
			                move_uploaded_file($files['files']['tmp_name'][$i],$fullPath);
			                
			                $query = $this->db_connection->prepare('INSERT INTO trainingDocuments (moduleId, documentPath, documentName) VALUES(:moduleId, :path, :document)');
							$query->bindValue(':moduleId', $moduleId, PDO::PARAM_INT);
							$query->bindValue(':path', $path, PDO::PARAM_STR);
							$query->bindValue(':document', $files['files']['name'][$i], PDO::PARAM_STR);
							$query->execute();
						}
		            }
		    	}

				$this->db_connection->commit();
				header("Location: " . $_SERVER['REQUEST_URI'] . '?success');
				
			}catch(PDOException $PDOEx){
				$this->errors[] = "Transaction Failed.";
	    		$this->db_connection->rollBack();
	        	//throw $PDOEx;
	        	return false;
	        }

        }
        
    }		
		
}
?>