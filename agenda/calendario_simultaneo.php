<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header('Content-Type: text/html; charset=utf-8');
require_once("../conecta.php");
?>
<style type="text/css">
    /* CSS Document */
    body {
        font-family:Verdana, Arial, Helvetica, sans-serif;
        font-size:10px;
        color: #345C7D;
    }
    .cal_time{
        border-top: 2px solid  #6593CF;
        vertical-align:top;
        font-family:"Trebuchet Ms";
        font-size:20px;
        color:#003366;
    }
    .cal_time_font_small{
        font-size:14px;
        vertical-align:top;
        padding-left: 3px;
    }
    .div_ativ{
        font-size:12px;
        vertical-align:middle;
        padding-left: 3px;
        border: 3px outset #0066FF;
        font-weight:bold;
    }
    .cal_title{
        background: #A5C7E4;
        font-family: "Trebuchet Ms";
        font-size:16px;
        font-weight:bold;
        padding-left:10px;
    }
    .cal_daysweekname{
        background: #A5C7E4;
        font-weight:bold;
        font-size:10px;
    }
    .cal_days{
        background: #E9E9E9;
        font-size:14px;
    }
    .cal_days_fds{
        background: #F9E3B7;
        font-size:14px;
    }
    .cal_days_off{
        color:#CCCCCC;
        font-size:14px;
        background: #E9E9E9;

    }
    .cal_days_especial{
        background: #F4CD7B;
        font-weight:bold;
        font-size:14px;
    }
    .toolTip{
        font-family:Verdana;
        font-size:10px;
        text-align:left;
        position:absolute;
        display: none;
        visibility: hidden;
        z-index: 2000;
        border: 1px solid #DBE9F4;
        background-color:#F4F4FF;
        padding: 10px;
        width: 300px;
    }
    .tit_tabela{
        padding-left: 30px !important;
        padding-bottom: 20px !important;;
    }
</style>
<script src="../js/function.js"></script>
<?php

function alert($str){
    return "<script>alert('$str')</script>";
}

$mes_name = array("", "Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
?>
<div id="dica" class="toolTip"></div>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" style="padding: 20px;">
    <tr>
        <td colspan="3" valign="center">
            <div align="left" valign="top"> &nbsp;&nbsp;&nbsp;<img src="images/seta.gif" width="4" height="7" />
                <span class="tit_detalhes">Calend&aacute;rios Por Grupos </span>&nbsp;&nbsp;&nbsp;
                M&ecirc;s:
                <select id="edtmes" name="edtmes">
                    <?php
                    for($i = 1; $i <= 12; $i++){
                        if(!empty($_GET['mes']) && $_GET['mes'] == $i){
                            $selected = 'selected="selected"';
                        }
                        elseif(empty($_GET['mes']) && $i == date("n")){
                            $selected = 'selected="selected"';
                        }
                        else{
                            $selected = '';
                        }
                        echo '<option value="'.$i.'" '.$selected.'>'.$mes_name[$i].'</option>';
                    }
                    ?>
                </select>
                Ano: <select id="edtano" name="edtano">
                    <?
                    $ano_inicio = 1998; //(date("Y")*1)+10;
                    for($i = 0; $i < 20; $i++){
                        if(!empty($_GET['ano']) && $_GET['ano'] == $ano_inicio){
                            $selected = 'selected="selected"';
                        }
                        elseif(empty($_GET['ano']) && $ano_inicio == date("Y")){
                            $selected = 'selected="selected"';
                        }
                        else{
                            $selected = '';
                        }
                        echo '<option value="'.$ano_inicio.'" '.$selected.'>'.$ano_inicio.'</option>';
                        $ano_inicio = $ano_inicio + 1;
                    }
                    ?>
                </select>
                Grupo: <select size="1" name="edtgrupo" id="edtgrupo">
                    <?php
                    $qry_grupos = query("SELECT numreg,nome_grupo_cal FROM is_grupo_cal ORDER BY ORDEM ASC");
                    while($ar_grupos = farray($qry_grupos)){
                        if(!empty($_GET['grupo']) && $_GET['grupo'] == $ar_grupos['numreg']){
                            $selected = 'selected="selected"';
                        }
                        else{
                            $selected = '';
                        }
                        echo ('<option value="'.$ar_grupos['numreg'].'" '.$selected.'>'.$ar_grupos['nome_grupo_cal'].'</option>');
                    }
                    ?>
                </select>
                <input type="button" value="Exibir" class="botao_form" onclick="javascript:exibe_programa('agenda/calendario_simultaneo.php?grupo=' + document.getElementById('edtgrupo').value + '&mes=' + document.getElementById('edtmes').value + '&ano=' + document.getElementById('edtano').value);" />
            </div>	</td>
        <td colspan="1" align="right"></td>
    </tr>
</table>
<?php
$mes = empty($_GET['mes'])?date("n"):intval($_GET['mes']);
$ano = empty($_GET['ano'])?date("Y"):$_GET['ano'];
$id_grupo = empty($_GET['grupo'])?"0":$_GET['grupo'];
$qry_grupos = query("SELECT * FROM is_grupo_cal_participante WHERE id_grupo_cal = ".$id_grupo." ORDER BY ORDEM ASC");
?>
<table width="100%" border="0" cellspacing="5" cellpadding="0" >
    <tr>
        <td colspan="2" class="tit_tabela style1" style="font-size:18px;"><?php echo $mes_name[$mes]." ".$ano; ?></td>
    </tr>
    <?php
    $break = false;
    $numrows_ar_grupos = numrows($qry_grupos);
    while($ar_grupos = farray($qry_grupos)){
        if($break == false){
            echo "<tr>";
        }
        echo '<td width="50%" align="center">';

        $id_usuario = $ar_grupos['id_usuario'];
        $dia_semana = farray(query("select nome_auxiliar from is_usuario, is_auxiliar where is_usuario.dia_rodizio = is_auxiliar.id_auxiliar and id_usuario = '".$id_usuario."'"));

        $titulo_cal = $ar_grupos['titulo_calendario'].' '.$dia_semana["nome_auxiliar"];
        $mes_atual = "";
        $ano_atual = "";
        $q_dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
        $inicio = array("Sun" => 1, "Mon" => 2, "Tue" => 3, "Wed" => 4, "Thu" => 5, "Fri" => 6, "Sat" => 7);
        $mes_name = array("", "Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
        $dia_inicio = $inicio[date("D", strtotime($ano."-".$mes."-"."01"))];
        $loop = $dia_inicio + $q_dias_mes;
        while($loop % 7 != 0){
            $loop++;
        }
        ?>
        <table class="table_border" width="90%" border="0" cellspacing="2" cellpadding="0">
            <tr>
                <td height="20" colspan="7" align="left" valign="middle" class="cal_title"><?php echo ($titulo_cal); ?></td>
            </tr>
            <tr>
                <td height="10" align="center" valign="middle" class="cal_daysweekname">Dom</td>
                <td height="10" align="center" valign="middle" class="cal_daysweekname">Seg</td>
                <td height="10" align="center" valign="middle" class="cal_daysweekname">Ter</td>
                <td height="10" align="center" valign="middle" class="cal_daysweekname">Qua</td>
                <td height="10" align="center" valign="middle" class="cal_daysweekname">Qui</td>
                <td height="10" align="center" valign="middle" class="cal_daysweekname">Sex</td>
                <td height="10" align="center" valign="middle" class="cal_daysweekname">S&aacute;b</td>
            </tr>
            <?php
            $w_d = 1;
            $day_to_show = 1;

            $show = false;
            for($i = 0; $i < $loop; $i++){
                if($w_d == 1){
                    echo "<tr>";
                }
                if($q_dias_mes < $day_to_show){
                    $show = false;
                }
                if($dia_inicio == $w_d && $q_dias_mes > $day_to_show){
                    $show = true;
                }
                if($show == true){

                    $dia = number_format($day_to_show, 0, '', '');
                    $data = $ano."-".$mes."-".$dia;
                    $sql_ativ ="
                      SELECT t1.* 
                        FROM is_atividade t1 
                          LEFT JOIN is_atividade_participante_int t2 
                          ON t1.numreg = t2.id_atividade 
                          WHERE (t2.id_usuario = $id_usuario OR t1.id_usuario_resp =$id_usuario) 
                            AND t1.dt_inicio = '$data'";
                    $qry_ativ = query($sql_ativ);
                    $num_rows_ativ = numrows($qry_ativ);

                    if($num_rows_ativ == 0){
                        $class_day = 'cal_days'.(($w_d == 1 || $w_d == 7)?"_fds":"");
                        $div_info = 'style="cursor:pointer"';
                        $link_det = "gera_cad_detalhe.php?pfuncao=atividades_cad_lista&pdrilldown=1&pnumreg=-1";
                        $onclick = 'onclick="window.open(\''.$link_det.'\',\'\',\'width=750, height=550,scrollbars=yes,top=100,left=100\');" ';
                    }
                    else{
                        $class_day = "cal_days_especial";
                        $div_info = '';
                        $div_info = 'style="cursor:pointer;" onMouseMove="dica(\'';
                        $ids_atividades = "";
                        while($ar_ativ = farray($qry_ativ)){


                            //@mysql_result(query("SELECT nome_tp_atividade FROM is_tp_atividades WHERE id_tp_atividade = '" . $ar_ativ['id_tp_atividade'] . "'"), 0, 'nome_tp_atividade') ;
                            $div_info .= '<strong>'.$ar_ativ['hr_inicio'].((!empty($ar_ativ['hr_prev_fim']))?' &agrave;s '.$ar_ativ['hr_prev_fim'].' - ':'').'<br> Assunto:</strong> '.str_replace('"','&quot;',$ar_ativ['assunto']);
                            $qe = farray(query("select razao_social_nome from is_pessoa where numreg = '".$ar_ativ['id_pessoa']."'"));
                            $div_info .= '<br> <strong>Conta: </strong>'.$qe["razao_social_nome"].'<hr size=1 noshade=noshade>';
                            $ids_atividades .= "numreg@igual@s".$ar_ativ['numreg']."@s@or";
                            if($num_rows_ativ == 1){
                                $ids_atividades = $ar_ativ['id_atividade'];
                            }
                        }

                        $div_info .= '\',event);" onMouseOut="dica(\'\',event);"';
                        $link_det = "gera_cad_lista.php?pfuncao=atividades_cad_lista&pdrilldown=1&pfixo=".substr($ids_atividades, 0, strlen($ids_atividades) - 3);
                        $onclick = 'onclick="window.open(\''.$link_det.'\',\'\',\'width=750, height=550,scrollbars=yes,top=100,left=100\');" ';


                    }
                    echo ('<td height="20" class="'.$class_day.'" align="center" valign="middle" '.$div_info.$onclick.'>'.$day_to_show.'</td>');
                    $day_to_show++;
                }
                else{
                    if($day_to_show > 20){

                        $qty_days_mb_dt = date("Y-m-d", strtotime($ano.'-'.$mes.'-'.$q_dias_mes.' + 1 day'));
                        $day_to_show_off = ($i - $dia_inicio + 2 - $q_dias_mes);
                        $onclick_mb = " title=\"Ver calend&aacute;rio de ".$mes_name[intval(substr($qty_days_mb_dt, 5, 2))]." de ".substr($qty_days_mb_dt, 0, 4)."\" onclick=\"javascript:exibe_programa('agenda/calendario_simultaneo.php?grupo=".$id_grupo."&mes=".substr($qty_days_mb_dt, 5, 2)."&ano=".substr($qty_days_mb_dt, 0, 4)."');\" ";
                    }
                    else{
                        $qty_days_mb_dt = date("Y-m-d", strtotime($ano.'-'.$mes.'-'.'01 - 1 day'));
                        $qty_days_mb = substr($qty_days_mb_dt, 8, 2);
                        $day_to_show_off = ($qty_days_mb + $w_d - $dia_inicio + 1);
                        $onclick_mb = " title=\"Ver calend&aacute;rio de ".$mes_name[intval(substr($qty_days_mb_dt, 5, 2))]." de ".substr($qty_days_mb_dt, 0, 4)."\" onclick=\"javascript:exibe_programa('agenda/calendario_simultaneo.php?grupo=".$id_grupo."&mes=".substr($qty_days_mb_dt, 5, 2)."&ano=".substr($qty_days_mb_dt, 0, 4)."');\" ";
                    }
                    echo '<td class="cal_days'.(($w_d == 1 || $w_d == 7)?"_fds":"").' cal_days_off" style="cursor:pointer;"'.$onclick_mb.'>'.$day_to_show_off.'</td>';
                }
                if($w_d == 7){
                    echo "</tr>";
                    $w_d = 0;
                }
                $w_d++;
            }
            echo "</table>";
            echo "</td>";
            //if($numrows_ar_grupos == 1){
            //echo '<td width="50%">&nbsp;</td>';
            //}

            if($break == true){
                echo "</tr>";
                $break = false;
            }
            else{
                $break = true;
            }
        }
        ?>
    </table>