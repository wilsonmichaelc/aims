<?php
class Users
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
    	if(isset($_POST['updateUserAccountType'])){
	    	$this->updateUser($_POST['accountType'], $_POST['updateUserAccountType']);
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
    
    public function getAllUsers(){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT id, first, last FROM users ORDER BY first');
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function getUser($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM users WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    
			return $query->fetch(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function updateUserAccountType($accountType, $id){
    
	  	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('UPDATE users SET accountType=:accountType WHERE id=:id');
		    $query->bindValue(':accountType', $accountType, PDO::PARAM_INT);
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    
		    header('Content-Type: application/text');
		    if($query->execute()){
			    return true;
		    }else{
			    return false;
		    }
		    		    
	    }
	    
    }
    
    public function updateUser($first, $last, $email, $institution, $id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('UPDATE users SET first=:first, last=:last, email=:email, institution=:institution WHERE id=:id');
		    $query->bindValue(':first', $first, PDO::PARAM_STR);
		    $query->bindValue(':last', $last, PDO::PARAM_STR);
		    $query->bindValue(':email', $email, PDO::PARAM_STR);
		    $query->bindValue(':institution', $institution, PDO::PARAM_STR);
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    
		    header('Content-Type: application/text');
		    if($query->execute()){
			    return true;
		    }else{
			    return false;
		    }
		    		    
	    }
	    
    }
    
    public function jsonGetUserInfo($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT id, username, first, last, email, institution, accountType, isAdmin, userActive, createdAt FROM users WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    
			header('Content-Type: application/json');
			return json_encode($query->fetchAll(PDO::FETCH_ASSOC));
		    
	    }
	    
    }
		
}
?>