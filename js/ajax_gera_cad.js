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

function gera_cad_excluir(nome_funcao) {

    var ajaxRec = XMLHTTPRequest();
    var div = document.getElementById("div_programa");

    ajaxRec.open("POST", (nome_funcao), true);
    ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxRec.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    ajaxRec.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    ajaxRec.setRequestHeader("Pragma", "no-cache");

    ajaxRec.onreadystatechange = function() {
        if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
        //div.innerHTML = '<font face="Verdana" size="1">Aguarde processando...</font>';
        }
        if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
            alert(ajaxRec.responseText);
        }
    }

    ajaxRec.send(null);
;
}

function gera_cad_tool_post() {

    var ajaxRec = XMLHTTPRequest();
    var div = document.getElementById("div_programa");

    ajaxRec.open("POST", 'gera_cad_tool_post.php', true);
    ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxRec.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    ajaxRec.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    ajaxRec.setRequestHeader("Pragma", "no-cache");

    ajaxRec.onreadystatechange = function() {
        if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
        //div.innerHTML = '<font face="Verdana" size="1">Aguarde processando...</font>';
        }
        if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
            alert(ajaxRec.responseText);
        }
    }

    ajaxRec.send('edtid_modulo='+document.getElementById('edtid_modulo').value+
        '&edtid_funcao='+document.getElementById('edtid_funcao').value+
        '&edtnome_funcao='+document.getElementById('edtnome_funcao').value+
        '&edtgrupo='+document.getElementById('edtgrupo').value+
        '&edttabelabd='+document.getElementById('edttabelabd').value);
;
}

function gera_bd_tool_post() {

    var ajaxRec = XMLHTTPRequest();
    var div = document.getElementById("div_programa");

    ajaxRec.open("POST", 'gera_bd_tool_post.php', true);
    ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxRec.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    ajaxRec.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    ajaxRec.setRequestHeader("Pragma", "no-cache");

    ajaxRec.onreadystatechange = function() {
        if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
        //div.innerHTML = '<font face="Verdana" size="1">Aguarde processando...</font>';
        }
        if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
            alert(ajaxRec.responseText);
        }
    }

    ajaxRec.send('edtid_funcao='+document.getElementById('edtid_funcao').value);
;
}

function gera_tool_extract_post() {

    var ajaxRec = XMLHTTPRequest();
    var div = document.getElementById("div_programa");

    ajaxRec.open("POST", 'gera_tool_extract_post.php', true);
    ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxRec.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    ajaxRec.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    ajaxRec.setRequestHeader("Pragma", "no-cache");

    ajaxRec.onreadystatechange = function() {
        if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
        //div.innerHTML = '<font face="Verdana" size="1">Aguarde processando...</font>';
        }
        if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
            document.getElementById('edtsql').value = ajaxRec.responseText;
        }
    }

    ajaxRec.send('edtid_funcao='+document.getElementById('edtid_funcao').value+'&edtbanco='+document.getElementById('edtbanco').value);
;
}

function gera_copia_tool_post() {

    var ajaxRec = XMLHTTPRequest();
    var div = document.getElementById("div_programa");

    ajaxRec.open("POST", 'gera_copia_tool_post.php', true);
    ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxRec.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    ajaxRec.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    ajaxRec.setRequestHeader("Pragma", "no-cache");

    ajaxRec.onreadystatechange = function() {
        if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
        //div.innerHTML = '<font face="Verdana" size="1">Aguarde processando...</font>';
        }
        if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
            alert(ajaxRec.responseText);
        }
    }

    ajaxRec.send('edtid_funcao='+document.getElementById('edtid_funcao').value+
        '&edtnova_funcao='+document.getElementById('edtnova_funcao').value);
;
}



function muda_senha_post() {

    var ajaxRec = XMLHTTPRequest();
    var div = document.getElementById("div_programa");

    ajaxRec.open("POST", 'muda_senha_post.php', true);
    ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxRec.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    ajaxRec.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    ajaxRec.setRequestHeader("Pragma", "no-cache");

    ajaxRec.onreadystatechange = function() {
        if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
        //div.innerHTML = '<font face="Verdana" size="1">Aguarde processando...</font>';
        }
        if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
            alert(ajaxRec.responseText);
        }
    }

    ajaxRec.send('edtsenha='+document.getElementById('edtsenha').value+
        '&edtsenhanova='+document.getElementById('edtsenhanova').value+
        '&edtsenhaconf='+document.getElementById('edtsenhaconf').value);
;
}

function set_valor_padrao_sql_ajax(sql_busca,conteudos_busca,campos_atualizar,sn_lupa_popup) {
    var AJAX = XMLHTTPRequest();

    var ar_conteudos_busca = conteudos_busca.split(';');
    var conteudos_busca_trat = '';

    for (i=0;i<ar_conteudos_busca.length;i++){
        if (sn_lupa_popup=='1') {
            conteudos_busca_trat += window.opener.document.getElementById('edt'+ar_conteudos_busca[i]).value+';';
        } else {
            conteudos_busca_trat += document.getElementById('edt'+ar_conteudos_busca[i]).value+';';
        }
    }

    var send = '';
    send += 'sql_busca=' + sql_busca;
    send += '&conteudos_busca=' + conteudos_busca_trat;
    send += '&campos_atualizar=' + campos_atualizar;

    AJAX.open("POST", "gera_cad_busca_ajax.php", false);
    AJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    AJAX.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    AJAX.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");


    AJAX.setRequestHeader("Pragma", "no-cache");
    AJAX.send(send);
    var resp = AJAX.responseText;
    var ar_resp = resp.split(';');
    var ar_atualizar = campos_atualizar.split(';');
    var cRet = '';

    for (i=0;i<ar_atualizar.length;i++){
        if ((ar_resp[i]) != undefined) {
            cRet = ar_resp[i];
        } else {
            cRet = '';
        }
        if (sn_lupa_popup=='1') {
            window.opener.document.getElementById('edt'+ar_atualizar[i]).value = cRet;
        } else {
            document.getElementById('edt'+ar_atualizar[i]).value = cRet;
        }
    }
;
}

function set_combo_sql_ajax(sql_busca,conteudos_busca,campo_atualizar) {
    var AJAX = XMLHTTPRequest();
    var ar_conteudos_busca = conteudos_busca.split(';');
    var conteudos_busca_trat = '';

    for (i=0;i<ar_conteudos_busca.length;i++){
        conteudos_busca_trat += document.getElementById('edt'+ar_conteudos_busca[i]).value+';';
    }

    var send = '';
    send += 'sql_busca=' + sql_busca;
    send += '&conteudos_busca=' + conteudos_busca_trat;
    send += '&campos_atualizar=' + campo_atualizar;

    AJAX.open("POST", "gera_cad_monta_combo_ajax.php", false);
    AJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    AJAX.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    AJAX.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
    AJAX.setRequestHeader("Pragma", "no-cache");
    AJAX.send(send);
    var resp = AJAX.responseText;
    var ar_resp = resp.split(';');
    var cRet = '';
    var combo = document.getElementById('edt'+campo_atualizar);
    combo.options.length=1;
    combo.options[1]=new Option('', '', false, false);
    for (i=0;i<ar_resp.length;i++){
        if ((ar_resp[i]) != undefined) {
            cRetCombo = ar_resp[i];
            var ar_resp_combo = cRetCombo.split('@descr_combo@');
            if ((ar_resp_combo[1]) != undefined) {
                combo.options[i+1]=new Option(ar_resp_combo[1], ar_resp_combo[0], false, false);
            }
        }

    }
;
}

