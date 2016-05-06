<?php
class EstimateCalculator
{
	
    private $db_connection            		= null;    // database connection   
    
    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */    
    public function __construct()
    {

        if(isset($_GET['pass'])){
	        $this->messages[] = "You passed!";
        }

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
    
    public function getServiceEstimate($json, $accountType){
	    
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
		    
		    if($samples >= $analysisRates[$type . 'Cutoff']){
			    $runningTotal += ($analysisRates[$type . 'Cutoff'] * $analysisRates[$type . 'Regular']) + (($samples - $analysisRates[$type . 'Cutoff']) * $analysisRates[$type . 'Discount']);
		    }else{
			    $runningTotal += $analysisRates[$type . 'Regular'] * $samples;
		    }

		    if($decodedJSON[$i]->prep == 'true'){
			    $prepRates = $this->getPrepRates($decodedJSON[$i]->id);
			    
			    if($samples >= $prepRates[$type . 'Cutoff']){
			    	$runningTotal += ($prepRates[$type . 'Cutoff'] * $prepRates[$type . 'Regular']) + (($samples - $prepRates[$type . 'Cutoff']) * $prepRates[$type . 'Discount']);
			    }else{
				    $runningTotal = $prepRates[$type . 'Regular'] * $samples;
			    }
			    /*
			    //  OLD --- DO NOT USE
			    if($samples > $prepRates[$type . 'Cutoff']){
				    $runningTotal += $prepRates[$type . 'Regular'] + (($samples - $prepRates[$type . 'Cutoff']) * $prepRates[$type . 'Discount']);
			    }else{
				    $runningTotal += $prepRates[$type . 'Regular'] * $samples;
			    }
			    */
		    }
		     
	    }
	    
	    header('Content-Type: application/text');
		return $runningTotal;
	    
    }
    
    
    // Fix this calculator to account for prep = 1/0 and use replicates as mulitplier
    public function ajaxGetServiceEstimate($requestId, $accountType){
    
    	$servicesSelected;
    
    	if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscServicesSelected WHERE requestId=:requestId');
		    $query->bindValue(':requestId', $requestId, PDO::PARAM_INT);
		    $query->execute();
		    		    
			$servicesSelected = $query->fetchAll(PDO::FETCH_ASSOC);
			
	    }
	    
	    $runningTotal = 0;
	    	    
	    if($accountType == 1){ $type='member'; }
    	if($accountType == 2){ $type='collaborator'; }
    	if($accountType == 3){ $type='affiliate'; }
    	if($accountType == 4){ $type='umb'; }
    	if($accountType == 5){ $type='nonProfit'; }
    	if($accountType == 6){ $type='forProfit'; }
	    
	    foreach($servicesSelected as $service){
	    
		    $samples = $service['samples'] * $service['replicates'];
		    
		    $analysisRates = $this->getAnalysisRates($service['serviceId']);
		    
		    $reg = $analysisRates[$type . 'Regular'];
		    $disc = $analysisRates[$type . 'Discount'];
		    $cut = $analysisRates[$type . 'Cutoff'];
		    
		    if($samples > $cut){
			    $runningTotal += ($cut * $reg) + (($samples - $cut) * $disc);
		    }else{
			    $runningTotal += $reg * $samples;
		    }

		    if($service['prep'] == true){
		    
			    $prepRates = $this->getPrepRates($service['serviceId']);
			    $p_reg = $prepRates[$type . 'Regular'];
			    $p_disc = $prepRates[$type . 'Discount'];
			    $p_cut = $prepRates[$type . 'Cutoff'];
			    
			    if($samples > $p_cut){
				    $runningTotal += ($p_cut * $p_reg) + (($samples - $p_cut) * $p_disc);
			    }else{
				    $runningTotal += $p_reg * $samples;
			    }
		    }
		     
	    }
	    
	    header('Content-Type: application/text');
		return $runningTotal;
	    
    }
    
    private function getAnalysisRates($id){
    
    	if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT * FROM mscAnalysisServices WHERE id=:id');
				$query->bindValue(':id', $id, PDO::PARAM_INT);
				$query->execute();
				return $query->fetch(PDO::FETCH_ASSOC);
	    }
	    
    }
    
    private function getPrepRates($id){
	    
	    if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT * FROM mscPrepServices WHERE analysisId=:id');
				$query->bindValue(':id', $id, PDO::PARAM_INT);
				$query->execute();
				return $query->fetch(PDO::FETCH_ASSOC);
	    }
	    
    }
    
    private function getStaffRateInternal($id){
	    
	    if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT staffRate FROM bookingRatesInternal WHERE accountTypeId=:id');
				$query->bindValue(':id', $id, PDO::PARAM_INT);
				$query->execute();
				return $query->fetchColumn();
	    }
	    
    }
    
	private function getStaffRateExternal($id){
	    
	    if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT staffRate FROM bookingRatesExternal WHERE accountTypeId=:id');
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
				
				header('Content-Type: application/text');
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
		    	
		    	header('Content-Type: application/text');
				return ($rate * $hours);
			}
    	}
	    
    }
    
    public function getTrainingEstimate($accountType, $hours){
    
    	$internal = array(1, 3);
    	$external = array(2, 4, 5, 6);
    	
    	if(in_array($accountType, $internal)){
	    	
	    	if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT staffRate FROM bookingRatesInternal WHERE accountTypeId=:id');
				$query->bindValue(':id', $accountType, PDO::PARAM_INT);
				$query->execute();
				$rate = $query->fetchColumn();
				
				header('Content-Type: application/text');
				return ($rate * $hours);
			}
	    	
    	}
    	
    	if(in_array($accountType, $external)){
	    	
	    	if ($this->databaseConnection()) {

				$query = $this->db_connection->prepare('SELECT staffRate FROM bookingRatesExternal WHERE accountTypeId=:accountType');
				$query->bindValue(':accountType', $accountType, PDO::PARAM_INT);
				$query->execute();
		    	$rate = $query->fetchColumn();
		    	
		    	header('Content-Type: application/text');
				return ($rate * $hours);
			}
    	}
	    
    }

		
}
?>