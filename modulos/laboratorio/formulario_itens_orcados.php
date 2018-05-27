<?php
# formulario_itens_orcados
# Expression package is undefined on line 3, column 5 in Templates/Scripting/EmptyPHP.php.
# Autor: Rodrigo Piva
# 30/08/2011
#
# Log de Alterações
# yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
#
header("Content-Type: text/html; charset=ISO-8859-1");
include_once('../../conecta.php');
include_once('../../functions.php');

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

<?php
$IdAtendimento = $_GET['pnumreg'];

$ArAtividade = (farray(query("SELECT id_orcamento FROM is_atividade WHERE numreg = '".$IdAtendimento."'")));
$ReadonlyCampos = ($ArAtividade['id_orcamento']!='')?'readonly="readonly"':'';
$DisableCampos = ($ArAtividade['id_orcamento']!='')?'disabled':'';

$SqlComponentesManutencao = "SELECT * FROM is_produto_orcamento_lab WHERE id_atividade = '".$IdAtendimento."'";
$QryComponentesManutencao = query($SqlComponentesManutencao);
?>
<form id="form1" name="form1" method="post" action="modulos/laboratorio/grava_itens_orcamento.php?pnumreg=<?php echo $IdAtendimento;?>">
    <fieldset class="fs_custom"><legend>Itens para o Orçamento</legend>
        <div style="height: 500px;overflow: auto;">
            <table class="bordatabela" width="100%" cellspacing="2" cellpadding="2" border="0" align="center">
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Qtde</th>
                    <th>UN</th>
                    <th>Tipo de Atendimento</th>
                    <th>Gera Orçamento</th>
                   <th>Motivo não orçamento</th>
                   <th>Observação</th>

                </tr>
                <?php
                $i = 0;
                while($ArComponenteManutencao = farray($QryComponentesManutencao)){
                     $IdErpProduto  = deparaIdErpCrm($ArComponenteManutencao['id_produto'],"id_produto_erp","numreg","is_produto");
                     $NomeProduto   = deparaIdErpCrm($ArComponenteManutencao['id_produto'],"nome_produto","numreg","is_produto");
                     $qtde          = $ArComponenteManutencao['qtde'];
                     $UnidadeMedida = deparaIdErpCrm($ArComponenteManutencao['id_unid_medida'],"id_unid_medida_erp","numreg","is_unid_medida");
                     $TipodeAtendimento = deparaIdErpCrm($ArComponenteManutencao['id_tp_chamado_atec'],"nome_tp_chamado_atec","numreg","is_tp_chamado_atec");

                ?>
                <tr id="linha3103" onmouseout="this.style.background='#EBEBEB';" onmouseover="this.style.background='lightblue';" style="background: none repeat scroll 0% 0% rgb(235, 235, 235);">
                    <td><?php echo $IdErpProduto; ?></td>
                    <td><?php echo $NomeProduto; ?></td>
                    <td><?php echo $qtde; ?></td>
                    <td><?php echo $UnidadeMedida; ?></td>
                    <td><?php echo $TipodeAtendimento; ?></td>
                    <td>
                        <input type="checkbox" <?php echo $DisableCampos; ?> <?php echo ($ArComponenteManutencao['sn_orcamento']=='1')?'checked="true"':''?> id="c<?php echo $i;?>" name="c<?php echo $i;?>">
                        </input>
                    </td>

                    <td>
                        <select <?php echo $DisableCampos; ?> id="b<?php echo $i;?>" name="b<?php echo $i;?>">
                            <option value=""></option>
                            <?php
                            $SqlMotivo = "SELECT * FROM is_motivo_n_orcto";
                            $QryMotivo = query($SqlMotivo);
                            while($ArMotivo = farray($QryMotivo)){
                                if($ArMotivo['numreg'] == $ArComponenteManutencao['id_motivo_n_orcto']){
                                    echo '<option selected="selected" value="'.$ArMotivo['numreg'].'">'.$ArMotivo['desc_motivo'].'</option>';
                                }else{
                                    echo '<option value="'.$ArMotivo['numreg'].'">'.$ArMotivo['desc_motivo'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td><input type="text" size="40" <?php echo $ReadonlyCampos; ?> id="obs<?php echo $i;?>" name="obs<?php echo $i++;?>" value="<?php echo $ArComponenteManutencao['obs']; ?>"></input></td>
                </tr>
                <?php
                }
                ?>
            </table>
            <td align="center" colspan="4">
                <div style="height:1px; width:100%; background-color:#E0E0E0; margin-top:2px; margin-bottom:2px;"></div>
                <?php
                    if($DisableCampos == ''){
                ?>
                    <input class="botao_form ui-button ui-widget ui-state-default ui-corner-all" value="Salvar" type="submit">
                <?php } ?>
                <div style="height:1px; width:100%; background-color:#E0E0E0; margin-top:2px; margin-bottom:2px;"></div>
            </td>
        </div>
    </fieldset>
</form>
