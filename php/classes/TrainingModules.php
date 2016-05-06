<?php
class TrainingModules
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
    	if (isset($_POST['requestTraining'])) {

			$this->createTrainingRequest(
				$_POST['moduleId'], 
				$_POST['userId'],
				$_POST['projectId']
			);

        }

        if (isset($_POST['cancelTraining'])) {

        	if($_POST['userId'] == $_SESSION['id']){
        		$this->cancelTrainingRequest(
        			$_POST['trainingId'],
        			$_POST['userId']
        		);
        	}

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
                return true;

            // If an error is catched, database connection failed
            } catch (PDOException $e) {
                $this->errors[] = "Database connection problem.";
                return false;
            }
        }
    }
    
    public function getTrainingModules(){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM trainingModules');
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function getActiveTrainingModules(){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM trainingModules WHERE status=1');
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }

    public function getTrainingDocuments($moduleId){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM trainingDocuments WHERE moduleId = :moduleId');
		    $query->bindValue(':moduleId', $moduleId, PDO::PARAM_INT);
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
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
    
    public function getTrainingAnswers($questionId){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM trainingAnswers WHERE questionId = :questionId');
		    $query->bindValue(':questionId', $questionId, PDO::PARAM_INT);
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function hasPassedTraining($userId, $moduleId){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM trainingRecords WHERE moduleId=:moduleId AND userId=:userId AND trainingPassed=1');
		    $query->bindValue(':moduleId', $moduleId, PDO::PARAM_INT);
		    $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->execute();
		    
			return (bool) $query->fetchColumn();
		    
	    }
	    
    }
    
    public function hasPassedQuiz($userId, $moduleId){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT quizPassed FROM trainingRecords WHERE moduleId=:moduleId AND userId=:userId');
		    $query->bindValue(':moduleId', $moduleId, PDO::PARAM_INT);
		    $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->execute();
		    
			return (bool) $query->fetchColumn();
		    
	    }
	    
    }
    
    public function updateStatus($moduleId, $userId, $status){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('INSERT INTO trainingRecords (moduleId, userId, trainingPassed) VALUES (:moduleId, :userId, :status) ON DUPLICATE KEY UPDATE trainingPassed=:status');
		    $query->bindValue(':moduleId', $moduleId, PDO::PARAM_INT);
		    $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->bindValue(':status', $status, PDO::PARAM_INT);
		    
			header('Content-Type: application/text');
		    if($query->execute()){
			    return 'Success!';
		    }else{
			    return 'Error! Change Not Saved!!';
		    }
		    
	    }
	    
    }
    
    public function jsonGetTrainingModulesPassed($userId){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM trainingRecords WHERE userId=:userId AND trainingPassed=1');
		    $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->execute();
		    
			header('Content-Type: application/json');
			return json_encode($query->fetchAll(PDO::FETCH_ASSOC));
		    
	    }
	    
	}
	
	private function createTrainingRequest($moduleId, $userId, $projectId)
    {
        
        if ($this->databaseConnection()) {
        
        	$query = $this->db_connection->prepare('INSERT INTO mscTrainingRequest (moduleId, userId, projectId) VALUES(:moduleId, :userId, :projectId)');
	        $query->bindValue(':moduleId', $moduleId, PDO::PARAM_INT);
	        $query->bindValue(':userId', $userId, PDO::PARAM_INT);
	        $query->bindValue(':projectId', $projectId, PDO::PARAM_INT);
			if($query->execute()){
				$this->sendNewTrainingEmail($userId, $moduleId);
			    header("Location: " . $_SERVER['REQUEST_URI'] . '?trainingRequested');
		    }else{
			    $this->errors[] = "Something went wrong when we tried to submit your training request.";
		    }
        }
        
    }

    private function cancelTrainingRequest($trainingId, $userId){

    	if ($this->databaseConnection()) {

    		$query = $this->db_connection->prepare('DELETE FROM mscTrainingRequest WHERE id=:trainingId AND userId=:userId');
	        $query->bindValue(':trainingId', $trainingId, PDO::PARAM_INT);
	        $query->bindValue(':userId', $userId, PDO::PARAM_INT);
			if($query->execute()){
			    header("Location: " . $_SERVER['REQUEST_URI'] . '?trainingCanceled');
		    }else{
			    $this->errors[] = "Something went wrong when we tried to cancel your training request.";
		    }

    	}

    }
    
    private function sendNewTrainingEmail($userId, $moduleId)
    {
    	if ($this->databaseConnection()) {
	    	$query = $this->db_connection->prepare('SELECT first, last FROM users WHERE id=:id');
			$query->bindValue(':id', $userId, PDO::PARAM_INT);
			$query->execute();
			$user = $query->fetch(PDO::FETCH_ASSOC);
			
			$query = $this->db_connection->prepare('SELECT name, contactEmail FROM trainingModules WHERE id=:id');
			$query->bindValue(':id', $moduleId, PDO::PARAM_INT);
			$query->execute();
			$module = $query->fetch(PDO::FETCH_ASSOC);
		}
        $mail = new PHPMailer;
        $body = '<html><body><div>';

        // please look into the config/config.php for much more info on how to use this!
        // use SMTP or use mail()
        if (EMAIL_USE_SMTP) {

            // Set mailer to use SMTP
            $mail->IsSMTP();
            //useful for debugging, shows full SMTP errors
            $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            // Enable SMTP authentication
            $mail->SMTPAuth = EMAIL_SMTP_AUTH;                               
            // Enable encryption, usually SSL/TLS
            if (defined(EMAIL_SMTP_ENCRYPTION)) {                
                $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;                              
            }
            // Specify host server
            $mail->Host = EMAIL_SMTP_HOST;  
            $mail->Username = EMAIL_SMTP_USERNAME;                            
            $mail->Password = EMAIL_SMTP_PASSWORD;                      
            $mail->Port = EMAIL_SMTP_PORT;       

        } else {

            $mail->IsMail();            
        }
        
        $mail->IsHTML(true);

        $mail->From = EMAIL_NEW_FFS_FROM;
        $mail->FromName = EMAIL_NEW_FFS_FROM_NAME;
        
        $attention = explode(',', $module['contactEmail']);
        foreach($attention as $to){
	        $mail->AddAddress($to);
        }
        $mail->Subject = EMAIL_NEW_FFS_FROM_SUBJECT;

		$body = $body . '<p><b>' . $user['first'] . ' ' . $user['last'] . '</b> has requested training on the '.$module['name'].'</p>';
		$body = $body . '<br>Please login to schedule this training session.</p></div></body></html>';

		$mail->Body = $body;

        if(!$mail->Send()) {
            return false;
        }else{
	        return true;
        }
    }
		
}
?>