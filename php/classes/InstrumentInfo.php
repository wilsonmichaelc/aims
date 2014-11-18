<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

date_default_timezone_set('America/New_York');

class InstrumentInfo
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

	// used to fetch only the online instruments that this user has access to
    public function getUsersBookableInstruments($userId){

	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscInstruments WHERE id IN (SELECT instrumentId FROM mscInstrumentAccess WHERE userId=:userId AND access=1) AND bookable=1');
		    $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->execute();
		    			
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }

    }
    
    // Used to fetch even the offline instruments
    public function getAllUsersBookableInstruments($userId){

	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscInstruments WHERE id IN (SELECT instrumentId FROM mscInstrumentAccess WHERE userId=:userId AND access=1) ORDER BY NAME');
		    $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->execute();
		    			
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }

    }
    
    public function getUsersBookableInstrumentIds($userId){

	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT id FROM mscInstruments WHERE id IN (SELECT instrumentId FROM mscInstrumentAccess WHERE userId=:userId AND access=1)');
		    $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->execute();
		    			
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }

    }
    
    public function getBookedInstruments($start, $end){
	    
	    $events = array();
	    session_start();
	    
        if ($this->databaseConnection()) {
            
            if(!$_SESSION['isAdmin']){
            	$thisUsersInstrumentList = $this->getUsersBookableInstrumentIds($_SESSION['id']);
            	//var_dump($thisUsersInstrumentList);
            }
            
	        $query = $this->db_connection->prepare('SELECT * FROM instrumentBookings WHERE (dateFrom BETWEEN :dateFrom AND :dateTo)');
	        $query->bindValue(':dateFrom', $start, PDO::PARAM_STR);
	        $query->bindValue(':dateTo', $end, PDO::PARAM_STR);
	        $query->execute();
	        	        
	        $bookings = $query->fetchAll(PDO::FETCH_ASSOC);
	        	        
	        foreach($bookings as $booking){
	        
	        	$instrument = $this->getInstrumentInfo($booking['instrumentId']);
	        	$user = $this->getUserInfo($booking['userId']);
	        	
	        	if(!$_SESSION['isAdmin']){
	        		foreach($thisUsersInstrumentList as $list){
		        		if($instrument['id'] == $list['id']){
			        		array_push($events,array(
								'className' => 'i' . $booking['instrumentId'] . 'i',
								'title' => $user['first'] . ' ' . $user['last'], 
								'start' =>  $booking['dateFrom'] . 'T' . $booking['timeFrom'] . 'Z', 
								'end' => $booking['dateTo'] . 'T' . $booking['timeTo'] . 'Z',
								'color' => $instrument['color'],
								'allDay' => false
							));
		        		}
	        		}
	        	}else{
		        	array_push($events,array(
						'className' => 'i' . $booking['instrumentId'] . 'i',
						'title' => $user['first'] . ' ' . $user['last'], 
						'start' =>  $booking['dateFrom'] . 'T' . $booking['timeFrom'] . 'Z', 
						'end' => $booking['dateTo'] . 'T' . $booking['timeTo'] . 'Z',
						'color' => $instrument['color'],
						'allDay' => false
					));
	        	}
	        	
			}
			
			header('Content-Type: application/json');
			return json_encode($events);
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
    
    public function jsonGetInstrumentInfo($id){
    
    	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscInstruments WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    header('Content-Type: application/json');
			return json_encode($query->fetch(PDO::FETCH_ASSOC));
		    
	    }
	    
    }
    
    public function jsonGetInstrumentAccess($userId){
    
    	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscInstrumentAccess WHERE userId=:userId');
		    $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->execute();
			header('Content-Type: application/json');
			return json_encode($query->fetchAll(PDO::FETCH_ASSOC));
		    
	    }
	    
    }
    
    private function getUserInfo($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM users WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
			return $query->fetch(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function getInstruments(){
    
    	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscInstruments ORDER BY NAME');
		    $query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
}
?>