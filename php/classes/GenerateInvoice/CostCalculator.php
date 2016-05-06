<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

class CostCalculator{

	public function bookingInternalCalculator($hours, $one, $four, $eight, $sixteen, $twentyFour){
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
    
    public function bookingExternalCalculator($hours, $rate){
	    return $hours * $rate;
    }  
    
    public function serviceRequestCalculator($samples, $replicates, $prep, $reg, $disc, $cut, $pReg, $pDisc, $pCut){

		$total = 0;
		$prepSamples = $samples;
		$samples = $samples * $replicates;

		if($prep){

			if($prepSamples >= $pCut){
	    		$total += ( ($prepSamples - $pCut) * $pDisc );
	    		$total += ( $pCut * $pReg );
	    	}else{
		    	$total += $pReg;
	    	} 

		}

		if($samples >= $cut){

			$total += ( ($samples - $cut) * $disc );
			$total += ( $cut * $reg );

    	}else{
	    	$total += $reg * $samples;
    	}    
    	
	    return $total;
    }
    
    public function trainingCalculator($hours, $rate){
	    return $hours * $rate;
    }

}

?>