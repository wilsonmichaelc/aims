<?php
require_once("PHPExcel.php");

class InvoiceService
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
    
    public function getInvoice($serviceId){
    
	    if ($this->databaseConnection()) {
	    	
		    // Make sure this invoice doesn't already exist
		    $query = $this->db_connection->prepare('SELECT * FROM invoiceService WHERE id=:serviceId');
			$query->bindValue(':serviceId', $serviceId, PDO::PARAM_INT);
			$query->execute();
			$invoice = $query->fetch(PDO::FETCH_ASSOC);
			
			if( $invoice != false ){
				return $this->generateExcel($invoice);
			}else{
				$service = $this->getService($serviceId);
				$servicesSelected = $this->getServicesSelected($serviceId);
				$user = $this->getUser($service['userId']);
				$project = $this->getProject($service['projectId']);
				$payment = $this->getPaymentInfo($project['paymentId']);
				$accountType = $this->getAccountName($user['accountType']);
								
				$servicesArray = array();
				$totalCost = 0;
				
				for($i=0; $i<sizeOf($servicesSelected); $i++){
					$sname = $this->getServiceName($servicesSelected[$i]['serviceId']);
					$aname = $this->getAccountName($user['accountType']);
					$scost = $this->getServiceCost($user['accountType'], $servicesSelected[$i]['samples'], $servicesSelected[$i]['replicates'], $servicesSelected[$i]['prep'], $servicesSelected[$i]['serviceId']);
					$subarray = array(
						'name'=>$sname,
						'accountType'=>$aname,
						'serviceId'=>$servicesSelected[$i]['serviceId'],
						'samples'=>$servicesSelected[$i]['samples'],
						'replicates'=>$servicesSelected[$i]['replicates'],
						'prep'=>$servicesSelected[$i]['prep'],
						'requestId'=>$servicesSelected[$i]['requestId'],
						'cost'=>$scost
					);
					$totalCost += $scost;
					array_push($servicesArray, $subarray);
				}
				
				$query = $this->db_connection->prepare('INSERT INTO invoiceService (id, userName, accountType, primaryInvestigator, institution, addressOne, addressTwo, city, state, zip, phone, fax, email, pmntPurchaseOrder, pmntProjectCostingBusinessUnit, pmntProjectId, pmntDepartmentId, projectId, serviceId, servicesSelected, total) VALUES (:id, :userName, :accountType, :primaryInvestigator, :institution, :addressOne, :addressTwo, :city, :state, :zip, :phone, :fax, :email, :pmntPurchaseOrder, :pmntProjectCostingBusinessUnit, :pmntProjectId, :pmntDepartmentId, :projectId, :serviceId, :servicesSelected, :total)');
				
				$query->bindValue(':id', $serviceId, PDO::PARAM_INT);
				$query->bindValue(':userName', $user['first'] . ' ' . $user['last'], PDO::PARAM_STR);
				$query->bindValue(':accountType', $accountType, PDO::PARAM_STR);
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
				$query->bindValue(':serviceId', $serviceId, PDO::PARAM_INT);
				$query->bindValue(':servicesSelected', json_encode($servicesArray), PDO::PARAM_STR);
				$query->bindValue(':total', $totalCost, PDO::PARAM_STR);
				
				if($query->execute()){
					$query = $this->db_connection->prepare('SELECT * FROM invoiceService WHERE id=:serviceId');
					$query->bindValue(':serviceId', $serviceId, PDO::PARAM_INT);
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
			
			$services = (array) json_decode($invoice['servicesSelected']);
			
			$row = 19;
			$abbreviations = array('Member'=>'MEM', 'Collaborator'=>'COL', 'Affiliate'=>'AF', 'UMB'=>'UMB', 'NonProfit'=>'NP', 'ForProfit'=>'FP');
			$calcTotal = 0;
			
			foreach($services as $service){
			
				$type;
		    	if($service->accountType == 'Member'){ $type='member'; }
		    	if($service->accountType == 'Collaborator'){ $type='collaborator'; }
		    	if($service->accountType == 'Affiliate'){ $type='affiliate'; }
		    	if($service->accountType == 'UMB'){ $type='umb'; }
		    	if($service->accountType == 'NonProfit'){ $type='nonProfit'; }
		    	if($service->accountType == 'ForProfit'){ $type='forProfit'; }
		    	
				$cutoff = $this->getCutoff($service->serviceId, $type);
				$discount = $this->getDiscount($service->serviceId, $type);
				$regular = $this->getRegular($service->serviceId, $type);
				$samples = $service->samples * $service->replicates;
				
				if($samples >= $cutoff){
					
					// One for the cutoff
					$sheet->setCellValue("A".$row, 'Service');
					$sheet->setCellValue("B".$row, $abbreviations[$service->accountType]);
					$sheet->setCellValue("C".$row, $service->name);
					$sheet->setCellValue("D".$row, $regular);
					$sheet->setCellValue("E".$row, $cutoff);
					$sheet->setCellValue("F".$row, ($regular * $cutoff));
					$calcTotal += ($regular * $cutoff);
					$samples = $samples - $cutoff;
					
					// One for the rest
					if($samples > 0){
						$row += 1;
						$sheet->setCellValue("A".$row, 'Service');
						$sheet->setCellValue("B".$row, $abbreviations[$service->accountType]);
						$sheet->setCellValue("C".$row, $service->name);
						$sheet->setCellValue("D".$row, $discount);
						$sheet->setCellValue("E".$row, $samples);
						$sheet->setCellValue("F".$row, ($samples * $discount));
						$calcTotal += ($samples * $discount);
					}
					
				}else if($samples < $cutoff){
					$sheet->setCellValue("A".$row, 'Service');
					$sheet->setCellValue("B".$row, $abbreviations[$service->accountType]);
					$sheet->setCellValue("C".$row, $service->name);
					$sheet->setCellValue("D".$row, $regular);
					$sheet->setCellValue("E".$row, $samples);
					$sheet->setCellValue("F".$row, ($samples * $regular));
					$calcTotal += ($samples * $regular);
				}
	
				$row += 1;
				
				if($service->prep == 1){
				
					$p_cutoff = $this->getPrepCutoff($service->serviceId, $type);
					$p_discount = $this->getPrepDiscount($service->serviceId, $type);
					$p_regular = $this->getPrepRegular($service->serviceId, $type);
					$samples = $service->samples * $service->replicates;
					
					if($samples >= $p_cutoff){
						
						// One for the cutoff
						$sheet->setCellValue("A".$row, 'Service');
						$sheet->setCellValue("B".$row, $service->accountType);
						$sheet->setCellValue("C".$row, $service->name.' -Sample Prep');
						$sheet->setCellValue("D".$row, $p_regular);
						$sheet->setCellValue("E".$row, $p_cutoff);
						$sheet->setCellValue("F".$row, ($p_regular * $p_cutoff));
						$calcTotal += ($p_regular * $p_cutoff);
						
						$samples = $samples - $p_cutoff;
						
						// One for the rest
						if($samples > 0){
							$row += 1;
							$sheet->setCellValue("A".$row, 'Service');
							$sheet->setCellValue("B".$row, $service->accountType);
							$sheet->setCellValue("C".$row, $service->name.' -Sample Prep');
							$sheet->setCellValue("D".$row, $p_discount);
							$sheet->setCellValue("E".$row, $samples);
							$sheet->setCellValue("F".$row, ($samples * $p_discount));
							$calcTotal += ($samples * $p_discount);
						}
						
					}else if($samples < $p_cutoff){
						$sheet->setCellValue("A".$row, 'Service');
						$sheet->setCellValue("B".$row, $service->accountType);
						$sheet->setCellValue("C".$row, $service->name.' -Sample Prep');
						$sheet->setCellValue("D".$row, $p_regular);
						$sheet->setCellValue("E".$row, $samples);
						$sheet->setCellValue("F".$row, ($samples * $p_regular));
						$calcTotal += ($samples * $p_regular);
					}
					
				}
				
			}
			
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
			
			$sheet->setCellValue("F31", $calcTotal);
			$sheet->setCellValue("F32", $invoice['total']);
			$sheet->setCellValue("F33", $invoice['paid']);
			$total = $calcTotal - $invoice['paid'];
			$sheet->setCellValue("F34", "$" . $total);
			$sheet->getStyle('F34')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
			$sheet->getStyle("F34")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$sheet->setCellValue("A35", "ProjectID:");
			
			$sheet->setCellValue("B35", $invoice['projectId']);
			
			
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
			$sheet->getColumnDimension('D')->setAutoSize(true);
			$sheet->getColumnDimension('A')->setWidth(13);
			
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
			$objWriter->save('../../tmp/service_invoice.xls');
			return true;
	
		}catch(Expection $ex){
			return false;
		}

    }
    
    public function getServiceCost($accountType, $samples, $replicates, $prep, $serviceId){
	    
	    //var_dump($accountType);
	    $runningTotal = 0;
	    	    
	    if($accountType == 1){ $type='member'; }
    	if($accountType == 2){ $type='collaborator'; }
    	if($accountType == 3){ $type='affiliate'; }
    	if($accountType == 4){ $type='umb'; }
    	if($accountType == 5){ $type='nonProfit'; }
    	if($accountType == 6){ $type='forProfit'; }
	    
	    $a_cut = $analysisRates[$type . 'Cutoff'];
	    $a_reg = $analysisRates[$type . 'Regular'];
	    $a_disc = $analysisRates[$type . 'Discount'];
	    
	    $analysisRates = $this->getAnalysisRates($serviceId);
	    
	    if($samples > $a_cut){
		    $runningTotal += ($a_cut * $a_reg) + (($samples - $a_cut) * $a_disc);
	    }else{
		    $runningTotal += $a_reg * $samples;
	    }
	    
	    if($prep == 'true'){
		    $prepRates = $this->getPrepRates($serviceId);
		    $p_cut = $prepRates[$type . 'Cutoff'];
		    $p_reg = $prepRates[$type . 'Regular'];
		    $p_disc = $prepRates[$type . 'Discount'];
		    
		    if($samples > $p_cut){
			    $runningTotal += ($p_cut * $p_reg) + (($samples - $p_cut) * $p_disc);
		    }else{
			    $runningTotal += $p_reg * $samples;
		    }
	    }
	    
		return $runningTotal;
	    
    }
    
    private function getAnalysisRates($id){
    
    	if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT * FROM mscAnalysisServices WHERE id=:id');
				$query->bindValue(':id', $id, PDO::PARAM_INT);
				$query->execute();
				return $query->fetch(PDO::FETCH_ASSOC);
	    }
	    
    }
    
    private function getPrepRates($id){
	    
	    if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT * FROM mscPrepServices WHERE analysisId=:id');
				$query->bindValue(':id', $id, PDO::PARAM_INT);
				$query->execute();
				return $query->fetch(PDO::FETCH_ASSOC);
	    }
	    
    }
    
    private function getService($id){
	    if ($this->databaseConnection()) {
		    $query = $this->db_connection->prepare('SELECT * FROM mscServiceRequest WHERE id=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			return $query->fetch(PDO::FETCH_ASSOC);
		}
    }
    
    private function getServicesSelected($id){
	    if ($this->databaseConnection()) {
		    $query = $this->db_connection->prepare('SELECT * FROM mscServicesSelected WHERE requestId=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
    }
    
    private function getServiceName($id){
	    if ($this->databaseConnection()) {
		    $query = $this->db_connection->prepare('SELECT name FROM mscAnalysisServices WHERE id=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			return $query->fetchColumn();
		}
    }
    
    private function getUser($id){
	    if ($this->databaseConnection()) {
		    $query = $this->db_connection->prepare('SELECT * FROM users WHERE id=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			return $query->fetch(PDO::FETCH_ASSOC);
		}
    }
    
    private function getProject($id){
	    if ($this->databaseConnection()) {
		    $query = $this->db_connection->prepare('SELECT * FROM projects WHERE id=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			return $query->fetch(PDO::FETCH_ASSOC);
		}
    }
    
    private function getPaymentInfo($id){
	    if ($this->databaseConnection()) {
		    $query = $this->db_connection->prepare('SELECT * FROM paymentInfo WHERE id=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			return $query->fetch(PDO::FETCH_ASSOC);
		}
    }
    
    private function getAccountName($id){
	    if ($this->databaseConnection()) {
		    $query = $this->db_connection->prepare('SELECT name FROM accountTypes WHERE id=:id');
			$query->bindValue(':id', $id, PDO::PARAM_INT);
			$query->execute();
			return $query->fetchColumn();
		}
    }

	private function getCutoff($serviceId, $accountType){
    
    	if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT * FROM mscAnalysisServices WHERE id=:id');
				$query->bindValue(':id', $serviceId, PDO::PARAM_INT);
				$query->execute();
				$analysisRates = $query->fetch(PDO::FETCH_ASSOC);
				
				return $analysisRates[$accountType . 'Cutoff'];			    
	    }
	    
    }
    
    private function getRegular($serviceId, $accountType){
    
    	if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT * FROM mscAnalysisServices WHERE id=:id');
				$query->bindValue(':id', $serviceId, PDO::PARAM_INT);
				$query->execute();
				$analysisRates = $query->fetch(PDO::FETCH_ASSOC);
				
				return $analysisRates[$accountType . 'Regular'];			    
	    }
	    
    }
    
    private function getDiscount($serviceId, $accountType){
        
    	if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT * FROM mscAnalysisServices WHERE id=:id');
				$query->bindValue(':id', $serviceId, PDO::PARAM_INT);
				$query->execute();
				$analysisRates = $query->fetch(PDO::FETCH_ASSOC);
				
				return $analysisRates[$accountType . 'Discount'];			    
	    }
	    
    }
    
	private function getPrepCutoff($serviceId, $accountType){
    
    	if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT * FROM mscPrepServices WHERE id=(SELECT samplePrepId FROM mscAnalysisServices WHERE id=:id)');
				$query->bindValue(':id', $serviceId, PDO::PARAM_INT);
				$query->execute();
				$prepRates = $query->fetch(PDO::FETCH_ASSOC);
				
				return $prepRates[$accountType . 'Cutoff'];			    
	    }
	    
    }
    
    private function getPrepRegular($serviceId, $accountType){
    
    	if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT * FROM mscPrepServices WHERE id=(SELECT samplePrepId FROM mscAnalysisServices WHERE id=:id)');
				$query->bindValue(':id', $serviceId, PDO::PARAM_INT);
				$query->execute();
				$prepRates = $query->fetch(PDO::FETCH_ASSOC);
				
				return $prepRates[$accountType . 'Regular'];			    
	    }
	    
    }
    
    private function getPrepDiscount($serviceId, $accountType){
        
    	if ($this->databaseConnection()) {
		    	$query = $this->db_connection->prepare('SELECT * FROM mscPrepServices WHERE id=(SELECT samplePrepId FROM mscAnalysisServices WHERE id=:id)');
				$query->bindValue(':id', $serviceId, PDO::PARAM_INT);
				$query->execute();
				$prepRates = $query->fetch(PDO::FETCH_ASSOC);
				
				return $prepRates[$accountType . 'Discount'];			    
	    }
	    
    }
		
}
?>