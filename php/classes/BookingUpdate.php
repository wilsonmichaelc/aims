<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

date_default_timezone_set('America/New_York');

class BookingUpdate
{
	
    private $db_connection            		= null;    // database connection   
    
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


	
	public function updateBooking($id, $dateFrom, $dateTo, $timeFrom, $timeTo){

	    if ($this->databaseConnection()) {
		    
			$query = $this->db_connection->prepare('UPDATE instrumentBookings SET dateFrom=:dateFrom, dateTo=:dateTo, timeFrom=:timeFrom, timeTo=:timeTo WHERE id=:id');
			$query->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
			$query->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
			$query->bindValue(':timeFrom', $timeFrom, PDO::PARAM_STR);
			$query->bindValue(':timeTo', $timeTo, PDO::PARAM_STR);
			$query->bindValue(':id', $id, PDO::PARAM_INT);
		    
		    header('Content-Type: application/text');
		    if($query->execute()){
			    return true;
		    }else{
			    return 'Error! ' . $query->errorInfo();
		    }
		    
	    }

    }
    
    public function updateTrainingBooking($id, $dateFrom, $dateTo, $timeFrom, $timeTo){

	    if ($this->databaseConnection()) {
		    
			$query = $this->db_connection->prepare('UPDATE trainingBookings SET dateFrom=:dateFrom, dateTo=:dateTo, timeFrom=:timeFrom, timeTo=:timeTo WHERE id=:id');
			$query->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
			$query->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
			$query->bindValue(':timeFrom', $timeFrom, PDO::PARAM_STR);
			$query->bindValue(':timeTo', $timeTo, PDO::PARAM_STR);
			$query->bindValue(':id', $id, PDO::PARAM_INT);
		    
		    header('Content-Type: application/text');
		    if($query->execute()){
			    return true;
		    }else{
			    return 'Error! ' . $query->errorInfo();
		    }
		    
	    }

    }
    
    public function archiveBooking($id){

	    if ($this->databaseConnection()) {
		    
			$query = $this->db_connection->prepare('UPDATE instrumentBookings SET archiveStatus=1 WHERE id=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
		    
		    header('Content-Type: application/text');
		    if($query->execute()){
			    return true;
		    }else{
			    return 'Error! ' . $query->errorInfo();
		    }
		    
	    }

    }
    
    public function unArchiveBooking($id){

	    if ($this->databaseConnection()) {
		    
			$query = $this->db_connection->prepare('UPDATE instrumentBookings SET archiveStatus=0 WHERE id=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
		    
		    header('Content-Type: application/text');
		    if($query->execute()){
			    return true;
		    }else{
			    return 'Error! ';
			    print_r($query->errorInfo());
		    }
		    
	    }

    }
    
}
?>