<?php
require "InstrumentInfo.php";
class Stats
{
	
    private $db_connection            		= null;    // database connection   
    public  $errors                   		= array(); // collection of error messages
    public  $messages                 		= array(); // collection of success / neutral messages
	//public  $instrumentInfo = new InstrumentInfo();
	
    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */    
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
    
    public function getInstrumentByMonth($startYear, $endYear, $instrument, $user, $month){
    
	    $instrumentInfo = new InstrumentInfo();
	    if ($this->databaseConnection()) {
		    
		    if($user != 'null'){
		    
		    	$endYear = $startYear;
		    	
		    	$instruments = $instrumentInfo->getInstruments();
		    
		    	$dataset = '{';
			    $dataset .= '"labels":[';
			    
			    for($i=0; $i<sizeOf($instruments); $i++){
			    	$dataset .= '"' . $instruments[$i]['name'] . '"';
			    	if($i < sizeof($instruments)){$dataset .= ',';}
			    }
			    $dataset = rtrim($dataset, ',');
			    
			    $dataset .= '],';
			    $dataset .= '"datasets":[';
			    
			    for($y=$startYear; $y<=$endYear; $y++){
			    
				    $red = mt_rand(0, 255);
				    $green = mt_rand(0, 255);
				    $blue = mt_rand(0, 255);
	
				    $data = '{"fillColor":"rgba('.$red.','.$green.','.$blue.',0.5)","strokeColor":"rgba('.$red.','.$green.','.$blue.',1)","data":[';
				    
				    for($i=0; $i<sizeOf($instruments); $i++){
				    
				    	if($month != 'null'){
					    	$query = $this->db_connection->prepare('SELECT count(*) FROM instrumentBookings WHERE userId=:userId AND instrumentId=:instrumentId AND MONTH(dateFrom) = :month AND YEAR(dateFrom) = :year');
							$query->bindValue(':month', $month, PDO::PARAM_INT);
				    	}else{
					    	$query = $this->db_connection->prepare('SELECT count(*) FROM instrumentBookings WHERE userId=:userId AND instrumentId=:instrumentId AND YEAR(dateFrom) = :year');
				    	}
					    
					    $query->bindValue(':instrumentId', $instruments[$i]['id'], PDO::PARAM_INT);
						$query->bindValue(':userId', $user, PDO::PARAM_INT);
						$query->bindValue(':year', $y, PDO::PARAM_INT);
						//$query->bindValue(':endYear', $endYear, PDO::PARAM_INT);
					    $query->execute();
						$count = $query->fetch();
						$data .= $count[0] . ',';
					
					}
				
				}
				
				$data = rtrim($data, ',');
				$data .= ']}';
				//if($y != sizeOf($instruments)){$data .= ',';}
								
				$dataset .= $data;
			    
			    $dataset .= ']}';
			    
		    }else{
			    
			    $dataset = '{';
			    $dataset .= '"labels":["January","February","March","April","May","June","July","August","September","October","November","December"],';
			    $dataset .= '"datasets":[';
			    
			    for($y=$startYear; $y<=$endYear; $y++){
			    
			    	$red = mt_rand(0, 255);
				    $green = mt_rand(0, 255);
				    $blue = mt_rand(0, 255);
	
				    $data = '{"fillColor":"rgba('.$red.','.$green.','.$blue.',0.5)","strokeColor":"rgba('.$red.','.$green.','.$blue.',1)","title":"'.$y.'","data":[';
					
				    $values = array();
				    for($i=0; $i<12; $i++){
				    
				    	if($instrument != 'null'){
					    	$query = $this->db_connection->prepare('SELECT count(*) FROM instrumentBookings WHERE YEAR(dateFrom) = :year AND MONTH(dateFrom) = :mo AND instrumentId=:id');
					    	$query->bindValue(':id', $instrument, PDO::PARAM_INT);
				    	}else{
					    	$query = $this->db_connection->prepare('SELECT count(*) FROM instrumentBookings WHERE YEAR(dateFrom) = :year AND MONTH(dateFrom) = :mo');
				    	}
					 
					    $query->bindValue(':year', $y, PDO::PARAM_INT);
					    $query->bindValue(':mo', $i+1, PDO::PARAM_INT);
					    $query->execute();
					    $count = $query->fetch();
						$data .= $count[0] . ',';
				    }
				    		   
					$data = rtrim($data, ',');
					$data .= ']}';
					if($y != $endYear){$data .= ',';}
									
					$dataset .= $data;
					
				}
			    
			    $dataset .= ']}';
			    
		    }
		    
			header('Content-Type: application/json');		    
		    return json_encode($dataset);
	    }
	    
    }
    
    public function getServiceRequestsByMonth($startYear, $endYear){
	    
	    if ($this->databaseConnection()) {
		    
		    if($startYear == 0 || $endYear == 0){
		    	$startYear = date("Y");
		    	$endYear = date("Y");
	    	}
		    
	    	$dataset = '{';
		    $dataset .= '"labels":["January","February","March","April","May","June","July","August","September","October","November","December"],';
		    $dataset .= '"datasets":[';
		    
		    for($y=$startYear; $y<=$endYear; $y++){
		    
		    	$red = mt_rand(0, 255);
			    $green = mt_rand(0, 255);
			    $blue = mt_rand(0, 255);

			    $data = '{"fillColor":"rgba('.$red.','.$green.','.$blue.',0.5)","strokeColor":"rgba('.$red.','.$green.','.$blue.',1)","title":"'.$y.'","data":[';
				
			    $values = array();
			    for($i=0; $i<12; $i++){
				    $query = $this->db_connection->prepare('SELECT count(*) FROM mscServiceRequest WHERE YEAR(createdAt) = :year AND MONTH(createdAt) = :mo');
				    $query->bindValue(':year', $y, PDO::PARAM_INT);
				    $query->bindValue(':mo', $i+1, PDO::PARAM_INT);
				    $query->execute();
				    $count = $query->fetch();
					$data .= $count[0] . ',';
			    }
			    		   
				$data = rtrim($data, ',');
				$data .= ']}';
				if($y != $endYear){$data .= ',';}
								
				$dataset .= $data;
				
			}
		    
		    $dataset .= ']}';
		    	    
			header('Content-Type: application/json');		    
		    return json_encode($dataset);
	    }
	    
    }
		
}
?>