<?php
ini_set('max_execution_time', '12000');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

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

$soapUrl = "https://webservices.infogreffe.fr/services/commercant-service/ws/commercant.wsdl";
$soapUser = '0003-0009';
$soapPassword = "SUPPORT";

class vitrineRequest
{
    public function __construct($siren, $nic)
    {
        $this->siren = $siren;
        if ($nic) {$this->nic = $nic;}
    }
}

class commandeRequest
{
    public function __construct($siren, $nic, $documentId, $modesDiffusion, $email)
    {
        $this->siren = $siren;
        if ($nic) {$this->nic = $nic;}
        $this->documentId = $documentId;
        $this->modesDiffusion = array();
        $this->modesDiffusion["mode"] = $modesDiffusion;
        if ($email) {$this->email = $email;}
    }
}

$client = new SoapClient($soapUrl, array("login" => $soapUser, "password" => $soapPassword, 'trace' => 1));
$client->__setLocation('https://webservices.infogreffe.fr/services/commercant-service/ws');

$vitrineRequest = new vitrineRequest($siren, $nic);
$commandeRequest = new commandeRequest($siren, $nic, $documentId, $modesDiffusion, $email);

$params_vitrine = array(
    "vitrineRequest" => $vitrineRequest,
);
$params_commande = array(
    "commandeRequest" => $commandeRequest,
);

try {
    $response_vitrine = $client->__soapCall("vitrine", $params_vitrine);
    if ($commande == "true") {
        $response_commande = $client->__soapCall("commande", $params_commande);
    }
} catch (SoapFault $exception) {
    echo 'Request : <br/><xmp>',
    $client->__getLastRequest(),
    '</xmp><br/><br/> Error Message : <br/>',
    $exception->getMessage();
}

$res_vitrine = json_decode(json_encode($response_vitrine), true);
$res_commande = json_decode(json_encode($response_commande), true);

if ($entreprise == "true") {
    ?>
    <div class="res_content-block">
        <div class="res_content-title">Entreprise:</div>
        <div>
            <span>Dénomination: </span><span><?php echo $res_vitrine["entreprise"]["denomination"]; ?></span>
        </div>
        <div>
            <span>Siren: </span><span><?php echo $res_vitrine["entreprise"]["siren"]; ?></span>
        </div>
        <div>
            <span>Nic: </span><span><?php echo $res_vitrine["entreprise"]["nic"]; ?></span>
        </div>
        <div>
            <span>Adresse: </span><span>
                <ul>
                    <li><span>Lignes: </span><?php echo $res_vitrine["entreprise"]["adresse"]["lignes"]; ?></li>
                    <li><span>Code postal: </span><?php echo $res_vitrine["entreprise"]["adresse"]["codePostal"]; ?></li>
                    <li><span>Bureau Distributeur: </span><?php echo $res_vitrine["entreprise"]["adresse"]["bureauDistributeur"]; ?></li>
                </ul>
            </span>
        </div>
        <div>
            <span>Type Etablissement: </span><span><?php echo $res_vitrine["entreprise"]["typeEtablissement"]; ?></span>
        </div>
        <div>
            <span>Forme Juridique: </span><span>
                <ul>
                    <li><span>Code: </span><?php echo $res_vitrine["entreprise"]["formeJuridique"]["code"]; ?></li>
                    <li><span>Libelle: </span><?php echo $res_vitrine["entreprise"]["formeJuridique"]["libelle"]; ?></li>
                </ul>
            </span>
        </div>
        <div>
            <span>Statut: </span><span><?php echo $res_vitrine["entreprise"]["statut"]; ?></span>
        </div>
        <div>
            <span>Identifiant Interne: </span><span>
                <ul>
                    <li><span>Code Greffe: </span><?php echo $res_vitrine["entreprise"]["identifiantInterne"]["codeGreffe"]; ?></li>
                    <li><span>Dossier Millesime: </span><?php echo $res_vitrine["entreprise"]["identifiantInterne"]["dossierMillesime"]; ?></li>
                    <li><span>Dossier Statut: </span><?php echo $res_vitrine["entreprise"]["identifiantInterne"]["dossierStatut"]; ?></li>
                    <li><span>Dossier Chrono: </span><?php echo $res_vitrine["entreprise"]["identifiantInterne"]["dossierChrono"]; ?></li>
                </ul>
            </span>
        </div>
    </div>
    <hr />
    <?php
}
if ($dernierStatut == "true") {
    ?>
    <div class="res_content-block">
        <div class="res_content-title">Dernier Statut:</div>
        <div>
            <span>Date Dépôt: </span><span><?php echo $res_vitrine["dernierStatut"]["dateDepot"]; ?></span>
        </div>
        <div>
            <span>Numéro Dépôt: </span><span><?php echo $res_vitrine["dernierStatut"]["numeroDepot"]; ?></span>
        </div>
        <div>
            <span>Numéro Dépôt Interne: </span><span><?php echo $res_vitrine["dernierStatut"]["numeroDepotInterne"]; ?></span>
        </div>
        <div>
            <span>Acte: </span><span>
                <ul>
                    <li><span>Numéro Acte: </span><?php echo $res_vitrine["dernierStatut"]["acte"]["numeroActe"]; ?></li>
                    <li><span>Type Acte: </span><?php echo $res_vitrine["dernierStatut"]["acte"]["typeActe"]; ?></li>
                    <li><span>Document: </span><ul><li><span>Ref: </span><?php echo $res_vitrine["dernierStatut"]["acte"]["document"]["ref"]; ?></li></ul></li>
                </ul>
            </span>
        </div>
    </div>
    <hr />
    <?php
}
if ($bilan == "true") {
    echo "<div>Bilan:</div>";
    print_r($res_vitrine["bilan"]);
}
if ($depotActes == "true") {
    echo "<div>Depot Actes:</div>";
    print_r($res_vitrine["depotActes"]);
}
if ($document_data == "true") {
    echo "<div>Documents:</div>";
    print_r($res_vitrine["document_data"]);
}

if ($commande == "true") {
    ?>
    <div class="res_content-block">
        <div class="res_content-title">Commande:</div>
        <div>
            <span>Référence: </span><span><?php echo $res_commande["reference"]; ?></span>
        </div>
        <div>
            <span>Date Commande: </span><span><?php echo $res_commande["dateCommande"]; ?></span>
        </div>
        <div>
            <span>Document: </span><span>
                <ul>
                    <li><span>Type: </span><?php echo $res_commande["document"]["type"]; ?></li>
                    <li><span>Modes Diffusion: </span><ul><li><span>mode: </span><?php echo $res_commande["document"]["modesDiffusion"]["mode"]; ?></li></ul></li>
                    <li><span>Date: </span><?php echo $res_commande["document"]["date"]; ?></li>
                    <li><span>Id: </span><?php echo $res_commande["document"]["id"]; ?></li>
                </ul>
            </span>
        </div>
        <div>
            <span>Url Access: </span><?php echo "<a href=".$res_commande["urlAccess"]." target=\"_blank\">".$res_commande["urlAccess"]."</a>"; ?></span>
        </div>
    </div>
    <?php
}
