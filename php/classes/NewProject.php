<?php
class NewProject
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
        // if we have such a POST request, call the registerNewUser() method
        if (isset($_POST['createProject'])) {

			$this->createNewProject(
				$_SESSION['id'], 
				$_POST['title'],
				$_POST['abstract'], 
				$_POST['primaryInvestigator'],
				$_POST['addressOne'],
				$_POST['addressTwo'],
				$_POST['city'], 
				$_POST['state'],
				$_POST['zip'],
				$_POST['phone'],
				$_POST['fax'],
				$_POST['projectCostingBusinessUnit'], 
				$_POST['projectId'],
				$_POST['departmentId'],
				$_POST['purchaseOrder']
			);

        }
        
        if(isset($_GET['success'])){
	        $this->messages[] = "Project created! You may now submit samples or get trained on an instrument!";
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
                $this->db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db_connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                return true;

            // If an error is catched, database connection failed
            } catch (PDOException $e) {
                $this->errors[] = "Database connection problem.";
                return false;
            }
        }
    }
    
    private function createNewProject($userId, $title, $abstract, $primaryInvestigator, $addressOne, $addressTwo, $city, $state, $zip, $phone, $fax, $projectCostingBusinessUnit, $projectId, $departmentId, $purchaseOrder)
    {
        
        if ($this->databaseConnection()) {
        
        	try
        	{

	        	$this->db_connection->beginTransaction();
	        	
	        	$queryPaymentInfo = $this->db_connection->prepare('INSERT INTO paymentInfo (purchaseOrder, projectCostingBusinessUnit, projectId, departmentId) VALUES(:purchaseOrder, :projectCostingBusinessUnit, :projectId, :departmentId)');
	        	$queryPaymentInfo->bindValue(':purchaseOrder', $purchaseOrder, PDO::PARAM_STR);
				$queryPaymentInfo->bindValue(':projectCostingBusinessUnit', $projectCostingBusinessUnit, PDO::PARAM_STR);
				$queryPaymentInfo->bindValue(':projectId', $projectId, PDO::PARAM_STR);
				$queryPaymentInfo->bindValue(':departmentId', $departmentId, PDO::PARAM_STR);
				$queryPaymentInfo->execute();
				$paymentId = $this->db_connection->lastInsertId();

				$queryProject = $this->db_connection->prepare('INSERT INTO projects (userId, paymentId, title, abstract, primaryInvestigator, addressOne, addressTwo, city, state, zip, phone, fax, status) 
				VALUES(:userId, :paymentId, :title, :abstract, :primaryInvestigator, :addressOne, :addressTwo, :city, :state, :zip, :phone, :fax, :status)');
		        $queryProject->bindValue(':userId', $userId, PDO::PARAM_INT);
		        $queryProject->bindValue(':paymentId', $paymentId, PDO::PARAM_INT);
  		        $queryProject->bindValue(':title', $title, PDO::PARAM_STR);
		        $queryProject->bindValue(':abstract', $abstract, PDO::PARAM_STR);
		        $queryProject->bindValue(':primaryInvestigator', $primaryInvestigator, PDO::PARAM_STR);
		        $queryProject->bindValue(':addressOne', $addressOne, PDO::PARAM_STR);
		        $queryProject->bindValue(':addressTwo', $addressTwo, PDO::PARAM_STR);
		        $queryProject->bindValue(':city', $city, PDO::PARAM_STR);
		        $queryProject->bindValue(':state', $state, PDO::PARAM_STR);
		        $queryProject->bindValue(':zip', $zip, PDO::PARAM_STR);
		        $queryProject->bindValue(':phone', $phone, PDO::PARAM_STR);
		        $queryProject->bindValue(':fax', $fax, PDO::PARAM_STR);
		        $queryProject->bindValue(':status', 'active', PDO::PARAM_STR);
		        $queryProject->execute();
		        $lastId = $this->db_connection->lastInsertId();

	        	if($this->db_connection->commit()){
		        	header("Location: " . $_SERVER['REQUEST_URI'] . '?success');
	        	}else{
		        	$this->errors[] = "Something went wrong when we tried to create your project.";
	        	}
	        	
        	
        	}
        	catch(PDOException $PDOEx)
        	{
	        	$this->db_connection->rollBack();
				throw $PDOEx;
        	}
	        
        }
        
    }		
		
}
?>