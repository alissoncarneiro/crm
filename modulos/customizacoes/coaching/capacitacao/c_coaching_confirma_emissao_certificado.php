<?php
	require('../../../../conecta.php');
	require('../../../../functions.php');

    if($_POST){
		$valor = implode("," , $_POST);

		$SqlUpdate = "UPDATE c_coaching_inscricao SET sn_certificado_emitido = 1 WHERE numreg in($valor)";
		$QryUpdate = mysql_query($SqlUpdate);
		if(!$QryUpdate){
			$Erro = true;
		}else{
			$Erro = false;
		}
    }else{
		$Erro = false;
	}

if($Erro === true){
    echo alert("Os registros não foram importados, verifique se esta selecionado.",true);
    echo windowlocationhref('c_coaching_relatorio_emissao_certificado_post.php',true);
}
else{
    echo alert("Os registros foram importados com sucesso!",true);
    echo windowlocationhref('c_coaching_relatorio_emissao_certificado_post.php',true);
}
?>