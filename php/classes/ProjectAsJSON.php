<?php
class ProjectAsJSON
{
	
    private $db_connection            		= null;    // database connection   
    
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
    
    public function getProjectAsJSON($projectId){
	    
	    if ($this->databaseConnection()) {
		    
		    // Get the Project
		    $query = $this->db_connection->prepare('SELECT * FROM projects WHERE id=:projectId');
		    $query->bindValue(':projectId', $projectId, PDO::PARAM_INT);
		    $query->execute();
		    $project = $query->fetch(PDO::FETCH_ASSOC);

		    $query = $this->db_connection->prepare('
		    	SELECT instrumentBookings.id, instrumentBookings.userId, instrumentBookings.projectId, instrumentBookings.instrumentId, instrumentBookings.dateFrom, instrumentBookings.dateTo, 
		    	instrumentBookings.timeFrom, instrumentBookings.timeTo, instrumentBookings.archiveStatus, 
		    	mscInstruments.name, mscInstruments.color, mscInstruments.accuracy
		    	FROM instrumentBookings
		    	INNER JOIN mscInstruments
		    	ON instrumentBookings.instrumentId=mscInstruments.id
		    	WHERE instrumentBookings.projectId=:projectId AND instrumentBookings.invoiced=:zero');

			// Get the Bookings
		    $query->bindValue(':projectId', $projectId, PDO::PARAM_INT);
		    $query->bindValue(':zero', 0, PDO::PARAM_INT);
		    $query->execute();
		    $bookings = $query->fetchAll(PDO::FETCH_ASSOC);

			// Get the Service Requests
			$query = $this->db_connection->prepare('SELECT * FROM mscServiceRequest WHERE projectId=:projectId');
			$query->bindValue(':projectId', $projectId, PDO::PARAM_INT);
			$query->execute();
			$serviceRequests = $query->fetchAll(PDO::FETCH_ASSOC);
			
			$servicesSelected = array();
			foreach($serviceRequests as $s){
				$query = $this->db_connection->prepare('SELECT 
			    	mscServicesSelected.*, mscAnalysisServices.name 
			    	FROM mscServicesSelected
			    	INNER JOIN mscAnalysisServices
			    	ON mscServicesSelected.serviceId=mscAnalysisServices.id
			    	WHERE mscServicesSelected.requestId=:id AND mscServicesSelected.invoiced=:zero');
				$query->bindValue(':id', $s['id'], PDO::PARAM_INT);
				$query->bindValue(':zero', 0, PDO::PARAM_INT);
				$query->execute();
				$result = $query->fetchAll(PDO::FETCH_ASSOC);
				if($result != false){
					//array_push($servicesSelected, $result);
					//foreach($result as $r){
					//	array_push($servicesSelected, $result[$r]);
					//}
					$servicesSelected[] = $result;
				}
			}
			
			// Get the Training Requests
			$query = $this->db_connection->prepare('SELECT
			trainingBookings.*, mscTrainingRequest.bookingId, mscInstruments.name, mscInstruments.model, mscInstruments.color
			FROM trainingBookings
			INNER JOIN mscTrainingRequest
			ON trainingBookings.id=mscTrainingRequest.bookingId
			INNER JOIN mscInstruments
			ON trainingBookings.instrumentId=mscInstruments.id
			WHERE trainingBookings.invoiced=:zero
			AND mscTrainingRequest.projectId=:projectId
			AND NOT (mscTrainingRequest.bookingId <=> NULL)');
			$query->bindValue(':projectId', $projectId, PDO::PARAM_INT);
			$query->bindValue(':zero', 0, PDO::PARAM_INT);
			$query->execute();
			$training = $query->fetchAll(PDO::FETCH_ASSOC);

			// Get the User
			$query = $this->db_connection->prepare('SELECT
			users.id, users.first, users.last, users.email, accountTypes.name
			FROM users
			INNER JOIN accountTypes
			ON users.accountType=accountTypes.id
			WHERE users.id=(SELECT projects.userId FROM projects WHERE projects.id=:projectId)');
		    $query->bindValue(':projectId', $projectId, PDO::PARAM_INT);
		    $query->execute();
		    $user = $query->fetch(PDO::FETCH_ASSOC);

			$completeProject = array('project' => $project, 'user' => $user, 'bookings' => $bookings, 'requests' => $servicesSelected, 'training' => $training);

			header('Content-Type: application/json');
			return json_encode($completeProject);
		    
	    }
	    
    }

    public function getProjectList($id){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('SELECT projects.*, users.first FROM projects INNER JOIN users ON projects.userId=users.id WHERE userId=:projectId');
		    $query->bindValue(':projectId', $id, PDO::PARAM_INT);
		    $query->execute();
		    $projects = $query->fetchAll(PDO::FETCH_ASSOC);

			header('Content-Type: application/json');
			return json_encode($projects);
		    
	    }
	    
    }
    
    public function getUsersForSelection($dateFrom, $dateTo){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('
		    SELECT users.id, users.first, users.last 
			FROM users 
			WHERE (id IN (SELECT userId FROM instrumentBookings WHERE dateFrom BETWEEN :dateFrom AND :dateTo))
			OR
			(id IN (SELECT userId FROM trainingBookings WHERE dateFrom BETWEEN :dateFrom AND :dateTo))
			OR
			(id IN (SELECT userId FROM mscServiceRequest WHERE createdAt BETWEEN :dateFrom AND :dateTo))
		    ');
		    $query->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
		    $query->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
		    $query->execute();
		    $projects = $query->fetchAll(PDO::FETCH_ASSOC);

			header('Content-Type: application/json');
			return json_encode($projects);
		    
	    }
	    
    }
    
    public function getProjectsForSelection($userId, $dateFrom, $dateTo){
	    
	    if ($this->databaseConnection()) {
		    
		    $query = $this->db_connection->prepare('
			SELECT id, title 
			FROM projects 
			WHERE userId=:userId
			AND (
			( id IN (SELECT projectId FROM instrumentBookings WHERE dateFrom BETWEEN :dateFrom AND :dateTo) )
			OR ( id IN (SELECT projectId FROM mscTrainingRequest WHERE bookingId IN (SELECT id FROM trainingBookings WHERE dateFrom BETWEEN :dateFrom AND :dateTo)) )
			OR ( id IN (SELECT projectId FROM mscServiceRequest WHERE userId=:userId AND createdAt BETWEEN :dateFrom AND :dateTo) )
		    )');
		    $query->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
		    $query->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
		    $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->execute();
		    $projects = $query->fetchAll(PDO::FETCH_ASSOC);

			header('Content-Type: application/json');
			return json_encode($projects);
		    
	    }
	    
    }
    
}
?>