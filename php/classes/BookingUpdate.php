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

				// Fetch the original entry
				$query_orig = $this->db_connection->prepare('SELECT * FROM instrumentBookings WHERE id=:bookingId');
				$query_orig->bindValue(':bookingId', $id, PDO::PARAM_INT);
				$query_orig->execute();
				$originalJSON = json_encode($query_orig->fetchAll(PDO::FETCH_ASSOC));
				// Log this incident
				$query_log = $this->db_connection->prepare('INSERT INTO bookingLog (activityType, modifiedById, modifiedByName, ipAddress, modifiedId, originalJSON) VALUES(:activityType, :sessionUserId, :sessionUserName, :ipAddress, :modifiedId, :originalJSON)');
        $query_log->bindValue(':activityType', 'update', PDO::PARAM_STR);
				$query_log->bindValue(':sessionUserId', $_SESSION['id'], PDO::PARAM_INT);
        $query_log->bindValue(':sessionUserName', $_SESSION['first'] . ' '. $_SESSION['last'], PDO::PARAM_STR);
				$query_log->bindValue(':ipAddress', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
        $query_log->bindValue(':modifiedId', $id, PDO::PARAM_INT);
				$query_log->bindValue(':originalJSON', $originalJSON, PDO::PARAM_STR);
				$query_log->execute();

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
