<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

class Queries{

	/**
     * Checks if database connection is opened and open it if not
     */
	private $db_connection = null;    // database connection

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

	public function getUserInfo($userId){
		if ($this->databaseConnection()) {
			$query = $this->db_connection->prepare('
				SELECT 
				users.first, users.last, users.email, users.institution,
				accountTypes.shortName AS `accountType`, accountTypes.id AS `accountTypeId`
				FROM users 
				INNER JOIN accountTypes
				ON users.accountType = accountTypes.id
				WHERE users.id=:userId');

			$query->bindValue(':userId', $userId, PDO::PARAM_INT);
		    $query->execute();
		    return $query->fetch(PDO::FETCH_ASSOC);
		}
    }

    public function getProjectInfo($projectId){
    	if ($this->databaseConnection()) {
			$query = $this->db_connection->prepare('
				SELECT 
				projects.title, projects.primaryInvestigator, projects.addressOne, projects.addressTwo, projects.city, projects.state, projects.zip, projects.phone,
				paymentInfo.purchaseOrder, paymentInfo.projectCostingBusinessUnit, paymentInfo.projectId, paymentInfo.departmentId
				FROM projects
				INNER JOIN paymentInfo
				ON projects.paymentId = paymentInfo.id
				WHERE projects.id = :projectId
			');
			$query->bindValue(':projectId', $projectId, PDO::PARAM_INT);
			$query->execute();
		    return $query->fetch(PDO::FETCH_ASSOC);
	    }
    }

    public function getBookingInternal($bid, $accountTypeId){
    	if ($this->databaseConnection()) {
			$query = $this->db_connection->prepare('
				SELECT 
				instrumentBookings.instrumentId, instrumentBookings.dateFrom, instrumentBookings.dateTo, instrumentBookings.timeFrom, instrumentBookings.timeTo, 
				mscInstruments.name AS `instrumentName`, mscInstruments.model AS `instrumentModel`, mscInstruments.accuracy, bookingRatesInternal.*
				FROM instrumentBookings 
				INNER JOIN mscInstruments
				ON instrumentBookings.instrumentId = mscInstruments.id
				INNER JOIN bookingRatesInternal
				ON bookingRatesInternal.accountTypeId = :accountTypeId
				WHERE instrumentBookings.id = :bid');

			$query->bindValue(':bid', $bid, PDO::PARAM_INT);
			$query->bindValue(':accountTypeId', $accountTypeId, PDO::PARAM_INT);
			$query->execute();
			return $query->fetch(PDO::FETCH_ASSOC);
		}
    }

    public function getBookingExternal($bid, $accountTypeId){
    	if ($this->databaseConnection()) {
			$query = $this->db_connection->prepare('
				SELECT 
				instrumentBookings.instrumentId, instrumentBookings.dateFrom, instrumentBookings.dateTo, instrumentBookings.timeFrom, instrumentBookings.timeTo, 
				mscInstruments.name AS `instrumentName`, mscInstruments.model AS `instrumentModel`, mscInstruments.accuracy, bookingRatesExternal.*
				FROM instrumentBookings 
				INNER JOIN mscInstruments
				ON instrumentBookings.instrumentId = mscInstruments.id
				INNER JOIN bookingRatesExternal
				ON bookingRatesExternal.accountTypeId = :accountTypeId
				WHERE instrumentBookings.id = :bid');

			$query->bindValue(':bid', $bid, PDO::PARAM_INT);
			$query->bindValue(':accountTypeId', $accountTypeId, PDO::PARAM_INT);
			$query->execute();
			return $query->fetch(PDO::FETCH_ASSOC);
		}
    }

    public function getTrainingInternal($tid, $accountTypeId){
    	if ($this->databaseConnection()) {
			$query = $this->db_connection->prepare('
				SELECT
				trainingBookings.dateFrom, trainingBookings.dateTo, trainingBookings.timeFrom, trainingBookings.timeTo,
				mscInstruments.name AS `instrumentName`,
				bookingRatesInternal.staffRate
				FROM trainingBookings
				INNER JOIN mscInstruments
				ON mscInstruments.id = trainingBookings.instrumentId
				INNER JOIN bookingRatesInternal
				ON bookingRatesInternal.accountTypeId=:accountTypeId
				WHERE trainingBookings.id=:tid');

			$query->bindValue(':tid', $tid, PDO::PARAM_INT);
			$query->bindValue(':accountTypeId', $accountTypeId, PDO::PARAM_INT);
			$query->execute();
			return $query->fetch(PDO::FETCH_ASSOC);
		}
    }

    public function getTrainingExternal($tid, $accountTypeId){
    	if ($this->databaseConnection()) {
			$query = $this->db_connection->prepare('
				SELECT
				trainingBookings.dateFrom, trainingBookings.dateTo, trainingBookings.timeFrom, trainingBookings.timeTo,
				mscInstruments.name AS `instrumentName`,
				bookingRatesExternal.staffRate
				FROM trainingBookings
				INNER JOIN mscInstruments
				ON mscInstruments.id = trainingBookings.instrumentId
				INNER JOIN bookingRatesExternal
				ON bookingRatesExternal.accountTypeId=:accountTypeId
				WHERE trainingBookings.id=:tid');

			$query->bindValue(':tid', $tid, PDO::PARAM_INT);
			$query->bindValue(':accountTypeId', $accountTypeId, PDO::PARAM_INT);
			$query->execute();
			return $query->fetch(PDO::FETCH_ASSOC);
		}
    }

    public function getServiceRequest($sid, $accountTypeId){
    	if ($this->databaseConnection()){
    		if($accountTypeId == 1){
				$query = $this->db_connection->prepare('
				SELECT 
				mscServicesSelected.requestId, mscServicesSelected.samples, mscServicesSelected.replicates, mscServicesSelected.prep, mscServicesSelected.createdAt,
				mscAnalysisServices.umbRegular AS `UMB_Regular`,
				mscAnalysisServices.name AS `serviceName`, mscAnalysisServices.memberRegular AS `regular`, mscAnalysisServices.memberDiscount AS `discount`, mscAnalysisServices.memberCutoff AS `cutoff`,
				mscPrepServices.umbRegular AS `UMB_Prep_Regular`,
				mscPrepServices.memberRegular AS `prepRegular`, mscPrepServices.memberDiscount AS `prepDiscount`, mscPrepServices.memberCutoff AS `prepCutoff`
				FROM mscServicesSelected 
				INNER JOIN mscAnalysisServices
				ON mscServicesSelected.serviceId = mscAnalysisServices.id
				LEFT JOIN mscPrepServices
				ON mscPrepServices.id = mscAnalysisServices.samplePrepId
				WHERE mscServicesSelected.id=:sid');
			}else if($accountTypeId == 2){
				$query = $this->db_connection->prepare('
				SELECT 
				mscServicesSelected.requestId, mscServicesSelected.samples, mscServicesSelected.replicates, mscServicesSelected.prep, mscServicesSelected.createdAt,
				mscAnalysisServices.umbRegular AS `UMB_Regular`,
				mscAnalysisServices.name AS `serviceName`, mscAnalysisServices.collaboratorRegular AS `regular`, mscAnalysisServices.collaboratorDiscount AS `discount`, mscAnalysisServices.collaboratorCutoff AS `cutoff`,
				mscPrepServices.umbRegular AS `UMB_Prep_Regular`,
				mscPrepServices.collaboratorRegular AS `prepRegular`, mscPrepServices.collaboratorDiscount AS `prepDiscount`, mscPrepServices.collaboratorCutoff AS `prepCutoff`
				FROM mscServicesSelected 
				INNER JOIN mscAnalysisServices
				ON mscServicesSelected.serviceId = mscAnalysisServices.id
				LEFT JOIN mscPrepServices
				ON mscPrepServices.id = mscAnalysisServices.samplePrepId
				WHERE mscServicesSelected.id=:sid');
			}else if($accountTypeId == 3){
				$query = $this->db_connection->prepare('
				SELECT 
				mscServicesSelected.requestId, mscServicesSelected.samples, mscServicesSelected.replicates, mscServicesSelected.prep, mscServicesSelected.createdAt,
				mscAnalysisServices.umbRegular AS `UMB_Regular`,
				mscAnalysisServices.name AS `serviceName`, mscAnalysisServices.affiliateRegular AS `regular`, mscAnalysisServices.affiliateDiscount AS `discount`, mscAnalysisServices.affiliateCutoff AS `cutoff`,
				mscPrepServices.umbRegular AS `UMB_Prep_Regular`,
				mscPrepServices.affiliateRegular AS `prepRegular`, mscPrepServices.affiliateDiscount AS `prepDiscount`, mscPrepServices.affiliateCutoff AS `prepCutoff`
				FROM mscServicesSelected 
				INNER JOIN mscAnalysisServices
				ON mscServicesSelected.serviceId = mscAnalysisServices.id
				LEFT JOIN mscPrepServices
				ON mscPrepServices.id = mscAnalysisServices.samplePrepId
				WHERE mscServicesSelected.id=:sid');
			}else if($accountTypeId == 4){
				$query = $this->db_connection->prepare('
				SELECT 
				mscServicesSelected.requestId, mscServicesSelected.samples, mscServicesSelected.replicates, mscServicesSelected.prep, mscServicesSelected.createdAt,
				mscAnalysisServices.umbRegular AS `UMB_Regular`,
				mscAnalysisServices.name AS `serviceName`, mscAnalysisServices.umbRegular AS `regular`, mscAnalysisServices.umbDiscount AS `discount`, mscAnalysisServices.umbCutoff AS `cutoff`,
				mscPrepServices.umbRegular AS `UMB_Prep_Regular`,
				mscPrepServices.umbRegular AS `prepRegular`, mscPrepServices.umbDiscount AS `prepDiscount`, mscPrepServices.umbCutoff AS `prepCutoff`
				FROM mscServicesSelected 
				INNER JOIN mscAnalysisServices
				ON mscServicesSelected.serviceId = mscAnalysisServices.id
				LEFT JOIN mscPrepServices
				ON mscPrepServices.id = mscAnalysisServices.samplePrepId
				WHERE mscServicesSelected.id=:sid');
			}else if($accountTypeId == 5){
				$query = $this->db_connection->prepare('
				SELECT 
				mscServicesSelected.requestId, mscServicesSelected.samples, mscServicesSelected.replicates, mscServicesSelected.prep, mscServicesSelected.createdAt,
				mscAnalysisServices.umbRegular AS `UMB_Regular`,
				mscAnalysisServices.name AS `serviceName`, mscAnalysisServices.nonProfitRegular AS `regular`, mscAnalysisServices.nonProfitDiscount AS `discount`, mscAnalysisServices.nonProfitCutoff AS `cutoff`,
				mscPrepServices.umbRegular AS `UMB_Prep_Regular`,
				mscPrepServices.nonProfitRegular AS `prepRegular`, mscPrepServices.nonProfitDiscount AS `prepDiscount`, mscPrepServices.nonProfitCutoff AS `prepCutoff`
				FROM mscServicesSelected 
				INNER JOIN mscAnalysisServices
				ON mscServicesSelected.serviceId = mscAnalysisServices.id
				LEFT JOIN mscPrepServices
				ON mscPrepServices.id = mscAnalysisServices.samplePrepId
				WHERE mscServicesSelected.id=:sid');
			}else if($accountTypeId == 6){
				$query = $this->db_connection->prepare('
				SELECT 
				mscServicesSelected.requestId, mscServicesSelected.samples, mscServicesSelected.replicates, mscServicesSelected.prep, mscServicesSelected.createdAt,
				mscAnalysisServices.umbRegular AS `UMB_Regular`,
				mscAnalysisServices.name AS `serviceName`, mscAnalysisServices.forProfitRegular AS `regular`, mscAnalysisServices.forProfitDiscount AS `discount`, mscAnalysisServices.forProfitCutoff AS `cutoff`,
				mscPrepServices.umbRegular AS `UMB_Prep_Regular`,
				mscPrepServices.forProfitRegular AS `prepRegular`, mscPrepServices.forProfitDiscount AS `prepDiscount`, mscPrepServices.forProfitCutoff AS `prepCutoff`
				FROM mscServicesSelected 
				INNER JOIN mscAnalysisServices
				ON mscServicesSelected.serviceId = mscAnalysisServices.id
				LEFT JOIN mscPrepServices
				ON mscPrepServices.id = mscAnalysisServices.samplePrepId
				WHERE mscServicesSelected.id=:sid');
			}
	        $query->bindValue(':sid', $sid, PDO::PARAM_INT);
	        $query->execute();
	        return $query->fetch(PDO::FETCH_ASSOC);
    	}
    }

    public function getAccountClass($accountType){
    	if ($this->databaseConnection()) {
			$query = $this->db_connection->prepare('SELECT class FROM accountTypes WHERE id=:accountTypeId');
			$query->bindValue(':accountTypeId', $accountType, PDO::PARAM_STR);
			$query->execute();
			return $query->fetch(PDO::FETCH_ASSOC);
		}
    }

    // This function will always returnt the high and low accuracy rates for the UMB user.
    public function getBaseRate(){
    	if ($this->databaseConnection()) {
	    	$qrate = $this->db_connection->prepare('SELECT highAccuracyRate, lowAccuracyRate FROM bookingRatesExternal WHERE accountTypeId=4');
			$qrate->execute();
			return $qrate->fetch(PDO::FETCH_ASSOC);
		}
    }

}

?>