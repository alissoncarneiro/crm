<?php
/*
 * p2_venda_atualiza_itens.php
 * Autor: Alex
 * 05/11/2010 14:50
 * -
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
header("Content-Type: text/html;  charset=ISO-8859-1");
session_start();
require('includes.php');

/*
 * Verifica se a v�ri�vel de tipo da venda foi preenchida.
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

$Erro = false;
foreach($Venda->getItens() as $IndiceItem => $Item){
    if($_POST['controle_alteracao_'.$Item->getNumregItem()] != 1){//Se o produto n�o foi alterado, n�o processa-o
        continue;
    }
    $Item->setDadosItemPOST($_POST);
    $AtualizaItem = $Item->AtualizaItemBD();
    if($AtualizaItem[0] !== true){//Se houve algum erro, informa ao usu�rio
        $Erro = true;
    }
}
$Venda->CalculaTotaisVenda();
$Venda->AtualizaTotaisVendaBD();
if(!$Venda->getAtualizacaoItensErro()){
    header("Location:".$_POST['url_retorno']);
    exit;
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title><?php echo ucwords($Venda->getTituloVenda());?></title>
        <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link href="../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />

        <link href="../../css/jquery.autocomplete.css" rel="stylesheet" type="text/css" />
        <link href="../../css/jquery.dlg.css" rel="stylesheet" type="text/css" />
        <link href="../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />

        <link href="estilo_venda.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquery.qtip.js"></script>

        <script type="text/javascript" src="../../js/jquery.dlg.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.easing.js"></script>

        <script type="text/javascript" src="../../js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>

        <script type="text/javascript" src="../../js/jquery.autocomplete.js"></script>

        <script type="text/javascript" src="js/modal_det_pessoa.js"></script>

        <script type="text/javascript" src="js/functions_venda.js"></script>

    </head>

    <body>
        <div id="jquery-dialog" style="display: none;"></div>
        <script>
        $(document).ready(function(){
            <?php if($Venda->getAtualizacaoItensErro()){?>
            $("#jquery-dialog").attr("title",'Alerta - <?php echo ucwords(strsadds($Venda->getTituloVenda(false)));?> - Atualiza&ccedil;&atilde;o de Itens');
            $("#jquery-dialog").html('<?php echo $Venda->getMensagemAtualizacaoItens();?>');
            $("#jquery-dialog").dialog({
                width: 600,
                height: 500,
                buttons:{"OK": function(){<?php echo windowlocationhref($_POST['url_retorno'],false);?>}},
                beforeClose: function(){<?php echo windowlocationhref($_POST['url_retorno'],false);?>},
                modal: true,
                show: "fade",
                hide: "fade"
            });
            <?php } ?>
        });
        </script>
    </body>
</html>