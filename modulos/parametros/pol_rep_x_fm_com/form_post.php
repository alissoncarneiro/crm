<?php
/*
 * form_post.php
 * Autor: Alex
 * 09/05/2012 13:33:08
 */
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=UTF-8");

if($_SESSION['id_usuario'] == ''){
    exit;
}

require('../../../conecta.php');
require('../../../functions.php');

$DOM = new DOMDocument('1.0', 'UTF-8');
$DOM->preserveWhiteSpace = false;
$DOM->formatOutput = true;
$Root = $DOM->createElement('resposta');

if($_POST['requisicao'] == ''){
    exit;
}

if($_POST['requisicao'] == 'recarrega_familia'){
    if($_POST['id_representante'] == ''){
        $Status = '2';
        $Mensagem = 'Nenhum representante selecionado!';
    }
    else{
        $CampoFamiliaOn = $DOM->createElement('campo_familia_on');
        $SqlFamiliaInativa = "SELECT numreg,nome_familia_comercial FROM is_familia_comercial WHERE numreg IN(SELECT id_familia_comercial FROM is_param_representantexfamilia_comercial WHERE id_representante = '".$_POST['id_representante']."') ORDER BY nome_familia_comercial";
        $QryFamiliaInativa = query($SqlFamiliaInativa);
        while($ArFamiliaInativa = farray($QryFamiliaInativa)){
            $Option = $DOM->createElement('option', utf8_encode($ArFamiliaInativa['nome_familia_comercial']));
            $Attribute = $DOM->createAttribute('value');
            $Attribute->value = $ArFamiliaInativa['numreg'];
            $Option->appendChild($Attribute);
            $CampoFamiliaOn->appendChild($Option);
        }
        $CampoFamiliaOff = $DOM->createElement('campo_familia_off');
        $SqlFamiliaInativa = "SELECT numreg,nome_familia_comercial FROM is_familia_comercial WHERE numreg NOT IN(SELECT id_familia_comercial FROM is_param_representantexfamilia_comercial WHERE id_representante = '".$_POST['id_representante']."') ORDER BY nome_familia_comercial";
        $QryFamiliaInativa = query($SqlFamiliaInativa);
        while($ArFamiliaInativa = farray($QryFamiliaInativa)){
            $Option = $DOM->createElement('option', utf8_encode($ArFamiliaInativa['nome_familia_comercial']));
            $Attribute = $DOM->createAttribute('value');
            $Attribute->value = $ArFamiliaInativa['numreg'];
            $Option->appendChild($Attribute);
            $CampoFamiliaOff->appendChild($Option);
        }
        $Status = '1';
        $Root->appendChild($CampoFamiliaOn);
        $Root->appendChild($CampoFamiliaOff);
    }
}
elseif($_POST['requisicao'] == 'add_familia'){
    if($_POST['id_representante'] == ''){
        $Status = '2';
        $Mensagem = 'Nenhum representante selecionado!';
    }
    elseif($_POST['id_familia'] == ''){
        $Status = '2';
        $Mensagem = 'Nenhuma família selecionada!';
    }
    else{
        foreach($_POST['id_familia'] as $IdFamilia){
            $ArSqlInsert = array(
                'id_representante' => $_POST['id_representante'],
                'id_familia_comercial' => $IdFamilia,
                'dthr_validade_ini' => '2000-01-01',
                'dthr_validade_fim' => '2099-12-31',
                'sn_ativo' => 1                
            );
            $SqlInsert = AutoExecuteSql(TipoBancoDados, 'is_param_representantexfamilia_comercial', $ArSqlInsert, 'INSERT');
            query($SqlInsert);
        }        
    }
}
elseif($_POST['requisicao'] == 'del_familia'){
    if($_POST['id_representante'] == ''){
        $Status = '2';
        $Mensagem = 'Nenhum representante selecionado!';
    }
    elseif($_POST['id_familia'] == ''){
        $Status = '2';
        $Mensagem = 'Nenhuma família selecionada!';
    }
    else{
        $ArFamilias = array();
        foreach($_POST['id_familia'] as $IdFamilia){
            $ArFamilias[] = $IdFamilia;
        }
        $SqlDelete = "DELETE FROM is_param_representantexfamilia_comercial WHERE id_representante = '".$_POST['id_representante']."' AND id_familia_comercial IN('".implode("','",$ArFamilias)."')";
        query($SqlDelete);        
    }
}
$Root->appendChild($DOM->createElement('status',$Status));
$Root->appendChild($DOM->createElement('mensagem', utf8_encode($Mensagem)));
$DOM->appendChild($Root);
header('Content-Type: text/xml');
print $DOM->saveXML();
exit;
?>