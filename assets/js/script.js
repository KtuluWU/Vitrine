console.log("Auteur: Yun WU");

function get_data() {
    var siren = document.form_data_saisi.siren.value;
    var nic = document.form_data_saisi.nic.value.replace(/\s+/g, "");
    var documentId = document.form_data_saisi.documentId.value.replace(/\s+/g, "");
    var modesDiffusion = document.form_data_saisi.modesDiffusion.value.replace(/\s+/g, "");
    var email = document.form_data_saisi.email.value.replace(/\s+/g, "");
    var entreprise = document.form_data_saisi.entreprise.checked;
    var dernierStatut = document.form_data_saisi.dernierStatut.checked;
    var bilan = document.form_data_saisi.bilan.checked;
    var depotActes = document.form_data_saisi.depotActes.checked;
    var document_data = document.form_data_saisi.document_data.checked;
    var commande = document.form_data_saisi.commande.checked;
    var url = "./action.php";
    var data = new FormData();
    var msg = document.getElementById("area_responses");

    siren = siren_checked(siren);

    if (siren) {
        data.append('siren', siren);
        data.append('nic', nic);
        if (!entreprise && !dernierStatut && !bilan && !depotActes && !document_data && !commande) {
            swal({
                title: "Échoué!",
                text: "Veuillez choisir au moins une réponse!",
                type: "error"
            })
            return false;
        }else if (commande_check(commande, documentId, modesDiffusion) && modesDiffusion_check(commande, modesDiffusion)) {
            document.getElementById("loading_gif").style.display = "block";
            data.append('documentId', documentId);
            data.append('modesDiffusion', modesDiffusion);
            data.append('email', email);
            data.append('entreprise', entreprise);
            data.append('dernierStatut', dernierStatut);
            data.append('bilan', bilan);
            data.append('depotActes', depotActes);
            data.append('document_data', document_data);
            data.append('commande', commande);

            var ajax = false;
            if (window.XMLHttpRequest) { //Mozilla 浏览器
                ajax = new XMLHttpRequest();
                if (ajax.overrideMimeType) {//设置MiME类别
                    ajax.overrideMimeType("text/xml");
                }
            }
            else if (window.ActiveXObject) { // IE浏览器
                try {
                    ajax = new ActiveXObject("Msxml2.XMLHTTP");
                } catch (e) {
                    try {
                        ajax = new ActiveXObject("Microsoft.XMLHTTP");
                    } catch (e) { }
                }
            }
            if (!ajax) { // 异常，创建对象实例失败
                window.alert("不能创建XMLHttpRequest对象实例.");
                return false;
            }

            //开始发送
            ajax.open("POST", url, true);
            //ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");//HTTP head

            ajax.send(data);

            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4 && ajax.status == 200) {
                    msg.innerHTML = ajax.responseText;
                    document.getElementById("loading_gif").style.display = "none";
                }
            }
        }
    }
}

function siren_checked(siren) {
    siren = siren.replace(/\s+/g, "");
    var len = siren.length;

    if (len == 0) {
        swal({
            title: "Échoué!",
            text: "Siren ne doit pas être vide!",
            type: "error"
        })
        return false;
    }

    if (siren.length > 9) {
        swal({
            title: "Échoué!",
            text: "Siren non disponible!",
            type: "error"
        })
        return false;
    } else {
        for (let i = 0; i < (9 - len); i++) {
            siren = "0" + siren;
        }
    }
    return siren;
}

function commande_check(commande, documentId, modesDiffusion) {
    if (commande) {
        if (!documentId) {
            swal({
                title: "Échoué!",
                text: "Le Document ID est obligatoire pour la requête <Commande>",
                type: "error"
            })
            return false;
        } else if (!modesDiffusion) {
            swal({
                title: "Échoué!",
                text: "Le Mode Diffusion est obligatoire pour la requête <Commande>",
                type: "error"
            })
            return false;
        } else {
            return true;
        }
    } else {
        return true;
    }
}

function modesDiffusion_check(commande, modesDiffusion) {
    if (commande && modesDiffusion != "T" && modesDiffusion != "XL") {
        swal({
            title: "Échoué!",
            text: "Modes de Diffusion non autorisés, ils doivent être \"T\" ou \"XL\" ",
            type: "error"
        })
        return false;
    } else {
        return true;
    }
}