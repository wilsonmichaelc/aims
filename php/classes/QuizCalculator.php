<?php
class QuizCalculator
{
	
    private $db_connection            		= null;    // database connection   
    public  $errors                   		= array(); // collection of error messages
    public  $messages                 		= array(); // collection of success / neutral messages
    
    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */    
    public function __construct()
    {

        if(isset($_POST['quiz'])){
            
            $questions = $this->getTrainingQuestions($_POST['moduleId']);
            $totalCorrect = 0;
            
            foreach($questions as $question){
            	
            	if($question['correctAnswer'] == $_POST[ $question['id'] ]){
	            	++$totalCorrect;
            	}
            
            }
            
            if( count($questions) == $totalCorrect ){
            	$this->updateQuizRecord($_SESSION['id'], $_POST['moduleId'], 1);
	            header("Location: " . $_SERVER['REQUEST_URI'] . '?pass');
            }else{
	            header("Location: " . $_SERVER['REQUEST_URI'] . '?fail');
            }

        }
        
        if(isset($_GET['pass'])){
	        $this->messages[] = "You passed!";
        }
        
        if(isset($_GET['fail'])){
	        $this->errors[] = "You did not pass. Please try again.";
        }

    }
    
    private function databaseConnection()
    {
        // connection already opened
        if ($this->db_connection != null) {
            return true;
        } else {
            // create a database connection, using the constants from config/config.php
            try {
                $this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME, DB_USER, DB_PASS);
                return true;

            // If an error is catched, database connection failed
            } catch (PDOException $e) {
                $this->errors[] = "Database connection problem.";
                return false;
            }
        }
    }  
    
    public function getTrainingQuestions($moduleId){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM trainingQuestions WHERE moduleId = :moduleId');
		    $query->bindValue(':moduleId', $moduleId, PDO::PARAM_INT);
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    private function updateQuizRecord($userId, $moduleId, $quizPassed){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('INSERT INTO trainingRecords (moduleId, userId, quizPassed) VALUES (:moduleId, :userId, :quizPassed) ON DUPLICATE KEY UPDATE quizPassed=:quizPassed');
		    $query->bindValue(':moduleId', $moduleId, PDO::PARAM_INT);
		    $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->bindValue(':quizPassed', $quizPassed, PDO::PARAM_INT);
		    
		    if($query->execute()){
			    return true;
		    }else{
			    return false;
		    }
		    		    
	    }
	    
    }
		
}
?>