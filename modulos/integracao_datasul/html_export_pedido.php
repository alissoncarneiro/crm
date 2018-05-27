<?php
session_start();
header("Content-Type: text/html; charset=ISO-8859-1");
if($_SESSION['id_usuario'] == ''){
    echo '<script type="text/javascript"> alert(\'Usuário não está logado.\'); window.location.href = window.location.href; </script>';
    exit;
}
include('../../conecta.php');
include('../../functions.php');
require("../../classes/class.uB.php");
require("../venda/classes/class.Venda.Parametro.php");
require("class.VendaExpTxt.php");
require("class.VendaExpTxtCustom.php");
$VendaExpTxt = new VendaExpTxtCustom();
if($_GET['dt_base_exportacao'] != ''){
    $DataBaseExportacao = dtbr2en($_GET['dt_base_exportacao']);
    $VendaExpTxt->setDataBaseExportacao($DataBaseExportacao);
}
$DataBaseExportacao = $VendaExpTxt->getDataBaseExportacao();
?>
<style type="text/css">
.fs_custom legend{
    font-weight:bold;
    font-size:14px;
}
.fs_custom table{
    border: 1px solid #ACC6DB;
}
.fs_custom table th{
    font-weight: bold;
    color: #345c7d;
    text-align: left;
    padding-left: 5px;
    background-color: #DAE8F4;
}
.campo_data{
    width:65px;
    text-align: center;
}
</style>
<fieldset class="fs_custom"><legend>Exportar Pedidos</legend>
    <?php if($VendaExpTxt->getVendaParametro()->getSnExportaNaDataEntrega()){?>
        Data base da exporta&ccedil;&atilde;o: <input type="text" class="campo_data" id="edtdt_base_exportacao" value="<?php echo dten2br($DataBaseExportacao);?>" />
        <input type="button" class="botao_jquery" id="btn_recarregar_tela" value="Recarregar" />
    <?php } ?>
    <table width="100%" border="0" cellspacing="2" cellpadding="0">
        <tr>
            <th>N&ordm; Pedido Cliente</th>
            <th>Data Pedido</th>
            <th>Data Entrega</th>
            <th>Cliente</th>
            <th>CNPJ/CPF</th>
            <th>Transp.</th>
            <th>CFOP</th>
        </tr>
        <?php
        $SqlPedidos = $VendaExpTxt->getSqlPedidos(false);
        $QryPedidos = query($SqlPedidos);
        $i = 0;
        while($ArPedidos = farray($QryPedidos)){
            $i++;
            $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
        ?>
        <tr bgcolor="<?php echo $bgcolor;?>">
            <td><?php echo $ArPedidos['id_pedido_cliente'];?></td>
            <td><?php echo dten2br($ArPedidos['dt_pedido']);?></td>
            <td><?php echo dten2br($ArPedidos['dt_entrega']);?></td>
            <td><?php echo $ArPedidos['fantasia_apelido'];?></td>
            <td><?php echo $ArPedidos['cnpj_cpf'];?></td>
            <td><?php echo $ArPedidos['nome_abrev_transportadora'];?></td>
            <td><?php echo $ArPedidos['id_cfop_erp'];?></td>
        </tr>
        <?php } ?>
    </table>
    <input type="button" class="botao_jquery" id="btn_exportar_pedidos" value="Exportar" />
</fieldset>

<fieldset class="fs_custom"><legend>Arquivos Exportados</legend>
    <div style="height: 300px;overflow: auto;">
        <table width="100%" border="0" cellspacing="2" cellpadding="0">
            <tr>
                <th>#</th>
                <th>Data/Hora</th>
                <th>Usu&aacute;rio</th>
                <th width="50">Download</th>
            </tr>
            <?php
            $SqlPedidosExportados = "SELECT ".((TipoBancoDados == 'mssql')?'TOP(100)':'')." t1.*, t2.nome_usuario FROM is_pedido_txt_datasul t1 LEFT JOIN is_usuario t2 ON t1.id_usuario = t2.numreg ORDER BY t1.dthr_exportacao DESC ".((TipoBancoDados == 'mysql')?'LIMIT 100':'');
            $QryPedidosExportados = query($SqlPedidosExportados);
            $i = 0;
            while($ArPedidosExportados = farray($QryPedidosExportados)){
                $i++;
                if($i > 100){break;}
                $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
            ?>
            <tr bgcolor="<?php echo $bgcolor;?>">
                <td><?php echo $ArPedidosExportados['numreg'];?></td>
                <td><?php echo uB::DataEn2Br($ArPedidosExportados['dthr_exportacao'],true);?></td>
                <td><?php echo $ArPedidosExportados['nome_usuario'];?></td>
                <td align="center"><img src="modulos/venda/img/salvar_pequeno.png" class="btn_download" title="Download" pnumreg="<?php echo $ArPedidosExportados['numreg'];?>" /></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</fieldset>
<script type="text/javascript">
    $(document).ready(function(){
        $(".botao_jquery").button();

        $("#btn_recarregar_tela").click(function(){
            var Url = 'modulos/integracao_datasul/html_export_pedido.php';
            Url = Url + '?dt_base_exportacao=' + $("#edtdt_base_exportacao").val();
            exibe_programa(Url);
        });

        $("#edtdt_base_exportacao").datepicker({
            showOn: "button",
            buttonImage: "images/agenda.gif",
            buttonImageOnly: true,
            changeMonth:true,
            changeYear:true
        });
        $("#btn_exportar_pedidos").click(function(){
            var width = 500;
            var height = 500;
            var ScreenWidth = screen.width;
            var ScreenHeight = screen.height;

            var left = (ScreenWidth - width) / 2;
            var top = (ScreenHeight - height) / 2;

            if(confirm('Exportar os Pedidos ?')){
                window.open('modulos/integracao_datasul/interface_pedido_exp.php?dt_base_exportacao=<?php echo dten2br($DataBaseExportacao);?>','exportacao_pedido','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,width=' + width + ',height=' + height + ',left=' + left + ',top = ' + top).focus();
            }
            return false;
        });

        $(".btn_download").click(function(){
            if(confirm('Deseja fazer download do arquivo ?')){
                window.open('modulos/integracao_datasul/interface_pedido_exp_download.php?pnumreg=' + $(this).attr("pnumreg"),'exportacao_pedido_download','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1').focus();
            }
        }).css("cursor","pointer");
    });
</script>
