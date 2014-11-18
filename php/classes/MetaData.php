<?php
class MetaData
{
	
    private $db_connection = null;    // database connection   

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
    
    public function getUserName($id){
    
	  	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT first, last FROM users WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    $row = $query->fetch(PDO::FETCH_ASSOC);
		    return $row['first'] . ' ' . $row['last'];
		    		    
	    }
	    
    }
    
    public function getUserFirstName($id){
    
	  	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT first FROM users WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    return $query->fetchColumn();		    		    
	    }
	    
    }
    
    public function getUserType($id){
    
	  	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT name, id FROM accountTypes WHERE id=(SELECT accountType FROM users WHERE id=:id)');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    return $query->fetch(PDO::FETCH_ASSOC);
		    		    
	    }
	    
    }
    
    public function getProjectTitle($id){
    
	  	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT title FROM projects WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    return $query->fetch(PDO::FETCH_ASSOC);
		    		    
	    }
	    
    }
    
    public function getInstrumentName($id){
    
	  	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT name FROM mscInstruments WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    return $query->fetch(PDO::FETCH_ASSOC);
		    		    
	    }
	    
    }
    
    public function getTrainingModuleName($id){
    
	  	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT name FROM trainingModules WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    return $query->fetchColumn();
		    		    
	    }
	    
    }
    
    public function getBookingEstimate($accountType, $instrument, $hours){
    
    	$internal = array(1, 3);
    	$external = array(2, 4, 5, 6);
    	
    	if(in_array($accountType, $internal)){
	    	
	    	if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT * FROM bookingRatesInternal WHERE accountTypeId=:id');
				$query->bindValue(':id', $accountType, PDO::PARAM_INT);
				$query->execute();
				$hourlyRates = $query->fetch(PDO::FETCH_ASSOC);
			
				$runningTotal = 0;
			
				while($hours > 0){
					
					if($hours >= 24){
						$runningTotal += $hourlyRates['twentyFourHours'];
						$hours -= 24;
					}else if($hours >= 16){
						$runningTotal += $hourlyRates['sixteenHours'];
						$hours -= 16;
					}else if($hours >= 8){
						$runningTotal += $hourlyRates['eightHours'];
						$hours -= 8;
					}else if($hours >= 4){
						$runningTotal += $hourlyRates['fourHours'];
						$hours -= 4;
					}else{
						$runningTotal += ($hours * $hourlyRates['oneHour']);
						$hours -= $hours;
					}
					
				}
				
				return ($runningTotal);
			}
	    	
    	}
    	
    	if(in_array($accountType, $external)){
	    	
	    	if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT accuracy FROM mscInstruments WHERE id=:id');
				$query->bindValue(':id', $instrument, PDO::PARAM_INT);
				$query->execute();
				$instrumentAccuracy = $query->fetch();
			
				if($instrumentAccuracy == 'high'){
					$query = $this->db_connection->prepare('SELECT highAccuracyRate FROM bookingRatesExternal WHERE accountTypeId=:accountType');
				}else{
					$query = $this->db_connection->prepare('SELECT lowAccuracyRate FROM bookingRatesExternal WHERE accountTypeId=:accountType');
				}
				
				$query->bindValue(':accountType', $accountType, PDO::PARAM_INT);
				$query->execute();
		    	$rate = $query->fetchColumn();
		    	
				return ($rate * $hours);
			}
    	}
	    
    }
    
    public function getServiceEstimate($serviceId, $accountType){
	    
	    $decodedJSON = json_decode($json);
	    $runningTotal = 0;
	    
	    //var_dump($decodedJSON);
	    
	    if($accountType == 1){ $type='member'; }
    	if($accountType == 2){ $type='collaborator'; }
    	if($accountType == 3){ $type='affiliate'; }
    	if($accountType == 4){ $type='umb'; }
    	if($accountType == 5){ $type='nonProfit'; }
    	if($accountType == 6){ $type='forProfit'; }
	    
	    for($i=0; $i<sizeOf($decodedJSON); $i++){
	    
		    $samples = $decodedJSON[$i]->samples;
		    
		    $analysisRates = $this->getAnalysisRates($decodedJSON[$i]->id);
		    
		    if($samples > $analysisRates[$type . 'Cutoff']){
			    $runningTotal += ($analysisRates[$type . 'Cutoff'] * $analysisRates[$type . 'Regular']) + (($samples - $analysisRates[$type . 'Cutoff']) * $analysisRates[$type . 'Discount']);
		    }else{
			    $runningTotal += $analysisRates[$type . 'Regular'] * $samples;
		    }
		    
		    if($decodedJSON[$i]->prep == true){
			    //$prepRates = $this->getPrepRates($decodedJSON[$i]->id);
			    $prepRates = $this->getPrepRates($analysisRates['samplePrepId']);
			    if($samples > $prepRates[$type . 'Cutoff']){
				    $runningTotal += ($prepRates[$type . 'Cutoff'] * $prepRates[$type . 'Regular']) + (($samples - $prepRates[$type . 'Cutoff']) * $prepRates[$type . 'Discount']);
			    }else{
				    $runningTotal += $prepRates[$type . 'Regular'] * $samples;
			    }
		    }
		     
	    }
	    
	    header('Content-Type: application/text');
		return $runningTotal;
	    
    }
    
    public function getServiceName($id){
    
	  	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT name FROM mscAnalysisServices WHERE id=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			header('Content-Type: application/text');
			return $query->fetchColumn();
		    		    
	    }
	    
    }
    
    public function getLogo($imageType){
	    if($imageType == "jpeg"){
		    if ($this->databaseConnection())
		    {
			    $query = $this->db_connection->prepare('SELECT jpeg FROM sopLogo LIMIT 1');
				$query->execute();
				header('Content-Type: application/text');
				return $query->fetchColumn();
			}
	    }
    }
    
	public function jsonGetPmntInfo($id){
    
	  	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM paymentInfo WHERE id=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			header('Content-Type: application/json');
			return json_encode($query->fetch(PDO::FETCH_ASSOC));
		    		    
	    }
	    
    }
    
    public function calculateHours($dateFrom, $timeFrom, $dateTo, $timeTo){
	    
	    date_default_timezone_set('America/New_York');
	    
        $from = new DateTime($dateFrom . 'T' . $timeFrom);
        $to = new DateTime($dateTo . 'T' . $timeTo);
        
                
        $diff = $to->diff($from);
        
        $hours = (($diff->i) / 60);
        $hours = $hours + $diff->h;
        $hours = $hours + ($diff->days*24);
        
        return $hours;
	    
    }
    	
}
?>