<?php
/*
 * is_aliquota_iva.php
 * Autor: Alex
 * 14/12/2010 12:27:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
include('../../conecta.php');
include('../../functions.php');
include('../../classes/class.importCSV.php');

class CSV extends importCSV{

    public function setValorCustom($ArDados){
        $ArDados['pct_iva']         = TrataFloatPost($ArDados['pct_iva']);

        return $ArDados;

    }

    public function setValorCustomInsert($ArDados){
        return $ArDados;
    }
}

$MimeType = $_FILES['edtarquivo_csv']['type'];

if($MimeType != 'application/vnd.ms-excel'){
    echo '<span>O arquivo deve ser um CSV de Excel v&aacute;lido.</span>';
}
else{
    $ArColunas = array('uf_destino','id_classificacao_fiscal','pct_iva');
    $CSV = new CSV();
    if($_POST['edtlimpa_tabela'] == '1'){
        $CSV->setLimparTabela(true);
    }
    else{
        $CSV->setLimparTabela(false);
    }
    $CSV->setCaminhoArquivo($_FILES['edtarquivo_csv']['tmp_name']);
    $CSV->setTabelaDestino('is_aliquota_iva');
    $CSV->setPossuiCabecalho(true);
    $CSV->setUsaCabecalho(false);
    $CSV->setVerificaSeExiste(false);
    $CSV->setArColunas($ArColunas);

    $CSV->setChaves($ArColunas);

    $CSV->Importa();
    $CSV->mostraResultado();
}
?>
<br />
<a href="#" onclick="javascript:history.go(-1); return false;">Voltar</a>