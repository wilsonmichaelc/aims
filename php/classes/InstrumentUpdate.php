<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

date_default_timezone_set('America/New_York');

class InstrumentUpdate
{
	
    private $db_connection            		= null;    // database connection   
    public  $errors                   		= array(); // collection of error messages
    public  $messages                 		= array(); // collection of success / neutral messages
    
    public function __construct()
    {
    	if(isset($_POST['addInstrument'])){
	        $this->addInstrument($_POST['name'], $_POST['model'], $_POST['asset'], $_POST['accuracy'], $_POST['minBookableUnit'], $_POST['color'], $_POST['bookable'], $_POST['location']);
        }
        
		if(isset($_GET['success'])){
	        $this->messages[] = "Instrument has been added!";
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

	public function addInstrument($name, $model, $asset, $accuracy, $minBookableUnit, $color, $bookable, $location){
		
		if ($this->databaseConnection()) {
		    

			$query = $this->db_connection->prepare('INSERT INTO mscInstruments (name, model, assetNumber, accuracy, minBookableUnit, color, bookable, location) VALUES(:name, :model, :asset, :accuracy, :minBookableUnit, :color, :bookable, :location)');
			$query->bindValue(':name', $name, PDO::PARAM_STR);
			$query->bindValue(':model', $model, PDO::PARAM_STR);
			$query->bindValue(':asset', $asset, PDO::PARAM_INT);
			$query->bindValue(':accuracy', $accuracy, PDO::PARAM_STR);
			$query->bindValue(':minBookableUnit', $minBookableUnit, PDO::PARAM_INT);
			$query->bindValue(':color', $color, PDO::PARAM_STR);
			$query->bindValue(':bookable', $bookable, PDO::PARAM_INT);
			$query->bindValue(':location', $location, PDO::PARAM_STR);

		    if($query->execute()){
			    header("Location: " . $_SERVER['REQUEST_URI'] . '?success');
        	}else{
	        	$this->errors[] = "Something went wrong when we tried to save the instrument.";
        	}
		    
	    }
		
	}
	
	public function updateInstrument($id, $name, $model, $asset, $accuracy, $minBookableUnit, $color, $bookable, $location){

	    if ($this->databaseConnection()) {
		    
			$query = $this->db_connection->prepare('UPDATE mscInstruments SET name=:name, model=:model, assetNumber=:asset, accuracy=:accuracy, minBookableUnit=:minBookableUnit, color=:color, bookable=:bookable, location=:location WHERE id=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->bindValue(':name', $name, PDO::PARAM_STR);
			$query->bindValue(':model', $model, PDO::PARAM_STR);
			$query->bindValue(':asset', $asset, PDO::PARAM_INT);
			$query->bindValue(':accuracy', $accuracy, PDO::PARAM_STR);
			$query->bindValue(':minBookableUnit', $minBookableUnit, PDO::PARAM_INT);
			$query->bindValue(':color', $color, PDO::PARAM_STR);
			$query->bindValue(':bookable', $bookable, PDO::PARAM_INT);
			$query->bindValue(':location', $location, PDO::PARAM_STR);
		    
		    header('Content-Type: application/text');
		    if($query->execute()){
			    return 'Success!';
		    }else{
			    return 'Error! Changes Not Saved!!';
		    }
		    
	    }

    }

    public function updateInstrumentAccess($instrumentId, $userId, $accessStatus){

	    if ($this->databaseConnection()) {

			$query = $this->db_connection->prepare('INSERT INTO mscInstrumentAccess (instrumentId, userId, access) VALUES(:instrumentId, :userId, :access) ON DUPLICATE KEY UPDATE access=:access');
			$query->bindValue(':instrumentId', $instrumentId, PDO::PARAM_INT);
			$query->bindValue(':userId', $userId, PDO::PARAM_INT);
			$query->bindValue(':access', $accessStatus, PDO::PARAM_INT);

		    
		    header('Content-Type: application/text');
		    if($query->execute()){
			    return 'Success!';
		    }else{
			    return 'Error! Change Not Saved!!';
		    }
		    
	    }

    }
    
}
?>