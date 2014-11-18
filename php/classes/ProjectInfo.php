<?php
class ProjectInfo
{
	
    private $db_connection            		= null;    // database connection   
    public  $errors                   		= array(); // collection of error messages
    public  $messages                 		= array(); // collection of success / neutral messages
    
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
    
    public function getActiveProjects($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM projects WHERE userId=:userId AND status=:status');
		    $query->bindValue(':userId', $id, PDO::PARAM_INT);
		    $query->bindValue(':status', "active", PDO::PARAM_STR);
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function getProject($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM projects WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    
			return $query->fetch(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function getPaymentInfo($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM paymentInfo WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    
			return $query->fetch(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function jsonGetUserProjects($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM projects WHERE userId=:userId');
		    $query->bindValue(':userId', $id, PDO::PARAM_INT);
		    $query->execute();
		    
			header('Content-Type: application/json');
			return json_encode($query->fetchAll(PDO::FETCH_ASSOC));
		    
	    }
	    
    }
    
}
?>