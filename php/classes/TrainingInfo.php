<?php
date_default_timezone_set('America/New_York');
require "EstimateCalculator.php";
class TrainingInfo
{
	
    private $db_connection            		= null;    // database connection   


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
    
    public function getTrainingRequest($moduleId, $userId)
    {
        
        if ($this->databaseConnection()) {
        
        	$query = $this->db_connection->prepare('SELECT * FROM mscTrainingRequest WHERE moduleId=:moduleId AND userId=:userId');
	        $query->bindValue(':moduleId', $moduleId, PDO::PARAM_INT);
	        $query->bindValue(':userId', $userId, PDO::PARAM_INT);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);

        }
        
    }
    
    public function getUserTrainingBookings($id)
    {
        
        if ($this->databaseConnection()) {
        
        	$query = $this->db_connection->prepare('SELECT * FROM trainingBookings WHERE userId=:id');
	        $query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			header('Content-Type: application/json');
			return json_encode($query->fetchAll(PDO::FETCH_ASSOC));

        }
        
    }
    
    public function getPendingTrainingRequests()
    {
        
        if ($this->databaseConnection()) {
        
        	$query = $this->db_connection->prepare('SELECT * FROM mscTrainingRequest WHERE bookingId IS NULL');
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);

        }
        
    }
    
    public function getBooking($bookingId)
    {
        
        if ($this->databaseConnection()) {
        
        	$query = $this->db_connection->prepare('SELECT * FROM trainingBookings WHERE id=:bookingId LIMIT 1');
	        $query->bindValue(':bookingId', $bookingId, PDO::PARAM_INT);
			$query->execute();
			return $query->fetch(PDO::FETCH_ASSOC);

        }
        
    }
    
    public function getBookedTraining($start, $end){
	    
	    $events = array();
	    
        if ($this->databaseConnection()) {
              
	        $query = $this->db_connection->prepare('SELECT * FROM trainingBookings WHERE (dateFrom BETWEEN :dateFrom AND :dateTo)');
	        $query->bindValue(':dateFrom', $start, PDO::PARAM_STR);
	        $query->bindValue(':dateTo', $end, PDO::PARAM_STR);
	        $query->execute();
	        	        
	        $bookings = $query->fetchAll(PDO::FETCH_ASSOC);
	        	        
	        foreach($bookings as $booking){
	        			
				array_push($events,array(
					'className' => 't' . $booking['requestId'] . 't',
					'title' => 'Training', 
					'start' =>  $booking['dateFrom'] . 'T' . $booking['timeFrom'] . 'Z', 
					'end' => $booking['dateTo'] . 'T' . $booking['timeTo'] . 'Z',
					'color' => 'black',
					'allDay' => false
				));

			}
			
			header('Content-Type: application/json');
			return json_encode($events);
        }
	    
    }
    
    // IMPORTANT !!!!!!
    // IMPORTANT !!!!!!
    // This is ONLY for populating the adminInvoice page with a complete training request
    // IMPORTANT !!!!!!
    // IMPORTANT !!!!!!
    public function getTrainingRange($start, $end, $userId){
	    
        if ($this->databaseConnection()) {

        	$allTrainings = array();
            
	        $query = $this->db_connection->prepare('SELECT * FROM trainingBookings WHERE userId=:userId AND (dateFrom BETWEEN :dateFrom AND :dateTo)');
	        $query->bindValue(':dateFrom', $start, PDO::PARAM_STR);
	        $query->bindValue(':dateTo', $end, PDO::PARAM_STR);
	        $query->bindValue(':userId', $userId, PDO::PARAM_INT);
	        $query->execute();
	        $training = $query->fetchAll(PDO::FETCH_ASSOC);
	        
	        foreach($training as $t){
	        	$tempT = array();
		        
		        $query = $this->db_connection->prepare('SELECT name FROM mscInstruments WHERE id=:id');
		        $query->bindValue(':id', $t['instrumentId'], PDO::PARAM_INT);
		        $query->execute();
		        $instrumentName = $query->fetchColumn();
		        
		        $query = $this->db_connection->prepare('SELECT first, last, accountType FROM users WHERE id=:id');
		        $query->bindValue(':id', $t['userId'], PDO::PARAM_INT);
		        $query->execute();
		        $user = $query->fetch(PDO::FETCH_ASSOC);
		        
		        $a = $t['dateFrom'] . ' ' . $t['timeFrom'];
		        $b = $t['dateTo'] . ' ' . $t['timeTo'];
		        $from = new DateTime($a);
		        $to = new DateTime($b);
		        
		        $diff = $to->diff($from);
		        $hours = $diff->h + ($diff->i / 60);
		        $estimateCalculator = new EstimateCalculator();
		        
		        $tempT = $t;
		        $tempT['estimate'] = $estimateCalculator->getTrainingEstimate($user['accountType'], $hours);
		        $tempT['hours'] = $hours;
		        $tempT['instrumentName'] = $instrumentName;
		        $tempT['userName'] = $user['first'] . ' ' . $user['last'];
		        array_push($allTrainings, $tempT);
	        }
	        
			header('Content-Type: application/json');
			return json_encode($allTrainings);

        }
	    
    }
		
}
?>