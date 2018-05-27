<?php
/*
 * gera_orcamento_atend_lab.php
 * Autor: Alex
 * 02/09/2011 15:03:01
 */
session_start();
header("Content-Type: text/html; charset=ISO-8859-1");
if($_SESSION['id_usuario'] == ''){
    echo '<script type="text/javascript"> alert(\'Usuário não está logado.\'); window.location.href = window.location.href; </script>';
    exit;
}
include('../../conecta.php');
include('../../functions.php');

$SqlCampoIdPessoa = "SELECT numreg,id_funcao_lupa FROM is_gera_cad_campos WHERE id_campo = 'id_pessoa' AND tipo_campo = 'lupa_popup' AND id_funcao_lupa = 'pessoa' AND (evento_change = '' OR evento_change IS NULL)";
$QryCampoIdPessoa = query($SqlCampoIdPessoa);
$ArCampoIdPessoa = farray($QryCampoIdPessoa);
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
<fieldset class="fs_custom"><legend>Gerar Or&ccedil;amento</legend>
    <strong>Conta: </strong><input type="hidden" name="edtid_pessoa" id="edtid_pessoa" readOnly="readOnly"/>
    <input type="text" name="edtdescrid_pessoa" id="edtdescrid_pessoa" size="50" readOnly="readOnly"/>
    <img border="0" width="15" height="15" id="btn_lupa_buscaedtid_pessoa" src="images/btn_busca.PNG" alt="Buscar" title="Buscar" style="cursor:pointer"/>
    <strong>Fabricante: </strong><?php echo TabelaParaCombobox('is_fabricante', 'numreg', 'nome_fabricante', 'edtid_fabricante');?>
    <input type="button" class="botao_jquery" id="btn_grade_atendimentos" value="Confirmar" />
</fieldset>
<fieldset class="fs_custom" id="fs_atendimentos"><legend>Atendimentos Em Or&ccedil;amento</legend>
    <div id="div_grade_atendimentos"></div>
</fieldset>
<script type="text/javascript">
    $(document).ready(function(){
        $(".botao_jquery").button();
        $("#btn_lupa_buscaedtid_pessoa").click(function(){
            window.open('gera_cad_lista.php?pfuncao=<?php echo $ArCampoIdPessoa['id_funcao_lupa'];?>&pdrilldown=1&plupa=<?php echo $ArCampoIdPessoa['numreg'];?>&pbloqincluir=1&pbloqexcluir=1','lupa_popup_id_pessoa','location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=810,height=550,top=250,left=250').focus();
        });
        $("#btn_grade_atendimentos").click(function(){
            var IdPessoa,IdFabricante;
            IdPessoa = $("#edtid_pessoa").val();
            IdFabricante = $("#edtid_fabricante").val();
            $.ajax({
                url:'modulos/laboratorio/gera_orcamento_atend_lab_grade_atend.php',
                global: false,
                type: "POST",
                data: ({
                    id_pessoa:IdPessoa,
                    id_fabricante:IdFabricante
                }),
                dataType: "html",
                async: true,
                beforeSend: function(){
                    $("#div_grade_atendimentos").html(HTMLLoadingGeral);
                },
                error: function(){
                    alert('Erro com a requisição');
                },
                success: function(responseText){
                    $("#div_grade_atendimentos").html(responseText);
                }
            });
        });
    });
</script>
