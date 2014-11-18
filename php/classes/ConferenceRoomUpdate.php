<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

class ConferenceRoomUpdate
{
	
    private $db_connection            		= null;    // database connection   
    public  $errors                   		= array(); // collection of error messages
    public  $messages                 		= array(); // collection of success / neutral messages
    
    public function __construct()
    {

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

    public function updateConferenceAccess($conferenceId, $userId, $accessStatus){

	    if ($this->databaseConnection()) {

			$query = $this->db_connection->prepare('INSERT INTO mscConferenceAccess (conferenceId, userId, access) VALUES(:conferenceId, :userId, :access) ON DUPLICATE KEY UPDATE access=:access');
			$query->bindValue(':conferenceId', $conferenceId, PDO::PARAM_INT);
			$query->bindValue(':userId', $userId, PDO::PARAM_INT);
			$query->bindValue(':access', $accessStatus, PDO::PARAM_INT);

		    if($query->execute()){
			    return true;
		    }else{
			    return false;
		    }
		    
	    }

    }
    
}
?>