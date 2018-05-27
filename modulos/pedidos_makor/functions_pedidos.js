// JavaScript Document
//Variavel Global
div_loading_pedidos = '<img src="loading.gif" align="absmiddle" /> Carregando...';
var_time_out_pesquisa_cliente = null;
var_time_out_pesquisa_item = null;

function maximizar() {
	window.moveTo (-4,-4);
	window.resizeTo (screen.availWidth + 8, screen.availHeight + 8);
}


function time_out_pesquisa_cliente(id_session){
	if(var_time_out_pesquisa_cliente){
		clearTimeout(var_time_out_pesquisa_cliente);
	}
	var fnc = "pesquisa_cliente_ajax('" + id_session + "','" + document.getElementById('edtcampo_filtro').value + "','" + document.getElementById('edttexto_filtro').value + "')";
	var_time_out_pesquisa_cliente = setTimeout(fnc,1000);
}
function time_out_pesquisa_item(id_session){
	if(var_time_out_pesquisa_item){
		clearTimeout(var_time_out_pesquisa_item);
	}
	var fnc = "pesquisa_item_ajax('" + id_session + "','" + document.getElementById('edtcampo_filtro_item').value + "','" + document.getElementById('edttexto_filtro_item').value + "')";
	var_time_out_pesquisa_item = setTimeout(fnc,1000);
}

function pesquisa_cliente_ajax(id_session,campo_filtro,texto_filtro){
	var url = 'p1_pesq_cli.php';
	var send = '';
	send += 'id_session=' + id_session;
	send += '&';
	send += 'campo_filtro=' + campo_filtro;
	send += '&';
	send += 'texto_filtro=' + texto_filtro;
	var div_principal = 'div_filtro_cli';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById(div_principal).innerHTML = xmlhttp.responseText;
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById(div_principal).innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById(div_principal).innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function pesquisa_item_ajax(id_session,campo_filtro,texto_filtro){
	var url = 'p2_pesq_item.php';
	var send = '';
	send += 'id_session=' + id_session;
	send += '&';
	send += 'campo_filtro=' + campo_filtro;
	send += '&';
	send += 'texto_filtro=' + texto_filtro;
	var div_principal = 'div_filtro_item';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById(div_principal).innerHTML = xmlhttp.responseText;
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById(div_principal).innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById(div_principal).innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function p1_exibe_formlario_cliente(urlget){
	var url = '../clientes/formulario.php' + urlget;
	var send = null;
	var div_principal = 'div_formulario_cliente';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById(div_principal).innerHTML = xmlhttp.responseText;
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById(div_principal).innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById(div_principal).innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function ajax_exibe_pedido_p1(id_session){
	var url = 'p1_seleciona_cliente.php';
	var send = '';
	send += 'id_session=' + id_session;
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById('div_conteudo').innerHTML = xmlhttp.responseText;
	Executa_JS_Ajax('div_conteudo','javascripts');
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById('div_conteudo').innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById('div_conteudo').innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function ajax_exibe_pedido_p2(id_session){
	var url = 'p2_itens.php';
	var send = 'id_session=' + id_session;
	var div_principal = 'div_conteudo';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById(div_principal).innerHTML = xmlhttp.responseText;
	p2_ajax_exibe_tabela_itens(id_session,'','');
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById(div_principal).innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById(div_principal).innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function ajax_exibe_pedido_p3(id_session){
	var url = 'p3_politica_comercial.php';
	var send = 'id_session=' + id_session;
	var div_principal = 'div_conteudo';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById(div_principal).innerHTML = xmlhttp.responseText;
	p2_ajax_exibe_tabela_itens(id_session,'S','S');
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById(div_principal).innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById(div_principal).innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function monta_combo_produtos(id_session,id_familia){
	var url = 'p2_monta_lista_produto.php';
	var send = '';
	send += 'id_familia=' + id_familia;
	send += '&';
	send += 'id_session=' + id_session;
	var div_principal = 'div_combo_produto';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById(div_principal).innerHTML = xmlhttp.responseText;
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById(div_principal).innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById(div_principal).innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function ajax_mostra_det_item(id_session,id_produto){
	var url = 'p2_det_item.php';
	send = '';
	send += 'id_produto=' + id_produto;
	send += '&';
	send += 'id_session=' + id_session;
	var div_principal = 'div_det_item';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById(div_principal).innerHTML = xmlhttp.responseText;
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById(div_principal).innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById(div_principal).innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function p2_ajax_add_item(id_session,id_produto,qtde){
	var url = 'p2_add_item.php';
	var send = 'id_session=' + id_session;
	send += '&';
	send += 'id_produto=' + id_produto;
	send += '&';
	send += 'qtde=' + qtde;
	var div_principal = 'div_det_item';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById(div_principal).innerHTML = xmlhttp.responseText;
	p2_ajax_exibe_tabela_itens(id_session,'');
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById(div_principal).innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById(div_principal).innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function p2_ajax_exibe_tabela_itens(id_session,pread,ppol_comerc,calc_margem){
	var url = 'p2_tabela_itens.php';
	var send = 'id_session=' + id_session;
	if(pread == 'S'){
		send += '&pread=S';
	}
	if(ppol_comerc == 'S'){
		send += '&ppol_comerc=S';
	}
	if(calc_margem == 'S'){
		send += '&pcalc_margem=S';
	}
	var div_principal = 'div_tabela_itens';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById(div_principal).innerHTML = xmlhttp.responseText;
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById(div_principal).innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById(div_principal).innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function p2_remove_item(id_session,id_produto){
	var url = 'p2_remove_item.php';
	var send = '';
	send += 'id_session=' + id_session;
	send += '&';
	send += 'id_produto=' + id_produto;
	var div_principal = 'div_tabela_itens';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	alert(xmlhttp.responseText);
	p2_ajax_exibe_tabela_itens(id_session,'','','');
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById(div_principal).innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========

	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function p2_exibe_prod_conf(id_session,id_produto,id_modelo){
	var url = 'p2_prod_conf.php';
	var send = '';
	send += 'id_session=' + id_session;
	send += '&';
	send += 'id_produto=' + id_produto;
	send += '&';
	send += 'id_modelo=' + id_modelo;
	var div_principal = 'div_prod_conf';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById(div_principal).innerHTML = xmlhttp.responseText;
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById(div_principal).innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById(div_principal).innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function p2_ajax_atualiza_tabela_itens(id_session,show,calc_margem){
	var url = 'p2_atualiza_itens.php';
	var send = '';
	for(i=0;i<document.form_itens.elements.length;i++){
		send += document.form_itens.elements[i].name + '=' + document.form_itens.elements[i].value + '&';
	}
	send += 'id_session=' + id_session;
	
	var div_principal = 'div_tabela_itens';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	if(show != '1'){
		p2_ajax_exibe_tabela_itens(id_session,'','',calc_margem);
	}
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById(div_principal).innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========

	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function ajax_exibe_pedido_p4(id_session){
	var url = 'p4_dados_pedido.php';
	var send = '';
	send += 'id_session=' + id_session;
	send += '&';
	send += 'pread=S';
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById('div_conteudo').innerHTML = xmlhttp.responseText;
	Executa_JS_Ajax('div_conteudo','javascripts');
	p2_ajax_exibe_tabela_itens(id_session,'S','');
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById('div_conteudo').innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById('div_conteudo').innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function ajax_exibe_pedido_p5(id_session){
	var url = 'p5_impressao_pedido.php';
	var send = '';
	send += 'id_session=' + id_session;
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	document.getElementById('div_conteudo').innerHTML = xmlhttp.responseText;
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	document.getElementById('div_conteudo').innerHTML = 'Desculpe, houve problema com s solicitação.';
	}}else{
	//=============On Loading===========
	document.getElementById('div_conteudo').innerHTML = div_loading_pedidos;
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function change_session(session){
	var url = 'change_session.php';
	var send = '';
	send = 'session=' + session;
	send += '&';
	send += 'id_session=' + document.getElementById('edtid_session').value;
	var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
	//===========Ação com a Resposta===================
	//alert(xmlhttp.responseText);	
	//=============Fim Ação com a Resposta===================
	}else{
	//Se a resposta nao for 200	
	alert('Falha ao atualizar campo.');
	}}else{
	//=============On Loading===========
	//===========Fim On Loading===========
	}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");xmlhttp.send(send);
}
function p4_salvar_pedido(id_session){
	Agree = confirm('Todos os dados estão corretos?');
	if(Agree){
		var url = 'p4_salva_pedido.php';
		var send = '';
		send += 'id_session=' + id_session;
		var xmlhttp = XMLHTTPRequest(); xmlhttp.onreadystatechange = function () {if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { if (xmlhttp.status == 200) {
		//===========Ação com a Resposta===================
		document.getElementById('div_btn_salvar').innerHTML = '';
		document.getElementById('btn_salvar').style.display = 'block';
		var xmlDoc = xmlhttp.responseXML;
		var acao = xmlDoc.getElementsByTagName("acao")[0].firstChild.nodeValue;
		if(acao == 'alert'){
			var texto = xmlDoc.getElementsByTagName("texto")[0].firstChild.nodeValue;
			alert(texto);	
		}
		var status = xmlDoc.getElementsByTagName("status")[0].firstChild.nodeValue;
		if(status == '1'){
			var id_relac = getXMLTAG(xmlDoc,'id_relac');
			var cod_emitente = getXMLTAG(xmlDoc,'cod_emitente');
			var cgc = getXMLTAG(xmlDoc,'cgc');
			var natureza = getXMLTAG(xmlDoc,'natureza');
			window.open('../clientes/formulario.php?trans_prosp_cli=S&id_session=' + id_session + '&id_relac=' + id_relac + '&cod_emitente=' + cod_emitente + '&natureza=' + natureza + '&cgc=' + cgc,'popup_pros_cli','location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=100,left=100');
		}
		else if(status == '5'){
			var id_relac = getXMLTAG(xmlDoc,'id_relac');
			var cod_emitente = getXMLTAG(xmlDoc,'cod_emitente');
			var cgc = getXMLTAG(xmlDoc,'cgc');
			var tp = getXMLTAG(xmlDoc,'tp');
			var nr_pedido = getXMLTAG(xmlDoc,'nr_pedido');
			window.location = '?tp=' + tp + '&id_relac=' + id_relac + '&cod_emitente=' + cod_emitente + '&nr_pedido=' + nr_pedido + '&cgc=' + cgc;
		}
		else if(status == '6'){
			var pnumreg = getXMLTAG(xmlDoc,'pnumreg');
			window.location = '?pnumreg=' + pnumreg;
		}
		var Eval = getXMLTAG(xmlDoc,'eval');
		eval(Eval);
		/*
		ajax_exibe_pedido_p4(id_session);
		alert(xmlhttp.responseText);
		if(xmlhttp.responseText == 'Para firmar o orçamento é necessário salvar o prospect como cliente'){
			window.open('modulos/customizacoes/clientes/formulario.php?id_session=' + id_session,'popup_pros_cli','location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=100,left=100');
		}
		*/
		//=============Fim Ação com a Resposta===================
		}else{
		//Se a resposta nao for 200	
		alert('Falha ao atualizar campo.');
		}}else{
		//=============On Loading===========
		document.getElementById('btn_salvar').style.display = 'none';
		document.getElementById('div_btn_salvar').innerHTML = div_loading_pedidos;
		//===========Fim On Loading===========
		}};xmlhttp.open('post', url, true);xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");xmlhttp.setRequestHeader("Pragma", "no-cache");xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");xmlhttp.send(send);
	}
}

function show_line_if_checked(id,show){
	var campo = document.getElementById(id);
	if(campo.checked == true){
		document.getElementById(show).style.display = '';	
	}
	else{
		document.getElementById(show).style.display = 'none';	
	}
}













