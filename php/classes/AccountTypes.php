<?php
class AccountTypes
{
	
    private $db_connection            		= null;    // database connection   
    
    public  $errors                   		= array(); // collection of error messages
    public  $messages                 		= array(); // collection of success / neutral messages
    
    public function __construct()
    {
		if(isset($_POST["newType"])){
			$this->addAccountType($_POST['longName'], $_POST['shortName']);
		}
		if(isset($_POST["updateType"])){
			$this->updateAccountType($_POST['id'], $_POST['longName'], $_POST['shortName']);
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
    
    public function getAccountTypes(){
	    
	    if ($this->databaseConnection()) {
		    
		    $query_select_account_types = $this->db_connection->prepare('SELECT * FROM accountTypes');
		    $query_select_account_types->execute();
		    
			return $query_select_account_types->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function addAccountType($name, $shortName){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('INSERT INTO accountTypes (name, shortName) VALUES(:name, :shortName)');
		    $query->bindValue(':name', $name, PDO::PARAM_STR);
		    $query->bindValue(':shortName', $shortName, PDO::PARAM_STR);
		    
		    if($query->execute()){
			    $this->messages[] = "Account Type added successfully!";
		    }else{
			    $this->errors[] = "Failed to add new account type.";
		    }
		    
	    }
	    
    }
    
    public function updateAccountType($id, $name, $shortName){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('UPDATE accountTypes SET name=:name, shortName=:shortName WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->bindValue(':name', $name, PDO::PARAM_STR);
		    $query->bindValue(':shortName', $shortName, PDO::PARAM_STR);
		    
		    if($query->execute()){
			    $this->messages[] = "Success!";
		    }else{
			    $this->errors[] = "Failed to update.";
		    }
		    
	    }
	    
    }
    
}