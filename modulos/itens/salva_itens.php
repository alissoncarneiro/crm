<?
@session_start();
require_once("../../conecta.php");
require_once("../../functions.php");
$id_session = $_POST['edtid_session'];
$tabela = $_SESSION[$id_session.'tabela'];
$chave = "numreg=";
$cond = array('00/00/0000','0000-00-00','Escolha','dd/mm/aaaa','aaaa-mm-dd','//','--','hh:mm:ss','hh:mm','hh','mm',':','::','0','0,0','0.00','0.0','NULL','');
$floats = array('');
echo "<pre>";
print_r($_SESSION[$id_session.'campos']);
foreach($_SESSION[$id_session.'campos'] as $k1 => $v1) {
    foreach($_SESSION[$id_session.'campos'][$k1] as $k => $v) {
        if(in_array(trim($v1),$cond)) {
            $v1 = "NULL";
            $valores[] = $v1;
            $update[] = "`".$k1."` = ".$v1."";
        } else {
            if(in_array(trim($k1),$floats)) {
                $v1 = str_replace(".","",$v1);
                $v1 = str_replace(",",".",$v1);
            }
            $valores[] = "'".addslashes(trim($v1))."'";
            $update[] = "`".$k1."` = \"".addslashes(trim($v1))."\"";
        }
        $campos[] = "`".$k1."`";
    }
    if($_SESSION[$id_session.'campos'][$k1]['numreg']=="") {
        echo "teste";
        mysql_query("INSERT INTO ".$tabela." (`".implode("`,`",$campos)."`) VALUES (".implode("'",$valores).")");
        $campos = $valores = array();
        $delete[] = mysql_insert_id();
    } elseif($_SESSION[$id_session.'campos'][$k1]['numreg']!="" || $_SESSION[$id_session.'campos'][$k1]['numreg']!="0") {
         //echo "teste2";
        mysql_query("UPDATE ".$tabela." SET ".$update." WHERE numreg = ".$_SESSION[$id_session.'campos'][$k1]['numreg']."");
        $delete[] = $_SESSION[$id_session.'campos'][$k1]['numreg'];
    }
}

//mysql_query("DELETE FROM ".$tabela." WHERE numreg not in('".implode("','",$delete)."')");

echo "javascript:alert('Dados atualizados.');";

?>