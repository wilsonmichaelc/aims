<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

date_default_timezone_set('America/New_York');

class NewBooking
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
        // if we have such a POST request, call the registerNewUser() method
        if (isset($_POST['createBooking'])) {

        	$df = explode('/', $_POST['dateFrom']);
        	$dt = explode('/', $_POST['dateTo']);
        	//$dateFrom = explode('/', $_POST['dateFrom'])[2] . '-' . explode('/', $_POST['dateFrom'])[0] . '-' . explode('/', $_POST['dateFrom'])[1];
        	//$dateTo = explode('/', $_POST['dateTo'])[2] . '-' . explode('/', $_POST['dateTo'])[0] . '-' . explode('/', $_POST['dateTo'])[1];

        	$dateFrom = $df[2] . '-' . $df[0] . '-' . $df[1];
        	$dateTo = $dt[2] . '-' . $dt[0] . '-' . $dt[1];

			if($_POST['projectId'] == 'training')
			{
				//insert a training request here...
				$x = explode('-', $_POST['trainingId']);

				$this->newTrainingBooking(
					$x[1], //userId
					$x[0], //requestId
					substr($_POST['instrumentId'], 1),
					$dateFrom,
					$dateTo,
					$_POST['timeFrom'],
					$_POST['timeTo']
				);
			}
			else
			{
				if( isset($_POST['instrumentId']) && $_POST['instrumentId'][0] == "i" && isset($_POST['projectId']) && !empty($_POST['projectId'])){

					$instrumentId = substr($_POST['instrumentId'], 1);

					$this->newInstrumentBooking(
						$_SESSION['id'],
						$_POST['projectId'],
						$instrumentId,
						$dateFrom,
						$dateTo,
						$_POST['timeFrom'],
						$_POST['timeTo'],
						$_POST['hours'],
						$_POST['postEstimate']

					);

				}else if( isset($_POST['instrumentId']) && $_POST['instrumentId'][0] == "c" && isset($_POST['projectId']) && !empty($_POST['projectId'])){

					$instrumentId = substr($_POST['instrumentId'], 1);

					$this->newConferenceBooking(
						$_SESSION['id'],
						$_POST['projectId'],
						$instrumentId,
						$dateFrom,
						$dateTo,
						$_POST['timeFrom'],
						$_POST['timeTo']
					);

				}
			}

        }

        if(isset($_GET['success'])){
	        $this->messages[] = "Booking request successful!";
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

    private function newInstrumentBooking($userId, $projectId, $instrumentId, $dateFrom, $dateTo, $timeFrom, $timeTo, $hours, $estimate)
    {

		if( $this->instrumentNotOverlapping($dateFrom, $dateTo, $timeFrom, $timeTo, $instrumentId) ){

            if( $this->notPastCurrentDate($dateFrom, $timeFrom)){

		        if ($this->databaseConnection()) {

	        		$query = $this->db_connection->prepare('INSERT INTO instrumentBookings (userId, projectId, instrumentId, dateFrom, dateTo, timeFrom, timeTo, hours, estimate) VALUES (:userId, :projectId, :instrumentId, :dateFrom, :dateTo, :timeFrom, :timeTo, :hours, :estimate)');

			        $query->bindValue(':userId', $userId, PDO::PARAM_INT);
			        $query->bindValue(':projectId', $projectId, PDO::PARAM_INT);
			        $query->bindValue(':instrumentId', $instrumentId, PDO::PARAM_INT);
			        $query->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
			        $query->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
			        $query->bindValue(':timeFrom', $timeFrom, PDO::PARAM_STR);
			        $query->bindValue(':timeTo', $timeTo, PDO::PARAM_STR);
			        $query->bindValue(':hours', $hours, PDO::PARAM_INT);
			        $query->bindValue(':estimate', $estimate, PDO::PARAM_INT);

					if($query->execute()){
						header("Location: " . $_SERVER['REQUEST_URI'] . '?success');
					}else{
						$this->errors[] = "Something went wrong when we tried to create your booking: " . $query->errorCode();
					}

		        }
	        }else{
		        $this->errors[] = "Your request is scheduled for date/time that has already passed.";
	        }

        }else{
	        $this->errors[] = "Your request is overlapping with an existing entry.";
        }

    }

    private function newConferenceBooking($userId, $projectId, $conferenceId, $dateFrom, $dateTo, $timeFrom, $timeTo)
    {

		if( $this->conferenceNotOverlapping($dateFrom, $dateTo, $timeFrom, $timeTo, $conferenceId) ){

            if( $this->notPastCurrentDate($dateFrom, $timeFrom)){

		        if ($this->databaseConnection()) {

	        		$query = $this->db_connection->prepare('INSERT INTO conferenceBookings (userId, conferenceId, dateFrom, dateTo, timeFrom, timeTo) VALUES(:userId, :conferenceId, :dateFrom, :dateTo, :timeFrom, :timeTo)');

			        $query->bindValue(':userId', $userId, PDO::PARAM_INT);
			        $query->bindValue(':conferenceId', $conferenceId, PDO::PARAM_INT);
			        $query->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
			        $query->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
			        $query->bindValue(':timeFrom', $timeFrom, PDO::PARAM_STR);
			        $query->bindValue(':timeTo', $timeTo, PDO::PARAM_STR);

					if( $query->execute() ){
						header("Location: " . $_SERVER['REQUEST_URI'] . '?success');
					}else{
						$this->errors[] = "Something went wrong when we tried to create your booking:" . $query->errorCore();
					}

		        }
	        }else{
		        $this->errors[] = "Your request is scheduled for date/time that has already passed.";
	        }

        }else{
	        $this->errors[] = "Your request is overlapping with an existing entry.";
        }

    }

    private function newTrainingBooking($userId, $requestId, $instrumentId, $dateFrom, $dateTo, $timeFrom, $timeTo)
    {

        if( $this->notPastCurrentDate($dateFrom, $timeFrom)){

	        if ($this->databaseConnection()) {

				try
	        	{

		        	$this->db_connection->beginTransaction();

		        	$queryA = $this->db_connection->prepare('INSERT INTO trainingBookings (userId, requestId, instrumentId, dateFrom, dateTo, timeFrom, timeTo) VALUES(:userId, :requestId, :instrumentId, :dateFrom, :dateTo, :timeFrom, :timeTo)');
					$queryA->bindValue(':userId', $userId, PDO::PARAM_INT);
			        $queryA->bindValue(':requestId', $requestId, PDO::PARAM_INT);
			        $queryA->bindValue(':instrumentId', $instrumentId, PDO::PARAM_INT);
			        $queryA->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
			        $queryA->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
			        $queryA->bindValue(':timeFrom', $timeFrom, PDO::PARAM_STR);
			        $queryA->bindValue(':timeTo', $timeTo, PDO::PARAM_STR);

					$queryA->execute();
					$lastId = $this->db_connection->lastInsertId();

					$queryB = $this->db_connection->prepare('UPDATE mscTrainingRequest SET bookingId=:bookingId WHERE id=:id');
					$queryB->bindValue(':bookingId', $lastId, PDO::PARAM_INT);
					$queryB->bindValue(':id', $requestId, PDO::PARAM_INT);
			        $queryB->execute();

					$this->db_connection->commit();
					var_dump($this->db_connection->errorCode());
		        	header("Location: " . $_SERVER['REQUEST_URI'] . '?success');

	        	}
	        	catch(PDOException $PDOEx)
	        	{
		        	$this->db_connection->rollBack();
		        	$this->errors[] = "Something went wrong when we tried to create your booking:" . $query->errorCode();
					throw $PDOEx;
	        	}

	        }

        }else{
	        $this->errors[] = "Your request is scheduled for date/time that has already passed.";
        }

    }

    private function instrumentNotOverlapping($dateFrom, $dateTo, $timeFrom, $timeTo, $instrumentId){

	    if ($this->databaseConnection()) {

	        //$query = $this->db_connection->prepare('SELECT * FROM instrumentBookings WHERE instrumentId=:instrumentId AND (:dateFrom >= dateFrom AND :dateFrom <= dateTo) AND (:timeFrom >= timeFrom AND :timeFrom <= timeTo)');
			$query = $this->db_connection->prepare('
			SELECT * FROM instrumentBookings WHERE instrumentId=:instrumentId
			AND (
			TIMESTAMP(:newDateFrom, :newTimeFrom) > TIMESTAMP(dateFrom, timeFrom) AND TIMESTAMP(:newDateFrom, :newTimeFrom) < TIMESTAMP(dateTo, timeTo)
			OR
			TIMESTAMP(:newDateTo, :newTimeTo) > TIMESTAMP(dateFrom, timeFrom) AND TIMESTAMP(:newDateTo, :newTimeTo) < TIMESTAMP(dateTo, timeTo)
			OR
			TIMESTAMP(:newDateFrom, :newTimeFrom) < TIMESTAMP(dateFrom, timeFrom) AND TIMESTAMP(:newDateTo, :newTimeTo) > TIMESTAMP(dateTo, timeTo)
			)
			');

	        $query->bindValue(':instrumentId', $instrumentId, PDO::PARAM_INT);
	        $query->bindValue(':newDateFrom', $dateFrom, PDO::PARAM_STR);
	        $query->bindValue(':newDateTo', $dateTo, PDO::PARAM_STR);
	        $query->bindValue(':newTimeFrom', $timeFrom, PDO::PARAM_STR);
	        $query->bindValue(':newTimeTo', $timeTo, PDO::PARAM_STR);
	        $query->execute();

	        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        }

	    if(empty($results)){
		    return true;
	    }else{
		    return false;
	    }

    }

    private function conferenceNotOverlapping($dateFrom, $dateTo, $timeFrom, $timeTo, $conferenceId){

	    if ($this->databaseConnection()) {

			/*
	        $query = $this->db_connection->prepare('
        	SELECT * FROM conferenceBookings WHERE conferenceId=:conferenceId AND
			( TIMESTAMP(CONCAT(:dateFrom, " ", :timeFrom)) BETWEEN TIMESTAMP(CONCAT(dateFrom, " ", timeFrom)) AND TIMESTAMP(CONCAT(dateTo, " ", timeTo)) )
			OR
			( TIMESTAMP(CONCAT(:dateTo, " ", :timeTo)) BETWEEN TIMESTAMP(CONCAT(dateFrom, " ", timeFrom)) AND TIMESTAMP(CONCAT(dateTo, " ", timeTo)) )
			OR
			( TIMESTAMP(CONCAT(:dateFrom, " ", :timeFrom)) < TIMESTAMP(CONCAT(dateFrom, " ", timeFrom)) AND TIMESTAMP(CONCAT(:dateTo, " ", :timeTo)) > TIMESTAMP(CONCAT(dateTo, " ", timeTo)) )
			');
			*/
			$query = $this->db_connection->prepare('
	        	SELECT * FROM conferenceBookings WHERE conferenceId=:conferenceId
	        	AND (
		        	TIMESTAMP(:dateFrom, :timeFrom) > TIMESTAMP(dateFrom, timeFrom) AND TIMESTAMP(:dateFrom, :timeFrom) < TIMESTAMP(dateTo, timeTo)
					OR
					TIMESTAMP(:dateTo, :timeTo) > TIMESTAMP(dateFrom, timeFrom) AND TIMESTAMP(:dateTo, :timeTo) < TIMESTAMP(dateTo, timeTo)
					OR
					TIMESTAMP(:dateFrom, :timeFrom) < TIMESTAMP(dateFrom, timeFrom) AND TIMESTAMP(:dateTo, :timeTo) > TIMESTAMP(dateTo, timeTo)
				)
			');

	        $query->bindValue(':conferenceId', $conferenceId, PDO::PARAM_INT);
	        $query->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
	        $query->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
	        $query->bindValue(':timeFrom', $timeFrom, PDO::PARAM_STR);
	        $query->bindValue(':timeTo', $timeTo, PDO::PARAM_STR);
	        $query->execute();

	        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        }

	    if(empty($results)){
		    return true;
	    }else{
		    return false;
	    }

    }

    private function notPastCurrentDate($df, $tf){

    	$dateTime = $df . ' ' . $tf;

	    if( new DateTime() > new DateTime($dateTime) ){
		    return false;
	    }else{
		    return true;
	    }

    }

}
?>
