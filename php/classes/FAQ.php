<?php
class FAQ
{
	
    private $db_connection = null;
    public  $errors = array();
    public  $messages = array();
    
    public function __construct()
    {
    	if(isset($_POST['newFaq']) && $_SESSION['isAdmin']){
	    	$this->newFaq($_POST['question'], $_POST['answer']);
    	}
    	if(isset($_GET['success'])){
	    	$this->messages[] = "FAQ Created Successfully!";
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
                return false;
            }
        }
    }
    
    public function newFaq($question, $answer){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('INSERT INTO faq (question, answer) VALUES(:question, :answer)');
		    $query->bindValue(':question', $question, PDO::PARAM_STR);
			$query->bindValue(':answer', $answer, PDO::PARAM_STR);
		    
		    if($query->execute()){
			    header("Location: " . $_SERVER['REQUEST_URI'] . '?success');
		    }else{
			    $this->errors[] = "Something went wrong and the new FAQ was not created.";
		    }		    
		    
	    }
	    
    }
    
    public function updateFaq($id, $question, $answer){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('UPDATE faq SET question=:question, answer=:answer WHERE id=:id');
		    $query->bindValue(':question', $question, PDO::PARAM_STR);
			$query->bindValue(':answer', $answer, PDO::PARAM_STR);
			$query->bindValue(':id', $id, PDO::PARAM_INT);
		    
		    if($query->execute()){
			    return true;
		    }else{
			    return false;
		    }
		    
	    }
	    
    }
    
    public function deleteFaq($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('DELETE FROM faq WHERE id=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
		    
		    if($query->execute()){
			    return true;
		    }else{
			    return false;
		    }
		    
	    }
	    
    }
    
    public function getFaqs(){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM faq');
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
}