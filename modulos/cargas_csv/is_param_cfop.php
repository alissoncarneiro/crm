<?php
include('../../conecta.php');
include('../../functions.php');
include('../../classes/class.importCSV.php');

class CSV extends importCSV{

    public function setValorCustom($ArDados){
        $ArDados['dthr_validade_ini'] = dtbr2en($ArDados['dthr_validade_ini']);
        $ArDados['dthr_validade_fim'] = dtbr2en($ArDados['dthr_validade_fim']);

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
    $ArColunas = array('dthr_validade_ini',
                            'dthr_validade_fim',
                            'sn_ativo',
                            'id_pessoa',
                            'id_pessoa_regiao',
                            'pessoa_cidade',
                            'pessoa_uf',
                            'id_pessoa_canal_venda',
                            'id_pessoa_grupo_cliente',
                            'id_tp_pessoa',
                            'sn_contribuinte_icms',
                            'id_produto',
                            'id_produto_familia',
                            'id_produto_familia_comercial',
                            'id_produto_grupo_estoque',
                            'id_produto_linha',
                            'id_pedido_estabelecimento',
                            'id_pedido_repres_pri',
                            'id_pedido_tab_preco',
                            'id_pedido_tp_pedido',
                            'id_pedido_tp_venda',
                            'id_pedido_dest_merc',
                            'id_pedido_moeda',
                            'cfop_estadual',
                            'cfop_interestadual',
                            'cfop_internacional',
                            'id_tp_item',
                            'id_tp_cliente',
                            'id_cfop_oper',
                            'pontos');
    $CSV = new CSV();
    if($_POST['edtlimpa_tabela'] == '1'){
        $CSV->setLimparTabela(true);
    }
    else{
        $CSV->setLimparTabela(false);
    }
    $CSV->setCaminhoArquivo($_FILES['edtarquivo_csv']['tmp_name']);
    $CSV->setPossuiCabecalho(true);
    $CSV->setUsaCabecalho(false);
    $CSV->setVerificaSeExiste(false);
    $CSV->setArColunas($ArColunas);

    $CSV->setChaves(array());

    $CSV->setCaminhoArquivo($_FILES['edtarquivo_csv']['tmp_name']);
    $CSV->setTabelaDestino('is_param_cfop');

    $CSV->setCampoDepara('id_produto');
    $CSV->setCampoDeparaTabelaCRM('is_produto');
    $CSV->addCampoDeparaTabelaChaveCRM('id_produto_erp');

    $CSV->setCampoDepara('cfop_estadual');
    $CSV->setCampoDeparaTabelaCRM('is_cfop');
    $CSV->addCampoDeparaTabelaChaveCRM('id_cfop_erp');

    $CSV->setCampoDepara('cfop_interestadual');
    $CSV->setCampoDeparaTabelaCRM('is_cfop');
    $CSV->addCampoDeparaTabelaChaveCRM('id_cfop_erp');

    $CSV->setCampoDepara('cfop_internacional');
    $CSV->setCampoDeparaTabelaCRM('is_cfop');
    $CSV->addCampoDeparaTabelaChaveCRM('id_cfop_erp');

    $CSV->Importa();
    $CSV->mostraResultado();
    
}
?>
<br />
<a href="#" onclick="javascript:history.go(-1); return false;">Voltar</a>