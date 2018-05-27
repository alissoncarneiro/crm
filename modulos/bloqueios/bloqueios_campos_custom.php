<?php
session_start();
require_once("../../conecta.php");
$ArrayFuncoesDesativadas = array('pessoa');
?>
<form>
<div id="div_rec_js"></div>
<div id="div_relatorio_pedidos">
    <div id="conteudo_detalhes">
        <table width="100%" border="0" cellspacing="2" cellpadding="0">
            <tr>
                <td width="1%"></td>
                <td colspan="3"><br />
                    <div align="left"><img src="images/seta.gif" width="4" height="7" /><span class="tit_detalhes">Bloqueios de Campos</span></div><br />
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <input type="hidden" name="pfuncao" value="relac_cad_lista">
                    <input type="hidden" name="pnumreg" value="13">
                    <input type="hidden" name="popc" value="alterar">
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td width="24%">&nbsp;</td>
                <td colspan="2">&nbsp;</td>
            </tr>

            <tr>
                <td>&nbsp;</td>
                <td><div align="right">Perfil:</div></td>
                <td width="2%">&nbsp;</td>
                <td width="73%">
                    <div align="left">
                        <select name="edtid_perfil">
                            <?php
                            $qry_perfil = query("SELECT id_perfil,nome_perfil FROM is_perfil");
                            while($ar_perfil = farray($qry_perfil)){
                                echo '<option value="'.$ar_perfil['id_perfil'].'">'.$ar_perfil['nome_perfil'].'</option>';
                            }
                            ?>
                        </select>
                        <select name="edtid_cad" id="edtid_cad">
                            <?php
                            $qry_cad = query("SELECT id_funcao,id_cad,titulo FROM is_gera_cad ORDER BY titulo");
                            while($ar_cad = farray($qry_cad)){
                                if(array_search($ar_cad['id_cad'], $ArrayFuncoesDesativadas) !== false){
                                    continue;
                                }
                                echo '<option value="'.$ar_cad['id_cad'].'">'.$ar_cad['titulo'].' - '.$ar_cad['id_cad'].'</option>';
                            }
                            ?>
                        </select>
                        <input type="button" onclick="javascript:bloqueios_campos_custom_p1();" value="Exibir" class="botao_form" />
                    </div>
                </td>
            </tr>
        </table>
        <div id="div_cont_bloqueio" align="center">
        </div>
    </div>
</div>
</form>
