<?php
session_start();
header('Content-Type: text/html; charset=iso-8859-1');
$odbc_c = true;
$PrefixoIncludes = '../';
include('../includes.php');
$tab_preco = $_GET['tab_preco'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-5589-1" />
<title>Adicionar Múltiplos Produtos</title>
<link href="../../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
<link href="../../../estilos_css/cadastro.css" rel="stylesheet" type="text/css" />

<link href="../../../css/jquery.autocomplete.css" rel="stylesheet" type="text/css" />
<link href="../../../css/jquery.dlg.css" rel="stylesheet" type="text/css" />
<link href="../../../css/redmond/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />

<link href="../estilo_venda.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/jquery.qtip.js"></script>

<script type="text/javascript" src="../../../js/jquery.dlg.min.js"></script>
<script type="text/javascript" src="../../../js/jquery.easing.js"></script>

<script type="text/javascript" src="../../../js/jquery-ui-1.8.5.custom.min.js"></script>
<script type="text/javascript" src="../../../js/jquery.ui.datepicker-pt-BR.js"></script>

<script type="text/javascript" src="../../../js/jquery.autocomplete.js"></script>

<script type="text/javascript" src="../js/modal_det_pessoa.js"></script>

<script type="text/javascript" src="../js/functions_venda.js"></script>
<script type="text/javascript">
$(document).ready(function(){

        $("#ulMenuBarraLateral li a").click(function(){
                        $(this).next().toggle("fast");
                        return false;
        });
        $("#busca_kit").click(function(){
            var id_kit = $("#edtid_kit").val();
            $.ajax({
                url: "mostra_produto_kit.php",
                global: false,
                type: "POST",
                data: ({
                    id_kit: id_kit,
                    numreg: '<?php echo $_GET['pnumreg'];?>',
                    ptp_venda: '<?php echo $_GET['ptp_venda'];?>',
                    tab_preco: '<?php echo $_GET['tab_preco'];?>',
                    pfuncao: '<?php echo $_GET['pfuncao'];?>'
                }),
                dataType: "html",
                async: true,
                beforeSend: function(){
                    $("#Conteudo").html('<p align="center"><img src="../img/ajax_loading_bar.gif" border="0" /></p>');
                },
                error: function(){
                    alert('Erro com a requisição');
                },
                success: function(responseText){
                    $("#Conteudo").html(responseText);
                }
            });
            return false;
        });
});
</script>
<style type="text/css">
	body{
		font-family:Arial, Helvetica, sans-serif;
		font-size:12px;
	}
	#BarraLateral ul{
                text-align: center;
		list-style:none;
		margin:0;
		padding:0 0 0 5px;
		background-color:#365D7C;
	}
	#BarraLateral ul li{
		font-weight:bold;
		padding: 4px 4px 4px 4px;
		border-bottom: 1px solid #C7E1F8;
	}
	#BarraLateral ul li ul li{
		font-weight:normal;
		background-color:#FFFFFF;
		display:block;
		border-bottom: 1px solid #365D7C;
		padding: 2px 2px 2px 2px;
	}
	#BarraLateral a{
		text-decoration:none;
		color:#FFFFFF;
	}
	#BarraLateral ul li ul li a{
		color:#365D7C;
	}
	#Loading{
		text-align:center;
	}
	#Conteudo{
            text-align: center;
		display:block;
		padding: 20px 0 0 0;
	}
</style>
</head>

<body>
<?php
if(empty($_REQUEST['pnumreg'])){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
    } else {
        $VendaParametro = new VendaParametro();
        $VendaOficial = new Orcamento(1,$_GET['pnumreg'],true,false);
    }
$sql_orcamento = 'SELECT t1.numreg FROM is_orcamento t1 where t1.id_pessoa = (SELECT id_pessoa FROM is_orcamento WHERE numreg ='.$_GET['pnumreg'].') and (sn_digitacao_completa=1 and sn_em_aprovacao_comercial=1
or sn_digitacao_completa=0) and t1.numreg <> '.$_GET['pnumreg']. ' ORDER BY t1.numreg';
$qry_orcamento = query($sql_orcamento);
$nrows_orcamento = numrows($qry_orcamento);
if(isset($_POST['gera_orc_unificado'])){
    $ContaItens = 0;
    while($ar_orcamento = farray($qry_orcamento) ){
        $id_moeda = $_POST['adicionar_id_moeda'].$ar_orcamento['numreg'];
        foreach($_POST['item'.$ar_orcamento['numreg']] as $k => $v){
            $ContaItens++;
            $ar_item = array();
            $ar_item_desconto = array();

            $sql_item = 'SELECT * FROM is_orcamento_item WHERE numreg = '.$v;
            $qry_item = query($sql_item);
            $ar_item = farray($qry_item);

            $Produto = new Produto($ar_item['id_produto']);
            $VlUnitario = number_format($Produto->getVlUnitarioTabelaBD($tab_preco),2,',','.');

            $ArDados['pnumreg']         = $_GET['pnumreg'];
            $ArDados['ptp_venda']       = 1;
            $ArDados['id_produto']      = $ar_item['id_produto'];
            $ArDados['qtde']            = number_format($ar_item['qtde'],2,',','.');
            $ArDados['id_moeda']        = $id_moeda[$k];
            $ArDados['id_unid_medida']  = $ar_item['id_unid_medida'];
            $ArDados['id_referencia']   = $ar_item['id_referencia'];
            $ArDados['vl_unitario']     = $VlUnitario;

            $NumregItem = $VendaOficial->AdicionaItemBD($ArDados['id_produto'],$ArDados);

            if($NumregItem === false){
                $Produto = new Produto($ArDados['id_produto']);
                $ErrorInsereProduto .= '<strong style="color:#FF0000;">Produto</strong><i> '. $Produto->getDadosProduto('id_produto_erp') . ' - ' . $Produto->getDadosProduto('nome_produto') . '</i><br /><strong style="color:#FF0000;">Não foi inserido!</strong><hr>';
            }
        }
    }
?>
        <script>
        $(document).ready(function(){
            $.dlg({
                title: 'Alerta - <?php echo ucwords(strsadds($VendaOficial->getTituloVenda(false)));?>',
                content: '<?php echo $ErrorInsereProduto.$VendaOficial->getMensagemAtualizacaoItens();?>',
                drag: true,
                focusButton :'ok',
                onComplete: function(){
                    window.opener.location.reload();
                    window.location.assign('<?php echo $_POST['url_retorno'];?>');
                }
            });
        });
    </script>
<?php
exit;
} else {
?>
<form method="POST" action="<?php echo $_SERVER['REQUEST_URI'];?>">
    <input type="hidden" name="url_retorno" id="url_retorno" value="<?php echo $_SERVER['REQUEST_URI'];?>" />
<div id="barra_topo"></div>
<div id="BarraLateral">
<ul id="ulMenuBarraLateral">
    <?php
    if($nrows_orcamento > 1){
        while($ar_orcamento = farray($qry_orcamento) ){
            $Venda = new Orcamento(1,$ar_orcamento['numreg'],true,false);
    ?>
        <li><a href="#">Orçamento Número <?php echo $ar_orcamento['numreg'];?></a>
            <ul>
                <li>
                    <?php if(count($Venda->getItens())>0){ ?>
                    <table width="100%" border="0" cellpadding="2" cellspacing="2" class="bordatabela">
                            <tr>
                                    <td bgcolor="#DBE9F4" class="tit_tabela">&nbsp;</td>
                                    <td bgcolor="#DBE9F4" class="tit_tabela">C&oacute;d.</td>
                                    <td bgcolor="#DBE9F4" class="tit_tabela">Descri&ccedil;&atilde;o</td>
                                    <td bgcolor="#DBE9F4" class="tit_tabela">Unid. <br />Med.</td>
                                    <td bgcolor="#DBE9F4" class="tit_tabela">Qtde.</td>
                                    <td bgcolor="#DBE9F4" class="tit_tabela">Valor Unit.<br />S/ Desc.</td>
                            </tr>
                            <?php
                            $contador = 0;
                            foreach($Venda->getItens() as $IndiceItem => $Item){
                                if(!$Item->getItemComercial()){
                                    continue;
                                }
                                $Produto = new Produto($Item->getDadosVendaItem('id_produto'));
                                $VlUnitario = number_format($Produto->getVlUnitarioTabelaBD($tab_preco),2,',','.');
                                ++$contador;
                                if($Venda->getTipoVenda() == 1 && $Item->getDadosVendaItem('sn_item_perdido') == 1){
                                    $bgcolor = '#FFCC66';
                                } else if($VendaOficial->VerificaSeExisteProduto($Item->getDadosVendaItem('id_produto'))){
                                    $bgcolor = '#FFBABA';
                                } else {
                                    if($contador%2 != 0){
                                        $bgcolor = '#FFFFFF';
                                    } else {
                                        $bgcolor = '#EAEAEA';
                                    }
                                }
                                if(!$VendaParametro->getSnPermiteAdicionarItemRepetido() && $VendaOficial->VerificaSeExisteProduto($Item->getDadosVendaItem('id_produto'))){
                                    $AllowRoles = false;
                                } else {
                                    if((!$VendaParametro->getPermiteAdicionarItemSemPreco() && $Produto->getVlUnitarioTabelaBD($tab_preco) <= 0) || (!$VendaParametro->getPermiteAdicionarItemSemReferencia() && trim($Item->getDadosVendaItem('id_referencia')) == '')){
                                        $bgcolor = '';
                                        $AllowRoles = false;
                                    } else {
                                        $AllowRoles = true;
                                    }
                                }
                            ?>
                            <input type="hidden" name="adicionar_id_moeda<?php echo $ar_orcamento['numreg'];?>[]" id="adicionar_id_moeda" value="1" />
                            <tr>
                                <td bgcolor="<?=$bgcolor;?>" class="tit_tabela">
                                    <?php if($AllowRoles){?>
                                    <input name="item<?php echo $ar_orcamento['numreg'];?>[]" type="checkbox" id="item<?php echo $ar_orcamento['numreg'];?>[]" value="<?php echo $Item->getNumregItem();?>" />
                                    <?php }?>
                                </td>
                                    <td bgcolor="<?=$bgcolor;?>"><?php echo $Item->getCodProdutoERP();?></td>
                                    <td bgcolor="<?=$bgcolor;?>" align="left"><?php echo $Item->getNomeProduto();?></td>
                                    <td bgcolor="<?=$bgcolor;?>">
                                    <?php
                                    if($Venda->getDigitacaoCompleta() || 1==1){ //Se a venda já estiver completa FIXO TEXTO
                                        if($Item->getDadosVendaItem('id_unid_medida') != ''){
                                            $QryUnidMedida = query("SELECT numreg, nome_unid_medida FROM is_unid_medida WHERE numreg = ".$Item->getDadosVendaItem('id_unid_medida'));
                                            $ArUnidMedida = farray($QryUnidMedida);
                                            echo $ArUnidMedida['nome_unid_medida'];
                                        }
                                    }
                                    ?>
                                    </td>
                                    <td bgcolor="<?=$bgcolor;?>"><div align="right"><?php echo $Venda->NFQ($Item->getDadosVendaItem('qtde'));?></div></td>
                                    <td bgcolor="<?=$bgcolor;?>"><div align="right"><?php echo number_format($Produto->getVlUnitarioTabelaBD($tab_preco),2,',','.');;?></div></td>
                            </tr>
                            <?php
                            }
                            ?>
                    </table>
                    <?php }else {
                       echo '<font color="red"><strong>Este orçamento não possui itens.</strong></font>';
                    }?>
                </li>
            </ul>
        </li>
    <?php
        }
        echo '<br /><br /><input style="border: 1px solid #365D7C; background-color: #C0000; cursor: pointer; padding: 2px;" name="gera_orc_unificado" type="submit" id="gera_orc_unificado" value="Gerar Orçamento Unificado"/><br /><br />';
    } else {
        echo '<li>Este cliente não possui a quantidade mínima de orçametos para utilizar esta função, quantidade de orçamentos do cliente é: '.$nrows_orcamento.'.</li>';
    }?>
</ul>

</div>
<!--<div id="Conteudo"></div>-->
</form>
<?php
}?>
</body>
</html>