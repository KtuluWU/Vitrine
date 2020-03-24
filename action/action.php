<?php
ini_set('max_execution_time', '12000');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$data = include '../config.php';

$soapUrl = $data['soapUrl'];
$soapUser = $data['soapUser'];
$soapPassword = $data['soapPassword'];

@$siren = $_POST["siren"];
@$nic = $_POST["nic"];
@$dernierStatut = $_POST["dernierStatut"];
@$depotActes = $_POST["depotActes"];
@$documents = $_POST["documents_data"];
@$commandes = $_POST["commandes"];
@$bilans = $_POST["bilans"];
@$modesDiffusion_T = $_POST["modesDiffusion_T"];
@$modesDiffusion_XL = $_POST["modesDiffusion_XL"];

$bilans_documentIds = explode(",", $bilans);
$actes_numeroDepotInterne = explode(",", $depotActes);
$documents_pre_r = explode(",", $documents);
$commande_documentIds = array();
$commande_modesDiffusion = array();
$documents_pre2_r = array();
$documentsType = array();
$documentsDateDepot = array();
foreach ($documents_pre_r as $document_pre) {
    array_push($commande_documentIds, substr($document_pre, 0, strpos($document_pre, '&')));
    array_push($documents_pre2_r, substr($document_pre, strpos($document_pre, '&') + 1));
}
foreach ($documents_pre2_r as $documents_pre2) {
    array_push($documentsType, substr($documents_pre2, 0, strpos($documents_pre2, '&')));
    array_push($documentsDateDepot, substr($documents_pre2, strpos($documents_pre2, '&') + 1));
}
if ($modesDiffusion_T == "true" && $modesDiffusion_XL == "false") {
    $commande_modesDiffusion = "T";
} else if ($modesDiffusion_T == "false" && $modesDiffusion_XL == "true") {
    $commande_modesDiffusion = "XL";
} else if ($modesDiffusion_T == "true" && $modesDiffusion_XL == "true") {
    $commande_modesDiffusion = "Array";
}

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
    public function __construct($siren, $nic, $documentId, $modesDiffusion)
    {
        $this->siren = $siren;
        if ($nic) {$this->nic = $nic;}
        $this->documentId = $documentId;
        $this->modesDiffusion = array();
        $this->modesDiffusion["mode"] = $modesDiffusion;
    }
}

$client = new SoapClient($soapUrl, array("login" => $soapUser, "password" => $soapPassword, 'trace' => 1));
$client->__setLocation('https://webservices.infogreffe.fr/services/commercant-service/ws');

$vitrineRequest = new vitrineRequest($siren, $nic);

$params_vitrine = array(
    "vitrineRequest" => $vitrineRequest,
);

try {
    $response_vitrine = $client->__soapCall("vitrine", $params_vitrine);
} catch (SoapFault $exception) {
    $response_vitrine = '<strong> Error Message : </strong>'.$exception->getMessage();
}

$commandeRequest = array();
$commandeRequest2 = array();
$response_commande = array();
$response_commande2 = array();
$res_commande = array();
$res_commande2 = array();
for ($i = 0; $i < count($commande_documentIds); $i++) {
    if ($commande_modesDiffusion != "Array") {
        $commandeRequest[$i] = new commandeRequest($siren, $nic, $commande_documentIds[$i], $commande_modesDiffusion);
        $params_commande = array(
            "commandeRequest" => $commandeRequest[$i],
        );
        try {
            $response_commande[$i] = $client->__soapCall("commande", $params_commande);
        } catch (SoapFault $exception) {
            $response_commande[$i] = '<strong>Mode '.$commande_modesDiffusion.', Document Type '.$documentsType[$i].': </strong>'.$exception->getMessage();
        }
        $res_commande[$i] = json_decode(json_encode($response_commande[$i]), true);
    } else if ($commande_modesDiffusion == "Array") {
        $commandeRequest[$i] = new commandeRequest($siren, $nic, $commande_documentIds[$i], "T");
        $commandeRequest2[$i] = new commandeRequest($siren, $nic, $commande_documentIds[$i], "XL");
        $params_commande = array(
            "commandeRequest" => $commandeRequest[$i],
        );
        $params_commande2 = array(
            "commandeRequest" => $commandeRequest2[$i],
        );
        try {
            $response_commande[$i] = $client->__soapCall("commande", $params_commande);
        } catch (SoapFault $exception) {
            $response_commande[$i] = '<strong> Mode T, Document Type '.$documentsType[$i].': </strong>'.$exception->getMessage();
        }
        try {
            $response_commande2[$i] = $client->__soapCall("commande", $params_commande2);
        } catch (SoapFault $exception) {
            $response_commande2[$i] = '<strong> Mode XL, Document Type '.$documentsType[$i].': </strong>'.$exception->getMessage();
        }
        $res_commande[$i] = json_decode(json_encode($response_commande[$i]), true);
        $res_commande2[$i] = json_decode(json_encode($response_commande2[$i]), true);
    } else {
        array_push($response_commande, "Veuillez choisir au moins un mode diffusion.");
    }
}

$res_commande = array_merge($res_commande, $res_commande2);

$res_vitrine = json_decode(json_encode($response_vitrine), true);

echo "<div class=\"link_to_res_block\">";
echo "<a href=\"#res_vitrine_entreprise\" class=\"link_to_res_part\">Entreprise</a>";
if ($dernierStatut == "true") {echo "<a href=\"#res_vitrine_dernierStatut\" class=\"link_to_res_part\">Dernier Statut</a>";}
if ($bilans) {echo "<a href=\"#res_vitrine_bilan\" class=\"link_to_res_part\">Bilan</a>";}
if ($depotActes) {echo "<a href=\"#res_vitrine_depotActes\" class=\"link_to_res_part\">Dépôt Actes</a>";}
if ($documents) {echo "<a href=\"#res_vitrine_document\" class=\"link_to_res_part\">Document</a>";}
if ($commandes == "true") {echo "<a href=\"#res_commande\" class=\"link_to_res_part\">Commande de Kabis</a>";}
echo "</div>";
?>
<div class="res_content-block">
    <div class="res_content-title" id="res_vitrine_entreprise">Vitrine Entreprise:</div>
    <div class="res_content-donnees">
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
                    <li><span>Libellé: </span><?php echo $res_vitrine["entreprise"]["formeJuridique"]["libelle"]; ?></li>
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
</div>
<?php

if ($dernierStatut == "true") {
    ?>
    <div class="res_content-block">
        <div class="res_content-title" id="res_vitrine_dernierStatut">Vitrine Dernier Statut:</div>
        <div class="res_content-donnees">
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
    </div>
    <?php
}

if ($depotActes) {
    ?>
    <div class="res_content-block">
        <div class="res_content-title" id="res_vitrine_depotActes">Vitrine Dépôt Actes:</div>
        <div class="res_content-donnees">
        <?php
            for ($i = 0; $i < count($res_vitrine["depotActes"]); $i++) {
                if (in_array($res_vitrine["depotActes"][$i]["numeroDepotInterne"], $actes_numeroDepotInterne)) {
        ?>
                <div>
                    <span>Index: </span><span><?php echo $i+1 ?></span>
                </div>
                <div>
                    <span>Date Dépôt: </span><span><?php echo $res_vitrine["depotActes"][$i]["dateDepot"]; ?></span>
                </div>
                <div>
                    <span>Numéro Dépôt: </span><span><?php echo $res_vitrine["depotActes"][$i]["numeroDepot"]; ?></span>
                </div>
                <div>
                    <span>Numéro Dépôt Interne: </span><span><?php echo $res_vitrine["depotActes"][$i]["numeroDepotInterne"]; ?></span>
                </div>
                <div>
                    <?php
                    if (array_key_exists('0', $res_vitrine["depotActes"][$i]["acte"])) {
                        foreach($res_vitrine["depotActes"][$i]["acte"] as $acte) {
                            ?>
                            <span>Acte: </span><span>
                                <ul>
                                    <li><span>Numéro Acte: </span><?php echo $acte["numeroActe"]; ?></li>
                                    <li><span>Type Acte: </span><?php echo $acte["typeActe"]; ?></li>
                                    <?php
                                    if (array_key_exists('decision', $res_vitrine["depotActes"][$i]["acte"])) { 
                                        ?>
                                        <li><span>Décision: </span>
                                            <ul>
                                                <li><span>Nature Décision: </span>
                                                    <ul>
                                                        <li><span>Code: </span><?php echo $acte["decision"]["natureDecision"]["code"]; ?></li>
                                                        <li><span>Libellé: </span><?php echo $acte["decision"]["natureDecision"]["libelle"]; ?></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                    <?php } ?>
                                    <li><span>Document: </span><ul><li><span>Ref: </span><?php echo $acte["document"]["ref"]; ?></li></ul></li>
                                </ul>
                            </span>
                            <?php
                        }
                    } else {
                    ?>
                    <span>Acte: </span><span>
                        <ul>
                            <li><span>Numéro Acte: </span><?php echo $res_vitrine["depotActes"][$i]["acte"]["numeroActe"]; ?></li>
                            <li><span>Type Acte: </span><?php echo $res_vitrine["depotActes"][$i]["acte"]["typeActe"]; ?></li>
                            <?php
                                if (array_key_exists('decision', $res_vitrine["depotActes"][$i]["acte"])) { 
                                    ?>
                                    <li><span>Décision: </span>
                                        <ul>
                                            <li><span>Nature Décision: </span>
                                                <ul>
                                                    <li><span>Code: </span><?php echo $res_vitrine["depotActes"][$i]["acte"]["decision"]["natureDecision"]["code"]; ?></li>
                                                    <li><span>Libellé: </span><?php echo $res_vitrine["depotActes"][$i]["acte"]["decision"]["natureDecision"]["libelle"]; ?></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                <?php } ?>
                            <li><span>Document: </span><ul><li><span>Ref: </span><?php echo $res_vitrine["depotActes"][$i]["acte"]["document"]["ref"]; ?></li></ul></li>
                        </ul>
                    </span><?php } ?>
                </div>
                <hr />
            <?php }} ?>
        </div>
    </div>
    <?php
}

if ($bilans) {
    ?>
    <div class="res_content-block">
        <div class="res_content-title" id="res_vitrine_bilan">Vitrine Bilan:</div>
        <div class="res_content-donnees">
    <?php
            for ($i = 0; $i < count($res_vitrine["bilan"]); $i++) {
                if (in_array($res_vitrine["bilan"][$i]["document"]["ref"], $bilans_documentIds)) {
    ?>
                <div>
                    <span>Index: </span><span><?php echo $i+1 ?></span>
                </div>
                <div>
                    <span>Date Clôture: </span><span><?php echo $res_vitrine["bilan"][$i]["dateCloture"]; ?></span>
                </div>
                <div>
                    <span>Numéro Dépôt: </span><span><?php echo $res_vitrine["bilan"][$i]["numeroDepot"]; ?></span>
                </div>
                <div>
                    <span>Millésime: </span><span><?php echo $res_vitrine["bilan"][$i]["millesime"]; ?></span>
                </div>
                <div>
                    <span>Type Liasse: </span><span><?php echo $res_vitrine["bilan"][$i]["typeLiasse"]; ?></span>
                </div>
                <div>
                    <span>Type Comptes: </span><span><?php echo $res_vitrine["bilan"][$i]["typeComptes"]; ?></span>
                </div>
                <div>
                    <span>Confidentialité: </span><span><?php echo $res_vitrine["bilan"][$i]["confidentialite"]; ?></span>
                </div>
                <div>
                    <span>Document: </span><span>
                        <ul>
                            <li><span>Ref: </span><?php echo $res_vitrine["bilan"][$i]["document"]["ref"]; ?></li>
                        </ul>
                    </span>
                </div>
                <hr />
            <?php }} ?>
        </div>
    </div>
    <?php
}

if ($documents) {
    ?>
    <div class="res_content-block">
        <div class="res_content-title" id="res_vitrine_document">Vitrine Document:</div>
        <div class="res_content-donnees">
    <?php
            for ($i = 0; $i < count($res_vitrine["document"]); $i++) {
                if(in_array($res_vitrine["document"][$i]["id"], $commande_documentIds)) {
    ?>
            <div>
                <span>Index: </span><span><?php echo $i+1 ?></span>
            </div>
            <div>
                <span>Type: </span><span><?php echo $res_vitrine["document"][$i]["type"]; ?></span>
            </div>
            <div>
                <span>Modes Diffusion: </span><span>
                    <ul>
                        <li><span>Mode: </span><?php echo $res_vitrine["document"][$i]["modesDiffusion"]["mode"]; ?></li>
                    </ul>
                </span>
            </div>
            <div>
                <span>Date: </span><span><?php echo $res_vitrine["document"][$i]["date"]; ?></span>
            </div>
            <div>
                <span>Id: </span><span><?php echo $res_vitrine["document"][$i]["id"]; ?></span>
            </div>
            <hr />
            <?php }} ?>
        </div>
    </div>
    <?php
}

if ($commandes == "true") {
        ?>
        <div class="res_content-block">
            <div class="res_content-title" id="res_commande">Commande:</div>
            <div class="res_content-donnees">
                <?php
                for ($i = 0; $i < count($res_commande); $i++) {
                    if (in_array($res_commande[$i]["document"]["id"], $commande_documentIds)) {
                    ?>
                    <div>
                        <span>Référence: </span><span><?php echo $res_commande[$i]["reference"]; ?></span>
                    </div>
                    <div>
                        <span>Date Commande: </span><span><?php echo $res_commande[$i]["dateCommande"]; ?></span>
                    </div>
                    <div>
                        <span>Montant: </span><span>
                            <ul>
                                <li><span>HT: </span><?php echo $res_commande[$i]["montant"]["ht"]; ?></li>
                                <li><span>TVA: </span><?php echo $res_commande[$i]["montant"]["tva"]; ?></li>
                                <li><span>TTC: </span><?php echo $res_commande[$i]["montant"]["ttc"]; ?></li>
                            </ul>
                        </span>
                    </div>
                    <div>
                        <span>Document: </span><span>
                            <ul>
                                <li><span>Type: </span><?php echo $res_commande[$i]["document"]["type"]; ?></li>
                                <li><span>Modes Diffusion: </span><ul><li><span>mode: </span><?php echo $res_commande[$i]["document"]["modesDiffusion"]["mode"]; ?></li></ul></li>
                                <li><span>Date: </span><?php echo $res_commande[$i]["document"]["date"]; ?></li>
                                <li><span>Id: </span><?php echo $res_commande[$i]["document"]["id"]; ?></li>
                            </ul>
                        </span>
                    </div>
                    <div>
                        <span>Url Access: </span><?php echo "<a href=".$res_commande[$i]["urlAccess"]." target=\"_blank\">".$res_commande[$i]["urlAccess"]."</a>"; ?></span>
                    </div>
                    <hr />
                <?php } else {
                ?>
                        <div>
                            <span><?php echo $res_commande[$i]; ?></span>
                        </div>
                        <hr />
                <?php
                }} ?>
            </div>
        </div>
        <?php
}