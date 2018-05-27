<?php
# busca_serie_produtos_post
# Expression package is undefined on line 3, column 5 in Templates/Scripting/EmptyPHP.php.
# Autor: Rodrigo Piva
# 15/09/2011
#
# Log de Alterações
# yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
#

require_once("../../conecta.php");
require_once("../../funcoes.php");
require_once("../../functions.php");

$XML = '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.'>'."\n";
$XML .= '<resposta>'."\n";
$XML .= "\t".'<mensagem></mensagem>'."\n";
$XML .= "\t".'<status>1</status>'."\n";

$serie = $_POST['nr_serie_produto'];

$SqlAtividade = "SELECT nr_serie_produto, id_nao_conformidade, id_atividade, dt_inicio
                 FROM is_atividade WHERE nr_serie_produto = ".$serie."
                                   AND   id_situacao = '4'
                                   AND   id_tp_atividade = '55'
                                   ORDER BY dt_inicio DESC";
$QryAtividade = query($SqlAtividade);
if($ArAtividade = farray($QryAtividade)){
    $NaoConfomidade = deparaIdErpCrm($ArAtividade['id_nao_conformidade'], "nome_nao_conformidade", "numreg", 'is_nao_conformidade');
    $XML .= "\t<obs>Produto com este número de série já antedido neste protocolo: ".$ArAtividade['id_atividade']."\ndo dia ".dten2br($ArAtividade['dt_inicio'])." com a não conformidade: ".$NaoConfomidade."</obs>\n";
}
$XML .= '</resposta>'."\n";
header("Content-Type: text/xml");
echo $XML;
?>
