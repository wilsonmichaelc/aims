<?php
class ServicesOffered
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
    	if( isset($_POST['newService']) ){
    	
    		if(isset($_POST['prepCheckbox'])){
    		
    			$this->createNewServiceWithPrep($_POST['name'],
		    		$_POST['memberRegular'],$_POST['memberDiscount'],$_POST['memberCutoff'],
		    		$_POST['collaboratorRegular'],$_POST['collaboratorDiscount'],$_POST['collaboratorCutoff'],
		    		$_POST['umbRegular'],$_POST['umbDiscount'],$_POST['umbCutoff'],
		    		$_POST['affiliateRegular'],$_POST['affiliateDiscount'],$_POST['affiliateCutoff'],
		    		$_POST['nonProfitRegular'],$_POST['nonProfitDiscount'],$_POST['nonProfitCutoff'],
		    		$_POST['forProfitRegular'],$_POST['forProfitDiscount'],$_POST['forProfitCutoff'],
		    		$_POST['p_memberRegular'],$_POST['p_memberDiscount'],$_POST['p_memberCutoff'],
		    		$_POST['p_collaboratorRegular'],$_POST['p_collaboratorDiscount'],$_POST['p_collaboratorCutoff'],
		    		$_POST['p_umbRegular'],$_POST['p_umbDiscount'],$_POST['p_umbCutoff'],
		    		$_POST['p_affiliateRegular'],$_POST['p_affiliateDiscount'],$_POST['p_affiliateCutoff'],
		    		$_POST['p_nonProfitRegular'],$_POST['p_nonProfitDiscount'],$_POST['p_nonProfitCutoff'],
		    		$_POST['p_forProfitRegular'],$_POST['p_forProfitDiscount'],$_POST['p_forProfitCutoff']

				);
	    		
    		}else{
    		
	    		$this->createNewService(
		    		$_POST['name'],
		    		$_POST['memberRegular'],$_POST['memberDiscount'],$_POST['memberCutoff'],
		    		$_POST['collaboratorRegular'],$_POST['collaboratorDiscount'],$_POST['collaboratorCutoff'],
		    		$_POST['umbRegular'],$_POST['umbDiscount'],$_POST['umbCutoff'],
		    		$_POST['affiliateRegular'],$_POST['affiliateDiscount'],$_POST['affiliateCutoff'],
		    		$_POST['nonProfitRegular'],$_POST['nonProfitDiscount'],$_POST['nonProfitCutoff'],
		    		$_POST['forProfitRegular'],$_POST['forProfitDiscount'],$_POST['forProfitCutoff']
	    		);
	    		
    		}

    	}
    	
    	if(isset($_GET['success'])){
	        $this->messages[] = "Service created!";
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
    
    public function getServicesOffered(){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscAnalysisServices');
		    $query->execute();
		    
			return $query->fetchAll(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function getPrepService($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT * FROM mscPrepServices WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->execute();
		    
			return $query->fetch(PDO::FETCH_ASSOC);
		    
	    }
	    
    }
    
    public function createNewServiceWithPrep($name, $memberRegular, $memberDiscount, $memberCutoff, $collaboratorRegular, $collaboratorDiscount, $collaboratorCutoff, $umbRegular, $umbDiscount, $umbCutoff, $affiliateRegular, $affiliateDiscount, $affiliateCutoff, $nonProfitRegular, $nonProfitDiscount, $nonProfitCutoff, $forProfitRegular, $forProfitDiscount, $forProfitCutoff, $p_memberRegular, $p_memberDiscount, $p_memberCutoff, $p_collaboratorRegular, $p_collaboratorDiscount, $p_collaboratorCutoff, $p_umbRegular, $p_umbDiscount, $p_umbCutoff, $p_affiliateRegular, $p_affiliateDiscount, $p_affiliateCutoff, $p_nonProfitRegular, $p_nonProfitDiscount, $p_nonProfitCutoff, $p_forProfitRegular, $p_forProfitDiscount, $p_forProfitCutoff){
	    

	    if ($this->databaseConnection()) {
	        	
        	// Insert the Analysis service
        	$query = $this->db_connection->prepare('INSERT INTO mscAnalysisServices (name, memberRegular, memberDiscount, memberCutoff, collaboratorRegular, collaboratorDiscount, collaboratorCutoff, umbRegular, umbDiscount, umbCutoff, affiliateRegular, affiliateDiscount, affiliateCutoff, nonProfitRegular, nonProfitDiscount, nonProfitCutoff, forProfitRegular, forProfitDiscount, forProfitCutoff) VALUES(:name, :memberRegular, :memberDiscount, :memberCutoff, :collaboratorRegular, :collaboratorDiscount, :collaboratorCutoff, :umbRegular, :umbDiscount, :umbCutoff, :affiliateRegular, :affiliateDiscount, :affiliateCutoff, :nonProfitRegular, :nonProfitDiscount, :nonProfitCutoff, :forProfitRegular, :forProfitDiscount, :forProfitCutoff)');
        	$query->bindValue(':name', $name,  PDO::PARAM_STR); 
        	$query->bindValue(':memberRegular', $memberRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':memberDiscount', $memberDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':memberCutoff', $memberCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorRegular', $collaboratorRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorDiscount', $collaboratorDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorCutoff', $collaboratorCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':umbRegular', $umbRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':umbDiscount', $umbDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':umbCutoff', $umbCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateRegular', $affiliateRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateDiscount', $affiliateDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateCutoff', $affiliateCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitRegular', $nonProfitRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitDiscount', $nonProfitDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitCutoff', $nonProfitCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitRegular', $forProfitRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitDiscount', $forProfitDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitCutoff', $forProfitCutoff,  PDO::PARAM_INT);
			
			if(!$query->execute()){
				$this->errors[] = "Something went wrong when we tried to create the service.";
				return;
			}
			$analysisId = $this->db_connection->lastInsertId(); // Get the ID for the analysis service
			
			// Insert the Prep Service
			$query = $this->db_connection->prepare('INSERT INTO mscPrepServices (name, analysisId, memberRegular, memberDiscount, memberCutoff, collaboratorRegular, collaboratorDiscount, collaboratorCutoff, umbRegular, umbDiscount, umbCutoff, affiliateRegular, affiliateDiscount, affiliateCutoff, nonProfitRegular, nonProfitDiscount, nonProfitCutoff, forProfitRegular, forProfitDiscount, forProfitCutoff) VALUES(:name, :analysisId, :memberRegular, :memberDiscount, :memberCutoff, :collaboratorRegular, :collaboratorDiscount, :collaboratorCutoff, :umbRegular, :umbDiscount, :umbCutoff, :affiliateRegular, :affiliateDiscount, :affiliateCutoff, :nonProfitRegular, :nonProfitDiscount, :nonProfitCutoff, :forProfitRegular, :forProfitDiscount, :forProfitCutoff)');
        	$query->bindValue(':name', $name,  PDO::PARAM_STR); 
        	$query->bindValue(':analysisId', $analysisId,  PDO::PARAM_INT); 
        	$query->bindValue(':memberRegular', $p_memberRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':memberDiscount', $p_memberDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':memberCutoff', $p_memberCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorRegular', $p_collaboratorRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorDiscount', $p_collaboratorDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorCutoff', $p_collaboratorCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':umbRegular', $p_umbRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':umbDiscount', $p_umbDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':umbCutoff', $p_umbCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateRegular', $p_affiliateRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateDiscount', $p_affiliateDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateCutoff', $p_affiliateCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitRegular', $p_nonProfitRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitDiscount', $p_nonProfitDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitCutoff', $p_nonProfitCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitRegular', $p_forProfitRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitDiscount', $p_forProfitDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitCutoff', $p_forProfitCutoff,  PDO::PARAM_INT);
        	
			if(!$query->execute()){
				$this->errors[] = "Something went wrong when we tried to create the service.";
				// Since inserting the prep service failed, remove the analysis service
				$query = $this->db_connection->prepare('DELETE * FROM mscAnalysisServices WHERE id=:id');
				$query->bindValue(':id', $analysisId, PDO::PARAM_INT);
				$query->execute();
				
				return;
			}
			$prepId = $this->db_connection->lastInsertId(); // Get the ID for the prep service
			
			// Update the Analysis service to include this prep service
			$query = $this->db_connection->prepare('UPDATE mscAnalysisServices SET samplePrepId=:prepId WHERE id=:analysisId');
			$query->bindValue(':prepId', $prepId,  PDO::PARAM_INT); 
	        $query->bindValue(':analysisId', $analysisId,  PDO::PARAM_INT);

	        if(!$query->execute()){
		        $this->errors[] = "Something went wrong when we tried to create the service.";

				$query = $this->db_connection->prepare('DELETE * FROM mscAnalysisServices WHERE id=:id');
				$query->bindValue(':id', $analysisId, PDO::PARAM_INT);
				$query->execute();
				
				$query = $this->db_connection->prepare('DELETE * FROM mscPrepServices WHERE id=:id');
				$query->bindValue(':id', $prepId, PDO::PARAM_INT);
				$query->execute();
				
				return;
	        }else{
		        header("Location: " . $_SERVER['REQUEST_URI'] . '?success');
	        }
		    
	    }
	    
    }
    
    public function createNewService($name, $memberRegular, $memberDiscount, $memberCutoff, $collaboratorRegular, $collaboratorDiscount, $collaboratorCutoff, $umbRegular, $umbDiscount, $umbCutoff, $affiliateRegular, $affiliateDiscount, $affiliateCutoff, $nonProfitRegular, $nonProfitDiscount, $nonProfitCutoff, $forProfitRegular, $forProfitDiscount, $forProfitCutoff){
	    

	    if ($this->databaseConnection()) {
	        	
        	// Insert the Analysis service
        	$query = $this->db_connection->prepare('INSERT INTO mscAnalysisServices (name, memberRegular, memberDiscount, memberCutoff, collaboratorRegular, collaboratorDiscount, collaboratorCutoff, umbRegular, umbDiscount, umbCutoff, affiliateRegular, affiliateDiscount, affiliateCutoff, nonProfitRegular, nonProfitDiscount, nonProfitCutoff, forProfitRegular, forProfitDiscount, forProfitCutoff) VALUES(:name, :memberRegular, :memberDiscount, :memberCutoff, :collaboratorRegular, :collaboratorDiscount, :collaboratorCutoff, :umbRegular, :umbDiscount, :umbCutoff, :affiliateRegular, :affiliateDiscount, :affiliateCutoff, :nonProfitRegular, :nonProfitDiscount, :nonProfitCutoff, :forProfitRegular, :forProfitDiscount, :forProfitCutoff)');
        	$query->bindValue(':name', $name,  PDO::PARAM_STR); 
        	$query->bindValue(':memberRegular', $memberRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':memberDiscount', $memberDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':memberCutoff', $memberCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorRegular', $collaboratorRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorDiscount', $collaboratorDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorCutoff', $collaboratorCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':umbRegular', $umbRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':umbDiscount', $umbDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':umbCutoff', $umbCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateRegular', $affiliateRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateDiscount', $affiliateDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateCutoff', $affiliateCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitRegular', $nonProfitRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitDiscount', $nonProfitDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitCutoff', $nonProfitCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitRegular', $forProfitRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitDiscount', $forProfitDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitCutoff', $forProfitCutoff,  PDO::PARAM_INT);
        	
        	if($query->execute())
        	{
	        	header("Location: " . $_SERVER['REQUEST_URI'] . '?success');
        	}else{
	        	$this->errors[] = "Something went wrong when we tried to create the service.";
        	}
		    
	    }
	    
    }
    
    public function ajaxUpdateAnalysisService($id, $memberRegular, $memberDiscount, $memberCutoff, $collaboratorRegular, $collaboratorDiscount, $collaboratorCutoff, $umbRegular, $umbDiscount, $umbCutoff, $affiliateRegular, $affiliateDiscount, $affiliateCutoff, $nonProfitRegular, $nonProfitDiscount, $nonProfitCutoff, $forProfitRegular, $forProfitDiscount, $forProfitCutoff){
    
	    if ($this->databaseConnection()) {
	        	
        	// Insert the Analysis service
        	$query = $this->db_connection->prepare('UPDATE mscAnalysisServices SET 
        		memberRegular=:memberRegular, 
        		memberDiscount=:memberDiscount, 
        		memberCutoff=:memberCutoff, 
        		collaboratorRegular=:collaboratorRegular, 
        		collaboratorDiscount=:collaboratorDiscount, 
        		collaboratorCutoff=:collaboratorCutoff, 
        		umbRegular=:umbRegular, 
        		umbDiscount=:umbDiscount, 
        		umbCutoff=:umbCutoff, 
        		affiliateRegular=:affiliateRegular, 
        		affiliateDiscount=:affiliateDiscount, 
        		affiliateCutoff=:affiliateCutoff, 
        		nonProfitRegular=:nonProfitRegular, 
        		nonProfitDiscount=:nonProfitDiscount, 
        		nonProfitCutoff=:nonProfitCutoff, 
        		forProfitRegular=:forProfitRegular, 
        		forProfitDiscount=:forProfitDiscount, 
        		forProfitCutoff=:forProfitCutoff
        		WHERE id=:id');

        	$query->bindValue(':memberRegular', $memberRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':memberDiscount', $memberDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':memberCutoff', $memberCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorRegular', $collaboratorRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorDiscount', $collaboratorDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorCutoff', $collaboratorCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':umbRegular', $umbRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':umbDiscount', $umbDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':umbCutoff', $umbCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateRegular', $affiliateRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateDiscount', $affiliateDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateCutoff', $affiliateCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitRegular', $nonProfitRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitDiscount', $nonProfitDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitCutoff', $nonProfitCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitRegular', $forProfitRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitDiscount', $forProfitDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitCutoff', $forProfitCutoff,  PDO::PARAM_INT);
        	$query->bindValue(':id', $id,  PDO::PARAM_INT);
        	
        	if($query->execute())
        	{
				return true;
        	}else{
				return false;
        	}

	    }
	    
    }
    
    public function ajaxUpdatePrepService($id, $memberRegular, $memberDiscount, $memberCutoff, $collaboratorRegular, $collaboratorDiscount, $collaboratorCutoff, $umbRegular, $umbDiscount, $umbCutoff, $affiliateRegular, $affiliateDiscount, $affiliateCutoff, $nonProfitRegular, $nonProfitDiscount, $nonProfitCutoff, $forProfitRegular, $forProfitDiscount, $forProfitCutoff){
    
	    if ($this->databaseConnection()) {
	        	
        	// Insert the Analysis service
        	$query = $this->db_connection->prepare('UPDATE mscPrepServices SET 
        		memberRegular=:memberRegular, 
        		memberDiscount=:memberDiscount, 
        		memberCutoff=:memberCutoff, 
        		collaboratorRegular=:collaboratorRegular, 
        		collaboratorDiscount=:collaboratorDiscount, 
        		collaboratorCutoff=:collaboratorCutoff, 
        		umbRegular=:umbRegular, 
        		umbDiscount=:umbDiscount, 
        		umbCutoff=:umbCutoff, 
        		affiliateRegular=:affiliateRegular, 
        		affiliateDiscount=:affiliateDiscount, 
        		affiliateCutoff=:affiliateCutoff, 
        		nonProfitRegular=:nonProfitRegular, 
        		nonProfitDiscount=:nonProfitDiscount, 
        		nonProfitCutoff=:nonProfitCutoff, 
        		forProfitRegular=:forProfitRegular, 
        		forProfitDiscount=:forProfitDiscount, 
        		forProfitCutoff=:forProfitCutoff
				WHERE id=:id');
        	$query->bindValue(':memberRegular', $memberRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':memberDiscount', $memberDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':memberCutoff', $memberCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorRegular', $collaboratorRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorDiscount', $collaboratorDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':collaboratorCutoff', $collaboratorCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':umbRegular', $umbRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':umbDiscount', $umbDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':umbCutoff', $umbCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateRegular', $affiliateRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateDiscount', $affiliateDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':affiliateCutoff', $affiliateCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitRegular', $nonProfitRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitDiscount', $nonProfitDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':nonProfitCutoff', $nonProfitCutoff,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitRegular', $forProfitRegular,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitDiscount', $forProfitDiscount,  PDO::PARAM_INT); 
        	$query->bindValue(':forProfitCutoff', $forProfitCutoff,  PDO::PARAM_INT);
        	$query->bindValue(':id', $id,  PDO::PARAM_INT);
        	
        	if($query->execute())
        	{
				return true;
        	}else{
				return false;
        	}

	    }
	    
    }
    
    public function updateServiceName($id, $name){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('UPDATE mscAnalysisServices SET name=:name WHERE id=:id');
		    $query->bindValue(':id', $id, PDO::PARAM_INT);
		    $query->bindValue(':name', $name, PDO::PARAM_STR);
		    if($query->execute()){
			    return true;
		    }else{
			    return false;
		    }
		    
	    }
	    
    }
    
}
?>