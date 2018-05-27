<?php
//Desenvolvido por ALisson Carneiro
// 08/05/2012 19:20
//Se for pessoa física obriga campos obrigatorios definidos na array ArrayCamposObrigatoriosPfSbc

if($id_funcao == 'pessoa'){
	if($_POST['edtid_tp_pessoa'] == 2){
		if($_POST['edtid_sexo'] == ""){
			echo alert("O Campo Sexo é Obrigatorio. Registro não foi salvo");
			$geraCadPost->DoJsPostBack($Url);
			exit;
		}
	}
}
?>