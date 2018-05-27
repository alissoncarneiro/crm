function Contar(fd,tamanho,Mostrar){
	(fd.value.length>tamanho) ? fd.value=fd.value.substring(0,tamanho) : document.getElementById(Mostrar).childNodes[0].data=(tamanho-fd.value.length);
}
function ajax_exibe_pedido_p1(){
	//Executa a fun��o objetoXML()
	var xmlhttp = XMLHTTPRequest(); 
	xmlhttp.onreadystatechange = function () {
		//Se a requisi��o estiver completada
		if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
		//Se o status da requisi��o estiver OK
			if (xmlhttp.status == 200) {
				document.getElementById('div_conteudo').innerHTML = xmlhttp.responseText;
				document.getElementById('div_topo_menu').innerHTML = '<img src="topop1.jpg" width="700" height="60" border="0" usemap="#map_pedido_topo_menu" />';
				Executa_JS_Ajax();
			}
		}
		else{
			   document.getElementById('div_conteudo').innerHTML = '<div align="center" valign="center"><img src="../../../images/wait.gif" align="absmiddle" /></div>';
		}
	};
	xmlhttp.open('post', 'p1_pedido.php', true);
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
	xmlhttp.setRequestHeader("Pragma", "no-cache");
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
	xmlhttp.send(null);
}
function ajax_exibe_pedido_p2(){
	//Executa a fun��o objetoXML()
	var xmlhttp = XMLHTTPRequest(); 
	xmlhttp.onreadystatechange = function () {
		//Se a requisi��o estiver completada
		if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
		//Se o status da requisi��o estiver OK
			if (xmlhttp.status == 200) {
				document.getElementById('div_conteudo').innerHTML = xmlhttp.responseText;
				document.getElementById('div_topo_menu').innerHTML = '<img src="topop2.jpg" width="700" height="60" border="0" usemap="#map_pedido_topo_menu" />';
				Executa_JS_Ajax();
				ajax_exibe_itens_pedido();
			}
		}
		else{
			   document.getElementById('div_conteudo').innerHTML = '<div align="center" valign="center"><img src="../../../images/wait.gif" align="absmiddle" /></div>';
		}
	};
	xmlhttp.open('post', 'p2_pedido.php', true);
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
	xmlhttp.setRequestHeader("Pragma", "no-cache");
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
	xmlhttp.send(null);
}
function ajax_exibe_pedido_p3(){

    //Executa a fun��o objetoXML()
	var xmlhttp = XMLHTTPRequest(); 
	xmlhttp.onreadystatechange = function () {
		//Se a requisi��o estiver completada
		if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
		//Se o status da requisi��o estiver OK
			if (xmlhttp.status == 200) {
				document.getElementById('div_conteudo').innerHTML = xmlhttp.responseText;
				document.getElementById('div_topo_menu').innerHTML = '<img src="topop3.jpg" width="700" height="60" border="0" usemap="#map_pedido_topo_menu" />';
				Executa_JS_Ajax();
			}
		}
		else{
			   document.getElementById('div_conteudo').innerHTML = '<div align="center" valign="center"><img src="../../../images/wait.gif" align="absmiddle" /></div>';
		}
	};
	xmlhttp.open('post', 'p3_dados_pedido.php', true);
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
	xmlhttp.setRequestHeader("Pragma", "no-cache");
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
	xmlhttp.send(null);
}
function ajax_exibe_pedido_p4(){
	//Executa a fun��o objetoXML()
	var xmlhttp = XMLHTTPRequest(); 
	xmlhttp.onreadystatechange = function () {
		//Se a requisi��o estiver completada
		if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
		//Se o status da requisi��o estiver OK
			if (xmlhttp.status == 200) {
				document.getElementById('div_conteudo').innerHTML = xmlhttp.responseText;
				Executa_JS_Ajax();
			}
		}
		else{
			   document.getElementById('div_conteudo').innerHTML = '<div align="center" valign="center"><img src="../../../images/wait.gif" align="absmiddle" /></div>';
		}
	};
	xmlhttp.open('post', 'p4_pedido.php', true);
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
	xmlhttp.setRequestHeader("Pragma", "no-cache");
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
	xmlhttp.send(null);
}
function ajax_exibe_dados_cliente(){
	//Executa a fun��o objetoXML()
	var xmlhttp = XMLHTTPRequest(); 
	xmlhttp.onreadystatechange = function () {
		//Se a requisi��o estiver completada
		if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
		//Se o status da requisi��o estiver OK
			if (xmlhttp.status == 200) {
				document.getElementById('div_dados_cliente').innerHTML = xmlhttp.responseText;
			}
		}
		else{
			   document.getElementById('div_dados_cliente').innerHTML = '<div align="center" valign="center"><img src="../../../images/wait.gif" align="absmiddle" /></div>';
		}
	};
	xmlhttp.open('post', 'p1_ajax_dados_cliente.php', true);
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
	xmlhttp.setRequestHeader("Pragma", "no-cache");
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
	xmlhttp.send('id_pessoa=' + document.getElementById('edtid_empresa').value);
}
function ajax_salva_pedido(){
	Agree = confirm('Todos os dados est�o corretos?');
	if(Agree){
		var send = '';
		for(i=0;i<document.p3_dados_pedido.elements.length;i++){
			NewString = document.p3_dados_pedido.elements[i].value;
			NewString = NewString.replace(/\&/g, "edte_comercial");
			NewString = NewString.replace(/\+/g, "edtmais");
			NewString = NewString.replace(/\=/g, "edtigual");
			send += document.p3_dados_pedido.elements[i].name + '=' + NewString + '&';
		}
		//var send = '';
	
		//Executa a fun��o objetoXML()
		var xmlhttp = XMLHTTPRequest(); 
		xmlhttp.onreadystatechange = function () {
			//Se a requisi��o estiver completada
			if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
			//Se o status da requisi��o estiver OK
				if (xmlhttp.status == 200) {
					eval(xmlhttp.responseText);
					//if(xmlhttp.responseText != 'J� existe ocorr�ncia do n�mero de pedido do propagandista.'){
						//window.opener.close();
						//window.close();
					//}
					//document.getElementById('btn_salvar_pedido').style.display = 'none';
				}
			}
			//else{
			//	   document.getElementById('div_dados_cliente').innerHTML = '<div align="center" valign="center"><img src="../../../images/wait.gif" align="absmiddle" /></div>';
			//}
		};
		xmlhttp.open('post', 'p4_salva_dados.php', true);
		xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		xmlhttp.setRequestHeader("Pragma", "no-cache");
		xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
		xmlhttp.send(send);
	}
}
function deletar_itens_pedido(id_produto){
	Agree = confirm('Deletar este produto?');
	if(Agree){
		//Executa a fun��o objetoXML()
		var xmlhttp = XMLHTTPRequest(); 
		xmlhttp.onreadystatechange = function () {
			//Se a requisi��o estiver completada
			if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
			//Se o status da requisi��o estiver OK
				if (xmlhttp.status == 200) {
					ajax_exibe_itens_pedido();
				}
			}
		};
		xmlhttp.open('post', 'remove_item.php', true);
		xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		xmlhttp.setRequestHeader("Pragma", "no-cache");
		xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
		xmlhttp.send('id_produto_delete=' + id_produto);
	}
}
function troca_tabela_preco(){
	Agree = confirm('Deseja trocar a tabela de pre�o?');
	if(Agree){
		//Executa a fun��o objetoXML()
		var xmlhttp = XMLHTTPRequest(); 
		xmlhttp.onreadystatechange = function () {
			//Se a requisi��o estiver completada
			if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
			//Se o status da requisi��o estiver OK
				if (xmlhttp.status == 200) {
					ajax_exibe_itens_pedido();
				}
			}
		};
		xmlhttp.open('post', 'troca_tabela_preco.php', true);
		xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		xmlhttp.setRequestHeader("Pragma", "no-cache");
		xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
		xmlhttp.send('tab_preco=' + document.getElementById('edttab_preco').value);
	}
}
function atualizar_itens_pedido(){
	var send = '';
	for(i=0;i<document.form_itens.elements.length;i++){
		send += document.form_itens.elements[i].name + '=' + document.form_itens.elements[i].value + '&';
	}

	//Executa a fun��o objetoXML()
	var ajax_atualiza_itens = XMLHTTPRequest(); 
	ajax_atualiza_itens.onreadystatechange = function () {
		//Se a requisi��o estiver completada
		if (ajax_atualiza_itens.readyState == 4 || ajax_atualiza_itens.readyState == 0) { 
		//Se o status da requisi��o estiver OK
			if (ajax_atualiza_itens.status == 200) {
				ajax_exibe_itens_pedido();
			}
		}
	};
	ajax_atualiza_itens.open('post', 'atualiza_qtde_caixa_itens.php', true);
	ajax_atualiza_itens.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	ajax_atualiza_itens.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
	ajax_atualiza_itens.setRequestHeader("Pragma", "no-cache");
	ajax_atualiza_itens.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
	ajax_atualiza_itens.send(send);
}
function atualizar_campo(Campo,Valor){
	//Executa a fun��o objetoXML()
	var xmlhttp = XMLHTTPRequest(); 
	xmlhttp.onreadystatechange = function () {
		//Se a requisi��o estiver completada
		if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
		//Se o status da requisi��o estiver OK
			if (xmlhttp.status == 200) {
				//alert(xmlhttp.responseText);
			}
		}
	};
	xmlhttp.open('post', 'atualiza_campo.php', true);
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
	xmlhttp.setRequestHeader("Pragma", "no-cache");
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
	xmlhttp.send('campo='+Campo+'&valor='+Valor);
}
function limpa_pedido(Opc){
	//Executa a fun��o objetoXML()
	var xmlhttp = XMLHTTPRequest(); 
	xmlhttp.onreadystatechange = function () {
		//Se a requisi��o estiver completada
		if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
		//Se o status da requisi��o estiver OK
			if (xmlhttp.status == 200) {
				//alert(xmlhttp.responseText);
			}
		}
	};
	xmlhttp.open('post', 'limpa_pedido_aberto.php', true);
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
	xmlhttp.setRequestHeader("Pragma", "no-cache");
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
	xmlhttp.send('acao=' + Opc);
}
function ajax_add_item_pedido(){
	//Executa a fun��o objetoXML()
	var xmlhttp = XMLHTTPRequest(); 
	xmlhttp.onreadystatechange = function () {
		//Se a requisi��o estiver completada
		if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
		//Se o status da requisi��o estiver OK
			if (xmlhttp.status == 200) {
				if(xmlhttp.responseText != ''){
					alert(xmlhttp.responseText);
				}
				ajax_exibe_itens_pedido();
			}
		}
		else{
			   document.getElementById('div_itens_pedido').innerHTML = '<div align="center" valign="center"><img src="../../../images/wait.gif" align="absmiddle" /></div>';
		}
	};
	xmlhttp.open('post', 'p2_add_item.php', true);
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
	xmlhttp.setRequestHeader("Pragma", "no-cache");
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
	xmlhttp.send('id_produto=' + document.getElementById('edtid_produto').value);
}
function ajax_exibe_itens_pedido(){
	//Executa a fun��o objetoXML()
	var xmlhttp = XMLHTTPRequest(); 
	xmlhttp.onreadystatechange = function () {
		//Se a requisi��o estiver completada
		if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
		//Se o status da requisi��o estiver OK
			if (xmlhttp.status == 200) {
				document.getElementById('div_itens_pedido').innerHTML = xmlhttp.responseText;
			}
		}
		else{
			   document.getElementById('div_itens_pedido').innerHTML = '<div align="center" valign="center"><img src="../../../images/wait.gif" align="absmiddle" /></div>';
		}
	};
	xmlhttp.open('post', 'p2_tabela_itens.php', true);
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
	xmlhttp.setRequestHeader("Pragma", "no-cache");
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
	xmlhttp.send(null);
}
function ajax_exibe_itens_pedido_pread(){
	//Executa a fun��o objetoXML()
	var xmlhttp = XMLHTTPRequest(); 
	xmlhttp.onreadystatechange = function () {
		//Se a requisi��o estiver completada
		if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
		//Se o status da requisi��o estiver OK
			if (xmlhttp.status == 200) {
				document.getElementById('div_itens_pedido').innerHTML = xmlhttp.responseText;
			}
		}
		else{
			   document.getElementById('div_itens_pedido').innerHTML = '<div align="center" valign="center"><img src="../../../images/wait.gif" align="absmiddle" /></div>';
		}
	};
	xmlhttp.open('post', 'p2_tabela_itens.php', true);
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
	xmlhttp.setRequestHeader("Pragma", "no-cache");
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
	xmlhttp.send('pread=S');
}
function ajax_exibe_linha_pedido_anterior(){
	//Executa a fun��o objetoXML()
	var xmlhttp = XMLHTTPRequest(); 
	xmlhttp.onreadystatechange = function () {
		//Se a requisi��o estiver completada
		if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) { 
		//Se o status da requisi��o estiver OK
			if (xmlhttp.status == 200) {
				document.getElementById('div_frases').innerHTML = xmlhttp.responseText;
			}
		}
		else{
			   document.getElementById('div_frases').innerHTML = '<div align="center" valign="center"><img src="../../../images/wait.gif" align="absmiddle" /></div>';
		}
	};
	xmlhttp.open('post', 'p3_frases_anteriores.php', true);
	xmlhttp.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
	xmlhttp.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
	xmlhttp.setRequestHeader("Pragma", "no-cache");
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//Envia o formul�rio com dados da vari�vel 'campos' (passado por par�metro)
	xmlhttp.send('id_cliente=' + document.getElementById('edtid_empresa').value);
}
function Executa_JS_Ajax(){   
	// Pegando os valores das Tags <script> que est�o na p�gina carregada pelo AJAX
	var scripts = document.getElementById('div_conteudo').getElementsByTagName("script");
	// Aki, vamos inserir o conte�do da tag <script> que pegamos na linha acima    
	for(i = 0; i < scripts.length; i++){
		// Pegando a div que recebr� o JavaScript
		var conteudo = document.getElementById("javascripts");
		// Declarando a cria��o de uma nova tag <script>
		var newElement = document.createElement("script");
		newElement.text = scripts[i].innerHTML;
		conteudo.appendChild(newElement);
	}
	// Agora, inserimos a nova tag <script> dentro da div na p�gina inicial
}
function add_novo_cliente(){
	if(document.getElementById('edttipo_pessoa').value != ''){
		window.open('../../../gera_cad_detalhe.php?pfuncao=' + document.getElementById('edttipo_pessoa').value + '&pnumreg=-1&psubdet=&pread=N&pnpai=&pfixo=','empresas_cad_lista__1','location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=100,left=100');
	}
	else{
		alert('Selecione o tipo de pessoa');		
	}
}
function ajax_editar_cliente(){
	window.open('editar_cliente.php?id_cliente=' + document.getElementById('edtid_empresa').value,'cliente' + document.getElementById('edtid_empresa').value,'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=100,left=100');
}

//=======================================================================================================================================================================
//OASIS-PEDIDOS/ORCAMENTOS
//=======================================================================================================================================================================
function gera_combobox(nome,id)
{
    var combobox = document.createElement('SELECT');
                   combobox.setAttribute('id',id);
                   combobox.setAttribute('name',nome);

    return combobox;
}
function gera_option(valor,nome)
{
    var opcao = document.createElement("option");
        opcao.setAttribute("value",valor);
        opcao.appendChild(document.createTextNode(nome));

    return opcao;
}

function lista_produtos(familia)
{
    if(familia)
    {
        var ajaxRec = XMLHTTPRequest();
		
		ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
		ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");

		ajaxRec.onreadystatechange = function() 
		{
			if (ajaxRec.readyState == 4) 
			{
				var resp = ajaxRec.responseText;
				
				if( (resp != "erro"))
				{
					xml = ajaxRec.responseXML;
    
					var campo_destino = document.getElementById('edtid_produto');
					var nova_opcao

					while(campo_destino.firstChild)
						campo_destino.removeChild(campo_destino.firstChild);

					if(xml)
					{
						raiz_xml = xml.getElementsByTagName('dado');

						if(raiz_xml.length > 0)
						{
							nova_opcao = gera_option('','(selecione)');
							campo_destino.appendChild(nova_opcao);

							for(var i = 0; i < raiz_xml.length; i++)
							{
								var item        = raiz_xml[i];
								var valor       = item.getElementsByTagName("valor")[0].firstChild.nodeValue;
								var descricao   = item.getElementsByTagName("descricao")[0].firstChild.nodeValue;

								nova_opcao = gera_option(valor,descricao);
								campo_destino.appendChild(nova_opcao);
								
								campo_destino.style.width = '';
							}
						}
						else{
							nova_opcao = gera_option('','Nenhum item encontrado.');
							campo_destino.appendChild(nova_opcao);

							esconde_detalhes_item_selecionado();
						}
					}
					else
						{
							nova_opcao = gera_option('','Selecione uma familia.');
							campo_destino.appendChild(nova_opcao);

							esconde_detalhes_item_selecionado();
						}
				}
				else{
					alert("erro!");
				}
			}
		}
		
			ajaxRec.send('familia='+familia+
						 '&requisicao=lista_produtos');	
    }
    else
        {
            var campo_destino = document.getElementById('edtid_produto');

            while(campo_destino.firstChild)
                campo_destino.removeChild(campo_destino.firstChild);

            esconde_detalhes_item_selecionado();
        }
}

function esconde_detalhes_item_selecionado()
{
    if(document.getElementById('detalhe_item_selecionado').style.display == '')
        document.getElementById('detalhe_item_selecionado').style.display = 'none';
}

function exibe_detalhes_item_selecionado()
{
    /*
     * Ordem dos argumentos desta função
     *
     * 1 - Produto selecionado
     * 2 - Familia do produto selecionado
     * 3 - Forma de pagamento
     *
     * */

    var produto, forma_pagamento, familia;

    if( arguments.length > 0 )
    {
        for(var i=0; i<arguments.length; i++ )
        {
            switch(i)
            {
                case 1:
                    produto = arguments[i];
                    break;

                case 2:
                    forma_pagamento = arguments[i];
                    break;

                case 3:
                    familia = arguments[i];
                    break;
            }
        }
    }
    else
        {
            produto         = document.getElementById('edtid_produto').value;
            forma_pagamento = document.getElementById('edtid_forma_pagto').value;
            familia         = document.getElementById('edtid_familia').value;
        }

    var url = "../../requisicoes_ajax.php";
    var string_send =   'id_produto='+produto+
                        '&id_forma_pagamento='+forma_pagamento+
                        '&id_familia='+familia+
                        '&requisicao=detalhes_item_selecionado';

    requisicao_http("POST",url,true,'exibe_detalhes_item_selecionado_ajax',string_send);
}
function exibe_detalhes_item_selecionado_ajax(ajax)
{
    xml = this.ajax.responseXML;
    var div_destino  = document.getElementById('detalhe_item_selecionado');
    var div_subitens = document.getElementById('subitens');

    if(document.getElementById('detalhe_item_selecionado').style.display == 'none')
        document.getElementById('detalhe_item_selecionado').style.display = '';

    if(xml)
    {
        raiz_xml = xml.getElementsByTagName('dado');

        if(raiz_xml.length > 0)
        {
            for(var i = 0; i < raiz_xml.length; i++)
            {
                var item                = raiz_xml[i];
                var id_produto          = item.getElementsByTagName("id_produto")[0].firstChild.nodeValue;
                var nome_produto        = item.getElementsByTagName("nome_produto")[0].firstChild.nodeValue;
                var valor_unitario      = item.getElementsByTagName("valor_unitario")[0].firstChild.nodeValue;
                var foto                = item.getElementsByTagName("foto")[0].firstChild.nodeValue;
                var quantidade_subitens = item.getElementsByTagName("quantidade_subitens")[0].firstChild.nodeValue;
            }

            document.getElementById('titulo_produto').innerHTML = nome_produto;
            document.getElementById('valor_unitario').innerHTML = valor_unitario;
            document.getElementById('imagem_produto').src = '../../../imgs_produtos/'+foto;

            /*
             *  Verifica se há comboboxes já criados e os remove junto com as quebras
             *  de linha (br)
             */
            var comboboxes_existentes = div_subitens.getElementsByTagName('select').length;

            if(comboboxes_existentes > 0)
            {
                var j = (comboboxes_existentes-1);

                while(j >= 0)
                {
                    var comboboxes_a_remover = div_subitens.getElementsByTagName('select')[j];
                    var tag_br = div_subitens.getElementsByTagName('br')[j];

                    div_subitens.removeChild(comboboxes_a_remover);
                    div_subitens.removeChild(tag_br);

                    j--;
                }
            }

            /*
             * Verifica se há subitens padrão e gera os comboboxes de acordo com estes
             * valores
             */
            k=1;
            while( quantidade_subitens >= k )
            {                
                var nome_id_combobox = 'subitem['+k+']';
                var campo_combobox = gera_combobox(nome_id_combobox,nome_id_combobox);
                
                var tag_br = document.createElement('BR');

                div_subitens.appendChild(campo_combobox);
                div_subitens.appendChild(tag_br);

                //alert(nome_id_combobox);
                lista_subitens_produto(id_produto,nome_id_combobox,k);

                k++;
            }

            if(document.getElementById('detalhe_item_selecionado').style.display == 'none')
                document.getElementById('detalhe_item_selecionado').style.display = '';
        }
        else
            {
                esconde_detalhes_item_selecionado();
            }
    }
}

function lista_subitens_produto_ajax(ajax)
{
   xml = this.ajax.responseXML;

    if(xml)
    {
        var raiz_xml = xml.getElementsByTagName('dado');
        var campo_destino = document.getElementById(xml.getElementsByTagName("campo_retorno")[0].firstChild.nodeValue);
        var nova_opcao;
        var opcao_criada;

        if(raiz_xml.length > 0)
        {
            while(campo_destino.firstChild)
                campo_destino.removeChild(campo_destino.firstChild);

            nova_opcao = gera_option('','');
            campo_destino.appendChild(nova_opcao);

            for(var i = 0; i < raiz_xml.length; i++)
            {
                var item        = raiz_xml[i];
                var valor       = item.getElementsByTagName("valor")[0].firstChild.nodeValue;
                var descricao   = item.getElementsByTagName("descricao")[0].firstChild.nodeValue;
                var selecionado = item.getElementsByTagName("selecionado")[0].firstChild.nodeValue;

                nova_opcao = gera_option(valor,descricao);
                opcao_criada = campo_destino.appendChild(nova_opcao);
                
                if(selecionado == 'S')
                    opcao_criada.setAttribute('selected','selected');
                    campo_destino.appendChild(nova_opcao);
            }
        }
    }
}
function lista_subitens_produto(id_produto,id_campo_combobox,qtd_combobox_criado)
{
	if(id_produto && id_campo_combobox && qtd_combobox_criado)
    {
        var url = "../../requisicoes_ajax.php";
        var string_send =    'id_produto='+id_produto+
                             '&id_campo_combobox='+id_campo_combobox+
                             '&qtd_combobox_criado='+qtd_combobox_criado+
                             '&requisicao=lista_subitens_produto';

        requisicao_http("POST",url,false,'lista_subitens_produto_ajax',string_send);
    }
}

function altera_produto_orcamento(id_produto_pai)
{
	var div_sub_itens = document.getElementById('detalhe_item_selecionado');
	var campos_combobox_existentes = div_sub_itens.getElementsByTagName('select').length;
	var sub_itens = "";
	
	if(campos_combobox_existentes > 0)
	{
		var i=campos_combobox_existentes-1;
		var existe_subitens_selecionados = 0;
						
		while(i >= 0)
		{
			var campo_combobox_a_remover = div_sub_itens.getElementsByTagName('select')[i].value;
			
			if(campo_combobox_a_remover != '')
				existe_subitens_selecionados++;
			
			sub_itens+= 'subitem['+i+']='+campo_combobox_a_remover;
			
			if(i != 0)
				sub_itens+= '&';
							
			i--;
		}
	}
	
	var ajaxRec = XMLHTTPRequest();
		
	ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
		ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");

	ajaxRec.onreadystatechange = function() 
	{
		if (ajaxRec.readyState == 4) 
		{
			var resp = ajaxRec.responseText;
			if( (resp != "erro"))
			{
				lista_produtos('');
			}
			else{
				alert("erro!");
			}
		}
	}
	
	ajaxRec.send('id_produto_pai='+id_produto_pai+
				 '&'+sub_itens+
				 '&requisicao=altera_produto_orcamento');		
}

function adiciona_produto_orcamento()
{
	var cliente = document.getElementById('edtid_empresa').value;
	var familia = document.getElementById('edtid_familia').value;
	var produto = document.getElementById('edtid_produto').value;
	var codigo_produto = document.getElementById('codigo_produto').value;
	var valor = document.getElementById('valor_unitario').innerHTML;
	
	var div_sub_itens = document.getElementById('detalhe_item_selecionado');
	var campos_combobox_existentes = div_sub_itens.getElementsByTagName('select').length;
	var sub_itens = "";
	
	if(campos_combobox_existentes > 0)
	{
		var i=campos_combobox_existentes-1;
		var existe_subitens_selecionados = 0;
						
		while(i >= 0)
		{
			var campo_combobox_a_remover = div_sub_itens.getElementsByTagName('select')[i].value;
			
			if(campo_combobox_a_remover != '')
				existe_subitens_selecionados++;
			
			sub_itens+= 'subitem['+i+']='+campo_combobox_a_remover;
			
			if(i != 0)
				sub_itens+= '&';
							
			i--;
		}
	}
	
	//if(cliente == '' || forma_pagto == '' || familia == '' || produto == '' || valor == '' || (existe_subitens_selecionados != campos_combobox_existentes ) )
	//if(cliente == '' || forma_pagto == '' || ((familia == '' && produto == '') && (codigo_produto == '')) || valor == '' )
	//{
		//alert('Os campos: Cliente, Forma de Pagamento, Valor, Familia, Produto e Sub-itens devem ser preenchidos');
	//}
	//else
	//{
		var ajaxRec = XMLHTTPRequest();
		
		ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
		ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");
	
	  	ajaxRec.onreadystatechange = function() 
		{
	    	if (ajaxRec.readyState == 4) 
			{
	       		var resp = ajaxRec.responseText;
				if( (resp != "erro"))
				{
					//alert(resp);
					ajax_exibe_itens_pedido();
					document.getElementById('detalhe_item_selecionado').style.display = 'none';
					document.getElementById('edtid_familia').value = '';
					lista_produtos('');
				}
				else{
					alert("erro!");
				}
	    	}
	  	}
	  	
		ajaxRec.send('cliente='+cliente+
					 '&familia='+familia+
					 '&produto='+produto+
					 '&codigo_produto='+codigo_produto+
					 '&valor='+valor+
					 '&'+sub_itens+
					 '&requisicao=adiciona_produto_orcamento');		
	//}
}

/////NESTA FUNÇÃO FARÁ O TRATAMENTO VIA AJAX PARA DESCONTOS QUE NAO PODEM ULTRAPASSAR VALOR MINIMO, DE ACORDO COM TABELA DE PREÇO
////TRATAR ERRO DESTA IMPLEMENTAÇÃO!!!!!
function calcula_desconto(item,valor,quantidade,percentagem)
{
	var ajaxRec = XMLHTTPRequest();
		
	ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
		ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");

  	ajaxRec.onreadystatechange = function() 
	{
    	if (ajaxRec.readyState == 4) 
		{
       		var resp = ajaxRec.responseText;
			
			if( (resp != "erro"))
			{
				if( resp.substring(0,2) == "R$" )
				{
					//alert(resp);
					document.getElementById('valor_unitario['+item+']').innerHTML = resp;	
					calcula_valor_total();					
					ajax_exibe_itens_pedido();
				}
				else
					{
						//alert(resp);
						document.getElementById('desconto['+item+']').value = '';
						document.getElementById('desconto['+item+']').focus();		
					}
			}
			else{
				alert("erro!");
			}
    	}
  	}
  	
		ajaxRec.send('item='+item+
					 '&valor='+valor+
					 '&quantidade='+quantidade+
					 '&percentagem='+percentagem+
					 '&requisicao=calcula_desconto');	
}

function calcula_valor_bruto(item,valor,quantidade)
{
	var ajaxRec = XMLHTTPRequest();
		
	ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
		ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");

  	ajaxRec.onreadystatechange = function() 
	{
    	if (ajaxRec.readyState == 4) 
		{
       		var resp = ajaxRec.responseText;
			
			if( (resp != "erro"))
			{
                calcula_quantidade_total();
				calcula_valor_total();
				document.getElementById('valor_bruto['+item+']').innerHTML = resp;
			}
			else{
				alert("erro!");
			}
    	}
  	}
  	
		ajaxRec.send('item='+item+
					 '&valor='+valor+
					 '&quantidade='+quantidade+
					 '&requisicao=calcula_valor_bruto');	
}
function calcula_quantidade_total()
{
	var ajaxRec = XMLHTTPRequest();
		
	ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
			ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");


  	ajaxRec.onreadystatechange = function() 
	{
    	if (ajaxRec.readyState == 4) 
		{
       		var resp = ajaxRec.responseText;
			
			if( (resp != "erro"))
			{
				if(resp)
					document.getElementById('quantidade_total').innerHTML = resp;
			}
			else{
				alert("erro!");
			}
    	}
  	}
  	
	ajaxRec.send('requisicao=calcula_quantidade_total');	
}
function calcula_valor_total()
{
	var ajaxRec = XMLHTTPRequest();
		
	ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
			ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");


  	ajaxRec.onreadystatechange = function() 
	{
    	if (ajaxRec.readyState == 4) 
		{
       		var resp = ajaxRec.responseText;
			
			if( (resp != "erro"))
			{
				if(resp)
                    if(document.getElementById('valor_total'))
                        document.getElementById('valor_total').innerHTML = resp;
			}
			else{
				alert("erro!");
			}
    	}
  	}
  	
	ajaxRec.send('requisicao=calcula_valor_total');	
}
function remove_item(item)
{
	var ajaxRec = XMLHTTPRequest();
		
	ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
			ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");


  	ajaxRec.onreadystatechange = function() 
	{
    	if (ajaxRec.readyState == 4) 
		{
       		var resp = ajaxRec.responseText;
			
			if( (resp != "erro"))
			{
				ajax_exibe_itens_pedido();
			
				calcula_quantidade_total();
				calcula_valor_total();
				
				lista_produtos('');
				
				alert('Item removido com sucesso.');
			}
			else{
				alert("erro!");
			}
    	}
  	}
  	
	ajaxRec.send('item='+item+'&requisicao=remove_item');	
}
function atualiza_precos_forma_pagto(forma_pagto)
{
	var ajaxRec = XMLHTTPRequest();
		
	ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
			ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");


  	ajaxRec.onreadystatechange = function() 
	{
    	if (ajaxRec.readyState == 4) 
		{
       		var resp = ajaxRec.responseText;
			
			if( (resp != "erro"))
			{		
				//calcula_quantidade_total();
				calcula_valor_total();
				ajax_exibe_itens_pedido();					
			}
			else{
				alert("erro!");
			}
    	}
  	}
  	
	ajaxRec.send('forma_pagto='+forma_pagto+'&requisicao=atualiza_precos_forma_pagto');	
}
function atualiza_preco_item_selecionado(forma_pagto)
{
	var familia = document.getElementById('edtid_familia').value;
	var produto = document.getElementById('edtid_produto').value;
	
	if(familia != '' && produto != '')
	{	
		var ajaxRec = XMLHTTPRequest();
		
		ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
				ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");

	
	  	ajaxRec.onreadystatechange = function() 
		{
	    	if (ajaxRec.readyState == 4) 
			{
	       		var resp = ajaxRec.responseText;
				
				if( (resp != "erro"))
				{
                    if(document.getElementById('valor_unitario'))
                        document.getElementById('valor_unitario').innerHTML = resp;
				}
				else{
					alert("erro!");
				}
	    	}
	  	}
	  	
		ajaxRec.send('produto='+produto+'&forma_pagto='+forma_pagto+'&requisicao=atualiza_preco_item_selecionado');	
	}
	
}

function valida_valor_minimo_pedido()
{
    var ajaxRec = XMLHTTPRequest();

    ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
    		ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");


    ajaxRec.onreadystatechange = function()
    {
        if (ajaxRec.readyState == 4)
        {
            var resp = ajaxRec.responseText;

            if( (resp != "erro"))
            {
                if(resp)
                    alert('O valor minimo para efetuar pedido: R$'+ resp);
                else
                   ajax_exibe_pedido_p3();
            }
            else{
                alert("erro!");
            }
        }
    }

    ajaxRec.send('requisicao=valida_valor_minimo_pedido');
}

function salvar_pedido()
{
    var ajaxRec = XMLHTTPRequest();

    ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
    		ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");


    ajaxRec.onreadystatechange = function()
    {
        if (ajaxRec.readyState == 4)
        {
            var resp = ajaxRec.responseText;

            if( (resp != "erro")){
                if(resp == 1)
                {
                    //valida_valor_minimo_pedido();
                    //ajax_exibe_pedido_p2();
                }
                else
                {
					//limpa_pedido('1');
					//alert(resp);
					alert(resp);
                   // window.opener.exibe_programa('modulos/customizacoes/pedidos/browse.php?pfuncao=pedidos_venda_cad');
                    //window.close();
                }
            }
            else{
                alert("erro!");
            }
        }
    }

    ajaxRec.send(
                    'status='+document.getElementById('status').value+
                    '&dt_estim_entrega='+document.getElementById('edt_estim_entrega').value+
                    '&requisicao=salvar_pedido'
                );
}

function busca_empresa(valor)
{
    if(valor != '')
	{
		var ajaxRec = XMLHTTPRequest();

		ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
				ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");


		ajaxRec.onreadystatechange = function()
		{
			if (ajaxRec.readyState == 4)
			{
				if(ajaxRec.status == 200)
				{
					var resp = ajaxRec.responseText;

					if(document.getElementById('div_busca_empresa').style.display == 'none')
						document.getElementById('div_busca_empresa').style.display = '';
						
					if(resp != '')
						document.getElementById('div_busca_empresa').innerHTML = resp;
					else
						{
							if(document.getElementById('div_busca_empresa').style.display == '')
								document.getElementById('div_busca_empresa').style.display = 'none';
						}
				}
			}
			else{
				document.getElementById('div_busca_empresa').style.display = '';
				document.getElementById('div_busca_empresa').innerHTML = 'Carregando...';
			}
		}

		ajaxRec.send('valor='+valor+'&requisicao=pedido_busca_empresa');
	}
	else
		{
			document.getElementById('div_busca_empresa').innerHTML = '';
			document.getElementById('div_busca_empresa').style.display = 'none';
		}
}

function lista_produtos_codigo(valor)
{
    if(valor != '')
	{
		var ajaxRec = XMLHTTPRequest();

		ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
				ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");


		ajaxRec.onreadystatechange = function()
		{
			if (ajaxRec.readyState == 4)
			{
				if(ajaxRec.status == 200)
				{
					var resp = ajaxRec.responseText;

					if(document.getElementById('div_busca_produto').style.display == 'none')
						document.getElementById('div_busca_produto').style.display = '';
						
					if(resp != '')
						document.getElementById('div_busca_produto').innerHTML = resp;
					else
						{
							if(document.getElementById('div_busca_produto').style.display == '')
								document.getElementById('div_busca_produto').style.display = 'none';
						}
				}
			}
			else{
				document.getElementById('div_busca_produto').style.display = '';
				document.getElementById('div_busca_produto').innerHTML = 'Carregando...';
			}
		}

		ajaxRec.send('valor='+valor+'&requisicao=pedido_busca_produto_codigo');
	}
	else
		{
			document.getElementById('div_busca_produto').innerHTML = '';
			document.getElementById('div_busca_produto').style.display = 'none';
		}
}

function pedido_cgc_cliente(cgc,nome)
{
	document.getElementById('edtid_empresa').value = cgc;
	document.getElementById('edtdescrid_empresa').value = nome;
	
	document.getElementById('div_busca_empresa').innerHTML = '';
	document.getElementById('div_busca_empresa').style.display = 'none';
	
	document.getElementById('bt_passo2').disabled = false;
}

function pedido_seleciona_cliente()
{	
	var ajaxRec = XMLHTTPRequest();

		ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
				ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");


		ajaxRec.onreadystatechange = function()
		{
			if (ajaxRec.readyState == 4)
			{
				if(ajaxRec.status == 200)
				{
					//alert(ajaxRec.responseText);
					
					ajax_exibe_pedido_p2();
					
				}
			}
		}

		ajaxRec.send(
					'cgc='+document.getElementById('edtid_empresa').value+
					'&nome='+document.getElementById('edtdescrid_empresa').value+
					'&requisicao=pedido_seleciona_cliente'
					);
}

function clica_codigo_produto(codigo)
{
	document.getElementById('codigo_produto').value = codigo; 
	document.getElementById('div_busca_produto').style.display='none';
	
	document.getElementById('edtid_familia').focus();
}

function pedido_detalhe_item(item,origem)
{
	if(item != '')
	{
		var ajaxRec = XMLHTTPRequest();
		
		ajaxRec.open("POST", ("p2_detalhe_item.php"), true);
				ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");


		ajaxRec.onreadystatechange = function()
		{
			if (ajaxRec.readyState == 4)
			{
				if(ajaxRec.status == 200)
				{
					var resp = ajaxRec.responseText;
					
					document.getElementById('detalhe_item_selecionado').style.display = '';
					document.getElementById('detalhe_item_selecionado').innerHTML = resp;
				}
			}
		}

		ajaxRec.send('origem='+origem+'&id_produto_pai='+item);
	}
	else
		{
			if(document.getElementById('detalhe_item_selecionado').style.display == '')
				document.getElementById('detalhe_item_selecionado').style.display = 'none';
				
			document.getElementById('detalhe_item_selecionado').innerHTML = '';
		}
}

function mostra_estoque_atual(posicao,item)
{
	var ajaxRec = XMLHTTPRequest();
		
	ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
			ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");


	ajaxRec.onreadystatechange = function()
	{
		if (ajaxRec.readyState == 4)
		{
			if(ajaxRec.status == 200)
			{
				var resp = ajaxRec.responseText;
				document.getElementById('qtd_estoque['+posicao+']').innerHTML = resp;
			}
		}
	}

	ajaxRec.send('id_produto_filho='+item+'&requisicao=pedido_estoque_atual');
}

function atualizar_obs(valor)
{	
	var ajaxRec = XMLHTTPRequest();

		ajaxRec.open("POST", ("../../requisicoes_ajax.php"), true);
				ajaxRec.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxRec.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
		ajaxRec.setRequestHeader("Cache-Control","post-check=0, pre-check=0");
		ajaxRec.setRequestHeader("Pragma", "no-cache");


		ajaxRec.onreadystatechange = function()
		{
			if (ajaxRec.readyState == 4)
			{
				if(ajaxRec.status == 200)
				{
					//alert(ajaxRec.ResponseText);
					//ajax_exibe_pedido_p2();
				}
			}
		}
		
		ajaxRec.send(
					'observacoes='+valor+
					'&requisicao=atualizar_obs'
					);
}
/*
function mascara(src, mask){
	
	var i = src.value.length;
	var saida = mask.substring(i,i+1);
	var ascii = event.keyCode;
	if (saida == "B"){ //Não aceita número como entrada no teclado
		if ((ascii < 48) && (ascii > 57)){
			event.keyCode -= 32;
		}
		else{
			event.keyCode = 0;
		}
	}
	else{
		if (saida == "A"){ //Aceita somente letras do alfabeto e maiúsculas como entrada no teclado
			if ((ascii >=97) && (ascii <= 122)){
				event.keyCode -= 32;
			}
			else{
				event.keyCode = 0;
			}
		}
		else{
			if (saida == "0"){ //Aceita somente números como entrada no teclado
				if ((ascii >= 48) && (ascii <= 57)){
					return;
				}
				else{
					event.keyCode = 0;
				}
			}
		}
	}
	else{ //Aceita qualquer entrada no teclado
		if (saida == "#"){
			return;
		}
		else{
			src.value += saida;
			i += 1
			saida = mask.substring(i,i+1);
			if (saida == "A"){
				if ((ascii >=97) && (ascii <= 122)){
					event.keyCode -= 32;
				}
				else{
					event.keyCode = 0;
				}
			}
			else{
				if (saida == "0"){
					if ((ascii >= 48) && (ascii <= 57)){
						return;
					}
					else{
						event.keyCode = 0;
					}
				}
				else{
					return;
				}
			}
		}
	}
}
*/