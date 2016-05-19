<?php
class BookingRates
{
	
    private $db_connection            		= null;    // database connection   
    
    public  $errors                   		= array(); // collection of error messages
    public  $messages                 		= array(); // collection of success / neutral messages
    
    public function __construct()
    {
		if(isset($_POST["updateInternalRates"])){
			$this->updateInternalRates($_POST['accountTypeId'], $_POST['staffRate'], $_POST['oneHour'], $_POST['fourHours'], $_POST['eightHours'], $_POST['sixteenHours'], $_POST['twentyFourHours']);
		}
		
		if(isset($_POST["updateExternalRates"])){
			$this->updateExternalRates($_POST['accountTypeId'], $_POST['staffRate'], $_POST['highAccuracyRate'], $_POST['lowAccuracyRate']);
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
    
    public function getInternalRates(){
	    
	    if ($this->databaseConnection()) {
		    
		    $query_select_account_types = $this->db_connection->prepare('SELECT bookingRatesInternal.*, accountTypes.name AS `accountName` FROM bookingRatesInternal INNER JOIN accountTypes ON bookingRatesInternal.accountTypeId = accountTypes.id');
		    $query_select_account_types->execute();
		    
			return $query_select_account_types->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function getExternalRates(){
	    
	    if ($this->databaseConnection()) {
		    
		    $query_select_account_types = $this->db_connection->prepare('SELECT bookingRatesExternal.*, accountTypes.name AS `accountName` FROM bookingRatesExternal INNER JOIN accountTypes ON bookingRatesExternal.accountTypeId = accountTypes.id');
		    $query_select_account_types->execute();
		    
			return $query_select_account_types->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function updateInternalRates($accountTypeId, $staffRate, $oneHour, $fourHours, $eightHours, $sixteenHours, $twentyFourHours){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('UPDATE bookingRatesInternal SET staffRate=:staffRate, oneHour=:oneHour, fourHours=:fourHours, eightHours=:eightHours, sixteenHours=:sixteenHours, twentyFourHours=:twentyFourHours WHERE accountTypeId=:accountTypeId');
		    $query->bindValue(':staffRate', $staffRate, PDO::PARAM_STR);
		    $query->bindValue(':oneHour', $oneHour, PDO::PARAM_STR);
		    $query->bindValue(':fourHours', $fourHours, PDO::PARAM_STR);
		    $query->bindValue(':eightHours', $eightHours, PDO::PARAM_STR);
		    $query->bindValue(':sixteenHours', $sixteenHours, PDO::PARAM_STR);
		    $query->bindValue(':twentyFourHours', $twentyFourHours, PDO::PARAM_STR);
		    $query->bindValue(':accountTypeId', $accountTypeId, PDO::PARAM_INT);
		    
		    if($query->execute()){
			    header('Location: adminBookingRates.php?success');
			    //$this->messages[] = "Account Type added successfully!";
		    }else{
			    header('Location: adminBookingRates.php?fail');
			    //$this->errors[] = "Failed to add new account type.";
		    }
		    
	    }
	    
    }
    
    public function updateExternalRates($accountTypeId, $staffRate, $highAccuracyRate, $lowAccuracyRate){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('UPDATE bookingRatesExternal SET staffRate=:staffRate, highAccuracyRate=:highAccuracyRate, lowAccuracyRate=:lowAccuracyRate WHERE accountTypeId=:accountTypeId');
		    $query->bindValue(':staffRate', $staffRate, PDO::PARAM_STR);
		    $query->bindValue(':highAccuracyRate', $highAccuracyRate, PDO::PARAM_STR);
		    $query->bindValue(':lowAccuracyRate', $lowAccuracyRate, PDO::PARAM_STR);
		    $query->bindValue(':accountTypeId', $accountTypeId, PDO::PARAM_INT);
		    
		    if($query->execute()){
			    header('Location: adminBookingRates.php?success');
			    //$this->messages[] = "Account Type added successfully!";
		    }else{
			    header('Location: adminBookingRates.php?fail');
			    //$this->errors[] = "Failed to add new account type.";
		    }
		    
	    }
	    
    }
    
}
