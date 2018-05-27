<?php
include('../../conecta.php');
include('../../functions.php');
include('../../classes/class.GeraLinhaTxt.php');
include('../../classes/class.uB.php');
include('../../classes/class.Usuario.php');
include('class.ClienteExpTxt.php');
include('class.ClienteExpTxtCustom.php');

$ClienteExpTxt = new ClienteExpTxtCustom();
?>
<div id="div_rec_js"></div>
<div id="div_relatorio_pedidos">
    <div id="conteudo_detalhes">
        <table width="100%" border="0" cellspacing="2" cellpadding="0">
            <tr>
                <td width="1%"></td>
                <td colspan="3"><br>
                    <div align="left"><img src="images/seta.gif" width="4" height="7" /><span class="tit_detalhes">Exportar Clientes </span></div>
                    <br></td>
            </tr>
            <tr>
                <td height="23" colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td align="right"><span style="font-weight: bold">Qtde. para Exportar</span>: </td>
                <td>&nbsp;</td>
                <?php $QryClientes = query($ClienteExpTxt->getSqlClientes()) ?>
                <td><?php echo numrows($QryClientes) ?></td>
            </tr>

            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr><td>&nbsp;</td>
                <td width="24%">&nbsp;</td>
                <td width="2%">&nbsp;</td><td width="73%"><div align="left"></div></td></tr>
            <tr><td>&nbsp;</td>
                <td width="24%">&nbsp;</td>
                <td width="2%">&nbsp;</td><td width="73%"><div align="left">
                        <input name="Submit" type="button" class="botao_form" value="Confirmar" onClick="javascript:abre_popup_integracao('modulos/integracao_datasul/interface_cliente_exp.php');" />
                    </div></td></tr>
            <tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>

            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><div align="left"></div></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>


            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
            </tr>
        </table>
    </div>
</div>
