<?php

	@session_start();

	require("calendario_funcs.php");


	$mes_sel = $_POST["mes_sel"];
	$ano_sel = $_POST["ano_sel"];
	$usu_sel = $_POST["usu_sel"];

	if (empty($mes_sel)) { $mes_sel = date("m"); }
	if (empty($ano_sel)) { $ano_sel = date("Y"); }
	if (empty($usu_sel)) { $usu_sel = $_SESSION["id_usuario"]; }

	$marc0=array("");
	$marc1=array("");
	$marc2=array("");
	$marc3=array("");
	$marc4=array("");
	$marc5=array("");
	$marc6=array("");

	echo "<br>&nbsp;&nbsp;Mes:&nbsp;&nbsp;";
	echo '<select name="edtmes_sel" id="edtmes_sel">';
	echo '<option value="01" ';  if ($mes_sel == '01') { echo 'selected'; } echo '>Janeiro</option>';
	echo '<option value="02" ';  if ($mes_sel == '02') { echo 'selected'; } echo '>Fevereiro</option>';
	echo '<option value="03" ';  if ($mes_sel == '03') { echo 'selected'; } echo '>Marco</option>';
	echo '<option value="04" ';  if ($mes_sel == '04') { echo 'selected'; } echo '>Abril</option>';
	echo '<option value="05" ';  if ($mes_sel == '05') { echo 'selected'; } echo '>Maio</option>';
	echo '<option value="06" ';  if ($mes_sel == '06') { echo 'selected'; } echo '>Junho</option>';
	echo '<option value="07" ';  if ($mes_sel == '07') { echo 'selected'; } echo '>Julho</option>';
	echo '<option value="08" ';  if ($mes_sel == '08') { echo 'selected'; } echo '>Agosto</option>';
	echo '<option value="09" ';  if ($mes_sel == '09') { echo 'selected'; } echo '>Setembro</option>';
	echo '<option value="10" ';  if ($mes_sel == '10') { echo 'selected'; } echo '>Outubro</option>';
	echo '<option value="11" ';  if ($mes_sel == '11') { echo 'selected'; } echo '>Novembro</option>';
	echo '<option value="12" ';  if ($mes_sel == '12') { echo 'selected'; } echo '>Dezembro</option>';
	echo '</select>';

	echo '&nbsp;&nbsp;Ano:&nbsp;&nbsp;';
	echo '<select name="edtano_sel" id="edtano_sel">';

	for ($a = (date("Y") - 10); $a <= (date("Y") + 10); $a++ ) {
		echo '<option value="'.$a.'" ';  if ($ano_sel == $a) { echo 'selected'; } echo '>'.$a.'</option>';
	}
	echo '</select>&nbsp;&nbsp;';

        $usuario = $_SESSION['id_usuario'];
        echo '<select name="edtusu_sel" id="edtusu_sel"> ';
        
        require('classes/class.ControleAcesso.php');
        $ControleAcesso = new ControleAcesso($_SESSION['id_usuario'],'agenda_mensal');
        $SqlBloqueio = ($ControleAcesso->AplicaFiltroBloqueio())?$ControleAcesso->GeraSqlBloqueio('numreg',' WHERE '):'';
        $qry_users = query("SELECT * FROM is_usuario ".$SqlBloqueio." ORDER BY nome_usuario ASC");
        while($ar_users = farray($qry_users)){
            
        if ($usu_sel == $ar_users['numreg']) { $selected = 'selected'; } else {$selected = ''; }
        echo '<option value="'.($ar_users['numreg']).'" '.$selected.'>'.($ar_users['nome_usuario']).'</option>';
        }
        echo '</select>&nbsp;&nbsp;';

	echo "<input class=\"botao_form\" type=\"button\" value=\"Exibir\" onclick=\"javascript:calendario_mensal();\">&nbsp;&nbsp;&nbsp;";
	echo "<input value=\"+ Incluir\" name=\"btnincluir\" onclick=\"javascript:window.open('gera_cad_detalhe.php?pfuncao=atividades_cad_lista&pnumreg=-1&psubdet=&pnpai=&prefpai=N', 'Agenda', 'location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=725,height=550,top=50,left=50');\" class=\"botao_form\" type=\"button\" /><br><br>";
	echo gerarCalendario($mes_sel,$ano_sel,$usu_sel,1,1,
					 array($marc0,$marc1,$marc2,$marc3,$marc4,$marc5,$marc6));
	echo '<div name="div_programa" id="div_programa"><input type="hidden" name="cbxfiltro" id="cbxfiltro"><input type="hidden" name="edtfiltro" id="edtfiltro"><input type="hidden" name="sql_filtro" id="sql_filtro"></div>';
