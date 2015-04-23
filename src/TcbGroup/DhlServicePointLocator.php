<?php
namespace TcbGroup;
class DhlServicePointLocator {

	const ENDPOINT_TEST = 'http://164.9.104.198/DHLServicePointLocatorWS/ServicePoint.svc';
	const ENDPOINT_PROD = 'http://dhltoolbox.se/DHLServicePointLocatorWS/ServicePoint.svc';

	const FEATURE_CASH_ON_DELIVERY = 'SF054';
	const BITCAT_INTERNATIONAL = 'BS01-TD';

	private $endpoint;
	private $client;

	public function __construct($endpoint=self::ENDPOINT_TEST){
		$this->endpoint = $endpoint;
	}

	private function getClient(){
		if(!isset($this->client)){
			$this->client = new \SoapClient($this->endpoint . '?wsdl', array('trace'=>1));
		}
		return $this->client;
	}

	private function buildParams($postCode, $street=null, $city=null, $featureCodes=array(), $bitCatCodes=array(), $maxNrOfItems = 10, $countryCode = 'S'){
		$params = new \stdClass();
		$params->PostCode = $postCode;
		$params->CountryCode = $countryCode;
		if($street != null) $params->Street = $street;
		if($city != null) $params->City = $city;
		if($featureCodes != null) $params->FeatureCodes = $featureCodes;
		if($bitCatCodes != null) $params->BitCatCodes = $bitCatCodes;
		$params->MaxNumberOfItems = $maxNrOfItems;
		return $params;
	}

	private function parseServicePoint($servicePoint){
		$featureCodes = array();
		if(isset($servicePoint->FeatureCodes)){
			foreach($servicePoint->FeatureCodes as $featureCode){
				$featureCodes[] = $featureCode;
			}
		}
		return array(
				'Id' => $servicePoint->Identity->Id,
				'DisplayName' => $servicePoint->Identity->DisplayName,
				'Distance' => floatval($servicePoint->Distance),
				'RouteDistance' => floatval($servicePoint->RouteDistance),
				'StreetName' => $servicePoint->StreetName,
				'PostCode' => $servicePoint->PostCode,
				'City' => $servicePoint->City,
				'FeatureCodes' => $featureCodes
		);
	}
	
	private function parseServicePointsFromResponse($response){
		$servicePoints = array();
		if(isset($response->ServicePoints) && isset($response->ServicePoints->NearbyServicePoint)){
			if(is_array($response->ServicePoints->NearbyServicePoint)){
				foreach($response->ServicePoints->NearbyServicePoint as $servicePoint){
					$servicePoints[] = $this->parseServicePoint($servicePoint);
				}
			}else{
				$servicePoints[] = $this->parseServicePoint($response->ServicePoints->NearbyServicePoint);
			}
		}
		return $servicePoints;
	}

	/**
	 * @param string $postCode
	 * @param string $countryCode
	 * @param string $street
	 * @param string $city
	 * @param array $featureCodes
	 * @param number $maxNrOfItems
	 * @return array
	 */
	public function search($postCode, $street=null, $city=null, $featureCodes = array(), $bitCatCodes = array(), $maxNrOfItems = 10, $countryCode = 'S'){
		$response = $this->getClient()->GetNearestServicePoints($this->buildParams($postCode, $street, $city, $featureCodes, $bitCatCodes, $maxNrOfItems, $countryCode));
		return $this->parseServicePointsFromResponse($response);
	}

}
?>