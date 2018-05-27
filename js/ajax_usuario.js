function XMLHTTPRequest() {
    try {
        return new XMLHttpRequest();
    } catch(ee) {
        try {
            return new ActiveXObject("Msxml2.XMLHTTP");
        } catch(e) {
            try {
                return new ActiveXObject("Microsoft.XMLHTTP");
            } catch(E) {
                return false;
            }
        }
    }
}



function valida_usuario() {

    var ajaxRec = XMLHTTPRequest();
    var div = document.getElementById("divConteudo");
    var resp, erro;


    ajaxRec.open("POST", ("modulos/usuarios/autentica.php"), true);
    ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxRec.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    ajaxRec.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    ajaxRec.setRequestHeader("Pragma", "no-cache");
    ajaxRec.onreadystatechange = function() {
        if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde já processou
            div.innerHTML = '<font face="Verdana" size="1">Aguarde processando...</font>';
        }
        if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde já processou
            resp = ajaxRec.responseText;
            erro = resp.substring(0,4);
            if ((erro == "erro")) {
                alert('Usuário ou senha inválido !');
            } else {
                $("#divConteudo").html(resp);
            }
        }
    }

    ajaxRec.send('edtusuario='+document.getElementById('edtusuario').value+

        '&edtsenha='+document.getElementById('edtsenha').value+

        '&edtfuncaoini='+document.getElementById('edtfuncaoini').value+

        '&edtnumregini='+document.getElementById('edtnumregini').value);



}