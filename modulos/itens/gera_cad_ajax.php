<?
	if($controle!=1) {

@session_start();
$id_session = $_POST['edtid_session'];
//$mode = $_SESSION[$id_session.'sessionmode'];
require_once("../../conecta.php");
require_once("../../functions.php");
$exibe=0;
$tam=1;
$linha=1;

$pfixo = str_replace('@igual', '=', $_GET['pfixo']);
$pfixo = str_replace('@diferente', '!=', $pfixo);
$pfixo = str_replace('@s', "'", $pfixo);
$pfixo = str_replace('@', "", $pfixo);

$_SESSION['pfixo'] = $pfixo;
//$_SESSION[$id_session.'pfuncao'] = $_GET['pfuncao'];

$mestre = mysql_fetch_array(mysql_query("SELECT * FROM is_gera_cad_sub WHERE numreg = '".$_GET['psubdet']."'"));
$cadastro_mestre = mysql_fetch_array(mysql_query("SELECT * FROM is_gera_cad WHERE id_cad = '".$mestre['id_funcao_mestre']."'"));
$cadastro_detalhe = mysql_fetch_array(mysql_query("SELECT * FROM is_gera_cad WHERE id_cad = '".$mestre['id_funcao_detalhe']."'"));
$ar_campos = mysql_query("SELECT * FROM is_gera_cad_campos WHERE id_funcao = '".$_GET['pfuncao']."' AND editavel = 'S' ORDER BY ordem ASC");
$campo_lupa = mysql_fetch_array(mysql_query("SELECT * FROM is_gera_cad_campos WHERE id_campo= 'id_produto' AND id_funcao = '".$_GET['pfuncao']."' AND editavel = 'S' ORDER BY ordem ASC"));
$ar_valores = mysql_query("SELECT * FROM ".$cadastro_detalhe['nome_tabela']." WHERE ".$pfixo."");

$_SESSION[$id_session.'tabela'] = $cadastro_detalhe['nome_tabela'];

while($campos = mysql_fetch_array($ar_campos,MYSQL_ASSOC)) {
    $campos_banco[] = $campos['id_campo'];
}
$campos_banco[] = 'numreg';
 $num = 1;
while($valores = mysql_fetch_array($ar_valores,MYSQL_ASSOC)) {

    foreach($campos_banco as $k => $v) {
        $_SESSION[$id_session.'campos'][$num][$v] = $valores[$v];
        $_SESSION[$id_session.'label'][$num][$v] = $campos_label[$k];
    }
    $num++;
}

?>

<table border="0" align="center" cellpadding="2" cellspacing="2" class="bordatabela">
    <tr>
        <td align="center" bgcolor="dae8f4" class="tit_tabela" >Itens</td>
    </tr>
    <tr>
        <td align="left" bgcolor="dae8f4" class="tit_tabela" >
            <input type="hidden" name="edtid_session" id="edtid_session" value="<?=$id_session;?>" />
            Item:
            <input id="edtid_produto" readonly="readonly" size="8" name="edtid_produto"/>
            -
            <input id="edtdescrid_produto" readonly="readonly" size="40" name="edtdescrid_produto"/>
            <a onClick="javascript:window.open('gera_cad_lista.php?pfuncao=<?=$campo_lupa['id_funcao_lupa'];?>&amp;pdrilldown=1&amp;plupa=<?=$campo_lupa['numreg']?>&amp;pfixo=','<?=$campo_lupa['id_funcao_lupa'];?>id_produto','location=0,menubar=0,resizable=0,status=1,toolbar=0,scrollbars=1,width=500,height=350,top=250,left=250'); return false;" href="#"> <img height="15" alt="Buscar" src="images/btn_busca.PNG" width="15" border="0" title = "Pesquisar"/></a>&nbsp;&nbsp;
            <input name="Button" type="button" class="botao_form" onclick="javascript:add_item(document.getElementById('edtid_session').value,'id_produto','<?=$cadastro_detalhe['nome_tabela'];?>','<?=$_GET['pfixo'];?>');" value="Adicionar" /></td>
    </tr>
    <tr>
        <td align="left" bgcolor="dae8f4" class="tit_tabela" ><div id="itens"><?php include "tabela_itens.php";?></div></td>
    </tr>
    <tr style="display:none">
        <td align="center" valign="middle" bgcolor="dae8f4" class="tit_tabela" >
            <label>
                <input type="button" name="salvar" id="salvar" value="Salvar" onclick="javascript:ajax_salva_dados(document.getElementById('edtid_session').value);"/>
            </label>
        </td>
    </tr>
</table>
<?
		$controle = 1;
	}?>