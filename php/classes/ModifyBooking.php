<?php
class ModifyBooking
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

    public function cancelBooking($bookingId){

	    if ($this->databaseConnection()) {

        // Fetch the original entry
				$query_orig = $this->db_connection->prepare('SELECT * FROM instrumentBookings WHERE id=:bookingId');
				$query_orig->bindValue(':bookingId', $bookingId, PDO::PARAM_INT);
				$query_orig->execute();
				$originalJSON = json_encode($query_orig->fetchAll(PDO::FETCH_ASSOC));
				// Log this incident
				$query_log = $this->db_connection->prepare('INSERT INTO bookingLog (activityType, modifiedById, modifiedByName, ipAddress, modifiedId, originalJSON) VALUES(:activityType, :sessionUserId, :sessionUserName, :ipAddress, :modifiedId, :originalJSON)');
        $query_log->bindValue(':activityType', 'cancel', PDO::PARAM_STR);
				$query_log->bindValue(':sessionUserId', $_SESSION['id'], PDO::PARAM_INT);
        $query_log->bindValue(':sessionUserName', $_SESSION['first'] . ' '. $_SESSION['last'], PDO::PARAM_STR);
				$query_log->bindValue(':ipAddress', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
        $query_log->bindValue(':modifiedId', $bookingId, PDO::PARAM_INT);
				$query_log->bindValue(':originalJSON', $originalJSON, PDO::PARAM_STR);
				$query_log->execute();
				// Delete the entry
		    $query = $this->db_connection->prepare('DELETE FROM instrumentBookings WHERE id=:bookingId');
		    $query->bindValue(':bookingId', $bookingId, PDO::PARAM_INT);

		    if($query->execute()){
			    return 'success';
		    }else{
			    return 'fail';
		    }

	    }

    }

    public function cancelTrainingBooking($id){

	    if ($this->databaseConnection()) {

	    	try{

        		$this->db_connection->beginTransaction();

		    	$query = $this->db_connection->prepare('UPDATE mscTrainingRequest SET bookingId=NULL WHERE id=(SELECT requestId FROM trainingBookings WHERE id=:id)');
			    $query->bindValue(':id', $id, PDO::PARAM_INT);
			    $query->execute();

			    $query = $this->db_connection->prepare('DELETE FROM trainingBookings WHERE id=:id');
			    $query->bindValue(':id', $id, PDO::PARAM_INT);
			    $query->execute();

			    $status = $this->db_connection->commit();
				return true;

			}catch(PDOException $ex){
				$this->db_connection->rollBack();
	        	return $query->errorInfo();
			}

	    }

    }

}
?>
