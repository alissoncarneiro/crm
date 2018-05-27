<?php

/*
 * gera_orcamento_atend_lab_grade_atend.php
 * Autor: Alex
 * 05/09/2011 14:03:25
 */
session_start();
header("Content-Type: text/html; charset=ISO-8859-1");
if($_SESSION['id_usuario'] == ''){
    echo '<script type="text/javascript"> alert(\'Usuário não está logado.\'); window.location.href = window.location.href; </script>';
    exit;
}
include('../../conecta.php');
include('../../functions.php');
include('../../classes/class.uB.php');

$_POST = uB::UrlDecodePost($_POST);

if($_POST['id_pessoa'] == ''){
    echo 'Selecione um cliente.';
    exit;
}

$i = 0;
$SqlAtendimentos = "    SELECT  
                            numreg,
                            id_usuario_resp,
                            nr_nota,
                            dt_inicio,
                            dt_prev_fim
                        FROM
                            is_atividade 
                        WHERE 
                            id_tp_atividade = 55 
                        AND 
                            id_status_reparo = 8 
                        AND 
                            id_pessoa = ".$_POST['id_pessoa']." 
                        AND 
                            id_fabricante = '".$_POST['id_fabricante']."' 
                        AND 
                            id_orcamento IS NULL";
$QryAtendimentos = query($SqlAtendimentos);
$NumRowsAtendimentos = numrows($QryAtendimentos);
?>
<table width="100%" border="0" cellspacing="2" cellpadding="0">
    <tr>
        <th width="10">&nbsp;</th>
        <th>N&ordm; Protocolo</th>
        <th>Respons&aacute;vel</th>
        <th>Nota Fiscal</th>
        <th>Dt. In&iacute;cio</th>
        <th>Dt. Prazo</th>
    </tr>
    <?php 
    while($ArAtendimentos = farray($QryAtendimentos)){
        $i++;
        $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
        $SqlItensAtendimento = "SELECT COUNT(*) AS CNT FROM is_produto_orcamento_lab WHERE id_atividade = ".$ArAtendimentos['numreg']." AND sn_orcamento = 1";
        $QryItensAtendimento = query($SqlItensAtendimento);
        $ArItensAtendimento = farray($QryItensAtendimento);
        if($ArItensAtendimento['CNT'] <= 0){
            continue;
        }
    ?>
    <tr bgcolor="<?php echo $bgcolor;?>">
        <td><input type="checkbox" name="edtchk_atendimento_<?php echo $ArAtendimentos['numreg'];?>" id="edtchk_atendimento_<?php echo $ArAtendimentos['numreg'];?>"/></td>
        <td><?php echo $ArAtendimentos['numreg'];?></td>
        <td><?php echo DeparaCodigoDescricao('is_usuario',array('nome_usuario'),array('numreg' => $ArAtendimentos['id_usuario_resp']));?></td>
        <td><?php echo $ArAtendimentos['nr_nota'];?></td>
        <td><?php echo dten2br($ArAtendimentos['dt_inicio']);?></td>
        <td><?php echo dten2br($ArAtendimentos['dt_prev_fim']);?></td>
    </tr>
    <?php } ?>
</table>
<?php if($NumRowsAtendimentos > 0){ ?>
<input type="button" class="botao_jquery" id="btn_gera_orcamento" value="Confirmar" />
<script type="text/javascript">
    $(document).ready(function(){
        $(".botao_jquery").button();
        $("#btn_gera_orcamento").click(function(){
            var Data = '';
            Data += 'id_pessoa=<?php echo $_POST['id_pessoa'];?>';
            Data += '&id_fabricante=<?php echo $_POST['id_fabricante'];?>';
            
            $("#fs_atendimentos :checkbox:checked").each(function(){
                Data += '&'+$(this).attr("name") + '=1';
            });
            
            $.ajax({
                url:'modulos/laboratorio/gera_orcamento_atend_lab_post.php',
                global: false,
                type: "POST",
                data: Data,
                dataType: "html",
                async: true,
                beforeSend: function(){

                },
                error: function(){
                    alert('Erro com a requisição');
                },
                success: function(responseText){
                    $("#btn_grade_atendimentos").click();
                    alert(responseText);
                }
            });
        });
    });
</script>
<?php } ?>