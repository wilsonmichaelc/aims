<?php
date_default_timezone_set('America/New_York');
class TrainingModuleUpdate
{
	
    private $db_connection = null;    // database connection  
    public  $errors                   		= array(); // collection of error messages
    public  $messages                 		= array(); // collection of success / neutral messages
    
    
    public function __construct()
    {
    	if(isset($_POST['updateTrainingModule'])){
    	
    		$questions = array();
    		$answers = array();
    		$correct = array();
    		
    		foreach($_POST as $key => $value){
    			
    			if($this->startsWith($key, 'tf_question')){
    			
    				$id = explode('_', $key)[2];
    				$questions[$id] = $_POST['tf_question_'.$id];
    				
    			}else if($this->startsWith($key, 'tf_correct')){
    			
	    			$id = explode('_', $key)[2];
	    			$correct[$id] = $_POST['tf_correct_'.$id];
	    			
    			}else if($this->startsWith($key, 'mc_question')){
    			
	    			$id = explode('_', $key)[2];
	    			$questions[$id] = $_POST['mc_question_'.$id];
	    			
    			}else if($this->startsWith($key, 'a_answer')){
    			
	    			$id = explode('_', $key)[2];
	    			$answers[$id] = $_POST['a_answer_'.$id];
	    			
    			}else if($this->startsWith($key, 'b_answer')){
    			
	    			$id = explode('_', $key)[2];
	    			$answers[$id] = $_POST['b_answer_'.$id];
	    			
    			}else if($this->startsWith($key, 'c_answer')){
    			
	    			$id = explode('_', $key)[2];
	    			$answers[$id] = $_POST['c_answer_'.$id];
	    			
    			}else if($this->startsWith($key, 'd_answer')){
    			
	    			$id = explode('_', $key)[2];
	    			$answers[$id] = $_POST['d_answer_'.$id];
	    			
    			}else if($this->startsWith($key, 'a_correct')){
    			
	    			$id = explode('_', $key)[2];
	    			if(array_key_exists($id, $correct)){
		    			$correct[$id] = $correct[$id] . ',' . $_POST['a_correct_'.$id];
	    			}else{
		    			$correct[$id] = $_POST['a_correct_'.$id];
	    			}
	    			
    			}else if($this->startsWith($key, 'b_correct')){
    			
	    			$id = explode('_', $key)[2];
	    			if(array_key_exists($id, $correct)){
		    			$correct[$id] = $correct[$id] . ',' . $_POST['b_correct_'.$id];
	    			}else{
		    			$correct[$id] = $_POST['b_correct_'.$id];
	    			}
	    			
    			}else if($this->startsWith($key, 'c_correct')){
    			
	    			$id = explode('_', $key)[2];
	    			if(array_key_exists($id, $correct)){
		    			$correct[$id] = $correct[$id] . ',' . $_POST['c_correct_'.$id];
	    			}else{
		    			$correct[$id] = $_POST['c_correct_'.$id];
	    			}
	    			
    			}else if($this->startsWith($key, 'd_correct')){
    			
	    			$id = explode('_', $key)[2];
	    			if(array_key_exists($id, $correct)){
		    			$correct[$id] = $correct[$id] . ',' . $_POST['d_correct_'.$id];
	    			}else{
		    			$correct[$id] = $_POST['d_correct_'.$id];
	    			}
	    			
    			}

    		}
	    	$this->updateModule($_POST['moduleId'], $_POST['name'], $_POST['contact'], $_POST['contactEmail'], $_POST['statusVal'], $_FILES, $questions, $answers, $correct);
    	}
    	
    	if(isset($_GET['success'])){
	        $this->messages[] = "Module Updated Successfully!";
        }
        if(isset($_GET['fail'])){
	        $this->messages[] = "Connection Error! Module has NOT been updated.";
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
    
    public function removeDocument($id)
    {
	    
	    if ($this->databaseConnection()) {
	    	$query = $this->db_connection->prepare('SELECT * FROM trainingDocuments WHERE id=:id');
	        $query->bindValue(':id', $id, PDO::PARAM_INT);
	        $query->execute();
	        $result = $query->fetch(PDO::FETCH_ASSOC);
	        $fullPath = $result['documentPath'] . $result['documentName'];
	    
	    	$query = $this->db_connection->prepare('DELETE FROM trainingDocuments WHERE id=:id');
	        $query->bindValue(':id', $id, PDO::PARAM_INT);
	        if($query->execute()){
	        	if(unlink('../../'.$fullPath)){
		        	return true;
	        	}else{
		        	return false;
	        	}
	        }else{
		        return false;
	        }
	    }
	    
    }
    
    public function removeQuestion($id)
    {

	    if ($this->databaseConnection()) {
	    	try{
	        	
	       		$this->db_connection->beginTransaction();
	       		
		    	$query = $this->db_connection->prepare('DELETE FROM trainingQuestions WHERE id=:id');
		        $query->bindValue(':id', $id, PDO::PARAM_INT);
		        $query->execute();
		        
		        $query = $this->db_connection->prepare('DELETE FROM trainingAnswers WHERE questionId=:id');
		        $query->bindValue(':id', $id, PDO::PARAM_INT);
		        $query->execute();
		        
		        $this->db_connection->commit();
		        
		        return 'true';
		        
		    }catch(PDOException $PDOEx){
				$this->db_connection->rollBack();
	        	throw $PDOEx;
	        	return 'false';   
		    }

	    
	    }
	    
    }
    
    public function updateStatus($id, $status){
	    
	    if ($this->databaseConnection()) {
	    	
	    	$query = $this->db_connection->prepare('UPDATE trainingModules SET status=:status WHERE id=:id');
	        $query->bindValue(':id', $id, PDO::PARAM_INT);
	        $query->bindValue(':status', $status, PDO::PARAM_INT);
	        
	        if($query->execute()){
		        return true;
	        }else{
		        return false;
	        }
	    	
	    }
	    
    }
    
    public function createBlankMCQ($id)
    {
	    
	    if ($this->databaseConnection()) {
	    
	    	$returnArray = array();
	    	try{
	        	
	       		$this->db_connection->beginTransaction();

		    	$query = $this->db_connection->prepare('INSERT INTO trainingQuestions (moduleId, question, correctAnswer) VALUES(:moduleId, :question, :correctAnswer)');
		        $query->bindValue(':moduleId', $id, PDO::PARAM_INT);
		        $query->bindValue(':question', "New Question", PDO::PARAM_STR);
		        $query->bindValue(':correctAnswer', "c", PDO::PARAM_STR);
		        $query->execute();
		        $qid = $this->db_connection->lastInsertId();
		        $returnArray['qid'] = $qid;
		        
		        $letters = array('a', 'b', 'c', 'd');
		        
		        for($i=0; $i<4; $i++){
			        $query = $this->db_connection->prepare('INSERT INTO trainingAnswers (questionId, letter, answer) VALUES(:questionId, :letter, :answer)');
					$query->bindValue(':questionId', $qid, PDO::PARAM_INT);
					$query->bindValue(':letter', $letters[$i], PDO::PARAM_STR);
					$query->bindValue(':answer', "--", PDO::PARAM_STR);
					$query->execute();
					$returnArray[$letters[$i]] = $this->db_connection->lastInsertId();
				}
				$this->db_connection->commit();
				
				header('Content-Type: application/text');
				return json_encode($returnArray);
				
			}catch(PDOException $PDOEx){
				$this->db_connection->rollBack();
	        	throw $PDOEx;    
		    }
	    }
	    
    }
    
    public function createBlankTFQ($id)
    {
	    
	    if ($this->databaseConnection()) {
	    
	    	$query = $this->db_connection->prepare('INSERT INTO trainingQuestions (moduleId, question, correctAnswer) VALUES(:moduleId, :question, :correctAnswer)');
	        $query->bindValue(':moduleId', $id, PDO::PARAM_INT);
	        $query->bindValue(':question', "New Question", PDO::PARAM_STR);
	        $query->bindValue(':correctAnswer', "t", PDO::PARAM_STR);
	        $query->execute();
	        $qid = $this->db_connection->lastInsertId();
			
			header('Content-Type: application/text');
			return $qid;

	    }
	    
    }
    
    private function updateModule($moduleId, $name, $contact, $email, $status, $files, $questions, $answers, $correct)
    {
        
        if ($this->databaseConnection()) {
        
	        try{
	        	
	       		$this->db_connection->beginTransaction();
	
	        	$query = $this->db_connection->prepare('UPDATE trainingModules SET name=:name, contact=:contact, contactEmail=:contactEmail, status=:status WHERE id=:id');
	        	$query->bindValue(':id', $moduleId, PDO::PARAM_INT);
	        	$query->bindValue(':name', $name, PDO::PARAM_STR);
	        	$query->bindValue(':contact', $contact, PDO::PARAM_STR);
	        	$query->bindValue(':contactEmail', $email, PDO::PARAM_STR);
	        	$query->bindValue(':status', $status, PDO::PARAM_INT);
				$query->execute();
	        	
	        	foreach($questions as $key => $val){    
			    	$query = $this->db_connection->prepare('UPDATE trainingQuestions SET question=:question WHERE id=:id');
		        	$query->bindValue(':id', $key, PDO::PARAM_INT);
		        	$query->bindValue(':question', $val, PDO::PARAM_STR);
					$query->execute();

		    	}
		    	
		    	foreach($correct as $key => $val){
					$query = $this->db_connection->prepare('UPDATE trainingQuestions SET correctAnswer=:correctAnswer WHERE id=:id');
					$query->bindValue(':id', $key, PDO::PARAM_INT);
					$query->bindValue(':correctAnswer', $val, PDO::PARAM_STR);
					$query->execute();
				}
		    	
		    	foreach($answers as $key => $val){
					$query = $this->db_connection->prepare('UPDATE trainingAnswers SET answer=:answer WHERE id=:id');
					$query->bindValue(':id', $key, PDO::PARAM_INT);
					$query->bindValue(':answer', $val, PDO::PARAM_STR);
					$query->execute();
				}
		    	
		    	if(isset($files)){
			    	$dir = 'files/module_' . $moduleId;
			    	$path = $dir . '/';
			    	$name = '';
			    	
			    	if (!file_exists($dir)) {
					    mkdir($dir, 0777, true);
					}
			    	
		            for($i=0; $i<sizeOf($files['files']['name']); $i++) 
		            {
		                if($files['files']['name'][$i] != ''){
			                
			                if(file_exists($path . $files['files']['name'][$i])){
			                	$parts = pathinfo($files['files']['name'][$i]);
			                	
			                	$name = $parts['filename'] . '_' . date('d-m-Y') . '.' . $parts['extension'];
				                //$name = time() . '_' . $files['files']['name'][$i];
			                }else{
				                $name = $files['files']['name'][$i];
			                }
		                
			                $fullPath = $path . $name;
			                move_uploaded_file($files['files']['tmp_name'][$i],$fullPath);
			                
			                $query = $this->db_connection->prepare('INSERT INTO trainingDocuments (moduleId, documentPath, documentName) VALUES(:moduleId, :path, :document)');
							$query->bindValue(':moduleId', $moduleId, PDO::PARAM_INT);
							$query->bindValue(':path', $path, PDO::PARAM_STR);
							$query->bindValue(':document', $name, PDO::PARAM_STR);
							$query->execute();
						}
		            }
		    	}
				
				if($this->db_connection->commit()){
					header("Location: " . $_SERVER['REQUEST_URI'] . '?success');
				}else{
					header("Location: " . $_SERVER['REQUEST_URI'] . '?fail');
				}
				
			}catch(PDOException $PDOEx){
	    		$this->db_connection->rollBack();
	        	throw $PDOEx;
	        }

        }
        
    }
    
    private function startsWith($haystack, $needle)
	{
	    return $needle === "" || strpos($haystack, $needle) === 0;
	}
	private function endsWith($haystack, $needle)
	{
	    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}
}
?>