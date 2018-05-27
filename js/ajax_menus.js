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


function exibe_programa(url, id_div) {
    var div;
    if (id_div == null ) {
    	div = $("#div_programa");
    }else {
    	div = $("#"+id_div);
    }

    if(url.indexOf('operacao=filtrar') == -1){
        var send = null;
    }else if(id_div == null && $("#cbxfiltro").length > 0 && $("#cbxfiltro").val() != ''){
        var send = '';
        send += 'cbxfiltro=' + $("#cbxfiltro").val();
        send += '&descr_filtro=' + $("#descr_filtro").val();
        send += '&cbxordem=' + $("#cbxordem").val();
        send += '&sql_filtro=' + $("#sql_filtro").val();
        send += '&edtfiltro=' + $("#edtfiltro").val();
    }else{
        var send = null;
    }

    $.ajax({
        url:url,
        global: true,
        type: "POST",
        data:send,
        dataType: "html",
        async: true,
        beforeSend:function(){
            $('html, body').animate({scrollTop:0}, 'fast');
            
            div.html('<div align="center"><img src="images/wait.gif" align="absmiddle" /></div>');
        },
        error: function(){
            alert('Erro com a requisição');
        },
        success: function(responseText){

         div.html(responseText);
        }
    });
}




function exibe_programa_off(nome_funcao, nome_div) {

var ajaxRec = XMLHTTPRequest();
var div;

if (nome_div == null ) {
	div = document.getElementById("div_programa")
} else {
	div = document.getElementById(nome_div)
}

ajaxRec.open("POST", (nome_funcao), true);
ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
ajaxRec.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
ajaxRec.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
ajaxRec.setRequestHeader("Pragma", "no-cache");

  ajaxRec.onreadystatechange = function() {
    if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = '<div align="center"><img src="images/wait.gif" align="absmiddle" /></div>';
    }
    if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = ajaxRec.responseText;
	   if(document.getElementById('div_rec_js')){
	   	   Executa_JS_Ajax("div_relatorio_pedidos","div_rec_js");
	   }
    }
  }

  if (nome_funcao.indexOf('operacao=filtrar') == -1 ) {
    ajaxRec.send(null);
  } else {
    if (nome_div == null ) {
       ajaxRec.send('cbxfiltro='+document.getElementById('cbxfiltro').value+
                 '&edtfiltro='+document.getElementById('edtfiltro').value+
                 '&descr_filtro='+document.getElementById('descr_filtro').value+
                 '&cbxordem='+document.getElementById('cbxordem').value+
                 '&sql_filtro='+document.getElementById('sql_filtro').value);
	} else {
       ajaxRec.send(null);
	}
  }
;
}


function exibe_programa_pai(nome_funcao, document, nome_div) {
  if (!opener.closed) {
	  if (window.opener.document.getElementById("btnfiltrar") != null) {
          window.opener.document.getElementById("btnfiltrar").click();
	  }
  }
}

function exibe_programa_vazio() {
      var div = document.getElementById("div_programa");
      div.innerHTML = '<div align="center"></div>';
}


function lupa(id_campo, id_funcao) {

var ajaxRec = XMLHTTPRequest();
var cRet,nPos, cCod, cDescr;

ajaxRec.open("POST", ('lupa.php?pid_campo='+id_campo+'&pid_funcao='+id_funcao), true);
ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
ajaxRec.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
ajaxRec.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
ajaxRec.setRequestHeader("Pragma", "no-cache");

  ajaxRec.onreadystatechange = function() {
    if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       //div.innerHTML = '<font face="Verdana" size="1">Aguarde processando...</font>';
    }
    if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       cRet = ajaxRec.responseText;
       nPos = cRet.indexOf('@');
       cCod = cRet.substr(0,nPos);
       cDescr = cRet.substr(nPos+1,cRet.length);
       if (nPos == 2) {
         alert('Registro não encontrado !');
       } else {
         document.getElementById('edt'+id_campo).value = cCod;
         document.getElementById('edtdescr'+id_campo).value = cDescr;
       }
    }
  }

  ajaxRec.send('conteudo='+document.getElementById('edtdescr'+id_campo).value+
               '&conteudocod='+document.getElementById('edt'+id_campo).value);
;
}


function calendario_mensal() {

var ajaxRec = XMLHTTPRequest();
var div = document.getElementById("div_programa");

ajaxRec.open("POST", "calendario_agenda.php", true);
ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
ajaxRec.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
ajaxRec.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
ajaxRec.setRequestHeader("Pragma", "no-cache");

  ajaxRec.onreadystatechange = function() {
    if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = '<div align="center"><img src="images/wait.gif" align="absmiddle" /></div>';
    }
    if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = ajaxRec.responseText;
    }
  }

    ajaxRec.send('mes_sel='+document.getElementById('edtmes_sel').value+
                 '&usu_sel='+document.getElementById('edtusu_sel').value+
                 '&ano_sel='+document.getElementById('edtano_sel').value);
;
}

function exibe_analise_vendas(parametros) {

var ajaxRec = XMLHTTPRequest();
var div = document.getElementById("div_programa");

ajaxRec.open("POST", ('modulos/analise_vendas/analise_vendas.php'), true);
ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  ajaxRec.onreadystatechange = function() {
    if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = '<div align="center"><img src="images/wait.gif" align="absmiddle" /></div>';
    }
    if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = ajaxRec.responseText;
    }
  }

  if (parametros.indexOf('operacao=filtrar') == -1 ) {
    ajaxRec.send(null);
  } else {
    ajaxRec.send('cbxvisao='+document.getElementById('cbxvisao').value+
                 '&cbxlinha='+document.getElementById('cbxlinha').value+
                 '&cbxcoluna='+document.getElementById('cbxcoluna').value+
                 '&cbxvalor='+document.getElementById('cbxvalor').value+
                 '&descr_filtro='+document.getElementById('descr_filtro').value+
                 '&cbxordeml='+document.getElementById('cbxordeml').value+
                 '&cbxordemc='+document.getElementById('cbxordemc').value+
                 '&sql_filtro='+document.getElementById('sql_filtro').value);

  }
;
}


function exibe_analise_graf_vendas_valores(parametros) {

var ajaxRec = XMLHTTPRequest();
var div = document.getElementById("div_programa");

ajaxRec.open("POST", ('modulos/analise_vendas/analise_graf_vendas_valores.php'), true);
ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  ajaxRec.onreadystatechange = function() {
    if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = '<div align="center"><img src="images/wait.gif" align="absmiddle" /></div>';
    }
    if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = ajaxRec.responseText;
    }
  }

  if (parametros.indexOf('operacao=filtrar') == -1 ) {
    ajaxRec.send(null);
  } else {
    ajaxRec.send('cbxvisao='+document.getElementById('cbxvisao').value+
                 '&tipografico='+document.getElementById('tipografico').value+
                 '&cbxcoluna='+document.getElementById('cbxcoluna').value+
                 '&edtcliente='+document.getElementById('edtcliente').value+
                 '&edtvendedor='+document.getElementById('edtvendedor').value+
                 '&edtdtini='+document.getElementById('edtdtini').value+
                 '&edtdtfim='+document.getElementById('edtdtfim').value);
  }
;
}


function exibe_analise_graf_vendas_estatistica(parametros) {

var ajaxRec = XMLHTTPRequest();
var div = document.getElementById("div_programa");

ajaxRec.open("POST", ('modulos/analise_vendas/analise_vendas_estatistica.php'), true);
ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  ajaxRec.onreadystatechange = function() {
    if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = '<div align="center"><img src="images/wait.gif" align="absmiddle" /></div>';
    }
    if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = ajaxRec.responseText;
    }
  }

  if (parametros.indexOf('operacao=filtrar') == -1 ) {
    ajaxRec.send(null);
  } else {
    ajaxRec.send('cbxfiltro='+document.getElementById('cbxfiltro').value+
                 '&cbxvisao='+document.getElementById('cbxvisao').value+
                 '&tipografico='+document.getElementById('tipografico').value);
  }
;
}

function exibe_analise_projecao(parametros) {

var ajaxRec = XMLHTTPRequest();
var div = document.getElementById("div_programa");

ajaxRec.open("POST", ('modulos/analise_vendas/analise_projecao.php'), true);
ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  ajaxRec.onreadystatechange = function() {
    if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = '<div align="center"><img src="images/wait.gif" align="absmiddle" /></div>';
    }
    if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = ajaxRec.responseText;
    }
  }

  if (parametros.indexOf('operacao=filtrar') == -1 ) {
    ajaxRec.send(null);
  } else {
    ajaxRec.send('cbxfiltro='+document.getElementById('cbxfiltro').value+
                 '&cbxvisao='+document.getElementById('cbxvisao').value+
                 '&cbxmes='+document.getElementById('cbxmes').value+
                 '&cbxvend='+document.getElementById('cbxvend').value);
  }
;
}


function exibe_analise_graf_vendas_mes(parametros) {

var ajaxRec = XMLHTTPRequest();
var div = document.getElementById("div_programa");

ajaxRec.open("POST", ('modulos/analise_vendas/analise_graf_vendas_mes.php'), true);
ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  ajaxRec.onreadystatechange = function() {
    if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = '<div align="center"><img src="images/wait.gif" align="absmiddle" /></div>';
    }
    if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = ajaxRec.responseText;
    }
  }

  if (parametros.indexOf('operacao=filtrar') == -1 ) {
    ajaxRec.send(null);
  } else {
    ajaxRec.send('cbxfiltro='+document.getElementById('cbxfiltro').value+
                 '&cbxvisao='+document.getElementById('cbxvisao').value+
                 '&tipografico='+document.getElementById('tipografico').value);
  }
;
}

function exibe_analise_graf_vendas_ano(parametros) {

var ajaxRec = XMLHTTPRequest();
var div = document.getElementById("div_programa");

ajaxRec.open("POST", ('modulos/analise_vendas/analise_graf_vendas_ano.php'), true);
ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  ajaxRec.onreadystatechange = function() {
    if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = '<div align="center"><img src="images/wait.gif" align="absmiddle" /></div>';
    }
    if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = ajaxRec.responseText;
    }
  }

  if (parametros.indexOf('operacao=filtrar') == -1 ) {
    ajaxRec.send(null);
  } else {
    ajaxRec.send('cbxvisao='+document.getElementById('cbxvisao').value+
                 '&tipografico='+document.getElementById('tipografico').value);
  }
;
}

function exibe_analise_graf_vendas_estado(parametros) {

var ajaxRec = XMLHTTPRequest();
var div = document.getElementById("div_programa");

ajaxRec.open("POST", ('modulos/analise_vendas/analise_graf_vendas_estado.php'), true);
ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  ajaxRec.onreadystatechange = function() {
    if (ajaxRec.readyState == 1) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = '<div align="center"><img src="images/wait.gif" align="absmiddle" /></div>';
    }
    if (ajaxRec.readyState == 4) { // 1=Estado onde ainda esta processando, 4=Estado onde j� processou
       div.innerHTML = ajaxRec.responseText;
    }
  }

  if (parametros.indexOf('operacao=filtrar') == -1 ) {
    ajaxRec.send(null);
  } else {
    ajaxRec.send('cbxfiltro='+document.getElementById('cbxfiltro').value+
                 '&cbxvisao='+document.getElementById('cbxvisao').value);
  }
;
}