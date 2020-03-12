<?php
ini_set('max_execution_time', '12000');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$data = include '../config.php';
$soapUrl = $data['soapUrl'];
$soapUser = $data['soapUser'];
$soapPassword = $data['soapPassword'];

$siren = $_GET["siren"];
$nic = $_GET["nic"];

class vitrineRequest
{
    public function __construct($siren, $nic)
    {
        $this->siren = $siren;
        if ($nic) {$this->nic = $nic;}
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
    $exception->getMessage();
}

$res_vitrine = json_decode(json_encode($response_vitrine), true);

$entreprise = $res_vitrine["entreprise"];
$dernierStatut = $res_vitrine["dernierStatut"];
$bilans = $res_vitrine["bilan"];
$depotActes = $res_vitrine["depotActes"];
$documents = $res_vitrine["document"];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel='stylesheet' href='../assets/css/style.css'>
    <link rel='stylesheet' href='../assets/vendor/css/sweet-alert.css'>
    <link rel='stylesheet' href='../assets/vendor/css/select.css'>
    <link rel='shortcut icon' href='../assets/data_favicon.png' />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/scale-extreme.css" />
    <title> Vitrine </title>
</head>

<body>
    <div class="container">
        <div class="title">Vitrine</div>
        <div class="subtitle">WebService Commerçant</div>
        <form id="form_res_saisi" name="form_res_saisi" action="" method="POST">
            <div class="content">
                <div class="area left">
                    <div>Requêtes <i class="tip material-icons">info</i></div>
                    <div class="content-input">
                        <div class="input-block">
                            <span class="input">
                                <span class="input_label"><span
                                        class="input_label-content">Siren: <?php echo $siren; ?></span></span>
                            </span>
                        </div>
                        <div>Veuillez choisir des réponses disponibles:</div>
                        <div class="checkbox-block">
                            <?php if ($dernierStatut) { ?>
                            <div class="checkbox">
                                <input type="checkbox" name="dernierStatut" id="dernierStatut" value="dernierStatut">
                                <label for="dernierStatut" class="checkbox_label">Dernier Statut</label>
                            </div>
                            <?php } ?>
                            <?php if ($depotActes) { 
                            ?>
                            <div class="checkbox">
                                <span class="select_block">
                                Dépôt Actes
                                    <select class="select_depotActes" multiple="multiple" name="depotActe">
                                        <?php
                                            foreach($depotActes as $key=>$depotActe) {
                                                echo "<option value=".$depotActe["numeroDepotInterne"].">".($key+1).". Date:".$depotActe["dateDepot"]." Numéro Dépôt Interne:".$depotActe["numeroDepotInterne"]."</option>";
                                            }
                                        ?>
                                    </select>
                                </span>
                            </div>
                            <?php } ?>
                            <?php if ($bilans) { 
                            ?>
                            <div class="checkbox">
                                <span class="select_block">
                                    Bilans
                                    <select class="select_bilans" multiple="multiple" name="bilan">
                                        <?php
                                            foreach($bilans as $key=>$bilan) {
                                                echo "<option value=".$bilan["document"]["ref"].">".($key+1).". Millésime:".$bilan["millesime"]." Numéro Dépôt:".$bilan["numeroDepot"]."</option>";
                                            }
                                        ?>
                                    </select>
                                </span>
                            </div>
                            <?php } ?>
                            <?php if ($documents) { 
                            ?>
                            <div class="checkbox">
                                <span class="select_block">
                                    Documents
                                    <select class="select_documents" multiple="multiple" name="document_data">
                                        <?php
                                            foreach($documents as $key=>$document) {
                                                echo "<option value=".$document["id"]."&".$document["type"]."&".$document["dateDepot"].">".($key+1).". Type:".$document["type"]." Date:".$document["date"]."</option>";
                                            }
                                        ?>
                                    </select>
                                </span>
                            </div>
                            <?php } ?>
                            <?php if ($documents) { ?>
                            <div class="checkbox">
                                <input type="checkbox" name="commande" id="commande" value="commande">
                                <label for="commande" class="checkbox_label">Kabis pour les documents choisis</label>
                            </div>
                            <div class="modesDiffusion" style="display:none">Modes Diffusion:
                                <div class="checkbox">
                                    <input type="checkbox" name="modesDiffusion_T" id="modesDiffusion_T" value="T">
                                    <label for="modesDiffusion_T" class="checkbox_label">Téléchargement</label>
                                </div>
                                <div class="checkbox">
                                    <input type="checkbox" name="modesDiffusion_XL" id="modesDiffusion_XL" value="XL">
                                    <label for="modesDiffusion_XL" class="checkbox_label">XML</label>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="content-button">
                        <input class="button" type="button" value="Envoyer" name="button" id="button"
                            onclick="get_data()">
                    </div>
                </div>
                <div class="area right">
                    <div class="res_title">Réponses</div>
                    <div class="res_content" id="area_responses"></div>
                </div>
            </div>
            <div style="display:none">
                <input name="siren" value="<?php echo $siren ?>">
                <input name="nic" value="<?php echo $nic ?>">
            </div>
        </form>
        <div class="loading">
            <img class="loading_gif" id="loading_gif" alt="Chargement..." src="../assets/loading_gr.gif">
        </div>
    </div>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src='../assets/js/script.js'></script>
    <script src='../assets/vendor/js/jquery-3.4.1.min.js'></script>
    <script src='../assets/vendor/js/sweet-alert.js'></script>
    <script src='../assets/vendor/js/classie.js'></script>
    <script src='../assets/vendor/js/select.js'></script>
    <script>
        tippy('.tip', {
            content: '<div class="tip_content"><div><strong>Vintrine:</strong></div><div>"Siren" obligatoire</div><div>"nic" option</div><div>"DocumentID" non</div><div>"ModesDiffusion" non</div><div>"Email" non</div><hr /><div><strong>Commande:</strong></div><div>"Siren" obligatoire</div><div>"nic" option</div><div>"DocumentID" obligatoire</div><div>"ModesDiffusion" obligatoire</div><div>"Email" option</div></div>',
            placement: 'right-start',
            animation: 'scale-extreme',
            arrow: true,
            inertia: true,
            allowHTML: true
        });


        (function () {
            [].slice.call(document.querySelectorAll('input.input_field')).forEach(function (inputEl) {
                if (inputEl.value.trim() !== '') {
                    classie.add(inputEl.parentNode, 'input-filled');
                }

                // events:
                inputEl.addEventListener('focus', onInputFocus);
                inputEl.addEventListener('blur', onInputBlur);
            });

            function onInputFocus(ev) {
                classie.add(ev.target.parentNode, 'input-filled');
            }

            function onInputBlur(ev) {
                if (ev.target.value.trim() === '') {
                    classie.remove(ev.target.parentNode, 'input-filled');
                }
            }
        })();

        $(document).ready(function() {
            $('.select_bilans').fSelect();
            $('.select_documents').fSelect();
            $('.select_depotActes').fSelect();
            $("input:checkbox[name='commande']").change(function() {
                if (this.checked) {
                    $('.modesDiffusion').show(500);
                } else {
                    $('.modesDiffusion').hide(500);
                }
            });
        })
    </script>
</body>

</html>