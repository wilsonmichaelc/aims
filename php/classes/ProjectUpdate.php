<?php
class ProjectUpdate
{
	
    private $db_connection            		= null;    // database connection   
    
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
    
    public function updateProject($projectId, $primaryInvestigator, $addressOne, $addressTwo, $city, $state, $zip, $phone, $fax, $status, $abstract, $purchaseOrder, $projectCostingBusinessUnit, $pmntProjectId, $departmentId, $pmntId)
    {
     	  
        if ($this->databaseConnection()) {
        
        	header('Content-Type: application/text');
        	
        	$query = $this->db_connection->prepare('UPDATE projects SET primaryInvestigator=:primaryInvestigator, addressOne=:addressOne, addressTwo=:addressTwo, city=:city, state=:state, zip=:zip, phone=:phone, fax=:fax, status=:status, abstract=:abstract WHERE id=:projectId');
        											
	        $query->bindValue(':primaryInvestigator', $primaryInvestigator, PDO::PARAM_STR);
	        $query->bindValue(':addressOne', $addressOne, PDO::PARAM_STR);
	        $query->bindValue(':addressTwo', $addressTwo, PDO::PARAM_STR);
	        $query->bindValue(':city', $city, PDO::PARAM_STR);
			$query->bindValue(':state', $state, PDO::PARAM_STR);
			$query->bindValue(':zip', $zip, PDO::PARAM_STR);
			$query->bindValue(':phone', $phone, PDO::PARAM_STR);
			$query->bindValue(':fax', $fax, PDO::PARAM_STR);
			$query->bindValue(':status', $status, PDO::PARAM_STR);
			$query->bindValue(':abstract', $abstract, PDO::PARAM_STR);
			$query->bindValue(':projectId', $projectId, PDO::PARAM_INT);
			
			if(!$query->execute()){
				return false;
			}

				
			$query = $this->db_connection->prepare('UPDATE paymentInfo SET purchaseOrder=:purchaseOrder, projectCostingBusinessUnit=:projectCostingBusinessUnit, projectId=:projectId, departmentId=:departmentId WHERE id=:id');
			$query->bindValue(':purchaseOrder', $purchaseOrder, PDO::PARAM_STR);
	        $query->bindValue(':projectCostingBusinessUnit', $projectCostingBusinessUnit, PDO::PARAM_STR);
	        $query->bindValue(':projectId', $pmntProjectId, PDO::PARAM_STR);
			$query->bindValue(':departmentId', $departmentId, PDO::PARAM_STR);
			$query->bindValue(':id', $pmntId, PDO::PARAM_INT);
			
			if(!$query->execute()){
			    return false;
		    }

			return true;

        }
        
    }
    
    public function updateProjectByUser($projectId, $title, $addressOne, $addressTwo, $city, $state, $zip, $phone, $fax, $abstract)
    {
        
        if ($this->databaseConnection()) {
        
        	$query = $this->db_connection->prepare('UPDATE projects SET title=:title, addressOne=:addressOne, addressTwo=:addressTwo, city=:city, state=:state, zip=:zip, phone=:phone, fax=:fax, abstract=:abstract WHERE id=:projectId');
	        $query->bindValue(':projectId', $projectId, PDO::PARAM_STR);
	        $query->bindValue(':title', $title, PDO::PARAM_STR);
	        $query->bindValue(':addressOne', $addressOne, PDO::PARAM_STR);
	        $query->bindValue(':addressTwo', $addressTwo, PDO::PARAM_STR);
	        $query->bindValue(':city', $city, PDO::PARAM_STR);
			$query->bindValue(':state', $state, PDO::PARAM_STR);
			$query->bindValue(':zip', $zip, PDO::PARAM_STR);
			$query->bindValue(':phone', $phone, PDO::PARAM_STR);
			$query->bindValue(':fax', $fax, PDO::PARAM_STR);
			$query->bindValue(':abstract', $abstract, PDO::PARAM_STR);
			
			header('Content-Type: application/text');
			if($query->execute()){
			    return true;
		    }else{
			    return false;
		    }
        }
        
    }
} 
?>