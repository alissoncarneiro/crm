<?

require('../../../../conecta.php');
require('../../../../functions.php');

// Definindo nome do arquivo que serÃ¡ exportado
$dataAtual = date("Y-m-d");

if($_GET['date_ini']){
    $bet = " and dt_inicio between  '".$_GET['date_ini']."'  and '".$_GET['date_end']."' ";
}else{
    $bet = "";
}


// ConfiguraÃ§Ãµes header para forÃ§ar o download
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/x-msexcel");
header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
header ("Content-Description: PHP Generated Data" );



$numTime = time().rand(0,9);
$baseNm = "contatos_atividade";
$arquivo = $baseNm.'_'.$dataAtual.'_'.$numTime.'.xls';




// Executa a Query
$sqlContatos = "
   select
      atividade.id_pessoa,
      forma.nome_forma_contato  as forma,
      origem.nome_origem_conta as origem,
      usuario.nome_usuario as UsuarioResponsavel,
      usuarioCad.nome_usuario as UsuarioCadastro,
      dt_inicio,
      dt_prev_fim

    from is_atividade as atividade

    inner join is_usuario as usuario
      on usuario.numreg = atividade.id_usuario_resp

    inner join is_usuario as usuarioCad
      on usuarioCad.numreg = atividade.id_usuario_cad

    inner join is_origem_conta as origem
      on origem.numreg  = atividade.atend_id_origem

    inner join is_forma_contato  as forma
      on  forma.numreg  = atividade.atend_id_forma_contato

  where id_tp_atividade = 20  $bet

order by  usuarioCad.nome_usuario asc ";


$queryContatos = mysql_query($sqlContatos);

while($arrContatos = mysql_fetch_assoc($queryContatos)){
    if(!isset($arrayPessoa[$arrContatos['UsuarioCadastro']][$arrContatos['origem']]['forma']))
        $arrayPessoa[$arrContatos['UsuarioCadastro']][$arrContatos['origem']]['forma'] = array(
            "total" => 0
        );
    
    if(!isset($arrayPessoa[$arrContatos['UsuarioCadastro']][$arrContatos['origem']]['forma'][$arrContatos['forma']])) {
        $arrayPessoa[$arrContatos['UsuarioCadastro']][$arrContatos['origem']]['forma'][$arrContatos['forma']]['total'] = 1;
    } else {
        $arrayPessoa[$arrContatos['UsuarioCadastro']][$arrContatos['origem']]['forma'][$arrContatos['forma']]['total']++;
    };
    
    $arrayPessoa[$arrContatos['UsuarioCadastro']][$arrContatos['origem']]['forma']['total']++;
    $arrayPessoa[$arrContatos['UsuarioCadastro']][$arrContatos['origem']]['forma'][$arrContatos['forma']][] = $arrContatos;
}

//foreach($arrayPessoa as $key => $value){
//    echo $key ;
//}
//exit;
/* Exportando Exel */
// Montando as Colunas/Linhas do Excel
$html = '';

$html .= '<table width="1200" height="81" border="1" bordercolor="#333333" cellpadding="2" cellspacing="2">';
	$html .=  '<tr align="center" bgcolor="#B6B6B6">';
        $html .=   '   <td width="23px" height="28" bgcolor="#FFFFFF" rowspan="2"></td>
                        <td colspan="3"><strong>Indicação de Contato</strong></td>
                        <td colspan="3" bgcolor="#B7B7B7"><strong>Tecnologia</strong></td>
                        <td colspan="3"><strong>Operação</strong></td>
                        <td colspan="3"><strong>Metodologia</strong></td>
                        
                        <td width="2px" height="28" bgcolor="#FFFFFF" rowspan = "'.(count($arrayPessoa) + 4).'" ></td>
                        
                        <td width="23px" height="28"  bordercolor="#333333" colspan="4"> <strong>Total</strong></td>

';
	$html .=   '</tr>';
	$html .=   '<tr align="center" bgcolor="#C9C9C9" style="font-size:11px">';
	$html .=   '    
                        <td width="30px">E-mail</td>
                        <td width="40px">Telefone</td>
                        <td width="48px">Sistema</td>
                        <td width="38px">E-mail</td>
                        <td width="52px">Telefone</td>
                        <td width="47px">Sistema</td>
                        <td width="24px">E-mail</td>
                        <td width="52px">Telefone</td>
                        <td width="47px">Sistema</td>
                        <td width="24px">E-mail</td>
                        <td width="52px">Telefone</td>
                        <td width="50px">Sistema</td>
                        
                        
                        <td width="30px">Contatos</td>
                        <td width="30px">E-mail</td>
                        <td width="40px">Telefone</td>
                        <td width="48px">Sistema</td>

';
	$html .=  ' </tr>';

$totalGeralEmail =  0;
$totalGeralTelefone = 0;
$totalGeralSistema = 0;
$totalGeralContato = 0;
$totalGeralContatoEmail = 0;
$totalGeralContatoTelefone = 0;
$totalGeralContatoSistema = 0;
        
foreach($arrayPessoa as $key => $value){
    
	$html .=   '<tr align="center" bgcolor="#FFFFFF">';
        $html .=   '    <td width="23px" height="23"> '.$key.'</td>';
        
        $total_indicacao_email = $arrayPessoa[$key]['Indicação de Contatos']['forma']['E-mail']['total'];
        $total_indicacao_telefone = $arrayPessoa[$key]['Indicação de Contatos']['forma']['Telefone']['total'];
        $total_indicacao_sistema = $arrayPessoa[$key]['Indicação de Contatos']['forma']['Safra']['total'];
        
        $html .=   '    <td width="24px">'.($total_indicacao_email ? $total_indicacao_email : '-').'</td>';
        $html .=   '    <td width="24px">'.($total_indicacao_telefone ? $total_indicacao_telefone : '-').'</td>';
        $html .=   '    <td width="24px">'.($total_indicacao_sistema ? $total_indicacao_sistema : '-').'</td>';
        
        $total_tecnologia_email = $arrayPessoa[$key]['Tecnologia']['forma']['E-mail']['total'];
        $total_tecnologia_telefone = $arrayPessoa[$key]['Tecnologia']['forma']['Telefone']['total'];
        $total_tecnologia_sistema = $arrayPessoa[$key]['Tecnologia']['forma']['Safra']['total'];
        
        $html .=   '    <td width="24px">'.($total_tecnologia_email ? $total_tecnologia_email : '-').'</td>';
        $html .=   '    <td width="52px">'.($total_tecnologia_telefone ? $total_tecnologia_telefone : '-').'</td>';
        $html .=   '    <td width="47px">'.($total_tecnologia_sistema ? $total_tecnologia_sistema : '-').'</td>';
        
        $total_operacao_email = $arrayPessoa[$key]['Operação']['forma']['E-mail']['total'];
        $total_operacao_telefone = $arrayPessoa[$key]['Operação']['forma']['Telefone']['total'];
        $total_operacao_sistema = $arrayPessoa[$key]['Operação']['forma']['Safra']['total'];
        
        $html .=   '    <td width="24px">'.($total_operacao_email ? $total_operacao_email : '-').'</td>';
        $html .=   '    <td width="52px">'.($total_operacao_telefone ? $total_operacao_telefone : '-').'</td>';
        $html .=   '    <td width="47px">'.($total_operacao_sistema ? $total_operacao_sistema : '-').'</td>';
        
        $total_metdologia_email = $arrayPessoa[$key]['Metodologia']['forma']['E-mail']['total'];
        $total_metdologia_telefone = $arrayPessoa[$key]['Metodologia']['forma']['Telefone']['total'];
        $total_metdologia_sistema = $arrayPessoa[$key]['Metodologia']['forma']['Safra']['total'];
        
        $html .=   '    <td width="24px">'.($total_metdologia_email ? $total_metdologia_email : '-').'</td>';
        $html .=   '    <td width="52px">'.($total_metdologia_telefone ? $total_metdologia_telefone : '-').'</td>';
        $html .=   '    <td width="50px">'.($total_metdologia_sistema ? $total_metdologia_sistema : '-').'</td>';
        
        
        
        $totalEmail =   $total_indicacao_email + $total_tecnologia_email + $total_operacao_email + $total_metdologia_email;
        $totalTelefone = $total_indicacao_telefone + $total_tecnologia_telefone + $total_operacao_telefone + $total_metdologia_telefone;
        $totalSistema = $total_indicacao_sistema + $total_tecnologia_sistema + $total_operacao_sistema + $total_metdologia_sistema;
        $totalContato =  $totalEmail + $totalTelefone + $totalSistema; 

        $html .=   '    <td width="24px">'.($totalContato ? $totalContato : '-').'</td>';
        $html .=   '    <td width="24px">'.($totalEmail ? $totalEmail : '-').'</td>';
        $html .=   '    <td width="59px">'.($totalTelefone ? $totalTelefone : '-').'</td>';
        $html .=   '    <td width="48px">'.($totalSistema ? $totalSistema : '-').'</td>';
        
	$html .=  ' </tr>';
        
        $totalGeralIndicacao += $total_indicacao_email + $total_indicacao_telefone + $total_indicacao_sistema;
        $totalGeralTecnologia += $total_tecnologia_email + $total_tecnologia_telefone + $total_tecnologia_sistema;
        $totalGeralMetodologia += $total_metdologia_email + $total_metdologia_telefone  + $total_metdologia_sistema;
        $totalGeralOperacao += $total_operacao_email + $total_operacao_telefone  + $total_operacao_sistema;
        
        $totalGeralContato += $totalEmail + $totalTelefone + $totalSistema ;
        $totalGeralContatoEmail += $totalEmail;
        $totalGeralContatoTelefone += $totalTelefone;
        $totalGeralContatoSistema += $totalSistema;
        
}

        $html .=  ' <tr align="center" >';
            $html .=  ' <td colspan="18" border="0" bgcolor="#FFF"> &nbsp;</td>';           
        $html .=  ' </tr>';
        
        
        $html .=  ' <tr align="center">';
            $html .=  ' <td> <strong>Total Geral</strong></td>';           
            $html .=  ' <td colspan="3">'.$totalGeralIndicacao.' </td>';
            $html .=  ' <td colspan="3"> '.$totalGeralTecnologia.'</td>';
            $html .=  ' <td colspan="3"> '.$totalGeralOperacao.'</td>';
            $html .=  ' <td colspan="3"> '.$totalGeralMetodologia.'</td>';

            $html .=  ' <td> '.$totalGeralContato.' </td>';
            $html .=  ' <td> '.$totalGeralContatoEmail.'</td>';
            $html .=  ' <td> '.$totalGeralContatoTelefone.'</td>';
            $html .=  ' <td> '.$totalGeralContatoSistema.'</td>';
        
        $html .=  ' </tr>';

$html .= '</table>';

// Exporta arquivo

echo $html;
exit;

?>
