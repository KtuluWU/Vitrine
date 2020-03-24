console.log("Auteur: Yun WU");

function get_siren() {
    var siren = document.form_siren.siren.value.replace(/\s+/g, "");
    var nic = document.form_siren.nic.value.replace(/\s+/g, "");
    var url = "./action/vitrine.php";

    siren = siren_checked(siren);

    if (siren) {
        window.location.href = url + "?siren=" + siren + "&nic=" + nic;
    }
}

function get_data() {
    var siren = document.form_res_saisi.siren.value;
    var nic = document.form_res_saisi.nic.value;
    var dernierStatut = document.form_res_saisi.dernierStatut.checked;
    var bilan = document.form_res_saisi.bilan;
    var depotActe = document.form_res_saisi.depotActe;
    var document_data = document.form_res_saisi.document_data;
    var commandes = document.form_res_saisi.commande.checked;
    var modesDiffusion_T = document.form_res_saisi.modesDiffusion_T.checked;
    var modesDiffusion_XL = document.form_res_saisi.modesDiffusion_XL.checked;
    var url = "../action/action.php";
    var data = new FormData();
    var msg = document.getElementById("area_responses");


    var bilans = getSelectValues(bilan);
    var documents = getSelectValues(document_data);
    var depotActes = getSelectValues(depotActe);

    if (!dernierStatut && !bilan && !depotActes && !document_data && !commandes) {
        swal({
            title: "Échoué!",
            text: "Veuillez choisir au moins une réponse!",
            type: "error"
        })
        return false;
    } else {
        document.getElementById("loading_gif").style.display = "block";
        data.append('siren', siren);
        data.append('nic', nic);
        data.append('dernierStatut', dernierStatut);
        data.append('bilans', bilans);
        data.append('depotActes', depotActes);
        data.append('documents_data', documents);
        data.append('commandes', commandes);
        data.append('modesDiffusion_T', modesDiffusion_T);
        data.append('modesDiffusion_XL', modesDiffusion_XL);

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

function siren_checked(siren) {
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

function getSelectValues(select) {
    var result = [];
    var options = select && select.options;
    var opt;

    for (var i = 0, iLen = options.length; i < iLen; i++) {
        opt = options[i];

        if (opt.selected) {
            result.push(opt.value || opt.text);
        }
    }
    return result;
}