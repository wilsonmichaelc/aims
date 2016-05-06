<?php
require_once("./php/config/config.php");
class NewServiceRequest
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
        if (isset($_POST['createServiceRequest'])) {

			$services_array = array();

			foreach($_POST as $key => $value){
				if($this->startsWith($key, 'msc') && isset($key)){
					$v = substr($key,3);
					$prep = 'na';
					//if(isset($_POST[$v.'_prep'])){$prep = $_POST[$v.'_prep'];}
					if( !isset($_POST[$v . '_Prep']) || $_POST[$v . '_Prep'] === '0'){$prep=0;}else{$prep=1;}
					//array_push($services_array, array('service'=>$v, 'samples'=>$_POST[$v . '_Samples'], 'replicates'=>$_POST[$v . '_Replicates'], 'prep'=>$prep) );
					array_push($services_array, array('service'=>$v, 'samples'=>$_POST[$v . '_Samples'], 'prep'=>$prep, 'replicates'=>$_POST[$v . '_Replicates']) );
				}
			}
			//var_dump($services_array);

			$this->createServiceRequest(
				$_SESSION['id'],
				$_POST['projectId'],
				$_POST['label'],
				$_POST['concentration'],
				$_POST['state'],
				$_POST['composition'],
				$_POST['digestionEnzyme'],
				$_POST['species'],
				$_POST['purification'],
				$_POST['redoxChemicals'],
				$_POST['molecularWeight'],
				$_POST['suspectedModifications'],
				$_POST['aaModifications'],
				$_POST['sequence'],
				$_POST['comments'],
				$services_array
			);

        }

        if(isset($_GET['success'])){
	        $this->messages[] = "Your request has been submitted! We will contact you if we have any questions.";
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

    private function createServiceRequest($userId, $projectId, $label, $concentration, $state, $composition, $digestionEnzyme, $species, $purification, $redoxChemicals, $molecularWeight, $suspectedModifications, $aaModifications, $sequence, $comments, $servicesSelected)
    {

        if ($this->databaseConnection()) {

        	try{

        		$this->db_connection->beginTransaction();

        		$query = $this->db_connection->prepare('INSERT INTO mscServiceRequest (userId, projectId, label, concentration, state, composition, digestionEnzyme, species, purification, redoxChemicals, molecularWeight, suspectedModifications, aaModifications, sequence, comments)
        		VALUES(:userId, :projectId, :label, :concentration, :state, :composition, :digestionEnzyme, :species, :purification, :redoxChemicals, :molecularWeight, :suspectedModifications, :aaModifications, :sequence, :comments)');
		        $query->bindValue(':userId', $userId, PDO::PARAM_INT);
		        $query->bindValue(':projectId', $projectId, PDO::PARAM_INT);
		        $query->bindValue(':label', $label, PDO::PARAM_STR);
		        $query->bindValue(':concentration', $concentration, PDO::PARAM_STR);
		        $query->bindValue(':state', $state, PDO::PARAM_STR);
		        $query->bindValue(':composition', $composition, PDO::PARAM_STR);
		        $query->bindValue(':digestionEnzyme', $digestionEnzyme, PDO::PARAM_STR);
		        $query->bindValue(':species', $species, PDO::PARAM_STR);
		        $query->bindValue(':purification', $purification, PDO::PARAM_STR);
		        $query->bindValue(':redoxChemicals', $redoxChemicals, PDO::PARAM_STR);
		        $query->bindValue(':molecularWeight', $molecularWeight, PDO::PARAM_STR);
		        $query->bindValue(':suspectedModifications', $suspectedModifications, PDO::PARAM_STR);
		        $query->bindValue(':aaModifications', $aaModifications, PDO::PARAM_STR);
		        $query->bindValue(':sequence', $sequence, PDO::PARAM_STR);
		        $query->bindValue(':comments', $comments, PDO::PARAM_STR);
		        $query->execute();
				$lastRequestId = $this->db_connection->lastInsertId();

				foreach($servicesSelected as $service){
			        $query = $this->db_connection->prepare('INSERT INTO mscServicesSelected (requestId, serviceId, samples, prep, replicates) VALUES(:requestId, :serviceId, :samples, :prep, :replicates)');
			        $query->bindValue(':requestId', $lastRequestId, PDO::PARAM_INT);
			        $query->bindValue(':serviceId', $service['service'], PDO::PARAM_INT);
			        $query->bindValue(':samples', $service['samples'], PDO::PARAM_INT);
			        $query->bindValue(':replicates', $service['replicates'], PDO::PARAM_INT);
			        $query->bindValue(':prep', $service['prep'], PDO::PARAM_INT);
			        $query->execute();
		        }

				$status = $this->db_connection->commit();

        	}catch(PDOException $PDOEx){
        		$this->db_connection->rollBack();
	        	throw $PDOEx;
        	}

        	if($status)
        	{
        		$this->sendNewFFSEmail($userId, $projectId, $lastRequestId, $comments, $servicesSelected);
	        	header("Location: " . $_SERVER['REQUEST_URI'] . '?success');
        	}else{
	        	$this->errors[] = "Something went wrong when we tried to create your service request.";
        	}

        }

    }



    private function startsWith($haystack, $needle){
	    return $needle === "" || strpos($haystack, $needle) === 0;
	}

	private function sendNewFFSEmail($userId, $projectId, $lastRequestId, $comments, $servicesSelected)
    {
    	if ($this->databaseConnection()) {
	       $query = $this->db_connection->prepare('SELECT first, last FROM users WHERE id=:id');
			   $query->bindValue(':id', $userId, PDO::PARAM_INT);
			   $query->execute();
			   $user = $query->fetch(PDO::FETCH_ASSOC);
		  }
        $mail = new PHPMailer;
        $body = '<html><body><div>';

        // please look into the config/config.php for much more info on how to use this!
        // use SMTP or use mail()
        if (EMAIL_USE_SMTP) {

            // Set mailer to use SMTP
            $mail->IsSMTP();
            //useful for debugging, shows full SMTP errors
            $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            // Enable SMTP authentication
            $mail->SMTPAuth = EMAIL_SMTP_AUTH;
            // Enable encryption, usually SSL/TLS
            if (defined(EMAIL_SMTP_ENCRYPTION)) {
                $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
            }
            // Specify host server
            $mail->Host = EMAIL_SMTP_HOST;
            $mail->Username = EMAIL_SMTP_USERNAME;
            $mail->Password = EMAIL_SMTP_PASSWORD;
            $mail->Port = EMAIL_SMTP_PORT;

        } else {

            $mail->IsMail();
        }

        $mail->IsHTML(true);

        $mail->From = EMAIL_NEW_FFS_FROM;
        $mail->FromName = EMAIL_NEW_FFS_FROM_NAME;

        foreach(EMAIL_FFS_ALERTS as $email){
          $mail->AddAddress($email);
        }

        $mail->Subject = EMAIL_NEW_FFS_FROM_SUBJECT;

		$body .= '<p><b>' . $user['first'] . ' ' . $user['last'] . '</b> has submitted a FFS request!</p>';
		$body .= '<div>ProjectID: ' . $projectId . '</div>';
		$body .= '<div>RequestID: ' . $lastRequestId . '</div>';
		$body .= '<div>Comments: ' . $comments . '</div>';

		foreach($servicesSelected as $service){
			if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT name FROM mscAnalysisServices WHERE id=:id');
				$query->bindValue(':id', $service['service'], PDO::PARAM_INT);
				$query->execute();
				$name = $query->fetchColumn();
			}
	        $body .= '<div><b>Service:</b>' . $name . ' (' . $service['service'] . ')</div>';
	        $body .= '<div>Samples:' . $service['samples'] . '</div>';

	        $body .= '<div>Replicates:';
	        	if($service['replicates'] == 1){
	        		$body .= 'none';
	        	}elseif($service['repilcates'] == 2){
		        	$body .= 'two';
	        	}else{
		        	$body .= 'three';
	        	}
	        $body .= '</div>';

	        $body .= '<div>Prep:';
	        if($service['prep'] == 0){
	        		$body .= 'no';
	        	}elseif($service['prep'] == 1){
		        	$body .= 'yes';
	        	}
	        $body .= '</div><br>';
        }

		$body .= '<div>Please login to view this request.</div>';
		$body .= '</p></div></body></html>';

		$mail->Body = $body;

        if(!$mail->Send()) {
            return false;
        }else{
	        return true;
        }
    }

}
?>
