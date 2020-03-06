<?php
ini_set('max_execution_time','6000');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

@$siren = $_GET["siren"];

$soapUrl = "https://webservices.infogreffe.fr/services/commercant-service/ws/commercant.wsdl";
$soapUser = '0003-0009';
$soapPassword = "SUPPORT";


class vitrineRequest {
    public function __construct($siren, $nic) 
    {
        $this->siren = $siren;
        $this->nic = $nic;
    }
}

$options = array(
    'login' => $soapUser,
    'password' => $soapPassword
);

$client = new SoapClient($soapUrl, array("login" => $soapUser, "password" => $soapPassword));
$client->__setLocation('https://webservices.infogreffe.fr/services/commercant-service/ws');
$vitrineRequest = new vitrineRequest($siren, '');

$params = array(
    "vitrineRequest" => $vitrineRequest
);

try {
    $response = $client->__soapCall("vitrine", $params);
}
catch (SoapFault $exception) {
    echo $exception;
}
var_dump(json_decode(json_encode($response), true));