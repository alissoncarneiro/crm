<?

require('../../../../conecta.php');
require('../../../../functions.php');

// Definindo nome do arquivo que será exportado
$dataAtual = date("dmY");
$numTime = time().rand(0,9);
$baseNm = "contatos_atividade";
$arquivo = $baseNm.'_'.$dataAtual.'_'.$numTime.'.xls';

// Configurações header para forçar o download
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/x-msexcel");
header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
header ("Content-Description: PHP Generated Data" );

// Executa a Query
$sqlContatos = "
				select
					  pessoa.razao_social_nome,
					  pessoa.tel1,
					  usuario.nome_usuario,
					  SUBSTRING(replace(atividade.obs, 'Telefone: (', '' ) FROM 23 ) as mensagem,
					  DATE_FORMAT(atividade.dt_inicio, '%d-%m-%Y')   as dt_inicio,
					  DATE_FORMAT(atividade.dt_prev_fim, '%d-%m-%Y')   as dt_prev_fim,
					  DATE_FORMAT(atividade.dt_real_fim, '%d-%m-%Y')   as dt_real_fim,
					  situacao.nome_situacao as situcao
						from is_atividade as atividade
						  inner join is_pessoa as pessoa
						  on pessoa.numreg = atividade.id_pessoa
						  inner join is_usuario as usuario
						  on usuario.numreg = atividade.id_usuario_resp
						  inner join is_situacao as situacao
						  on situacao.numreg  = atividade.id_situacao
					  where atividade.id_tp_atividade = 1";

$queryContatos = mysql_query($sqlContatos);



/* Exportando Exel */

// Montando as Colunas/Linhas do Excel

$html = '';
$html .= '<table width="100%" border="1" cellspacing="1" cellpadding="2" bordercolor="#696969" >';
	$html .=  '<tr>';
	$html .=   '  <td  colspan="9" align="center" bgcolor="#EEEEEE"><i>EMPRESA</i></td>';
	$html .=   '</tr>';
	$html .=   '<tr>';
	$html .=   '  <td width="20px" bgcolor="#EEEEEE" align="center">Data envio</td>';
	$html .=   '  <td width="20px" bgcolor="#EEEEEE" align="center">Data Execucao</td>';
	$html .=   '  <td width="30px" bgcolor="#EEEEEE" align="center">Nome</td>';
	$html .=   '  <td width="30px" bgcolor="#EEEEEE" align="center">Contato</td>';
	$html .=   '  <td width="50px" bgcolor="#EEEEEE" align="center">Mensagem</td>';
	$html .=   '  <td width="10px" bgcolor="#EEEEEE" align="center">Descritivo</td>';
	$html .=   ' <td width="10px"  bgcolor="#EEEEEE" align="center">Valor Oportunidade</td>';
	$html .=   '  <td width="10px" bgcolor="#EEEEEE" align="center">Atividade</td>';
	$html .=   '  <td  width="10px" bgcolor="#EEEEEE" align="center">Situacao</td>';
	$html .=  ' </tr>';

while($arrContatos = mysql_fetch_array($queryContatos)){
	$html .=   '<tr>';
	$html .=   '  <td width="7%"  align="center"  bgcolor="#FFFFFF" >'.$arrContatos["dt_inicio"].'</td>';
	$html .=   '  <td width="8%"  align="center" bgcolor="#FFFFFF" >'.$arrContatos["dt_real_fim"].'</td>';
	$html .=   '  <td width="13%" align="left" bgcolor="#FFFFFF" >'.ucfirst(strtolower(array_shift(explode(" ", $arrContatos["razao_social_nome"])))).'</td>';
	$html .=   '  <td width="14%" align="left" bgcolor="#FFFFFF" >'.$arrContatos["nome_usuario"].'</td>';
	$html .=   '  <td width="29%" align="left" bgcolor="#FFFFFF" > ' .$arrContatos["mensagem"].'</td>';
	$html .=   '  <td width="8%"  align="center" bgcolor="#FFFFFF" ></td>';
	$html .=   '  <td width="9%"  align="center" bgcolor="#FFFFFF" ></td>';
	$html .=   '  <td width="12%" align="center" bgcolor="#FFFFFF" ></td>';
	$html .=   '  <td width="12%" align="center" bgcolor="#FFFFFF" >'.$arrContatos["situacao"].'</td>';
	$html .=  ' </tr>';
}

$html .= '</table>';
// Exporta arquivo

echo $html;
exit;

?>
