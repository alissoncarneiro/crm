<?php
/*
 * c_coaching_grade_agenda.php
 * Autor: Alex
 * 21/07/2011 15:33:00
 */
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('../../../../conecta.php');
require('../../../../functions.php');
require('../../../../classes/class.uB.php');
require('../../../../classes/class.Url.php');
require('c_coaching.class.Inscricao.php');
require('c_coaching.class.InscricaoCurso.php');

if(!$_POST){
    BloqueiaAcessoDireto();
}

$IdInscricao = $_POST['id_inscricao'];
$Inscricao = new Inscricao($IdInscricao);

$DataInscricao = $Inscricao->getDadosInscricao('dt_inscricao');

$SqlParte = "SELECT * FROM c_coaching_parte WHERE id_curso = ".$_POST['id_curso'];
$QryParte = query($SqlParte);
$ArOptionsParte = array(array('','Todas'));
while($ArParte = farray($QryParte)){
    $ArOptionsParte[] = array($ArParte['numreg'],$ArParte['nome_parte']);
}

$SqlModulo = "SELECT * FROM c_coaching_modulo WHERE id_parte IN(SELECT numreg FROM c_coaching_parte WHERE id_curso = ".$_POST['id_curso'].")";
$QryModulo = query($SqlModulo);
$ArOptionsModulo = array(array('','Todos'));
while($ArModulo = farray($QryModulo)){
    $ArOptionsModulo[] = array($ArModulo['numreg'],$ArModulo['nome_modulo']);
}

$SqlLocal = "SELECT DISTINCT t2.numreg,t2.nome_local_curso FROM c_coaching_agenda_curso t1 
                                                    INNER JOIN c_coaching_local_curso t2 ON t1.id_local_curso = t2.numreg 
                                                    WHERE t1.id_curso = ".$_POST['id_curso']." 
                                                    AND t1.dt_limite_inscricao >= '".$DataInscricao."'";

$QryLocal = query($SqlLocal);
$ArOptionsLocal = array(array('','Todos'));
while($ArLocal = farray($QryLocal)){
    $ArOptionsLocal[] = array($ArLocal['numreg'],$ArLocal['nome_local_curso']);
}

$SqlHotel = "SELECT numreg,nome_hotel FROM c_coaching_hotel ORDER BY nome_hotel ASC";
$QryHotel = query($SqlHotel);
$ArOptionsHotel = array(array('','Todos'));
while($ArHotel = farray($QryHotel)){
    $ArOptionsHotel[] = array($ArHotel['numreg'],$ArHotel['nome_hotel']);
}

$SqlInstrutor = "SELECT DISTINCT t2.numreg,t2.nome_usuario FROM c_coaching_agenda_curso t1 
                                                    INNER JOIN is_usuario t2 ON t1.id_instrutor = t2.numreg 
                                                    WHERE t1.id_curso = ".$_POST['id_curso']." 
                                                    AND t1.dt_limite_inscricao >= '".$DataInscricao."'";
$QryInstrutor = query($SqlInstrutor);
$ArOptionsInstrutor = array(array('','Todos'));
while($ArInstrutor = farray($QryInstrutor)){
    $ArOptionsInstrutor[] = array($ArInstrutor['numreg'],$ArInstrutor['nome_usuario']);
}
?>
<strong>Parte:</strong>
<select id="edtid_parte">
    <?php echo Array2Options($ArOptionsParte,$_POST['id_parte']);?>
</select>
<strong>M&oacute;dulo:</strong>
<select id="edtid_modulo">
    <?php echo Array2Options($ArOptionsModulo,$_POST['id_modulo']);?>
</select>
<strong>Local:</strong>
<select id="edtid_local_curso">
    <?php echo Array2Options($ArOptionsLocal,$_POST['id_local_curso']);?>
</select>
<strong>Hotel:</strong>
<select id="edtid_hotel">
    <?php echo Array2Options($ArOptionsHotel,$_POST['id_hotel']);?>
</select>
<br/>
<strong>Instrutor:</strong>
<select id="edtid_instrutor">
    <?php echo Array2Options($ArOptionsInstrutor,$_POST['id_instrutor']);?>
</select>
<strong>Per&iacute;odo:</strong>
    <input type="text" class="c_campo_data" name="edtdt_de" id="edtdt_de" value="<?php echo $_POST['dt_de'];?>"/>
    <input type="text" class="c_campo_data" name="edtdt_ate" id="edtdt_ate" value="<?php echo $_POST['dt_ate'];?>"/>
<input type="button" id="btnfiltrar_grade" class="botao_jquery" value="Filtrar"/>
<input type="button" id="btn_limpar_filtrar_grade" class="botao_jquery" value="Limpar"/>
<script type="text/javascript">
    $(document).ready(function(){
        $(".botao_jquery").button();
        
        $("#edtdt_de,#edtdt_ate").datepicker({
            showOn: "button",
            buttonImage: "../../../../images/agenda.gif",
            buttonImageOnly: true,
            changeMonth:true, 
            changeYear:true
        });
        
        $("#btnfiltrar_grade").click(function(){
            RecarregaGradeAgendas($("#edtid_curso").val(),$("#edtid_parte").val(),$("#edtid_modulo").val(),$("#edtid_local_curso").val(),$("#edtid_hotel").val(),$("#edtid_instrutor").val(),$("#edtdt_de").val(),$("#edtdt_ate").val());
        });
        
        $("#btn_limpar_filtrar_grade").click(function(){
            RecarregaGradeAgendas($("#edtid_curso").val(),'','','','','','','');
        });
        
        $(".btn_adiciona_agenda").click(function(){
            if(confirm("Deseja adiconar a agenda ?")){
                var IdAgenda = $(this).attr("numreg");
                $.ajax({
                    url: "c_coaching_tela_inscricao_post.php",
                    global: false,
                    type: "POST",
                    data: ({
                        id_requisicao: 2,
                        pnumreg: $("#pnumreg").val(),
                        id_agenda: IdAgenda
                    }),
                    dataType: "html",
                    async: true,
                    beforeSend: function(){

                    },
                    error: function(){
                        alert("Erro com a requisição");

                    },
                    success: function(responseText){
                        RecarregaGradeAgendas($("#edtid_curso").val(),$("#edtid_parte").val(),$("#edtid_modulo").val(),$("#edtid_local_curso").val(),$("#edtid_hotel").val(),$("#edtid_instrutor").val(),$("#edtdt_de").val(),$("#edtdt_ate").val());
                        RecarregaGradeAgendasSelecionadas();
                    }
                });
            }
            return;
        }).css("cursor","pointer");
    });
</script>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" class="venda_tabela_itens">
    <tr class="venda_titulo_tabela">
        <td width="15">&nbsp;</td>
        <td>Parte</td>
        <td>M&oacute;dulo</td>
        <td>De</td>
        <td>Até</td>
        <td>Local</td>
        <td>Hotel</td>
        <td>Instrutor</td>
        <td>Vagas Restantes</td>
    </tr>                       
<?php
$SqlGradeCurso = "SELECT 
                        t1.numreg,
                        t2.nome_modulo,
                        t3.nome_local_curso,
                        t4.nome_parte,
                        t8.nome_hotel,
                        t11.nome_usuario,
                        (SELECT MIN(t7.dt_curso) FROM c_coaching_agenda_curso_detalhe t7 WHERE t7.id_agenda_curso = t1.numreg) AS dt_de,
                        (SELECT MAX(t6.dt_curso) FROM c_coaching_agenda_curso_detalhe t6 WHERE t6.id_agenda_curso = t1.numreg) AS dt_ate,
                        (SELECT COUNT(DISTINCT t10.id_pessoa) FROM c_coaching_inscricao_curso_detalhe t10 WHERE t10.id_agenda = t1.numreg) AS qtde_inscritos,
                        (CAST(t1.qtde_max_inscricao AS SIGNED INT) - (SELECT qtde_inscritos)) AS vagas_restantes
                    FROM c_coaching_agenda_curso t1
                    INNER JOIN c_coaching_modulo t2 ON t1.id_modulo = t2.numreg
                    INNER JOIN c_coaching_local_curso t3 ON t1.id_local_curso = t3.numreg 
                    INNER JOIN c_coaching_parte t4 ON t1.id_parte = t4.numreg
                    INNER JOIN c_coaching_hotel t8 ON t1.id_hotel = t8.numreg
                    INNER JOIN is_usuario t11 ON t1.id_instrutor = t11.numreg
                    WHERE t1.id_curso = ".$_POST['id_curso']." 
                    AND t1.dt_limite_inscricao >= '".$DataInscricao."'
                    AND NOT t1.numreg IN(SELECT t5.id_agenda FROM c_coaching_inscricao_curso t5 WHERE t5.id_inscricao = '".$IdInscricao."' AND t5.id_venda IS NULL)
                    AND NOT t1.numreg IN(SELECT t9.id_agenda FROM c_coaching_inscricao_curso_detalhe t9 WHERE t9.id_inscricao = '".$IdInscricao."')
                    AND t1.id_situacao not in(4,5)";
/* Aplicando filtros */
if($_POST['id_parte'] != ''){
    $SqlGradeCurso .= " AND t4.numreg = '".$_POST['id_parte']."'";
}
if($_POST['id_modulo'] != ''){
    $SqlGradeCurso .= " AND t2.numreg = '".$_POST['id_modulo']."'";
}
if($_POST['id_local_curso'] != ''){
    $SqlGradeCurso .= " AND t3.numreg = '".$_POST['id_local_curso']."'";
}
if($_POST['id_hotel'] != ''){
    $SqlGradeCurso .= " AND t1.id_hotel = '".$_POST['id_hotel']."'";
}
if($_POST['id_instrutor'] != ''){
    $SqlGradeCurso .= " AND t1.id_instrutor = '".$_POST['id_instrutor']."'";
}
if($_POST['dt_de'] != ''){
    $SqlGradeCurso .= " HAVING dt_de >= '".dtbr2en($_POST['dt_de'])." 00:00:00'";
}
if($_POST['dt_ate'] != ''){
    $SqlGradeCurso .= (($_POST['dt_de'] != '')?' AND ':' HAVING ')." dt_ate <= '".dtbr2en($_POST['dt_ate'])." 00:00:00'";
}
$QryGradeCurso = query($SqlGradeCurso);
$i = 0;
while($ArGradeCurso = farray($QryGradeCurso)){
    $bgcolor = ($i%2==0)?'#EAEAEA':'#FFFFFF';$i++;
?>
<tr bgcolor="<?php echo $bgcolor;?>">
    <td align="center"><img src="../../../../images/btn_add.PNG" class="btn_adiciona_agenda" numreg="<?php echo $ArGradeCurso['numreg'];?>" /></td>
    <td><?php echo $ArGradeCurso['nome_parte'];?></td>
    <td><?php echo $ArGradeCurso['nome_modulo'];?></td>
    <td><?php echo dten2br($ArGradeCurso['dt_de']);?></td>
    <td><?php echo dten2br($ArGradeCurso['dt_ate']);?></td>
    <td><?php echo $ArGradeCurso['nome_local_curso'];?></td>
    <td><?php echo $ArGradeCurso['nome_hotel'];?></td>
    <td><?php echo $ArGradeCurso['nome_usuario'];?></td>
    <td><?php echo $ArGradeCurso['vagas_restantes'];?></td>
</tr>
<?php } ?>
</table>