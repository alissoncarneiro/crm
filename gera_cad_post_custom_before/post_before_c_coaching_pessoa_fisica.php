<?php
//Desenvolvido por ALisson Carneiro
// 08/05/2012 19:20
//Se for pessoa f�sica obriga campos obrigatorios definidos na array ArrayCamposObrigatoriosPfSbc

if($id_funcao == 'pessoa'){
	if($_POST['edtid_tp_pessoa'] == 2){
		if($_POST['edtid_sexo'] == ""){
			echo alert("O Campo Sexo � Obrigatorio. Registro n�o foi salvo");
			$geraCadPost->DoJsPostBack($Url);
			exit;
		}
	}
}
?>