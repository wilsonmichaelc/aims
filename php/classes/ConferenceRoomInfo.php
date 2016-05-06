<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

date_default_timezone_set('America/New_York');

class ConferenceRoomInfo
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

    public function getUsersBookableConferenceRooms($userId){

	    if ($this->databaseConnection()) {
		    
		    $query_user_access = $this->db_connection->prepare('SELECT * FROM mscConferenceRooms WHERE id IN (SELECT conferenceId FROM mscConferenceAccess WHERE userId=:userId) AND bookable=1');
		    $query_user_access->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query_user_access->execute();
		    			
			return $query_user_access->fetchAll(PDO::FETCH_ASSOC);
		    
	    }

    }
    
    public function getBookedConferenceRooms($start, $end){
	    
	    $events = array();
	    
        if ($this->databaseConnection()) {
              
	        $query = $this->db_connection->prepare('SELECT * FROM conferenceBookings WHERE (dateFrom BETWEEN :dateFrom AND :dateTo)');
	        $query->bindValue(':dateFrom', $start, PDO::PARAM_STR);
	        $query->bindValue(':dateTo', $end, PDO::PARAM_STR);
	        $query->execute();
	        	        
	        $bookings = $query->fetchAll(PDO::FETCH_ASSOC);
	        	        
	        foreach($bookings as $booking){
	        
	        	$room = $this->getConferenceRoomInfo($booking['conferenceId']);
	        	$user = $this->getUserInfo($booking['userId']);
	        			
				array_push($events,array(
					'className' => 'i' . $booking['conferenceId'] . 'i',
					'title' => $user['first'] . ' ' . $user['last'], 
					'start' =>  $booking['dateFrom'] . 'T' . $booking['timeFrom'] . 'Z', 
					'end' => $booking['dateTo'] . 'T' . $booking['timeTo'] . 'Z',
					'color' => $room['color'],
					'allDay' => false
				));

			}
			
			header('Content-Type: application/json');
			return json_encode($events);
        }
	    
    }
    
    public function getConferenceRoomInfo($id){
    
    	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscConferenceRooms WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
			return $query->fetch(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function getConferenceRooms(){
    
    	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscConferenceRooms');
		    $query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
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
    
    public function jsonGetConferenceAccess($userId){
    
    	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscConferenceAccess WHERE userId=:userId');
		    $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->execute();
			header('Content-Type: application/json');
			return json_encode($query->fetchAll(PDO::FETCH_ASSOC));
		    
	    }
	    
    }
    
}
?>