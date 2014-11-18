<?php
class ServiceRequestUpdate
{
	
    private $db_connection = null;    // database connection   
    
    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
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
    
    public function updateServiceRequest($id, $label, $concentration, $state, $composition, $digestionEnzyme, $purification, $redoxChemicals, $molecularWeight, $suspectedModifications, $aaModifications, $species, $sequence, $comments, $status){
	    
	    if ($this->databaseConnection()) {

		    $query = $this->db_connection->prepare('UPDATE mscServiceRequest SET label=:label, concentration=:concentration, state=:state, composition=:composition, digestionEnzyme=:digestionEnzyme, purification=:purification, redoxChemicals=:redoxChemicals, molecularWeight=:molecularWeight, suspectedModifications=:suspectedModifications, aaModifications=:aaModifications, species=:species, sequence=:sequence, comments=:comments, status=:status WHERE id=:id');
		    
		    $query->bindValue(':label', $label, PDO::PARAM_STR);
		    $query->bindValue(':concentration', $concentration, PDO::PARAM_STR);
		    $query->bindValue(':state', $state, PDO::PARAM_STR);
		    $query->bindValue(':composition', $composition, PDO::PARAM_STR);
		    $query->bindValue(':digestionEnzyme', $digestionEnzyme, PDO::PARAM_STR);
		    $query->bindValue(':purification', $purification, PDO::PARAM_STR);
		    $query->bindValue(':redoxChemicals', $redoxChemicals, PDO::PARAM_STR);
		    $query->bindValue(':molecularWeight', $molecularWeight, PDO::PARAM_STR);
		    $query->bindValue(':suspectedModifications', $suspectedModifications, PDO::PARAM_STR);
		    $query->bindValue(':aaModifications', $aaModifications, PDO::PARAM_STR);
		    $query->bindValue(':species', $species, PDO::PARAM_STR);
		    $query->bindValue(':sequence', $sequence, PDO::PARAM_STR);
		    $query->bindValue(':comments', $comments, PDO::PARAM_STR);
		    $query->bindValue(':status', $status, PDO::PARAM_STR);
			$query->bindValue(':id', $id, PDO::PARAM_INT);

		    if($query->execute()){
			    return $this->db_connection->lastInsertId();;
		    }else{
			    return false;
		    }
		    
	    }
	    
    }
    
    public function updateSelectedService($id, $samples, $replicates, $prep){
	    
	    if ($this->databaseConnection()) {

		    $query = $this->db_connection->prepare('UPDATE mscServicesSelected SET samples=:samples, replicates=:replicates, prep=:prep WHERE id=:id');
		    
		    $query->bindValue(':samples', $samples, PDO::PARAM_INT);
		    $query->bindValue(':replicates', $replicates, PDO::PARAM_INT);
		    $query->bindValue(':prep', $prep, PDO::PARAM_INT);
			$query->bindValue(':id', $id, PDO::PARAM_INT);

		    if($query->execute()){
			    return true;
		    }else{
			    return false;
		    }
		    
	    }
	    
    }
    
    public function addServiceToRequest($rid, $sid, $samples, $replicates, $prep){
	    
	    if ($this->databaseConnection()) {

		    $query = $this->db_connection->prepare('INSERT INTO mscServicesSelected (requestId, serviceId, samples, replicates, prep) VALUES (:rid, :sid, :samples, :replicates, :prep)');

			$query->bindValue(':rid', $rid, PDO::PARAM_INT);
			$query->bindValue(':sid', $sid, PDO::PARAM_INT);
		    $query->bindValue(':samples', $samples, PDO::PARAM_INT);
		    $query->bindValue(':replicates', $replicates, PDO::PARAM_INT);
		    $query->bindValue(':prep', $prep, PDO::PARAM_INT);

		    if($query->execute()){
			    return $this->db_connection->lastInsertId();
		    }else{
			    return false;
		    }
		    
	    }
	    
    }
    
    public function deleteServiceFromRequest($sid){
	    
	    if ($this->databaseConnection()) {

		    $query = $this->db_connection->prepare('DELETE FROM mscServicesSelected WHERE id=:sid');
			$query->bindValue(':sid', $sid, PDO::PARAM_INT);

		    if($query->execute()){
			    return true;
		    }else{
			    return false;
		    }
		    
	    }
	    
    }
    
}
?>