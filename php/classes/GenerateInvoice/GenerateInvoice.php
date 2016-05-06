<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require_once("PHPExcel.php");
require_once("CostCalculator.php");
require_once("Queries.php");

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

    	$costCalculator = new CostCalculator();
    	$queries = new Queries();

    	$json = json_decode($json_string, true);
    	/*
    	 * Keep track of the CELL ROW
    	 * We are starging at row 17 for new Items
    	 * Row 16 will be the first header line (gray background)
    	 */
    	$r = 17;

    	$TotalRegularSubsity = 0;
    	$TotalProgressiveSubsidy = 0;

    	$TotalCharges = 0;
    	$NetDueFromPI = 0;

    	$HourlySubsity = 0;

    	$TotalHours = 0;
    	$unitPrice = 0;

    	$hasBooking=FALSE;
    	$hasTraining=FALSE;
    	$hasService=FALSE;

    	$redTextFields = array();

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

	    $borderMedium = array('borders' => array(
	      	'left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
		  	'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
			'top' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
	        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
	      )
	    );

		$allBordersMedium = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_MEDIUM
				)
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
			$phpExcel->getActiveSheet()->setTitle("Invoice");

			//$sheet2 = $phpExcel->setActiveSheetIndex(2);

			// Create an array to hold volume discount data
			$volumeDiscountSamples = array();
			$volumeDiscountPrep = array();

			$sheet->getDefaultStyle()->getFont()->setName('Arial');
			$sheet->getDefaultStyle()->getFont()->setSize(12);

			// Get the users profile
			$user = $queries->getUserInfo($json['userId']);

		    // Get the users class (internal or external)
		    $class = $queries->getAccountClass($user['accountTypeId']);
		    $class =  $class['class'];

		    // Get the project info
			$project = $queries->getProjectInfo($json['projectId']);

			// IF there are bookings to add, fetch and add them
        	if(array_key_exists('bookings', $json)){

        		foreach($json['bookings'] as $bid){

        			// Get this booking and associated data based on user type. The user type is determined by the CODE.
        			//echo 'Booking ID: ' . $bid . ' AccountTypeID: ' . $user['accountTypeId'] . '   ';
        			$booking;
        			if($class == "INT"){
        				$booking = $queries->getBookingInternal($bid, $user['accountTypeId']);
        			}else{
	        			$booking = $queries->getBookingExternal($bid, $user['accountTypeId']);
        			}

	        		// Get the base rate so we can calculate the subsidies
					$baseRates = $queries->getBaseRate();

					// How many hours for this bookings?
					$hours = (strtotime($booking['dateTo'] . ' ' . $booking['timeTo']) - strtotime($booking['dateFrom'] . ' ' . $booking['timeFrom'])) / 3600;

					/*
					 *	Add this booking to the invoice
					 *
					 */
					$sheet->setCellValue('A'.$r, "Booking" . "(" . $bid . ")");	// Booking ID
					$sheet->setCellValue('B'.$r, $user['accountType']);			// Rate CODE
					$sheet->setCellValue('C'.$r, $booking['instrumentName']);	// Instrument Name
					// Add the range booked
					if($booking['dateFrom'] == $booking['dateTo']){
						$sheet->setCellValue('D'.$r, $booking['dateFrom'].":".$booking['timeFrom'].' to '.$booking['timeTo']);
					}else{
						$sheet->setCellValue('D'.$r, $booking['dateFrom'] .':'.$booking['timeFrom']. ' to ' . $booking['dateTo'].':'.$booking['timeTo']);
					}

					if($class == "INT"){

						// unitPrice == UMB RATE FOR EXTERNAL USERS
						// Calculate the Internal Subsity Rate (IST) -> UMB Base Rate (high or low) - hourly (for internal user) * hours
						if($booking['accuracy'] == 'high'){
							$unitPrice = $baseRates['highAccuracyRate'];
						}else{
							$unitPrice = $baseRates['lowAccuracyRate'];
						}

						$sheet->setCellValue('E'.$r, $unitPrice);
						$thisNetDue = $costCalculator->bookingInternalCalculator($hours, $booking['oneHour'], $booking['fourHours'], $booking['eightHours'], $booking['sixteenHours'], $booking['twentyFourHours']);
						$NetDueFromPI += $thisNetDue;

						// REMOVE THESE LINES LATER
						//$note = "Regular Subsidy: (". $unitPrice . '*' . $hours . ')-(' . $booking['oneHour'] . '*' . $hours . ') eq: ' . (($unitPrice*$hours)-($booking['oneHour']*$hours));
						//$sheet->setCellValue("L".$r, $thisTotal);
						//$sheet->setCellValue("H".$r, $note);
						// END REMOVE LINES

						// Calculate and add the non-subsidy cost for this booking
						$thisTotalCharges = ($unitPrice * $hours);
						$TotalCharges += $thisTotalCharges;

						$HourlySubsity = $unitPrice-$booking['oneHour'];

						// Add this cost to the invoice
						$sheet->setCellValue("G".$r, $thisTotalCharges);

						// Add hours for this entry to the running total
						$TotalHours += $hours;

						// Calculate the Regular Subsidy for THIS entry and add it to the TotalRegularSubsity
						$TotalRegularSubsity += (($unitPrice*$hours)-($booking['oneHour']*$hours));

					}else{
						if($booking['accuracy'] == 'high'){
							$sheet->setCellValue('E'.$r, $baseRates['highAccuracyRate']);
							$thisNetDue = $costCalculator->bookingExternalCalculator($hours, $baseRates['highAccuracyRate']);
							$NetDueFromPI += $thisNetDue;
							$TotalCharges += $thisNetDue;
							$sheet->setCellValue('G'.$r, $thisNetDue);
						}else{
							$sheet->setCellValue('E'.$r, $baseRates['lowAccuracyRate']);
							$thisNetDue = $costCalculator->bookingExternalCalculator($hours, $baseRates['lowAccuracyRate']);
							$NetDueFromPI += $thisNetDue;
							$TotalCharges += $thisNetDue;
							$sheet->setCellValue('G'.$r, $thisNetDue);
						}
						$TotalHours += $hours;
					}
					$sheet->setCellValue('F'.$r, $hours . ' hr(s)');
					$sheet->getStyle('F'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$sheet->getStyle('G'.$r)->getNumberFormat()->setFormatCode("$#,###.00");
					$r++;

					$hasBooking=TRUE;
				}

			}

			// IF there are serviceRequests to add, fetch and add them
			if(array_key_exists('serviceRequests', $json)){

				// Create an array to hold the volume discount data
				$volumeDiscountSamples = array();
				$volumeDiscountPrep = array();

				$totalServiceSampleSubsidy = array();
    			$totalServicePrepSubsidy = array();

				foreach($json['serviceRequests'] as $sid){

					// Get this service request
					$serviceRequest = $queries->getServiceRequest($sid, $user['accountTypeId']);

			        // Add it to the invoice
					$sheet->setCellValue('A'.$r, 'Service' . '(' . $serviceRequest['requestId'] . '-' . $sid . ')');
					$sheet->setCellValue('B'.$r, $user['accountType']);
					$sheet->setCellValue('C'.$r, $serviceRequest['serviceName']);
					$sheet->setCellValue('D'.$r, $serviceRequest['createdAt']);
					$sheet->setCellValue('E'.$r, $serviceRequest['UMB_Regular']);
					$sheet->setCellValue('F'.$r, $serviceRequest['samples'] * $serviceRequest['replicates']);
					$sheet->getStyle('F'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

					// Calculat the total cost for this item at regular rate (no discount)
					$totalSamples = $serviceRequest['samples'] * $serviceRequest['replicates'];
					$unitCostTotalRegular = $totalSamples * $serviceRequest['UMB_Regular'];

					// Calculate the total charges billed to the user for this item using the discount rate chart
					$thisNetAmtDue = $costCalculator->serviceRequestCalculator($serviceRequest['samples'], $serviceRequest['replicates'], $serviceRequest['prep'], $serviceRequest['regular'], $serviceRequest['discount'], $serviceRequest['cutoff'], $serviceRequest['prepRegular'], $serviceRequest['prepDiscount'], $serviceRequest['prepCutoff']);
					$NetDueFromPI += $thisNetAmtDue;

					// Add to Total Charges
					$TotalCharges += $unitCostTotalRegular;

					// Add lines to invoice
					$sheet->setCellValue('G'.$r, $unitCostTotalRegular); // Total cost for this item at regular rate
					$sheet->getStyle('G'.$r)->getNumberFormat()->setFormatCode("$#,###.00");

					// Add volume discount data to the array
					$discountValue = $serviceRequest['regular'] - $serviceRequest['discount'];
					$prepDiscountValue = $serviceRequest['prepRegular'] - $serviceRequest['prepDiscount'];

					// Calculate the total subsidy for services (umb rate - this rate) * num_samples
					$sub = (($serviceRequest['UMB_Regular'] - $serviceRequest['regular']) * $totalSamples);
					$base = ($serviceRequest['UMB_Regular'] - $serviceRequest['regular']);
					array_push($totalServiceSampleSubsidy, array("name" => $serviceRequest['serviceName'], "subsidy" => $sub, "base" => $base, "samples" => $totalSamples));

					if($discountValue != 0){
						if(array_key_exists($serviceRequest['serviceName'], $volumeDiscountSamples)){
							$samp = $volumeDiscountSamples[$serviceRequest['serviceName']][1] + $totalSamples - $serviceRequest['cutoff'];
							$volumeDiscountSamples[$serviceRequest['serviceName']] = array($discountValue, $samp);
						}else{
							$volumeDiscountSamples[$serviceRequest['serviceName']] = array($discountValue, $totalSamples - $serviceRequest['cutoff']);
						}
					}
					if($prepDiscountValue != 0){
						if($serviceRequest['prep'] == 1){

							$r++;
							$volumeDiscountPrep[$serviceRequest['serviceName']] = array($prepDiscountValue, ($serviceRequest['samples']-$serviceRequest['prepCutoff']) );
							//$sheet->setCellValue('A'.$r, 'Service' . '(' . $serviceRequest['requestId'] . '-' . $sid . ')');
							//$sheet->setCellValue('B'.$r, $user['accountType']);
							$sheet->setCellValue('C'.$r, "-- Sample Prep --");
							$sheet->setCellValue('E'.$r, $serviceRequest['prepRegular']);
							$sheet->setCellValue('F'.$r, $serviceRequest['samples']);
							$sheet->setCellValue('G'.$r, ($serviceRequest['prepRegular']*$serviceRequest['samples']) );
							$sheet->getStyle('F'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$TotalCharges += $serviceRequest['prepRegular']*$serviceRequest['samples'];

							// Calculate the total subsidy for sample prep (umb prep rate - this prep rate) * num_samples
							$sub = ($serviceRequest['UMB_Prep_Regular'] - $serviceRequest['prepRegular']) * $totalSamples;
							$base = ($serviceRequest['UMB_Prep_Regular'] - $serviceRequest['prepRegular']);
							array_push($totalServicePrepSubsidy, array("name" => $serviceRequest['serviceName'], "subsidy" => $sub, "base" => $base, "samples" => $totalSamples));

						}
					}

					// Increment the line
					$r++;
					$hasService = TRUE;
		        }
	        }

	        // IF there are training sessions to add, fetch and add them
	        if(array_key_exists('trainings', $json)){
	        	foreach($json['trainings'] as $tid){

	        		// Get this training session
	        		if($class == "INT"){
						$training = $queries->getTrainingInternal($tid, $user['accountTypeId']);
					}else{
						$training = $queries->getTrainingExternal($tid, $user['accountTypeId']);
					}

			        // Calculate Hours
					$hours = (strtotime($training['dateTo'] . ' ' . $training['timeTo']) - strtotime($training['dateFrom'] . ' ' . $training['timeFrom'])) / 3600;

					// Add this training session tot the invoice
					$sheet->setCellValue('A'.$r, 'Training' . '(' . $tid . ')');
					$sheet->setCellValue('B'.$r, $user['accountType']);
					$sheet->setCellValue('C'.$r, $training['instrumentName']);
					if($training['dateFrom'] == $training['dateTo']){
						$sheet->setCellValue('D'.$r, $training['dateFrom'].":".$training['timeFrom'].' to '.$training['timeTo']);
					}else{
						$sheet->setCellValue('D'.$r, $training['dateFrom'] .':'.$training['timeFrom']. ' to ' . $training['dateTo'].':'.$training['timeTo']);
					}
					$sheet->setCellValue('E'.$r, $training['staffRate']);
					$sheet->setCellValue('F'.$r, $hours . 'hr(s)');
					$sheet->getStyle('F'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$thisTotal = $costCalculator->trainingCalculator($hours, $training['staffRate']);
					$NetDueFromPI += $thisTotal;
					$TotalCharges += $thisTotal;
					$sheet->setCellValue('G'.$r, $thisTotal);
					$sheet->getStyle('G'.$r)->getNumberFormat()->setFormatCode("$#,###.00");
					$r++;
		        }
	        }

	        // Now Add some space after the last item is entered
	        $r=$r+2;


	        /*
	         *  BUILD THE EXCEL FILE HERE
	         */

			// Return Address
			$sheet->setCellValue("A1", "Mass Spectrometry Center");
			$sheet->setCellValue("A2", "Pharmaceutical Sciences");
			$sheet->setCellValue("A3", "20 N. Pine Street");
			$sheet->setCellValue("A4", "Room N725");
			$sheet->setCellValue("A5", "Baltimore, MD 21201");
			$sheet->getStyle('A1:A5')->getFont()->setBold(true);

			// University Logo
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setWorksheet($sheet);
			$objDrawing->setName("Logo");
			$objDrawing->setDescription("UMB Logo");
			$objDrawing->setPath('../../images/umbmsclogo.png');
			$objDrawing->setCoordinates('E1');
			$objDrawing->setResizeProportional(true);
			$objDrawing->setWidth(381);

			// Customer Address
			$sheet->setCellValue("A8", $project['primaryInvestigator']);
			$sheet->getStyle('A8')->getFont()->setBold(true);
			$sheet->setCellValue("A9", 'CC: ' . $user['first'] . ' ' . $user['last']);
		    $sheet->setCellValue("A10", $user['institution']);
		    $sheet->setCellValue("A11", $project['addressOne']);
		    $sheet->setCellValue("A12", $project['addressTwo']);
		    $sheet->setCellValue("A13", $project['city'] . ' ' . $project['state'] . ' ' . $project['zip']);

		    // Invoice/PO Number and date stamp
		    $sheet->setCellValue("G8", 'INVOICE');
		    $sheet->setCellValue("F9", 'Invoice #');
		    $sheet->getStyle('G9')->getNumberFormat()->setFormatCode('000000');
		    $sheet->setCellValue("F10", 'P.O.#');
		    if($project['purchaseOrder'] != ''){
		    	$sheet->setCellValue("G10", $project['purchaseOrder']);
		    }else{
		    	$sheet->setCellValue("G10", 'Use Chart String');
		    }
		    $sheet->setCellValue("F11", 'Invoice Date');
		    $sheet->setCellValue("G11", date('m-d-Y'));
		    $sheet->setCellValue("F12", 'Due Date');
		    $sheet->setCellValue("G12", date('m-d-Y', strtotime("+30 days")));

		    // Header Row
		    $sheet->setCellValue("A16", 'Item');
		    $sheet->setCellValue("B16", 'Rate');
		    $sheet->setCellValue("C16", 'Description');
		    $sheet->setCellValue("D16", 'Date of Service');
			$sheet->setCellValue("E16", 'Unit Price');
			$sheet->setCellValue("F16", 'Quantity');
		    $sheet->setCellValue("G16", 'Amount');

		    // Style
		    $sheet->getStyle("A16:G16")->getFont()->setBold(true);
		    $sheet->getStyle("A16:G16")->applyFromArray($allBordersMedium);
		    $sheet->getStyle("A16:G16")->applyFromArray($grayFill);

		    // Total Charges
		    $sheet->mergeCells("D".$r.":F".$r);
		    $sheet->setCellValue("D".$r, "Total Charges for Services Provided");
		    $sheet->setCellValue("G".$r, $TotalCharges);
		    // Style
		    $sheet->getStyle("D".$r)->getFont()->setBold(true);
		    $sheet->getStyle("G".$r)->getFont()->setBold(true);
		    $sheet->getStyle("D".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		    // Skip a couple lines and save the line number for a clean looking line break
		    $sheet->getStyle("A".($r+1).":G".($r+1))->applyFromArray($grayFill);
		    $r=$r+3;

		    if($hasBooking){

		    	// If user is Member, Subsidy is from Campus, else Subsidy is from Department
			   	if($user['accountTypeId'] == 3){
			   		$sheet->setCellValue("D".$r, "Less Department Payment (Subsidy)");
					$sheet->setCellValue("G".$r, $TotalRegularSubsity);
					$sheet->setCellValue("E".$r, $HourlySubsity);
					$sheet->setCellValue("F".$r, $TotalHours);
					array_push($redTextFields, "G".$r);
					//$cellSubsidyA = "G".$r;
				}else if($user['accountTypeId'] == 1){
					$sheet->setCellValue("D".$r, "Less Campus Payment (Subsidy)");
					$sheet->setCellValue("G".$r, $TotalRegularSubsity);
					$sheet->setCellValue("E".$r, $HourlySubsity);
					$sheet->setCellValue("F".$r, $TotalHours);
					array_push($redTextFields, "G".$r);
					//$cellSubsidyA = "G".$r;
				}

				// Members and Affiliates get additional Progressive Subsidy
				if($user['accountTypeId'] == 1 || $user['accountTypeId'] == 3){
					$r++;
					// Might not need this
					$TotalProgressiveSubsidy = $TotalCharges - $TotalRegularSubsity - $NetDueFromPI;
					$sheet->setCellValue("D".$r, "Less Progressive Payment (Subsidy)");
					$sheet->setCellValue("G".$r, $TotalProgressiveSubsidy);
					$sheet->setCellValue("E".$r, "see attached schedule");
					$sheet->getStyle("E".$r)->getFont()->setSize(10);
					array_push($redTextFields, "G".$r);
					//$cellSubsidyB = "G".$r;
					$r=$r+2;
				}

			}

			if($hasService){

				// Iterate over SAMPLE subsidies and list their totals
				foreach($volumeDiscountSamples as $key => $val){
					$sheet->setCellValue("C".$r, "Volume Discount");
					//$sheet->setCellValue("D".$r, $key);
					$ten = substr($key, 0, 10);
					$sheet->setCellValue("D".$r, $ten . " - Samples");
					$sheet->setCellValue("E".$r, $val[0]);
					$sheet->setCellValue("F".$r, $val[1]);
					$sheet->setCellValue("G".$r, ($val[0]*$val[1]));
					array_push($redTextFields, "G".$r);
					//$cellSubsidyC = "G".$r;
					$r++;
				}

				// Iterate over PREP subsidies and list their totals
				foreach($volumeDiscountPrep as $key => $val){
					$sheet->setCellValue("C".$r, "Volume Discount");
					//$sheet->setCellValue("D".$r, $key . "Sample Prep");
					$ten = substr($key, 0, 10);
					$sheet->setCellValue("D".$r, $ten . " - Prep");
					$sheet->setCellValue("E".$r, $val[0]);
					$sheet->setCellValue("F".$r, $val[1]);
					$sheet->setCellValue("G".$r, ($val[0]*$val[1]));
					array_push($redTextFields, "G".$r);
					//$cellSubsidyD = "G".$r;
					$r++;
				}

				//var_dump($totalServiceSampleSubsidy);
				//var_dump($totalServicePrepSubsidy);

				// If the Service Subsidy is a positive value, list it here
				foreach($totalServiceSampleSubsidy as $entry){
					if($entry["subsidy"] > 0){

						if($user['accountTypeId'] == 1 || $user['accountTypeId'] == 2){
							$sheet->setCellValue("C".$r, "Less Campus Payment (Subsidy)");
						}else if($user['accountTypeId'] == 3){
							$sheet->setCellValue("C".$r, "Less Department Payment (Subsidy)");
						}else{
							$sheet->setCellValue("C".$r, "Positive Subsidy Value for NON-MEM/NON-AF - THIS EVENT SHOULD NOT OCCUR. CHECK RATE TABLE");
						}

						$ten = substr($entry["name"], 0, 10);
						$sheet->setCellValue("D".$r, $ten . " - Samples");
						$sheet->setCellValue("E".$r, $entry["base"]);
						$sheet->setCellValue("F".$r, $entry["samples"]);
						$sheet->setCellValue("G".$r, $entry["subsidy"]);
						array_push($redTextFields, "G".$r);
						//$cellSubsidyE = "G".$r;
						$r++;
					}
				}

				foreach($totalServicePrepSubsidy as $entry){
					if($entry["subsidy"] > 0){

						if($user['accountTypeId'] == 1 || $user['accountTypeId'] == 2){
							$sheet->setCellValue("C".$r, "Less Campus Payment (Subsidy)");
						}else if($user['accountTypeId'] == 3){
							$sheet->setCellValue("C".$r, "Less Department Payment (Subsidy)");
						}else{
							$sheet->setCellValue("C".$r, "Positive Subsidy Value for NON-MEM/NON-AF - THIS EVENT SHOULD NOT OCCUR. CHECK RATE TABLE");
						}

						$ten = substr($entry["name"], 0, 10);
						$sheet->setCellValue("D".$r, $ten . " - Prep");
						$sheet->setCellValue("E".$r, $entry["base"]);
						$sheet->setCellValue("F".$r, $entry["samples"]);
						$sheet->setCellValue("G".$r, $entry["subsidy"]);
						array_push($redTextFields, "G".$r);
						//$cellSubsidyF = "G".$r;
						$r++;
					}
				}

				$r++;

			}

			// Net Amount Due
			$boxedRowNetAmt = $r;
			$sheet->setCellValue("E".$r, "Net Amount Due from PI");
			// Total Actual - Regular Subsidy - User Cost
			$sheet->setCellValue("G".$r, $NetDueFromPI);

			$sheet->getStyle("E".$r)->getFont()->setBold(true);
			$sheet->getStyle("G".$r)->getFont()->setBold(true);
			$sheet->getStyle('A'.$r.':G'.$r)->applyFromArray($topBorder);
			$sheet->getStyle("A16:G".$r)->applyFromArray($border);

			// Add line for chart string
			$r=$r+2;
			$sectionBTop = $r;
			$sheet->setCellValue("A".$r, "Payment Chart String (Internal Customer)");
			$sheet->getStyle("A".$r)->getFont()->setBold(true);
			$r++;

		    // Chart String
			$sheet->setCellValue("A".($r), 'PCBU:');
			if($project['projectCostingBusinessUnit'] != ''){
				$sheet->setCellValue("B".($r), $project['projectCostingBusinessUnit']);
			}else{
				$sheet->setCellValue("B".($r), 'n/a');
			}

		    $sheet->setCellValue("A".($r+1), 'Project ID:');
		    if($project['projectId'] != ''){
				$sheet->setCellValue("B".($r+1), $project['projectId']);
			}else{
				$sheet->setCellValue("B".($r+1), 'n/a');
			}

		    $sheet->setCellValue("A".($r+2), 'Dept ID:');
		    if($project['departmentId'] != ''){
				$sheet->setCellValue("B".($r+2), $project['departmentId']);
			}else{
				$sheet->setCellValue("B".($r+2), 'n/a');
			}

			// Total Cost and amount
			$sheet->setCellValue("E".($r), 'Total');
			$sheet->setCellValue("G".($r), $NetDueFromPI);

			$sheet->setCellValue("E".($r+1), 'Amount Paid');
			$sheet->setCellValue("G".($r+1), 0.0);

			$sheet->setCellValue("E".($r+2), 'Balance Due (USD)');
			$due = "=G".$r."-G".($r+1);
			$sheet->setCellValue("G".($r+2), $due);

			$sheet->getStyle("G17:G".($r+2))->getNumberFormat()->setFormatCode("$#,###.00");
			$sheet->getStyle("A".$sectionBTop.":G".($r+2))->applyFromArray($border);
			$sheet->getStyle("E".$sectionBTop.":G".($r+2))->applyFromArray($border);
			$sheet->getStyle("E".($r+2).":G".($r+2))->applyFromArray($grayFill);

			// Skip over the chart string / totals + 1 blank line
			$r=$r+4;

			// Project title and ID
			$sheet->setCellValue("A".$r, $project['title'] . ' (' . $json['projectId'] . ')');
			$r++;

			// Users full name and ID
			$sheet->setCellValue("A".$r, $user['first'] . ' ' . $user['last'] . ' (' . $json['userId'] . ')');
			$r++;

			// Keep track of the location where the AIMS Invoice Number is supposed to go
			$invoiceLocation = $r;
			$r++;

			// Note at the bottom
			$objRichText = new PHPExcel_RichText();
			$objRichText->createText(INVOICE_REPLY_LINE);
			$objRed = $objRichText->createTextRun(INVOICE_DAYS_TO_RESPOND);
			$objRed->getFont()->setColor( new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED) );
			$objRichText->createText(" days.");

			$sheet->setCellValue("A".$r, $objRichText);
		    $sheet->getStyle("A".$r)->getFont()->setBold(true);
		    $sheet->getStyle("A".$r)->getFont()->setSize(10);

		    // Set column widths
			$sheet->getColumnDimension('A')->setWidth(12);
			$sheet->getColumnDimension('C')->setAutoSize(true);
			$sheet->getColumnDimension('D')->setWidth(29);
			$sheet->getColumnDimension('F')->setAutoSize(true);
			$sheet->getColumnDimension('G')->setAutoSize(true);

			// Set alignment for right most column
			$sheet->getStyle("G9:G".$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
			$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
			$sheet->getPageSetup()->setFitToPage(true);
			$sheet->getPageSetup()->setFitToWidth(1);
			$sheet->getPageSetup()->setFitToHeight(0);

			// Set the subsidy cells to red with parens
			foreach($redTextFields as $field){
				$sheet->getStyle($field)->getNumberFormat()->setFormatCode("[Red]($#,###.00)");
			}
			/*
			if($hasBooking){

				if($user['accountTypeId'] == 1 || $user['accountTypeId'] == 3){
					$sheet->getStyle($cellSubsidyA)->getNumberFormat()->setFormatCode("[Red]($#,###.00)");
					$sheet->getStyle($cellSubsidyB)->getNumberFormat()->setFormatCode("[Red]($#,###.00)");
				}
			}
			if($hasService){
				if($user['accountTypeId'] == 1 || $user['accountTypeId'] == 3){
					$sheet->getStyle($cellSubsidyC)->getNumberFormat()->setFormatCode("[Red]($#,###.00)");
					$sheet->getStyle($cellSubsidyD)->getNumberFormat()->setFormatCode("[Red]($#,###.00)");
					$sheet->getStyle($cellSubsidyE)->getNumberFormat()->setFormatCode("[Red]($#,###.00)");
					$sheet->getStyle($cellSubsidyF)->getNumberFormat()->setFormatCode("[Red]($#,###.00)");
				}
			}
			*/
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
		        $query->bindValue(':total', $NetDueFromPI, PDO::PARAM_STR);
		        $query->execute();

				// Finally, insert this info and retrieve the invoice number
				$sheet->setCellValue("A".$invoiceLocation, "AIMS Invoice ID: " . $this->db_connection->lastInsertId());
        $sheet->setCellValue("G9", $this->db_connection->lastInsertId());
				// If we haven't crashed yet, we are ready to return the file for download
				$this->db_connection->commit();
				//$this->db_connection->rollBack();
				/*
				 *	Save the sheet for download via ajax
				 */
				$phpExcel->setActiveSheetIndex(0);
				$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
				$objWriter->save('../../tmp/invoice.xls');
				return true;

			}catch(PDOException $ex){
				$this->db_connection->rollBack();
				//return false;
			}catch(Exception $e){
				throw $e;
			}
			return false;

	    }

    }



}
?>
