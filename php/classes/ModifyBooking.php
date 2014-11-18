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