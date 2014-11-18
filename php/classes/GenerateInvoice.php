<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require_once("PHPExcel.php");

class GenerateInvoice
{
	
    private $db_connection = null;    // database connection   

	public function __construct()
    {
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
                return true;

            // If an error is catched, database connection failed
            } catch (PDOException $e) {
                $this->errors[] = "Database connection problem.";
                return false;
            }
        }
    }
    
    public function generateInvoice($json_string){
    
    	$json = json_decode($json_string, true);
    	/* 
    	 * Keep track of the CELL ROW
    	 * We are starging at row 17 for new Items
    	 * Row 16 will be the first header line (gray background)
    	 */
    	$r = 17;
    	
    	// Also, we need to keep a running total for the invoice later
    	$running_total = 0;
    	
    	/*
    	 * Style definitions
    	 *
    	 */
    	$border = array('borders' => array(
	      	'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
		  	'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
	        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
	      )
	    );
	    
	    $topBorder = array('borders' => array(
	        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
	      )
	    );
	    
	    $bottomBorder = array('borders' => array(
	        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
	      )
	    );
	    
	    $grayFill = array('fill' => array(
	        'type' => PHPExcel_Style_Fill::FILL_SOLID,
	        'color' => array('rgb' => 'C0C0C0')
	        )
	    );
	    
	    $font = array('font'  => array(
	        'size'  => 12,
	        'name'  => 'Arial'
			)
	    );
	    
	    if ($this->databaseConnection()) {

			/*
			 *  First we get the user/project information. 
			 *  Some of this information will be added starting at row 8
			 *  ROW #8 COL A = User/PI/Full Address
			 *  ROW #8 COL E/F = Invoice #, PO#, Dates
			 */
			 
			// Create a sheet with which to work
			$phpExcel = new PHPExcel();
			$sheet = $phpExcel->getActiveSheet();
			
			$sheet->getDefaultStyle()->getFont()->setName('Arial');
			$sheet->getDefaultStyle()->getFont()->setSize(12);
			
			// Get the user info
			$query = $this->db_connection->prepare('
				SELECT 
				users.first, users.last, users.email, users.institution,
				accountTypes.shortName AS `accountType`, accountTypes.id AS `accountTypeId`
				FROM users 
				INNER JOIN accountTypes
				ON users.accountType = accountTypes.id
				WHERE users.id=:userId');
			$query->bindValue(':userId', $json['userId'], PDO::PARAM_INT);
		    $query->execute();
		    $user = $query->fetch(PDO::FETCH_ASSOC);
		    
		    // Set the booking rate database name based on accountType
		    $bookingRatesDatabase = '';
		    if($user['accountTypeId'] == 1 || $user['accountTypeId'] == 3){
			    $bookingRatesDatabase = 'bookingRatesInternal';
			    $internal = true;
		    }else{
			    $bookingRatesDatabase = 'bookingRatesExternal';
			    $internal = false;
		    }
		    		        
		    // Get the project info
			$query = $this->db_connection->prepare('
				SELECT 
				projects.title, projects.primaryInvestigator, projects.addressOne, projects.addressTwo, projects.city, projects.state, projects.zip, projects.phone,
				paymentInfo.purchaseOrder, paymentInfo.projectCostingBusinessUnit, paymentInfo.projectId, paymentInfo.departmentId
				FROM projects
				INNER JOIN paymentInfo
				ON projects.paymentId = paymentInfo.id
				WHERE projects.id = :projectId
			');
			$query->bindValue(':projectId', $json['projectId'], PDO::PARAM_INT);
			$query->execute();
		    $project = $query->fetch(PDO::FETCH_ASSOC);

			// IF there are bookings to add, fetch and add them 			
        	if(array_key_exists('bookings', $json)){
        		foreach($json['bookings'] as $bid){
        		
        			if($internal){
        				$query = $this->db_connection->prepare("
	        			SELECT 
	        			instrumentBookings.instrumentId, instrumentBookings.dateFrom, instrumentBookings.dateTo, instrumentBookings.timeFrom, instrumentBookings.timeTo, 
	        			mscInstruments.name AS `instrumentName`, mscInstruments.model AS `instrumentModel`, mscInstruments.accuracy,
	        			bookingRatesInternal.*
	        			FROM instrumentBookings 
	        			INNER JOIN mscInstruments
						ON instrumentBookings.instrumentId = mscInstruments.id
						INNER JOIN bookingRatesInternal
						ON bookingRatesInternal.accountTypeId = :accountTypeId
	        			WHERE instrumentBookings.id=:bid");

        			}else{
	        			$query = $this->db_connection->prepare("
	        			SELECT 
	        			instrumentBookings.instrumentId, instrumentBookings.dateFrom, instrumentBookings.dateTo, instrumentBookings.timeFrom, instrumentBookings.timeTo, 
	        			mscInstruments.name AS `instrumentName`, mscInstruments.model AS `instrumentModel`, mscInstruments.accuracy,
	        			bookingRatesExternal.*
	        			FROM instrumentBookings 
	        			INNER JOIN mscInstruments
						ON instrumentBookings.instrumentId = mscInstruments.id
						INNER JOIN bookingRatesExternal
						ON bookingRatesExternal.accountTypeId = :accountTypeId
	        			WHERE instrumentBookings.id=:bid");
        			}
	        		
	        		$query->bindValue(':bid', $bid, PDO::PARAM_INT);
	        		$query->bindValue(':accountTypeId', $user['accountTypeId'], PDO::PARAM_INT);
					$query->execute();
					$booking = $query->fetch(PDO::FETCH_ASSOC);

					// Calculate Hours
					$hours = (strtotime($booking['dateTo'] . ' ' . $booking['timeTo']) - strtotime($booking['dateFrom'] . ' ' . $booking['timeFrom'])) / 3600;
					
					// ADD THIS BOOKING
					$sheet->setCellValue('A'.$r, 'Booking' . '(' . $bid . ')');
					$sheet->setCellValue('B'.$r, $user['accountType']);
					$sheet->setCellValue('C'.$r, $booking['instrumentName']);
					if($internal){
						$sheet->setCellValue('D'.$r, $booking['oneHour']);
						$thisTotal = $this->bookingInternalCalculator($hours, $booking['oneHour'], $booking['fourHours'], $booking['eightHours'], $booking['sixteenHours'], $booking['twentyFourHours']);
						$running_total += $thisTotal;
						$sheet->setCellValue('F'.$r, $thisTotal);
					}else{
						if($booking['accuracy'] == 'high'){
							$sheet->setCellValue('D'.$r, $booking['highAccuracyRate']);
							$thisTotal = $this->bookingExternalCalculator($hours, $booking['highAccuracyRate']);
							$running_total += $thisTotal;
							$sheet->setCellValue('F'.$r, $thisTotal);
						}else{
							$sheet->setCellValue('D'.$r, $booking['lowAccuracyRate']);
							$thisTotal = $this->bookingExternalCalculator($hours, $booking['lowAccuracyRate']);
							$running_total += $thisTotal;
							$sheet->setCellValue('F'.$r, $thisTotal);
						}
					}
					$sheet->setCellValue('E'.$r, $hours . ' hr(s)');
					$sheet->getStyle('E'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sheet->getStyle('F'.$r)->getNumberFormat()->setFormatCode("$#,###.00");
					$r++;
				}
			}
			
			// IF there are serviceRequests to add, fetch and add them
			if(array_key_exists('serviceRequests', $json)){
				foreach($json['serviceRequests'] as $sid){
				
					if($user['accountTypeId'] == 1){
						$query = $this->db_connection->prepare('
						SELECT 
						mscServicesSelected.requestId, mscServicesSelected.samples, mscServicesSelected.replicates, mscServicesSelected.prep,
						mscAnalysisServices.name AS `serviceName`, mscAnalysisServices.memberRegular AS `regular`, mscAnalysisServices.memberDiscount AS `discount`, mscAnalysisServices.memberCutoff AS `cutoff`,
						mscPrepServices.memberRegular AS `prepRegular`, mscPrepServices.memberDiscount AS `prepDiscount`, mscPrepServices.memberCutoff AS `prepCutoff`
						FROM mscServicesSelected 
						INNER JOIN mscAnalysisServices
						ON mscServicesSelected.serviceId = mscAnalysisServices.id
						LEFT JOIN mscPrepServices
						ON mscPrepServices.id = mscAnalysisServices.samplePrepId
						WHERE mscServicesSelected.id=:sid');
					}else if($user['accountTypeId'] == 2){
						$query = $this->db_connection->prepare('
						SELECT 
						mscServicesSelected.requestId, mscServicesSelected.samples, mscServicesSelected.replicates, mscServicesSelected.prep,
						mscAnalysisServices.name AS `serviceName`, mscAnalysisServices.memberRegular AS `regular`, mscAnalysisServices.memberDiscount AS `discount`, mscAnalysisServices.memberCutoff AS `cutoff`,
						mscPrepServices.memberRegular AS `prepRegular`, mscPrepServices.memberDiscount AS `prepDiscount`, mscPrepServices.memberCutoff AS `prepCutoff`
						FROM mscServicesSelected 
						INNER JOIN mscAnalysisServices
						ON mscServicesSelected.serviceId = mscAnalysisServices.id
						LEFT JOIN mscPrepServices
						ON mscPrepServices.id = mscAnalysisServices.samplePrepId
						WHERE mscServicesSelected.id=:sid');
					}else if($user['accountTypeId'] == 3){
						$query = $this->db_connection->prepare('
						SELECT 
						mscServicesSelected.requestId, mscServicesSelected.samples, mscServicesSelected.replicates, mscServicesSelected.prep,
						mscAnalysisServices.name AS `serviceName`, mscAnalysisServices.memberRegular AS `regular`, mscAnalysisServices.memberDiscount AS `discount`, mscAnalysisServices.memberCutoff AS `cutoff`,
						mscPrepServices.memberRegular AS `prepRegular`, mscPrepServices.memberDiscount AS `prepDiscount`, mscPrepServices.memberCutoff AS `prepCutoff`
						FROM mscServicesSelected 
						INNER JOIN mscAnalysisServices
						ON mscServicesSelected.serviceId = mscAnalysisServices.id
						LEFT JOIN mscPrepServices
						ON mscPrepServices.id = mscAnalysisServices.samplePrepId
						WHERE mscServicesSelected.id=:sid');
					}else if($user['accountTypeId'] == 4){
						$query = $this->db_connection->prepare('
						SELECT 
						mscServicesSelected.requestId, mscServicesSelected.samples, mscServicesSelected.replicates, mscServicesSelected.prep,
						mscAnalysisServices.name AS `serviceName`, mscAnalysisServices.memberRegular AS `regular`, mscAnalysisServices.memberDiscount AS `discount`, mscAnalysisServices.memberCutoff AS `cutoff`,
						mscPrepServices.memberRegular AS `prepRegular`, mscPrepServices.memberDiscount AS `prepDiscount`, mscPrepServices.memberCutoff AS `prepCutoff`
						FROM mscServicesSelected 
						INNER JOIN mscAnalysisServices
						ON mscServicesSelected.serviceId = mscAnalysisServices.id
						LEFT JOIN mscPrepServices
						ON mscPrepServices.id = mscAnalysisServices.samplePrepId
						WHERE mscServicesSelected.id=:sid');
					}else if($user['accountTypeId'] == 5){
						$query = $this->db_connection->prepare('
						SELECT 
						mscServicesSelected.requestId, mscServicesSelected.samples, mscServicesSelected.replicates, mscServicesSelected.prep,
						mscAnalysisServices.name AS `serviceName`, mscAnalysisServices.memberRegular AS `regular`, mscAnalysisServices.memberDiscount AS `discount`, mscAnalysisServices.memberCutoff AS `cutoff`,
						mscPrepServices.memberRegular AS `prepRegular`, mscPrepServices.memberDiscount AS `prepDiscount`, mscPrepServices.memberCutoff AS `prepCutoff`
						FROM mscServicesSelected 
						INNER JOIN mscAnalysisServices
						ON mscServicesSelected.serviceId = mscAnalysisServices.id
						LEFT JOIN mscPrepServices
						ON mscPrepServices.id = mscAnalysisServices.samplePrepId
						WHERE mscServicesSelected.id=:sid');
					}else if($user['accountTypeId'] == 6){
						$query = $this->db_connection->prepare('
						SELECT 
						mscServicesSelected.requestId, mscServicesSelected.samples, mscServicesSelected.replicates, mscServicesSelected.prep,
						mscAnalysisServices.name AS `serviceName`, mscAnalysisServices.memberRegular AS `regular`, mscAnalysisServices.memberDiscount AS `discount`, mscAnalysisServices.memberCutoff AS `cutoff`,
						mscPrepServices.memberRegular AS `prepRegular`, mscPrepServices.memberDiscount AS `prepDiscount`, mscPrepServices.memberCutoff AS `prepCutoff`
						FROM mscServicesSelected 
						INNER JOIN mscAnalysisServices
						ON mscServicesSelected.serviceId = mscAnalysisServices.id
						LEFT JOIN mscPrepServices
						ON mscPrepServices.id = mscAnalysisServices.samplePrepId
						WHERE mscServicesSelected.id=:sid');
					}
			        $query->bindValue(':sid', $sid, PDO::PARAM_INT);
			        $query->execute();
			        $serviceRequest = $query->fetch(PDO::FETCH_ASSOC);
			        
			        // ADD THIS SERVICE
					$sheet->setCellValue('A'.$r, 'Service' . '(' . $sid . ')');
					$sheet->setCellValue('B'.$r, $user['accountType']);
					$sheet->setCellValue('C'.$r, $serviceRequest['serviceName']);
					$sheet->setCellValue('D'.$r, $serviceRequest['regular']);
					$sheet->setCellValue('E'.$r, $serviceRequest['samples'] . 'S / ' . $serviceRequest['replicates'] . 'R');
					$sheet->getStyle('E'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$thisTotal = $this->serviceRequestCalculator($serviceRequest['samples'], $serviceRequest['replicates'], $serviceRequest['prep'], $serviceRequest['regular'], $serviceRequest['discount'], $serviceRequest['cutoff'], $serviceRequest['prepRegular'], $serviceRequest['prepDiscount'], $serviceRequest['prepCutoff']);
					$running_total += $thisTotal;
					$sheet->setCellValue('F'.$r, $thisTotal);
					$sheet->getStyle('F'.$r)->getNumberFormat()->setFormatCode("$#,###.00");
					$r++;
		        }
	        }
	        
	        // IF there are training sessions to add, fetch and add them
	        if(array_key_exists('trainings', $json)){
	        	foreach($json['trainings'] as $tid){
	        	
	        		if($internal){
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
					}else{
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
					}
						
			        $query->bindValue(':tid', $tid, PDO::PARAM_INT);
			        $query->bindValue(':accountTypeId', $user['accountTypeId'], PDO::PARAM_INT);
			        $query->execute();
			        $training = $query->fetch(PDO::FETCH_ASSOC);
			        // ADD THIS TRAINING
			        // Calculate Hours
					$hours = (strtotime($training['dateTo'] . ' ' . $training['timeTo']) - strtotime($training['dateFrom'] . ' ' . $training['timeFrom'])) / 3600;
					$sheet->setCellValue('A'.$r, 'Training' . '(' . $tid . ')');
					$sheet->setCellValue('B'.$r, $user['accountType']);
					$sheet->setCellValue('C'.$r, $training['instrumentName']);
					$sheet->setCellValue('D'.$r, $training['staffRate']);
					$sheet->setCellValue('E'.$r, $hours . 'hr(s)');
					$sheet->getStyle('E'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$thisTotal = $this->trainingCalculator($hours, $training['staffRate']);
					$running_total += $thisTotal;
					$sheet->setCellValue('F'.$r, $thisTotal);
					$sheet->getStyle('F'.$r)->getNumberFormat()->setFormatCode("$#,###.00");
					$r++;
		        }
	        }
	        
	        // Now Add some space after the last item is entered 
	        $r = $r + 5;
	        
	        
	        /*
	         *  BUILD THE EXCEL FILE HERE
	         */
			 
			$sheet->setCellValue("A1", "Mass Spectrometry Center");
			$sheet->setCellValue("A2", "Pharmaceutical Sciences");								//
			$sheet->setCellValue("A3", "20 N. Pine Street");									// Return address
			$sheet->setCellValue("A4", "Room N719");											//  ""
			$sheet->setCellValue("A5", "Baltimore, MD 21201");									//  ""
			
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setWorksheet($sheet);
			$objDrawing->setName("Logo");														//
			$objDrawing->setDescription("UMB Logo");											// University of Maryland, Baltimore
			$objDrawing->setPath('../../images/umbmsclogo.png');								// Mass Spectrometry Center
			$objDrawing->setCoordinates('D1');													// Logo
			$objDrawing->setResizeProportional(true);											//
			$objDrawing->setWidth(381);
			
			$sheet->setCellValue("A8", $project['primaryInvestigator']);
			$sheet->setCellValue("A9", 'CC: ' . $user['first'] . ' ' . $user['last']);
		    $sheet->setCellValue("A10", $user['institution']);
		    $sheet->setCellValue("A11", $project['addressOne']);
		    $sheet->setCellValue("A12", $project['addressTwo']);
		    $sheet->setCellValue("A13", $project['city'] . ' ' . $project['state'] . ' ' . $project['zip']);
		    
		    $sheet->setCellValue("F8", 'INVOICE');
		    
		    $sheet->setCellValue("E9", 'Invoice #');
		    // Actual invoice number gets set later
		    
		    $sheet->setCellValue("E10", 'P.O.#');
		    if($project['purchaseOrder'] != ''){$sheet->setCellValue("F10", $project['purchaseOrder']);}else{$sheet->setCellValue("F10", 'Use Chart String');}
		    
		    $sheet->setCellValue("E11", 'Invoice Date');
		    $sheet->setCellValue("F11", date('m-d-Y'));
		    
		    $sheet->setCellValue("E12", 'Due Date');
		    $sheet->setCellValue("F12", date('m-d-Y', strtotime("+30 days")));
		    
		    $sheet->setCellValue("A16", 'Item');
		    $sheet->setCellValue("B16", 'Rate');
		    $sheet->setCellValue("C16", 'Description');
			$sheet->setCellValue("D16", 'Unit Price');
			$sheet->setCellValue("E16", 'Quantity');
		    $sheet->setCellValue("F16", 'Amount');

			$sheet->setCellValue("A".($r-2), 'PCBU:');
			if($project['projectCostingBusinessUnit'] != ''){
				$sheet->setCellValue("B".($r-2), $project['projectCostingBusinessUnit']);
			}else{
				$sheet->setCellValue("B".($r-2), 'n/a');
			}
		    
		    $sheet->setCellValue("A".($r-1), 'Project ID:');
		    if($project['projectId'] != ''){
				$sheet->setCellValue("B".($r-1), $project['projectId']);
			}else{
				$sheet->setCellValue("B".($r-1), 'n/a');
			}
		    
		    $sheet->setCellValue("A".$r, 'Dept ID:');
		    if($project['departmentId'] != ''){
				$sheet->setCellValue("B".$r, $project['departmentId']);
			}else{
				$sheet->setCellValue("B".$r, 'n/a');
			}
			
			$sheet->setCellValue("D".($r-3), 'Subtotal');
			$sheet->setCellValue("D".($r-2), 'Total');
			$sheet->setCellValue("D".($r-1), 'Amount Paid');
			$sheet->setCellValue("D".$r, 'Balance Due (USD)');
			
			$sheet->setCellValue('F'.($r-3), '=SUM(F17:F'.($r-6).')');
			$sheet->setCellValue('F'.($r-2), '=SUM(F17:F'.($r-6).')');
			$sheet->setCellValue('F'.$r, '=(F'.($r-2).'-F'.($r-1).')');
			
			$sheet->getStyle('F'.($r-3).':F'.($r))->getNumberFormat()->setFormatCode("$#,###.00");
			
			$sheet->setCellValue("A".($r+2), $project['title'] . ' (' . $json['projectId'] . ')');
			$sheet->setCellValue("A".($r+3), $user['first'] . ' ' . $user['last'] . ' (' . $json['userId'] . ')');
		    
			/*
			 * Style the sheet
			 */
			
		    $sheet->getStyle('A1:A5')->getFont()->setBold(true); 								// BOLD
		    $sheet->getStyle('A8')->getFont()->setBold(true);
		    $sheet->getStyle('A16:F16')->getFont()->setBold(true);
		    $sheet->getStyle('E9:E13')->getFont()->setBold(true);
		    $sheet->getStyle('F8')->getFont()->setBold(true);
		    $sheet->getStyle('D34')->getFont()->setBold(true);
		    $sheet->getStyle('E34')->getFont()->setBold(true);
		    
			$sheet->getStyle('E9:F13')->applyFromArray($border);								// BORDERS
			$sheet->getStyle('A16:F'.$r)->applyFromArray($border);
			$sheet->getStyle('D'.($r-3).':F'.$r)->applyFromArray($border);
			
			$sheet->getStyle('A16:F16')->applyFromArray($topBorder);
			$sheet->getStyle('A'.($r-3).':C'.($r-3))->applyFromArray($topBorder);
			
			$sheet->getStyle('A16:F16')->applyFromArray($bottomBorder);
			$sheet->getStyle('D'.($r-3).':F'.($r-3))->applyFromArray($bottomBorder);
			
			$sheet->getStyle('F9')->getNumberFormat()->setFormatCode('000000');
			
			$sheet->getColumnDimension('E')->setAutoSize(true);
			$sheet->getColumnDimension('F')->setAutoSize(true);
			$sheet->getColumnDimension('A')->setWidth(11);
			$sheet->getColumnDimension('C')->setWidth(26);
			
			$sheet->getStyle('A16:F16')->applyFromArray($grayFill);								// FILL COLOR
			$sheet->getStyle('D'.$r.':F'.$r)->applyFromArray($grayFill);
			
			$sheet->getStyle('F9:F13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
			$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
			$sheet->getPageSetup()->setFitToPage(true);
			$sheet->getPageSetup()->setFitToWidth(1);
			$sheet->getPageSetup()->setFitToHeight(0);
			
			
			/*
			 *
			 *  Now we need to record the selected bookings, service requests and trainings as invoiced.
			 *  We also need to create a record with these selections and retrieve that record ID to use as the invoice #
			 *  To do this, we will use a transaction because we only want to commit if all inserts/updates succeed.
			 *
			 */
			try{
			
				$this->db_connection->beginTransaction();
				
				// First, update all the bookings
				if(array_key_exists('bookings', $json)){
		        	foreach($json['bookings'] as $bid){
		        		$query = $this->db_connection->prepare('UPDATE instrumentBookings SET invoiced=1 WHERE id=:id');
		        		$query->bindValue(':id', $bid, PDO::PARAM_INT);
				        $query->execute();
		        	}
	        	}
				// Second, update all the services requests
				if(array_key_exists('serviceRequests', $json)){
		        	foreach($json['serviceRequests'] as $sid){
		        		$query = $this->db_connection->prepare('UPDATE mscServicesSelected SET invoiced=1 WHERE id=:id');
		        		$query->bindValue(':id', $sid, PDO::PARAM_INT);
				        $query->execute();
		        	}
	        	}
				// Third, update all the training bookings
				if(array_key_exists('trainings', $json)){
		        	foreach($json['trainings'] as $tid){
		        		$query = $this->db_connection->prepare('UPDATE trainingBookings SET invoiced=1 WHERE id=:id');
		        		$query->bindValue(':id', $tid, PDO::PARAM_INT);
				        $query->execute();
		        	}
	        	}
	        	
	        	$query = $this->db_connection->prepare('INSERT INTO invoices 
	        	(userId, projectId, primaryInvestigator, addressOne, addressTwo, city, state, zip, pcbu, pid, did, po, jsonString, total) 
	        	VALUES 
	        	(:userId, :projectId, :primaryInvestigator, :addressOne, :addressTwo, :city, :state, :zip, :pcbu, :pid, :did, :po, :jsonString, :total)');
	        	
        		$query->bindValue(':userId', $json['userId'], PDO::PARAM_INT);
		        $query->bindValue(':projectId', $json['projectId'], PDO::PARAM_INT);
		        $query->bindValue(':primaryInvestigator', $project['primaryInvestigator'], PDO::PARAM_STR);
		        $query->bindValue(':addressOne', $project['addressOne'], PDO::PARAM_STR);
		        $query->bindValue(':addressTwo', $project['addressTwo'], PDO::PARAM_STR);
		        $query->bindValue(':city', $project['city'], PDO::PARAM_STR);
		        $query->bindValue(':state', $project['state'], PDO::PARAM_STR);
		        $query->bindValue(':zip', $project['zip'], PDO::PARAM_INT);
		        $query->bindValue(':pcbu', $project['projectCostingBusinessUnit'], PDO::PARAM_STR);
		        $query->bindValue(':pid', $project['projectId'], PDO::PARAM_STR);
		        $query->bindValue(':did', $project['departmentId'], PDO::PARAM_STR);
		        $query->bindValue(':po', $project['purchaseOrder'], PDO::PARAM_STR);
		        $query->bindValue(':jsonString', json_encode($json), PDO::PARAM_STR);
		        $query->bindValue(':total', $running_total, PDO::PARAM_STR);
		        $query->execute();
	        	
				// Finally, insert this info and retrieve the invoice number
				$sheet->setCellValue("F9", $this->db_connection->lastInsertId());
				// If we haven't crached yet, we are ready to return the file for download
				$this->db_connection->commit();
				
				/*
				 *	Save the sheet for download via ajax
				 */
				$phpExcel->setActiveSheetIndex(0);													
				$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
				$objWriter->save('../../tmp/invoice.xls');
				return true;
				
			}catch(Exception $ex){
				$this->db_connection->rollBack();
				return false;
			}

	    }
	    
    }
    
    private function bookingInternalCalculator($hours, $one, $four, $eight, $sixteen, $twentyFour){
    	$total = 0;
    	while($hours > 0){
					
			if($hours >= 24){
				$total += $twentyFour;
				$hours -= 24;
			}else if($hours >= 16){
				$total += $sixteen;
				$hours -= 16;
			}else if($hours >= 8){
				$total += $eight;
				$hours -= 8;
			}else if($hours >= 4){
				$total += $four;
				$hours -= 4;
			}else{
				$total += ($hours * $one);
				$hours -= $hours;
			}
			
		}
	    return $total;
    }
    
    private function bookingExternalCalculator($hours, $rate){
	    return $hours * $rate;
    }  
    
    private function serviceRequestCalculator($samples, $replicates, $prep, $reg, $disc, $cut, $pReg, $pDisc, $pCut){
		$total = 0;
		$samples = $samples * $replicates;

		if($prep){
			$pSamples = $samples;
			if($pSamples >= $pCut){
	    		$total = $total + $pReg * $pCut;
	    		$pSamples = $pSamples - $pCut;
	    		$total = $total + $pDisc * $pSamples;
	    	}else{
		    	$total = $total + $pReg * $pSamples;
	    	} 
		}

		if($samples >= $cut){
    		$total = $total + $reg * $cut;
    		$samples = $samples - $cut;
    		$total = $total + $disc * $samples;
    	}else{
	    	$total = $total + $reg * $samples;
    	}    
    	
	    return $total;
    }
    
    private function trainingCalculator($hours, $rate){
	    return $hours * $rate;
    }
    
}
?>