<?php
require_once("PHPExcel.php");
require_once("EstimateCalculator.php");
require_once("ProjectInfo.php");
require_once("Users.php");
date_default_timezone_set('America/New_York');
class InvoiceTraining
{
	
    private $db_connection            		= null;    // database connection   

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
                return false;
            }
        }
    }
    
    public function getTrainingRequest($trainingId){
	    
        if ($this->databaseConnection()) {

            $temp = array();
            
	        $query = $this->db_connection->prepare('SELECT * FROM trainingBookings WHERE id=:trainingId');
	        $query->bindValue(':trainingId', $trainingId, PDO::PARAM_INT);
	        $query->execute();
	        $trainingBooking = $query->fetch(PDO::FETCH_ASSOC);
	        //var_dump($trainingBooking);

	        $query = $this->db_connection->prepare('SELECT name FROM mscInstruments WHERE id=:id');
	        $query->bindValue(':id', $trainingBooking['instrumentId'], PDO::PARAM_INT);
	        $query->execute();
	        $instrumentName = $query->fetchColumn();
	        //var_dump($instrumentName);
	        
	        $query = $this->db_connection->prepare('SELECT projectId FROM mscTrainingRequest WHERE bookingId=:bookingId');
	        $query->bindValue(':bookingId', $trainingBooking['requestId'], PDO::PARAM_INT);
	        $query->execute();
	        $projectId = $query->fetchColumn();
	        //var_dump($projectId);
	        
	        $query = $this->db_connection->prepare('SELECT first, last, accountType FROM users WHERE id=:id');
	        $query->bindValue(':id', $trainingBooking['userId'], PDO::PARAM_INT);
	        $query->execute();
	        $user = $query->fetch(PDO::FETCH_ASSOC);
	        //var_dump($user);
	        
	        $a = $trainingBooking['dateFrom'] . ' ' . $trainingBooking['timeFrom'];
	        $b = $trainingBooking['dateTo'] . ' ' . $trainingBooking['timeTo'];
	        $from = new DateTime($a);
	        $to = new DateTime($b);
	        
	        $diff = $to->diff($from);
	        $hours = $diff->h + ($diff->i / 60);
	        
	        $temp = $trainingBooking;
	        $estimateCalculator = new EstimateCalculator();
	        $temp['estimate'] = $estimateCalculator->getTrainingEstimate($user['accountType'], $hours);
	        $temp['hours'] = $hours;
	        $temp['instrumentName'] = $instrumentName;
	        $temp['projectId'] = $projectId;
	        $temp['userName'] = $user['first'] . ' ' . $user['last'];
	        $temp['accountType'] = $user['accountType'];
	        
			return $temp;

        }
	    
    }
    
    public function getInvoice($trainingId){
    
    	$projectInfo = new ProjectInfo();
    	$userInfo = new Users();
    
	    if ($this->databaseConnection()) {
	    
		    // Make sure this invoice doesn't already exist
		    $query = $this->db_connection->prepare('SELECT * FROM invoiceTraining WHERE id=:trainingId');
			$query->bindValue(':trainingId', $trainingId, PDO::PARAM_INT);
			$query->execute();
			$invoice = $query->fetch(PDO::FETCH_ASSOC);

			if( $invoice != false ){
				$this->generateExcel($invoice);
			}else{
				
				$trainingRequest = $this->getTrainingRequest($trainingId);
				$user = $userInfo->getUser($trainingRequest['userId']);
				$project = $projectInfo->getProject($trainingRequest['projectId']);
				$payment = $projectInfo->getPaymentInfo($project['paymentId']);

				
				$query = $this->db_connection->prepare('INSERT INTO invoiceTraining (id, userId, accountType, userName, primaryInvestigator, institution, addressOne, addressTwo, city, state, zip, phone, fax, email, pmntPurchaseOrder, pmntProjectCostingBusinessUnit, pmntProjectId, pmntDepartmentId, projectId, bookingId, instrumentId, instrumentName, unitPrice, hours, total, dueDate) VALUES (:id, :userId, :accountType, :userName, :primaryInvestigator, :institution, :addressOne, :addressTwo, :city, :state, :zip, :phone, :fax, :email, :pmntPurchaseOrder, :pmntProjectCostingBusinessUnit, :pmntProjectId, :pmntDepartmentId, :projectId, :bookingId, :instrumentId, :instrumentName, :unitPrice, :hours, :total, :dueDate)');
				
				$query->bindValue(':id', $trainingId, PDO::PARAM_INT);
				$query->bindValue(':userId', $trainingRequest['userId'], PDO::PARAM_INT);
				$query->bindValue(':accountType', $trainingRequest['accountType'], PDO::PARAM_STR);
				$query->bindValue(':userName', $trainingRequest['userName'], PDO::PARAM_STR);
				$query->bindValue(':primaryInvestigator', $project['primaryInvestigator'], PDO::PARAM_STR);
				$query->bindValue(':institution', $user['institution'], PDO::PARAM_STR);
				$query->bindValue(':addressOne', $project['addressOne'], PDO::PARAM_STR);
				$query->bindValue(':addressTwo', $project['addressTwo'], PDO::PARAM_STR);
				$query->bindValue(':city', $project['city'], PDO::PARAM_STR);
				$query->bindValue(':state', $project['state'], PDO::PARAM_STR);
				$query->bindValue(':zip', $project['zip'], PDO::PARAM_STR);
				$query->bindValue(':phone', $project['phone'], PDO::PARAM_STR);
				$query->bindValue(':fax', $project['fax'], PDO::PARAM_STR);
				$query->bindValue(':email', $user['email'], PDO::PARAM_STR);
				$query->bindValue(':pmntPurchaseOrder', $payment['purchaseOrder'], PDO::PARAM_INT);
				$query->bindValue(':pmntProjectCostingBusinessUnit', $payment['projectCostingBusinessUnit'], PDO::PARAM_INT);
				$query->bindValue(':pmntProjectId', $payment['projectId'], PDO::PARAM_INT);
				$query->bindValue(':pmntDepartmentId', $payment['departmentId'], PDO::PARAM_INT);
				$query->bindValue(':projectId', $project['id'], PDO::PARAM_INT);
				$query->bindValue(':bookingId', $trainingRequest['id'], PDO::PARAM_INT);
				$query->bindValue(':instrumentId', $trainingRequest['instrumentId'], PDO::PARAM_INT);
				$query->bindValue(':instrumentName', $trainingRequest['instrumentName'], PDO::PARAM_STR);
				$query->bindValue(':unitPrice', ($trainingRequest['estimate'] / $trainingRequest['hours']), PDO::PARAM_INT);
				$query->bindValue(':hours', $trainingRequest['hours'], PDO::PARAM_STR);
				$query->bindValue(':total', $trainingRequest['estimate'], PDO::PARAM_STR);
				$now = new DateTime(null, new DateTimeZone('America/New_York'));
				$now = $now->format("Y-m-d H:i:s");
				$query->bindValue(':dueDate', $now, PDO::PARAM_STR);
				if($query->execute()){
					$query = $this->db_connection->prepare('SELECT * FROM invoiceTraining WHERE id=:trainingId');
					$query->bindValue(':trainingId', $trainingId, PDO::PARAM_INT);
					$query->execute();
					$invoice = $query->fetch(PDO::FETCH_ASSOC);
					return $this->generateExcel($invoice);
				}else{
					return 'Database insert error.';
				}

			}
		    		    
	    }
	    
    }
    
    public function generateExcel($invoice){
	    
	    try{
	    
		    $phpExcel = new PHPExcel();
		    $sheet = $phpExcel->getActiveSheet();
		    
			$sheet->setCellValue("A1", "Mass Spectrometry Center");
			$sheet->setCellValue("A2", "Pharmaceutical Sciences");
			$sheet->setCellValue("A3", "20 N. Pine Street");
			$sheet->setCellValue("A4", "Room N719");
			$sheet->setCellValue("A5", "Baltimore, MD 21201");
			
			$sheet->setCellValue("A8", $invoice['primaryInvestigator']);
			$sheet->setCellValue("A9", "cc: " . $invoice['userName']);
			$sheet->setCellValue("A10", $invoice['institution']);
			$sheet->setCellValue("A11", $invoice['addressOne']);
			$sheet->setCellValue("A12", $invoice['addressTwo']);
			$sheet->setCellValue("A13", $invoice['city'] . " " . $invoice['state'] . " " . $invoice['zip']);
			$sheet->setCellValue("A14", $invoice['email']);
			
			$sheet->setCellValue("F8", "I N V O I C E");
			
			$sheet->setCellValue("E10", "Invoice #");
			$sheet->setCellValue("E11", "P.O.#");
			$sheet->setCellValue("E12", "Invoice Date");
			$sheet->setCellValue("E13", "Due Date");
			
			$sheet->setCellValue("F10", $invoice['invoiceNumber']);
			$sheet->setCellValue("F11", $invoice['pmntPurchaseOrder']);
			$sheet->setCellValue("F12", $invoice['invoiceDate']);
			$sheet->setCellValue("F13", $invoice['dueDate']);
			
			$sheet->setCellValue("A18", "Item");
			$sheet->setCellValue("B18", "Rate");
			$sheet->setCellValue("C18", "Description");
			$sheet->setCellValue("D18", "Unit Price");
			$sheet->setCellValue("E18", "Quantity");
			$sheet->setCellValue("F18", "Amount");
			
			$sheet->setCellValue("A19", "Training (" . $invoice['id'] . ")");
			
			$abbreviations = array('1'=>'MEM', '2'=>'COL', '3'=>'AF', '4'=>'UMB', '5'=>'NP', '6'=>'FP');
			
			$sheet->setCellValue("B19", $abbreviations[$invoice['accountType']]);
			$sheet->setCellValue("C19", $invoice['instrumentName']);
			$sheet->setCellValue("D19", $invoice['unitPrice']);
			$sheet->setCellValue("E19", $invoice['hours']);
			$sheet->setCellValue("F19", $invoice['total']);
			
			$sheet->setCellValue("A31", 'NOTES: UMB Chart String');
			$sheet->setCellValue("A32", 'PCBU:');
			$sheet->setCellValue("A33", 'Project ID:');
			$sheet->setCellValue("A34", 'Dept ID:');
			
			$sheet->setCellValue("C32", $invoice['pmntProjectCostingBusinessUnit']);
			$sheet->setCellValue("C33", $invoice['pmntProjectId']);
			$sheet->setCellValue("C34", $invoice['pmntDepartmentId']);
			
			$sheet->setCellValue("D31", 'Subtotal');
			$sheet->setCellValue("D32", 'Total');
			$sheet->setCellValue("D33", 'Amount Paid');
			$sheet->setCellValue("D34", 'Balance Due (USD)');
			
			$sheet->setCellValue("F31", $invoice['total']);
			$sheet->setCellValue("F32", $invoice['total']);
			$sheet->setCellValue("F33", $invoice['paid']);
			$total = $invoice['total'] - $invoice['paid'];
			$sheet->setCellValue("F34", "$" . $total);
			$sheet->getStyle('F34')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
			$sheet->getStyle("F34")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$sheet->setCellValue("A35", "TrainingID:");
			$sheet->setCellValue("A36", "ProjectID:");
			
			$sheet->setCellValue("B35", $invoice['id']);
			$sheet->setCellValue("B36", $invoice['projectId']);
			
			
			// Formatting
			$border = array(
		      'borders' => array(
		        'left' => array(
		          'style' => PHPExcel_Style_Border::BORDER_THIN,
		        ),
		        'right' => array(
		          'style' => PHPExcel_Style_Border::BORDER_THIN,
		        ),
				'top' => array(
		          'style' => PHPExcel_Style_Border::BORDER_THIN,
		        ),
		        'bottom' => array(
		          'style' => PHPExcel_Style_Border::BORDER_THIN,
		        ),
		      ),
		    );
		    
		    $topBorder = array(
		      'borders' => array(
		        'top' => array(
		          'style' => PHPExcel_Style_Border::BORDER_THIN,
		        ),
		      ),
		    );
		    
		    $bottomBorder = array(
		      'borders' => array(
		        'bottom' => array(
		          'style' => PHPExcel_Style_Border::BORDER_THIN,
		        ),
		      ),
		    );
		    
		    $grayFill = array(
		        'fill' => array(
		            'type' => PHPExcel_Style_Fill::FILL_SOLID,
		            'color' => array('rgb' => 'C0C0C0')
		        ),
		    );
		    
		    $font = array(
		    	'font'  => array(
			        'size'  => 12,
			        'name'  => 'Arial'
				)
		    );
		    $sheet->getStyle('A1:F40')->applyFromArray($font);
		    
		    // Set Bold Cells
		    $sheet->getStyle('A1:A5')->getFont()->setBold(true);
		    $sheet->getStyle('A8:A9')->getFont()->setBold(true);
		    $sheet->getStyle('A18:F18')->getFont()->setBold(true);
		    $sheet->getStyle('E10:E13')->getFont()->setBold(true);
		    $sheet->getStyle('F8')->getFont()->setBold(true);
		    $sheet->getStyle('D34')->getFont()->setBold(true);
		    $sheet->getStyle('E34')->getFont()->setBold(true);
		    
		    // Set Borders
			$sheet->getStyle('E9:F14')->applyFromArray($border);
			$sheet->getStyle('A18:F34')->applyFromArray($border);
			$sheet->getStyle('A31:F34')->applyFromArray($border);
			$sheet->getStyle('D31:F34')->applyFromArray($border);
			$sheet->getStyle('D32:F34')->applyFromArray($border);
			$sheet->getStyle('A18:F18')->applyFromArray($topBorder);
			$sheet->getStyle('A18:F18')->applyFromArray($bottomBorder);
			
			// Set auto column size
			$sheet->getColumnDimension('C')->setAutoSize(true);
			$sheet->getColumnDimension('E')->setAutoSize(true);
			$sheet->getColumnDimension('F')->setAutoSize(true);
			$sheet->getColumnDimension('A')->setWidth(13);
			$sheet->getColumnDimension('D')->setWidth(10);
			
			// Background color
			$sheet->getStyle('A18:F18')->applyFromArray($grayFill);
			$sheet->getStyle('D34:F34')->applyFromArray($grayFill);
			
			// Insert the UMB MSC Logo
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setWorksheet($sheet);
			$objDrawing->setName("Logo");
			$objDrawing->setDescription("UMB Logo");
			$objDrawing->setPath('../../images/umbmsclogo.png');
			$objDrawing->setCoordinates('D1');
			$objDrawing->setResizeProportional(false);
			$objDrawing->setWidth(190);
			$objDrawing->setHeight(66);
			
			// Save the file
			$phpExcel->setActiveSheetIndex(0);
			$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
			$objWriter->save('../../tmp/training_invoice.xls');
			return true;
			
		}catch(Exception $ex){
			return false;
		}

    }

}
?>