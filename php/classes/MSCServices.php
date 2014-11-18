<?php
class MSCServices
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
    
    public function getPrepService($analysisId){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscPrepServices WHERE analysisId = :analysisId');
		    $query->bindValue(':analysisId', $analysisId, PDO::PARAM_INT);
		    $query->execute();
		    
			return $query->fetch(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function getAnalysisServices(){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscAnalysisServices');
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
}
?>