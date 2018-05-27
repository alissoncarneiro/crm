<?php
/*
 * recria_ordem_campos_post.php
 * Autor: Alex
 * 17/12/2010 08:32
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html;  charset=ISO-8859-1");
$IdCadastro = trim($_POST['edtid_cad']);
if(empty($IdCadastro)){
    echo 'Cadastro nao informado!';
    exit;
}
require('../../conecta.php');
require('../../functions.php');
$SqlGeraCadCampos = "SELECT numreg FROM is_gera_cad_campos WHERE id_funcao = '".$IdCadastro."' ORDER BY nome_aba,nome_grupo,ordem ASC";
$QryGeraCadCampos = query($SqlGeraCadCampos);
$Ordem = 10;
while($ArGeraCadCampos = farray($QryGeraCadCampos)){
    $ArSqlUpdate = array();
    $ArSqlUpdate['numreg']  = $ArGeraCadCampos['numreg'];
    $ArSqlUpdate['ordem']   = $Ordem;

    $SqlUpdate = AutoExecuteSql(TipoBancoDados,'is_gera_cad_campos',$ArSqlUpdate,'UPDATE',array('numreg'));
    query($SqlUpdate);

    $Ordem          += 10;
}
echo 'Atualização finalizada';
?>