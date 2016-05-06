<?php
class BookingInfo
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
    
    public function getBookings($projectId, $userId){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM instrumentBookings WHERE projectId=:projectId AND userId=:userId');
		    $query->bindValue(':projectId', $projectId, PDO::PARAM_INT);
		    $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function getBookingRange($dateFrom, $dateTo, $orderBy, $userId){
	    
	    if ($this->databaseConnection()) {
		    
		    $q = '';
		    if(!empty($userId)){
			    $q = 'SELECT * FROM instrumentBookings WHERE userId=' . $userId . ' AND dateFrom BETWEEN :dateFrom AND :dateTo AND NOT projectId=709 ORDER BY ' . $orderBy;
		    }else{
			    $q = 'SELECT * FROM instrumentBookings WHERE dateFrom BETWEEN :dateFrom AND :dateTo AND NOT projectId=709 ORDER BY ' . $orderBy;
		    }
		    
		    $query = $this->db_connection->prepare($q);
		    $query->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
		    $query->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
		    $query->execute();
		    
			header('Content-Type: application/json');
			return json_encode($query->fetchAll(PDO::FETCH_ASSOC));
		    
	    }
	    
    }
    
    public function getInstrumentInfo($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscInstruments WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    
			return $query->fetch(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function getUserBookings($id){
	    if ($this->databaseConnection())
	    {
		    $query = $this->db_connection->prepare('SELECT * FROM instrumentBookings WHERE userId=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			
			header('Content-Type: application/json');
			return json_encode($query->fetchAll(PDO::FETCH_ASSOC));
		}
    }
    
}
?>