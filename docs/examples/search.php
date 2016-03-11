<?php
use TcbGroup\DhlServicePointLocator;
require_once 'src/TcbGroup/DhlServicePointLocator.php';

function printServicePoint($servicePoint){
	echo $servicePoint['Id'] . ' - ' . $servicePoint['DisplayName'] . "\n";
	echo $servicePoint['StreetName'] . "\n";
	echo $servicePoint['PostCode'] . ' ' . $servicePoint['City'] . "\n";
	echo 'Dist: ' . $servicePoint['Distance'] . "\tRouteDistance: " . $servicePoint['RouteDistance'] . "\n";
	echo "Features:\n";
	foreach($servicePoint['FeatureCodes'] as $featureCodes){
		if(is_array($featureCodes)){
			foreach($featureCodes as $featureCode){
				echo "\t" . $featureCode . "\n";
			}
		}else{
			echo "\t" . $featureCodes . "\n";
		}
	}
	echo "\n";
}

$locator = new DhlServicePointLocator(DhlServicePointLocator::ENDPOINT_PROD);

$servicePoints = $locator->search('13540');
foreach($servicePoints as $servicePoint){
	printServicePoint($servicePoint);
}

echo "\n-----------------------------------------------------\n\n";

$servicePoints = $locator->search('11448', 'Kommendörsgatan 30', 'Stockholm', array(DhlServicePointLocator::FEATURE_CASH_ON_DELIVERY), array(DhlServicePointLocator::BITCAT_INTERNATIONAL), 1);
foreach($servicePoints as $servicePoint){
	printServicePoint($servicePoint);
}

?>