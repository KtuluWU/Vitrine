console.log("Auteur: Yun WU");

function get_data() {
    var siren = document.form_siren_saisi.siren.value;
    var url = "./action.php";
    siren = siren_checked(siren);

    if (siren) {
        location.href = url+"?"+"siren="+siren;
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