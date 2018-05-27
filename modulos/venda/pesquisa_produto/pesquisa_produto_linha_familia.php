<?php
/*
 * pesquisa_produto_linha_familia.php
 * Autor: Alex
 * 01/11/2010 11:55
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header ("content-type: text/xml");
session_start();

$PrefixoIncludes = '../';
include('../includes.php');

/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */
if($_POST['ptp_venda'] == 1 || $_POST['ptp_venda'] == 2){
        if($_POST['ptp_venda'] == 1){
            $Venda = new Orcamento($_POST['ptp_venda'],$_POST['pnumreg']);
        }
        elseif($_POST['ptp_venda'] == 2){
            $Venda = new Pedido($_POST['ptp_venda'],$_POST['pnumreg']);
        }
} else{
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
$VendaParametro = new VendaParametro();

$Requisicao = $_POST['prequisicao'];

echo '<?'.'xml version="1.0" encoding="ISO-8859-1"'.'?>'."\n";

if($Requisicao == 'familia'){
    $SqlFamiliaComercial = "SELECT numreg,nome_familia_comercial FROM is_familia_comercial WHERE id_produto_linha = ".$_POST['pid_linha'];
    if($VendaParametro->getSnUsaBloqueioRepxFam()){
        $SqlFamiliaComercial .= " AND numreg IN(SELECT DISTINCT tsq1.id_familia_comercial FROM is_param_representantexfamilia_comercial tsq1 WHERE tsq1.id_representante = '".$_SESSION['id_usuario']."' AND tsq1.sn_ativo = 1 AND tsq1.dthr_validade_ini <= '".date("Y-m-d")."' AND tsq1.dthr_validade_fim >= '".date("Y-m-d")."')";
    }
    $BloqueioCustom = VendaCallBackCustom::ExecutaVenda($Venda, 'Passo_2Combobox_Familia', 'BloqueioCampo');
    
    $SqlFamiliaComercial = ($BloqueioCustom != '1')?$SqlFamiliaComercial.$BloqueioCustom:$SqlFamiliaComercial;
    
    $SqlFamiliaComercial .= " ORDER BY nome_familia_comercial ASC";
    $QryFamiliaComercial = query($SqlFamiliaComercial);
    if(numrows($QryFamiliaComercial) > 0){
        echo '<select>';
        echo "\t".'<option value="">--Selecione--</option>'."\n";
        while($ArFamiliaComercial = farray($QryFamiliaComercial)){
            echo "\t".'<option value="'.$ArFamiliaComercial['numreg'].'">'.$ArFamiliaComercial['nome_familia_comercial'].'</option>'."\n";
        }
        echo '</select>';
    }
    else{
        echo '<select>'."\n";
        echo "\t".'<option value="">--Nenhuma família encontrada--</option>'."\n";
        echo '</select>';
    }
}
elseif($Requisicao == 'produto'){
    $SqlProduto = "SELECT numreg,nome_produto FROM is_produto WHERE id_familia_comercial = ".$_POST['pid_familia_comercial'];
    $BloqueioCustom = VendaCallBackCustom::ExecutaVenda($Venda, 'Passo_2Combobox_Produto', 'BloqueioCampo');
    $SqlProduto = ($BloqueioCustom != '1')?$SqlProduto.$BloqueioCustom:$SqlProduto;
    $SqlProduto .= " ORDER BY nome_produto ASC";
    
    $QryProduto = query($SqlProduto);
    if(numrows($QryProduto) > 0){
        echo '<select>';
        while($ArProduto = farray($QryProduto)){
            echo "\t".'<option value="'.$ArProduto['numreg'].'">'.$ArProduto['nome_produto'].'</option>'."\n";
        }
        echo '</select>';
    }
    else{
        echo '<select>'."\n";
        echo "\t".'<option value="">--Nenhuma produto encontrado--</option>'."\n";
        echo '</select>';
    }
}
?>