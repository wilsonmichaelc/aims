<?php
class ServiceRequestInfo
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
    
    public function getActiveServiceRequests($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscServiceRequest WHERE projectId=:projectId AND NOT (status=:status)');
		    $query->bindValue(':projectId', $id, PDO::PARAM_INT);
			$query->bindValue(':status', 'archived', PDO::PARAM_STR);
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function jsonGetUserServiceRequests($uid){
	    
	    // complete array that gets returned as json	    
	    $completeArray = array();
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscServiceRequest WHERE userId=:uid');
		    $query->bindValue(':uid', $uid, PDO::PARAM_INT);
		    $query->execute();
			$mscServiceRequest = $query->fetchAll(PDO::FETCH_ASSOC);
		    
		    // iterate over the service requests for this user
		    foreach($mscServiceRequest as $mscsr){
		    
		    	// create a new key in the array to hold the service information.
		    	$mscsr['servicesSelected'] = array();
		    	
		    	$query = $this->db_connection->prepare('SELECT * FROM mscServicesSelected WHERE requestId=:rid');
			    $query->bindValue(':rid', $mscsr['id'], PDO::PARAM_INT);
			    $query->execute();
				$servicesSelected = $query->fetchAll(PDO::FETCH_ASSOC);
				
				// create array that can be modified
				$completeServicesSelected = array();
				foreach($servicesSelected as $service){
				
					// add a key to the existing
					$service['serviceName'] = array();
					// get the name for the service in the existing service
					$serviceName = $this->getServiceName($service['serviceId'])['name'];
					// save the name at the key we just created.
					$service['serviceName'] = $serviceName;
					// push the new service onto the 
					array_push($completeServicesSelected, $service);
				}
				
				// save the list of services at the key we just created.
			    $mscsr['servicesSelected'] = $completeServicesSelected;
			    
			    // push the service onto the array that we are actually going to return
			    array_push($completeArray, $mscsr);
		    }
		    		    
	    }
	    
	    header('Content-Type: application/json');
	    return json_encode($completeArray);
	    
    }
    
    public function getServicesSelected($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscServicesSelected WHERE requestId=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function ajaxGetServicesSelected($requestId){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscServicesSelected WHERE requestId=:requestId');
		    $query->bindValue(':requestId', $requestId, PDO::PARAM_INT);
		    $query->execute();
		    		    
			header('Content-Type: application/json');
			return json_encode($query->fetchAll(PDO::FETCH_ASSOC));
		    
	    }
	    
    }
    
    public function getServiceName($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscAnalysisServices WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    
			return $query->fetch(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function getServiceRange($dateFrom, $dateTo, $orderBy, $userId){
	    
	    if ($this->databaseConnection()) {
		    
		    $q = '';
		    if(!empty($userId)){
			    $q = 'SELECT * FROM mscServiceRequest WHERE userId=' . $userId . ' AND createdAt BETWEEN :dateFrom AND :dateTo';
		    }else{
			    $q = 'SELECT * FROM mscServiceRequest WHERE createdAt BETWEEN :dateFrom AND :dateTo';
		    }
		    
		    $query = $this->db_connection->prepare($q);
		    $query->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
		    $query->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
		    $query->execute();
		    		    
			header('Content-Type: application/json');
			return json_encode($query->fetchAll(PDO::FETCH_ASSOC));
		    
	    }
	    
    }
    
}
?>