<?php
/*
 * MostraSaldoEstoque.php
 * Autor: Lucas
 * 27/11/2010 10:51:00
 *
 * Arquivo que exibe o saldo de estoque com detalhes do produto, exibe as previsões de compra
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
header("Content-Type: text/html; charset=ISO-8859-1");

include('../../conecta.php');
include('../../functions.php');
include('../../classes/class.ConsultaEstoque.php');
include('../../classes/class.ConsultaEstoqueCustom.php');
include('../../classes/class.ConsultaEstoqueXMLErpDatasul.php');
include('../../classes/class.ConsultaPrevisao.php');
include('../../classes/class.uB.php');
include('../../classes/class.Usuario.php');
include('../venda/classes/class.Venda.Parametro.php');

$ArrayParametrosExibicao = array(
    'exibe_saldo_disp'              => true,
    'exibe_estoque_disp_erp'        => true,
    'exibe_estoque_erp'             => true,
    'exibe_detalhe_estoque_erp'     => true,
    'exibe_ped_nao_aloc_erp'        => true,
    'exibe_ped_nao_integrado_crm'   => true,
);

include('ConsultaEstoqueCustom.php');

$VendaParametro = new VendaParametro();

$_GET['pnumreg'] = $_POST['id_produto'];

$IdProduto          = $_POST['id_produto'];
$IdEstabelecimento  = ''; /* $_POST['id_estabelecimento']; */

$PermiteVerInfCompra = false;

if($_SESSION['id_usuario'] != ''){
    $Usuario = new Usuario($_SESSION['id_usuario']);
    $PermiteVerInfCompra = $Usuario->getPermissao('sn_permite_ver_info_comp_estoq');
}

if($VendaParametro->getSnUsaURLEstoqXmlDatasul() && $VendaParametro->getURLEstoqueXmlErpDatasul() != ''){
    $ConsultaEstoqueErpDatasul      = new ConsultaEstoqueXMLErpDatasul($VendaParametro,$IdProduto,$IdEstabelecimento);
    $ar_SaldoEstoque                = $ConsultaEstoqueErpDatasul->getArrayEstoqueAtual();
    $ar_pedds                       = $ConsultaEstoqueErpDatasul->getArrayPedidosNaoFaturadosERP();
    if($PermiteVerInfCompra){
        $ar_previsao                    = $ConsultaEstoqueErpDatasul->getArrayPrevisaoCompras();
    }
    $ar_pedcrm                      = $ConsultaEstoqueErpDatasul->getArrayPedidosNaoIntegrados();

    $QuantidadeDisponivel           = $ConsultaEstoqueErpDatasul->getQuantidadeDisponivel();
    $SaldoEstoqueTotal              = $ConsultaEstoqueErpDatasul->getQuantidadeAtual();
    $PedidosNaoFaturadosErpTotal    = $ConsultaEstoqueErpDatasul->getQuantidadeNaoFaturada();
    $PedidosNaoIntegradosTotal      = $ConsultaEstoqueErpDatasul->getQuantidadeNaoIntegrada();
}
else{
    //ESTOQUE
    $SaldoEstoque = new ConsultaEstoqueCustom($VendaParametro);
    $SaldoEstoque->setIdProduto($_GET['pnumreg']);

    //DADOS DE PREVISÃO
    $Previsao = new ConsultaPrevisao($VendaParametro);
    $Previsao->setIdProduto($_GET['pnumreg']);

    $ar_SaldoEstoque = $SaldoEstoque->getSaldoEstoque();
    $ar_pedds = $SaldoEstoque->getPedidosNaoFaturadosErp();
    if($PermiteVerInfCompra){
        $ar_previsao = $Previsao->getConsultaPrevisao();
    }
    $ar_pedcrm = $SaldoEstoque->getPedidosNaoIntegrados();

    $SaldoEstoqueTotal              = $SaldoEstoque->getSaldoEstoqueTotal();
    $PedidosNaoFaturadosErpTotal    = $SaldoEstoque->getPedidosNaoFaturadosErpTotal();
    $PedidosNaoIntegradosTotal      = $SaldoEstoque->getPedidosNaoIntegradosTotal();
    $QuantidadeDisponivel           = $SaldoEstoque->qtidade_disp;
}
$info_produto = farray(query('select numreg,id_produto_erp,nome_produto from is_produto where numreg = '.$_GET['pnumreg'].''));
?>

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: OASIS :: Consulta de Estoque</title>
        <link href="../../estilos_css/estilo.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../../estilos_css/cadastro.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../estilos_css/calendar-blue.css" title="win2k-cold-1" />
        <style type="text/css">
            #div_prod_similar{
                background-color:#000000;
            }
        </style>
        <!--
        <script type="text/javascript" src="../../js/calendario/calendario.js"></script>
        <script type="text/javascript" src="../../js/calendario/calendario-pt.js"></script>
        <script type="text/javascript" src="../../js/calendario/calendario-config.js"></script>
        <script language="JavaScript" src="../../js/ajax.js">
        </script>
        <script language="JavaScript" src="functions_estoque.js"></script>
        -->
    </head>
    <body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
        <!--
            <div id="modal_all" style="width:100%; background:#999999; position:absolute; z-index:999999; -moz-opacity: 0.3;filter: progid:DXImageTransform.Microsoft.Alpha(opacity=30); vertical-align:middle; text-align:center;"></div>
            <div id="javascripts"></div>
            <center>
                <div id="principal_detalhes">
                    <div id="topo_detalhes">
                        <div id="logo_empresa"></div>
                        logo
                    </div> topo -->
        <div id="conteudo_detalhes">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
                <tr>
                    <td height="10" width="5%" align="center" background="../../images/aba_base.gif" colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="4" >
                        <div name="div_programa" id="div_programa"></div>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#dbe9f4" class="sub_tit" colspan="4">
                        <div align="center">Informações do produto</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" >&nbsp;</td>
                </tr>
                <tr>
                    <td >&nbsp;</td>
                    <td width="18%" >
                        <div align="right">Produto: </div>
                    </td>
                    <td width="1%" >&nbsp;</td>
                    <td width="76%" >
                        <div align="left">
                            <?php echo $info_produto['id_produto_erp'],' - ',$info_produto['nome_produto']; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td >&nbsp;</td>
                </tr>
                <tr>
                    <td bgcolor="#dbe9f4" class="sub_tit" colspan="4">
                        <div align="center">Informações de Estoque</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">&nbsp;</td>
                </tr>
                <?php if($ArrayParametrosExibicao['exibe_saldo_disp'] === true): ?>
                <tr>
                    <td  bgcolor="#dbe9f4" class="sub_tit">&nbsp;</td>
                    <td width="18%" bgcolor="#dbe9f4" class="sub_tit">
                        <div align="right">Saldo Dispon&iacute;vel:</div>
                    </td>
                    <td width="1%"  bgcolor="#dbe9f4" class="sub_tit">&nbsp;</td>
                    <td width="76%" bgcolor="#dbe9f4" class="sub_tit" style="font-size:18px;font-weight: bold;"><?php echo number_format_min(($QuantidadeDisponivel - $PedidosNaoFaturadosErpTotal - $PedidosNaoIntegradosTotal),2,',','.'); ?></td>
                </tr>
                <?php endif ?>
                <tr>
                    <td colspan="4">&nbsp;</td>
                </tr>
                <?php if($ArrayParametrosExibicao['exibe_estoque_disp_erp'] === true): ?>
                <tr>
                    <td  bgcolor="#dbe9f4" class="sub_tit">&nbsp;</td>
                    <td width="18%" bgcolor="#dbe9f4" class="sub_tit">
                        <div align="right">Estoque Dispon&iacute;vel ERP:</div>
                    </td>
                    <td width="1%"  bgcolor="#dbe9f4" class="sub_tit">&nbsp;</td>
                    <td width="76%" bgcolor="#dbe9f4" class="sub_tit"><strong>
                            <?php echo number_format_min($QuantidadeDisponivel,2,',','.'); ?>
                        </strong></td>
                </tr>
                <?php endif ?>
                <tr>
                    <td >&nbsp;</td>
                    <td width="18%" valign="top">
                        <?php if($ArrayParametrosExibicao['exibe_estoque_erp'] === true): ?>
                        <div align="right">Estoque ERP:</div>
                        <?php endif ?>
                    </td>
                    <td width="1%" >&nbsp;</td>
                    <td width="76%" >
                        <div align="left">
                            <?php if($ArrayParametrosExibicao['exibe_estoque_erp'] === true): ?>
                            <?php echo number_format_min($SaldoEstoqueTotal,2,',','.'); ?>
                            <?php endif ?>
                            <?php if($ArrayParametrosExibicao['exibe_detalhe_estoque_erp'] === true): ?>
                            <table border="0" id="tabela_lotes_vog">
                                <tr bgcolor="#cccccc">
                                    <td bgcolor="#DAE8F4" class="tit_tabela">Estabelecimento</td>
                                    <td bgcolor="#DAE8F4" class="tit_tabela">Qtde. em Estoque</td>
                                    <td bgcolor="#DAE8F4" class="tit_tabela">Lote</td>
                                    <td bgcolor="#DAE8F4" class="tit_tabela">Validade</td>
                                    <td bgcolor="#DAE8F4" class="tit_tabela">C&oacute;d. Refer.</td>
                                </tr>
                                <?php
                                if(count($ar_SaldoEstoque) > 0){
                                    foreach($ar_SaldoEstoque as $k => $v){
                                        $bg_color = ($i_estoque % 2 == 0) ? '#EAEAEA' : '#FFFFFF';
                                        $i_estoque++;
                                ?>
                                        <tr bgcolor="<?php echo $bg_color; ?>">
                                            <td><?php echo $v[0]; ?></td>
                                            <td align="right"><?php echo number_format_min($v[1],2,',','.'); ?></td>
                                            <td><?php echo $v[2]; ?></td>
                                            <td align="right"><?php echo uB::DataEn2Br($v[3]); ?></td>
                                            <td><?php echo $v[4]; ?></td>
                                        </tr>
                                <?php }
                                } ?>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                            <?php endif ?>
                        </div>
                    </td>
                </tr>
                <?php if($ArrayParametrosExibicao['exibe_ped_nao_aloc_erp'] === true): ?>
                <tr>
                    <td >&nbsp;</td>
                    <td width="18%" valign="top">
                        <div align="right">Pedidos N&atilde;o Alocados - ERP:</div>
                    </td>
                    <td width="1%" >&nbsp;</td>
                    <td width="76%" valign="top">
                        <div align="left">
                            <?php echo number_format_min($PedidosNaoFaturadosErpTotal,2,',','.'); ?>
                                <table border="0" id="tabela_estoque_pedidos_ds">
                                    <tr bgcolor="#cccccc">
                                        <td bgcolor="#DAE8F4" class="tit_tabela">Cliente</td>
                                        <td bgcolor="#DAE8F4" class="tit_tabela">Vendedor</td>
                                        <td bgcolor="#DAE8F4" class="tit_tabela">Qtde N&atilde; Alocada</td>
                                        <td bgcolor="#DAE8F4" class="tit_tabela">Data de entrega</td>
                                    </tr>
                                <?php
                                $i_previsao = 0;
                                if(count($ar_pedds) > 0){
                                    foreach($ar_pedds as $k => $v){
                                        $bg_color = ($i_pedds % 2 == 0) ? '#EAEAEA' : '#FFFFFF';
                                        $i_pedds++;
                                ?>
                                        <tr bgcolor="<?php echo $bg_color; ?>">
                                            <td><?php echo $v[0]; ?></td>
                                            <td><?php echo $v[1]; ?></td>
                                            <td align="right"><?php echo number_format_min($v[2],2,',','.'); ?></td>
                                            <td align="right"><?php echo uB::DataEn2Br($v[3]); ?></td>
                                        </tr>
                                <?php }
                                } ?>
                                <tr><td>&nbsp;</td></tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <?php endif ?>
                <?php if($ArrayParametrosExibicao['exibe_ped_nao_integrado_crm'] === true): ?>
                <tr>
                    <td >&nbsp;</td>
                    <td width="18%" valign="top">
                        <div align="right">Pedidos Não Integrados - CRM :</div>
                    </td>
                    <td width="1%" >&nbsp;</td>
                    <td width="76%" valign="top">
                        <div align="left">
                            <?php echo number_format_min($PedidosNaoIntegradosTotal,2,',','.'); ?>
                                <table border="0" id="tabela_estoque_pedidos_ds">
                                    <tr bgcolor="#cccccc">
                                        <td bgcolor="#DAE8F4" class="tit_tabela">Pedido</td>
                                        <td bgcolor="#DAE8F4" class="tit_tabela">Cliente</td>
                                        <td bgcolor="#DAE8F4" class="tit_tabela">Qtde</td>
                                        <td bgcolor="#DAE8F4" class="tit_tabela">Data de entrega</td>
                                        <td bgcolor="#DAE8F4" class="tit_tabela">Vendedor</td>
                                    </tr>
                                <?php
                                $i_previsao = 0;
                                if(count($ar_pedcrm) > 0){
                                    foreach($ar_pedcrm as $k => $v){
                                        $bg_color = ($i_pedcrm % 2 == 0) ? '#EAEAEA' : '#FFFFFF';
                                        $i_pedcrm++;
                                ?>
                                        <tr bgcolor="<?php echo $bg_color; ?>">
                                            <td><?php echo $v[0]; ?></td>
                                            <td><?php echo $v[1]; ?></td>
                                            <td align="right"><?php echo number_format_min($v[2],2,',','.'); ?></td>
                                            <td align="right"><?php echo uB::DataEn2Br($v[3]); ?></td>
                                            <td><?php echo $v[4]; ?></td>
                                        </tr>
                                <?php }
                                } ?>
                                <tr><td>&nbsp;</td></tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <?php endif ?>
                <tr>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                </tr>
                <?php if($PermiteVerInfCompra){ ?>
                <tr>
                    <td bgcolor="#dbe9f4" class="sub_tit" colspan="4">
                        <div align="center">Informações de Compra</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" >&nbsp;</td>
                </tr>
                <tr>
                    <td >&nbsp;</td>
                    <td colspan=2 width="18%" >&nbsp;</td>
                    <td width="76%" align="left">
                        <table border="0">
                            <tr bgcolor="#CCCCCC">
                                <td bgcolor="#DAE8F4" class="tit_tabela">Pedido</td>
                                <td bgcolor="#DAE8F4" class="tit_tabela">Data</td>
                                <td bgcolor="#DAE8F4" class="tit_tabela">Fornecedor</td>
                                <td bgcolor="#DAE8F4" class="tit_tabela">Previsão de entrega</td>
                                <td bgcolor="#DAE8F4" class="tit_tabela">Quantidade</td>
                            </tr>
                            <?php
                                $i_previsao = 0;
                                if(count($ar_previsao) > 0){
                                    foreach($ar_previsao as $k => $v){
                                        $bg_color = ($i_estoque % 2 == 0) ? '#EAEAEA' : '#FFFFFF';
                                        $i_estoque++;
                            ?>
                                        <tr bgcolor="<?php echo $bg_color; ?>">
                                            <td><?php echo $v[0]; ?></td>
                                            <td align="right"><?php echo uB::DataEn2Br($v[1]); ?></td>
                                            <td><?php echo $v[2]; ?></td>
                                            <td align="right"><?php echo uB::DataEn2Br($v[3]); ?></td>
                                            <td align="right"><?php echo number_format_min($v[4],2,',','.'); ?></td>
                                        </tr>
                            <?php }
                                } ?>
                        </table>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <!--     </div>
            </center>
        -->
    </body>
</html>