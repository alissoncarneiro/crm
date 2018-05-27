<?php
/*
 * configura_odbc_ini_datasul_verifica_conexao.php
 * Autor: Alex
 * 23/02/2011 12:38
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
header("Content-Type: text/html; charset=ISO-8859-1",true);
session_start();
require('../../classes/class.uB.php');
require('../../functions.php');
require('configura_odbc_ini_datasul_lista_tabelas.php');

$ArrayTabelasUtilizadasDatasulAssociadas = array();

$_POST = uB::UrlDecodePost($_POST);

$ArrayConexoes = array();

for($i=1;$i<=6;$i++){
    if(trim($_POST['alias'.$i]) != ''){
        $TestaConexao = odbc_connect($_POST['alias'.$i],'sysprogress','sysprogress');
        if($TestaConexao){
            $ArrayConexoes[] = array($TestaConexao,$_POST['alias'.$i],$i);
        }
        else{
            $ArrayConexoes[] = array(false,$_POST['alias'.$i],$i);
        }
    }
}
foreach($ArrayTabelasUtilizadasDatasul as $IndiceTabela => $Tabela){
    $ArrayTabelasUtilizadasDatasulAssociadas[$Tabela] = NULL;
    foreach($ArrayConexoes as $IndiceConexao => $ArConexao){
        if($ArConexao[0] !== false){
            $Sql = "SELECT COUNT(*) FROM pub.\"".$Tabela."\"";
            $Qry = odbc_exec($ArConexao[0],$Sql);
            if($Qry){
                $ArrayTabelasUtilizadasDatasulAssociadas[$Tabela] = $IndiceConexao;
                break;
            }
        }
    }
}
/*
 * Fechando as conexões
 */
foreach($ArrayConexoes as $IndiceConexao => $ArConexao){
    if($ArConexao[0] !== false){
        odbc_close($ArConexao[0]);
    }
}
if($_POST['sn_gerar_arquivo'] == 1){
    $StringArquivoINI = '';
    $StringArquivoINI .= "; Definição dos alias de conexão do erp Datasul por tabelas\r\n";
    $StringArquivoINI .= "[Alias]\r\n";
    $StringArquivoINI .= "1=".$_POST['alias1']."\r\n";
    $StringArquivoINI .= "2=".$_POST['alias2']."\r\n";
    $StringArquivoINI .= "3=".$_POST['alias3']."\r\n";
    $StringArquivoINI .= "4=".$_POST['alias4']."\r\n";
    $StringArquivoINI .= "5=".$_POST['alias5']."\r\n";
    $StringArquivoINI .= "6=".$_POST['alias6']."\r\n";

    $StringArquivoINI .= "[TabelasxAlias]\r\n";
    foreach($ArrayTabelasUtilizadasDatasulAssociadas as $Tabela => $Alias){
        $StringArquivoINI .= $Tabela." = ".$ArrayConexoes[$Alias][2]."\r\n";
    }
    $CaminhoArquivoINI = '../../conecta_odbc_erp_datasul.ini';
    $ArquivoINI = fopen($CaminhoArquivoINI,"w+");
    fwrite($ArquivoINI,$StringArquivoINI);
    fclose($ArquivoINI);
    ?>
<fieldset>
    <legend>Resultado</legend><img src="images/btn_verde.png" alt="Status" /> Arquivo gerado com sucesso!
</fieldset>
    <?php
    exit;
}
?>
<fieldset>
    <legend>Status da Verifica&ccedil;&atilde;o</legend>
    <table border="0" align="left" cellpadding="2" cellspacing="2" class="bordatabela">
        <tr bgcolor="#DAE8F4" class="tit_tabela">
            <td>Status</td>
            <td>Tabela</td>
            <td>Conex&atilde;o</td>
        </tr>
        <?php
        $i=0;
        foreach($ArrayTabelasUtilizadasDatasulAssociadas as $Tabela => $Alias){
            $i++;
            $BgColor = ($i%2==0)?'#EAEAEA':'#FFFFFF';
            $ImgStatus = ($Alias === NULL)?'btn_vermelho.png':'btn_verde.png';
            if($Alias === NULL){
                $StringConexao = '';
            }
            else{
                $StringConexao = $ArrayConexoes[$Alias][2].'|'.$ArrayConexoes[$Alias][1];
            }
            ?>
            <tr bgcolor="<?php echo $BgColor;?>">
                <td><img src="images/<?php echo $ImgStatus;?>" alt="Status" /></td>
                <td><?php echo $Tabela;?></td>
                <td><?php echo $StringConexao;?></td>
            </tr>
        <?php } ?>
    </table>
</fieldset>
