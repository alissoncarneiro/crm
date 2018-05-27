<?
header("Content-Type: text/html;  charset=ISO-8859-1");
require('../../conecta.php');

$sql_busca_param = "select * from is_atividade_solicitacao_param";
$sql_busca_param .= " where (id_tp_motivo_atend is null or id_tp_motivo_atend = '".$_POST["tipo_solicitacao"]."')";
$sql_busca_param .= " and (id_produto is null or id_produto = '".$_POST["produto"]."')";
$sql_busca_param .= " order by id_tp_motivo_atend desc, id_produto desc";
$a_busca_param = farray(query($sql_busca_param));

$retorno  = $a_busca_param["id_usuario_resp_padrao"].';';
$retorno .= $a_busca_param["id_prioridade_padrao"].';';
$retorno .= $a_busca_param["id_tp_atividade"].';';
$retorno .= $a_busca_param["assunto_padrao"].';';
$retorno .= $a_busca_param["sn_gerar_oportunidade"].';';
$retorno .= $a_busca_param["sn_gerar_orcamento"].';';

echo $retorno;

?>
