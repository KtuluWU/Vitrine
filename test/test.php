<?php

@$siren = $_POST["siren"];
@$nic = $_POST["nic"];
@$documentId = $_POST["documentId"];
@$modesDiffusion = $_POST["modesDiffusion"];
@$email = $_POST["email"];
@$entreprise = $_POST["entreprise"];
@$dernierStatut = $_POST["dernierStatut"];
@$bilan = $_POST["bilan"];
@$depotActes = $_POST["depotActes"];
@$document_data = $_POST["document_data"];
@$commande = $_POST["commande"];

var_dump($siren);
echo "<hr>";
var_dump($nic);
echo "<hr>";
var_dump($documentId);
echo "<hr>";
var_dump($modesDiffusion);
echo "<hr>";
var_dump($email);
echo "<hr>";
var_dump($entreprise);
echo "<hr>";
var_dump($dernierStatut);
echo "<hr>";
var_dump($bilan);
echo "<hr>";
var_dump($depotActes);
echo "<hr>";
var_dump($document_data);
echo "<hr>";
var_dump($commande);

if ($nic) {
    echo "yes nic";
} else {
    echo "no nic";
}