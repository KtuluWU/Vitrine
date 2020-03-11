<?php

@$siren = $_POST["siren"];
@$nic = $_POST["nic"];
@$dernierStatut = $_POST["dernierStatut"];
@$depotActes = $_POST["depotActes"];
@$documents = $_POST["documents_data"];
@$commandes = $_POST["commandes"];
@$bilans = $_POST["bilans"];

$bilans_r = explode(",", $bilans);
$documents_pre_r = explode(",", $documents);
$documents_id = array();
$documents_modesdiffusion = array();
foreach($documents_pre_r as $document_pre) {
    array_push($documents_id, substr($document_pre,0,strpos($document_pre, '&')));
    array_push($documents_modesdiffusion, substr($document_pre,strpos($document_pre,'&')+1));
}

var_dump($siren);
echo "<hr>";
var_dump($nic);
echo "<hr>";
var_dump($commandes);
echo "<hr>";
var_dump($dernierStatut);
echo "<hr>";
var_dump($depotActes);
echo "<hr>";
var_dump($bilans);
echo "<hr>";
var_dump($documents);
echo "<hr>";
var_dump($documents_pre_r);
echo "<hr>";
var_dump($documents_id);
echo "<hr>";
var_dump($documents_modesdiffusion);
echo "<hr>";

