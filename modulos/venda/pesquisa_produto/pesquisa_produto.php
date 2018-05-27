<?php
/*
 * pesquisa_produto.php
 * Autor: Alex
 * 29/10/2010 15:46:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html; charset=ISO-8859-1");
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

$texto_filtro_1 = trim(addslashes(stripslashes($_POST['edttexto_filtro_1'])));
$texto_filtro_2 = trim(addslashes(stripslashes($_POST['edttexto_filtro_2'])));
$texto_filtro_3 = trim(addslashes(stripslashes($_POST['edttexto_filtro_3'])));
$texto_filtro_4 = trim(addslashes(stripslashes($_POST['edttexto_filtro_4'])));

if($texto_filtro_1 == '' && $texto_filtro_2 == '' && $texto_filtro_3 == '' && $texto_filtro_4 == ''){
    echo '<div class="error">Preencha no mínimo um campo de filtro.</div>';
    exit;
}

$UsaTabelasComplementares = false;
if($VendaParametro->getSnUsaTabComplProd() && $texto_filtro_3 != ''){ /* Se utiliza tabelas de cod complementar de produto o campo de filtro foi preenchido */
    $UsaTabelasComplementares = true;
}

$MaxResult = 50;
$SqlProduto = "SELECT DISTINCT ".((TipoBancoDados == 'mssql')?'TOP('.$MaxResult.')':'')." t1.numreg,t1.nome_produto,t1.id_produto_erp,t1.id_familia_comercial,t1.id_produto_compl,t2.nome_familia_comercial".(($UsaTabelasComplementares === true)?',t4.id_produto_cod_compl,t5.id_produto_cod_compl_hist':'')." FROM is_produto t1 ";
$SqlProduto .= " LEFT JOIN is_familia_comercial t2 ON t1.id_familia_comercial = t2.numreg ";
$SqlProduto .= " LEFT JOIN is_produto_pessoa t3 ON t1.numreg = t3.id_produto ";
if($UsaTabelasComplementares === true){
    $SqlProduto .= " LEFT JOIN is_produto_cod_compl t4 ON t1.numreg = t4.id_produto ";
    $SqlProduto .= " LEFT JOIN is_produto_cod_compl_hist t5 ON t1.numreg = t5.id_produto ";
}
$SqlProduto .= " WHERE t1.sn_ativo = 1 ";
$ParamCondicao = array();
switch ($_POST['edttp_filtro']){
    case '1' ://Iniciado com
        $ParamCondicao[0] = 'LIKE';
        $ParamCondicao[1] = '';
        $ParamCondicao[2] = '%';
        break;
    case '2' ://Contenha
        $ParamCondicao[0] = 'LIKE';
        $ParamCondicao[1] = '%';
        $ParamCondicao[2] = '%';
        break;
    case '3' ://Igual
        $ParamCondicao[0] = '=';
        $ParamCondicao[1] = '';
        $ParamCondicao[2] = '';
        break;
    default ://Default é contenha
        $ParamCondicao[0] = 'LIKE';
        $ParamCondicao[1] = '%';
        $ParamCondicao[2] = '%';
        break;
}

/*
 * Aplicando os filtros
 */
$SqlProduto .= ($texto_filtro_1 != '')?" AND t1.id_produto_erp ".   $ParamCondicao[0]." '".$ParamCondicao[1].$texto_filtro_1.$ParamCondicao[2]."'":'';
$SqlProduto .= ($texto_filtro_2 != '')?" AND t1.nome_produto ".     $ParamCondicao[0]." '".$ParamCondicao[1].$texto_filtro_2.$ParamCondicao[2]."'":'';
$SqlProduto .= ($texto_filtro_4 != '')?" AND t3.id_produto_pessoa ".$ParamCondicao[0]." '".$ParamCondicao[1].$texto_filtro_4.$ParamCondicao[2]."'":'';

if($UsaTabelasComplementares === true){
    $SqlProduto .= ($texto_filtro_3 != '')?" AND (t1.id_produto_compl ". $ParamCondicao[0]." '".$ParamCondicao[1].$texto_filtro_3.$ParamCondicao[2]."'":'';
    $SqlProduto .= ($texto_filtro_3 != '')?" OR t4.id_produto_cod_compl ".     $ParamCondicao[0]." '".$ParamCondicao[1].$texto_filtro_3.$ParamCondicao[2]."'":'';
    $SqlProduto .= ($texto_filtro_3 != '')?" OR t5.id_produto_cod_compl_hist ".$ParamCondicao[0]." '".$ParamCondicao[1].$texto_filtro_3.$ParamCondicao[2]."'":'';
    $SqlProduto .= ")";
}
else{
    $SqlProduto .= ($texto_filtro_3 != '')?" AND t1.id_produto_compl ". $ParamCondicao[0]." '".$ParamCondicao[1].$texto_filtro_3.$ParamCondicao[2]."'":'';
}
if($VendaParametro->getSnUsaBloqueioRepxFam()){
    $SqlProduto .= " AND t2.numreg IN(SELECT DISTINCT tsq1.id_familia_comercial FROM is_param_representantexfamilia_comercial tsq1 WHERE tsq1.id_representante = '".$_SESSION['id_usuario']."' AND tsq1.sn_ativo = 1 AND tsq1.dthr_validade_ini <= '".date("Y-m-d")."' AND tsq1.dthr_validade_fim >= '".date("Y-m-d")."')";
}

$BloqueioCustom = VendaCallBackCustom::ExecutaVenda($Venda,'SQLPesquisaProduto','');
$SqlProduto .= ($BloqueioCustom != '1')?$BloqueioCustom:'';

$SqlProduto .= (TipoBancoDados == 'mysql')?' LIMIT '.$MaxResult:'';

$QryProduto = query($SqlProduto);
?>
<fieldset><legend>Resultado da Pesquisa</legend>
    <?php if(numrows($QryProduto) > 0){ ?>
    <hr size="1">
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="bordatabela" id="tabela_itens">
            <tr bgcolor="#DAE8F4" style="font-weight:bold;">
                <td>C&oacute;d.</td>
                <td>Descri&ccedil;&atilde;o Prod.</td>
                <td>C&oacute;d. Fam&iacute;lia</td>
                <td>Fam&iacute;lia</td>
                <td><?php echo htmlentities($VendaParametro->getNomeCampoCodComplementarProduto());?></td>
            </tr>
            <?php
            $i = 0;
            while($ArProduto = farray($QryProduto)){
                $i++;
                $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
            ?>
            <tr bgcolor="<?php echo $bgcolor;?>">
                <td><?php echo $ArProduto['id_produto_erp'];?></td>
                <td><a href="#anc_det_item" onClick="javascript:exibe_detalhe_produto(<?php echo $ArProduto['numreg'];?>);"><?php echo $ArProduto['nome_produto'];?></a></td>
                <td><?php echo $ArProduto['id_familia_comercial'];?></td>
                <td><?php echo $ArProduto['nome_familia_comercial'];?></td>
                <td><?php
                    $ArCodCompl = array();
                    if($ArProduto['id_produto_compl'] != ''){
                        $ArCodCompl[] = $ArProduto['id_produto_compl'];
                    }
                    if($ArProduto['id_produto_cod_compl'] != ''){
                        $ArCodCompl[] = $ArProduto['id_produto_cod_compl'];
                    }
                    if($ArProduto['id_produto_cod_compl_hist'] != ''){
                        $ArCodCompl[] = $ArProduto['id_produto_cod_compl_hist'];
                    }
                    echo implode(' - ', $ArCodCompl);
                    ?>
                </td>
            </tr>
            <?php } ?>
        </table>
    <hr size="1">
    <?php } else {?>
    Nenhum produto encontrado
    <?php } ?>
</fieldset>