function Executa_JS_Ajax(){
    var scripts = document.getElementById('div_conteudo').getElementsByTagName("script");
    for(i = 0; i < scripts.length; i++){
        var conteudo = document.getElementById("javascripts");
        var newElement = document.createElement("script");
        newElement.text = scripts[i].innerHTML;
        conteudo.appendChild(newElement);
    }
}
function ajax_add_item(id_session){
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            if (xmlhttp.status == 200) {
                if(xmlhttp.responseText != ''){
                    alert(xmlhttp.responseText);
                }
                document.getElementById('itens').innerHTML = xmlhttp.responseText;
            }
        }
    };
    xmlhttp.open('post', 'tabela_itens.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlhttp.send('edtid_session=' + id_session + '&id_produto=' + document.getElementById('edtid_produto').value + '&qtd=' + document.getElementById('edtid_qtde').value);
}
function change_session_itens(session){
    var url = 'change_session.php';
    var send = '';
    send = 'session=' + session;
    send += '&';
    send += 'id_session=' + document.getElementById('edtid_session').value;
    var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            if (xmlhttp.status == 200) {
                document.getElementById('itens').innerHTML = xmlhttp.responseText;
            }else{
                alert('Falha ao atualizar campo.');
            }
        }
    };xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function ajax_salva_dados(id_session){
    Agree = confirm('Todos os itens estao corretos?');
    if(Agree){
        var send = '';
        for(i=0;i<document.itens.elements.length;i++){
            NewString = document.itens.elements[i].value;
            NewString = NewString.replace(/\&/g, "edte_comercial");
            NewString = NewString.replace(/\+/g, "edtmais");
            NewString = NewString.replace(/\=/g, "edtigual");
            send += document.itens.elements[i].name + '=' + NewString + '&';
        }
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                if (xmlhttp.status == 200) {
                    eval(xmlhttp.responseText);
                }
            }
        };
        xmlhttp.open('post', 'salva_dados.php', true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send(send + 'edtid_session=' + id_session);
    }
}
function deletar_itens(id_produto,id_session,id_padrao){
        Agree = confirm('Deletar este produto?');
    if(Agree){
        var xmlhttp = XMLHTTPRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
                if (xmlhttp.status == 200) {
                    document.getElementById('itens').innerHTML = xmlhttp.responseText;
                }
            }
        };
        xmlhttp.open('post', 'remove_item.php', true);
        xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
        xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
        xmlhttp.setRequestHeader("Pragma", "no-cache");
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send('id_produto_delete=' + id_produto + '&edtid_session=' + id_session);
    }
}
function busca_forn(session_id,procura,retorno,sessao,tabela,qtd){
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            if (xmlhttp.status == 200) {
                if(xmlhttp.responseText != 'barras' && xmlhttp.responseText != 'b' && xmlhttp.responseText != ''){
                    retorno.value = xmlhttp.responseText;
                    atualizar_campo(document.getElementById('edtid_session').value,sessao,procura.value);
                } else if(xmlhttp.responseText == 'b'){
                    alert('Este cliente esta bloqueado.\nVeja mais detalhes indo no cadastro do cliente.');
                    procura.value = '';
                    retorno.value = '';
                    procura.focus();
                } else if(xmlhttp.responseText == 'barras'){
                    //alert(procura.value.substring(2,6));
                    //alert((procura.value.substring(7,12)*1)/100);
                    qtd.value = (procura.value.substring(7,12)*1)/1000;
                    procura.value = procura.value.substring(2,6);
                    //qtd.value = (procura.value.substring(7,12)*1)/1000;
                    //retorno.value = '';
                    ajax_add_item_pedido(session_id);
                    retorno.value = '';
                    qtd.value = '';
                    procura.value = '';
                    setTimeout(procura.focus(),1000);
                }else  {
                    alert('Codigo nao cadastrado.');
                    procura.value = '';
                    retorno.value = '';
                    procura.focus();
                }
            }
        }
    };
    xmlhttp.open('post', 'busca_fornecedor.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlhttp.send('edtcodigo=' + procura.value + '&tabela=' + tabela + '&session_id='+session_id);
}
function atualizar_campo(id_session,Campo,Valor){
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            if (xmlhttp.status == 200) {
            }
        }
    };
    xmlhttp.open('post', 'atualiza_campo.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlhttp.send('edtid_session='+id_session+'&campo='+Campo+'&valor='+Valor);
}
function atualizar_campo_din(id_session,campo,valor,val_chave){
    var xmlhttp = XMLHTTPRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            if (xmlhttp.status == 200) {
            }
        }
    };
    xmlhttp.open('post', 'atualiza_itens.php', true);
    xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
    xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
    xmlhttp.setRequestHeader("Pragma", "no-cache");
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlhttp.send('edtid_session='+id_session+'&campo='+campo+'&valor='+valor+'&val_chave='+val_chave);
}