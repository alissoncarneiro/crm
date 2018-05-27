<?php
/*
 * is_nf_entrada.php
 * Autor: Rodrigo Piva
 * 29/07/2011 16:00
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
session_start();
set_time_limit(600); /* 10 minutos */
include_once('../../../conecta.php');
include_once('../../../functions.php');

$ArrayConf = parse_ini_file('../../../conecta_odbc_erp_datasul.ini',true);
$CnxODBC = ConectaODBCErpDatasul($ArrayConf,'docum-est');
if(!$CnxODBC){
    echo 'Não foi possível estabelecer uma conexão com o ERP.';
    exit;
}

$ArDtHora = farray(query("SELECT * FROM is_param_integracao_lab WHERE numreg = 1"));


$DtAtualiza = $ArDtHora['dt_ult_atualizacao'];
$HrAtualiza = $ArDtHora['hr_ult_atualizacao'];

$SqlCfopLab = "SELECT * FROM is_cfop_lab";
$QryCfopLab = query($SqlCfopLab);
$i = 0;
while($ArCfopLab = farray($QryCfopLab)){
   $IdCfopLab[$i] = deparaIdErpCrm($ArCfopLab['id_cfop'],"id_cfop_erp","numreg","is_cfop");
   $i++;
}


$SqlNfEntrada = "SELECT t1.\"nro-docto\",
                        t2.\"it-codigo\",
                        t1.\"cod-emitente\",
                        t1.\"serie-docto\",
                        t1.\"dt-emissao\",
                        t1.\"dt-atualiza\",
                        t1.\"hr-atualiza\",
                        t2.\"preco-total-me\",
                        t2.\"nat-operacao\",
                        t1.\"cod-estabel\",
                        t1.\"tipo-docto\",
                        t1.\"uf\",
                        t1.\"estab-de-or\",
                        t1.\"usuario\",
                        t1.\"tipo-nota\",
                        t2.\"sequencia\",
                        t2.\"quantidade\",
                        t2.\"un\",
                        t2.\"preco-total-me\",
                        t2.\"num-pedido\",
                        t2.\"cod-refer\",
                        t2.\"nr-pedcli\",
                        t1.\"CE-atual\",
                        t2.\"cod-depos\"
                 FROM pub.\"item-doc-est\" t2
                 INNER JOIN pub.\"docum-est\" t1
                 ON t2.\"nro-docto\" = t1.\"nro-docto\"
                 AND t2.\"serie-docto\" = t1.\"serie-docto\"
                 AND t2.\"cod-emitente\" = t1.\"cod-emitente\"
                 WHERE (t1.\"dt-atualiza\" = '".$DtAtualiza."')
                 AND t1.\"nat-operacao\" IN('".implode("','",$IdCfopLab)."')
                 ";
$QryNfEntrada = odbc_exec($CnxODBC, $SqlNfEntrada);
if(!$QryNfEntrada){
    echo odbc_errormsg();
}

while ( $ArNfEntrada = odbc_fetch_array($QryNfEntrada)){
    
    $IdProduto = deparaIdErpCrm($ArNfEntrada['it-codigo'],"numreg","id_produto_erp","is_produto");
    $IdPessoa  = deparaIdErpCrm($ArNfEntrada['cod-emitente'],"numreg","id_pessoa_erp", "is_pessoa");
    $IdEstabelecimento = deparaIdErpCrm($ArNfEntrada['cod-estabel'], "numreg", "id_estabelecimento_erp", "is_estabelecimento");
    $IdEstabelecimentoOrigem = deparaIdErpCrm($ArNfEntrada['estab-de-or'], "numreg", "id_estabelecimento_erp", "is_estabelecimento");
    $IdEstoque = deparaIdErpCrm($ArNfEntrada['cod-depos'], "numreg", "nome_estoque", "is_estoque");

    if(!$IdEstoque){
        $IdEstoque = '';
    }

    if($IdPessoa == NULL || $IdProduto == NULL ){
        continue;
    }

    $SqlProdutoNfEntrada = "SELECT * FROM is_produto_nf_entrada WHERE id_nota_fiscal = '".$ArNfEntrada['nro-docto']."'
                           AND id_produto = '".$IdProduto."'
                           AND id_pessoa  = '".$IdPessoa."'
                           ";
    $QryProdutoNfEntrada = query($SqlProdutoNfEntrada);

    $ArInsert = array(
        'id_nota_fiscal'        => $ArNfEntrada['nro-docto'],
        'id_produto'            => $IdProduto,
        'id_pessoa'             => $IdPessoa,
        'serie'                 => $ArNfEntrada['serie-docto'],
        'dt_emissao'            => $ArNfEntrada['dt-emissao'],
        'dt_atualiza'           => $ArNfEntrada['dt-atualiza'],
        'hr_atualiza'           => $ArNfEntrada['hr-atualiza'],
        'valor_total'           => $ArNfEntrada['preco-total-me'],
        'id_cfop'               => $ArNfEntrada['nat-operacao'],
        'id_estabelecimento'    => $IdEstabelecimento,
        'tp_documento'          => $ArNfEntrada['tipo-docto'],
        'uf'                    => $ArNfEntrada['uf'],
        'id_estabelecimento_origem' => $IdEstabelecimentoOrigem,
        'tp_nota_fiscal'        => $ArNfEntrada['tipo-nota'],
        'sequencia'             => $ArNfEntrada['sequencia'],
        'quantidade'            => $ArNfEntrada['quantidade'],
        'un'                    => $ArNfEntrada['un'],
        'preco_total'           => $ArNfEntrada['preco-total-me'],
        'numero_pedido'         => $ArNfEntrada['nr-pedcli'],
        'usuario_erp'           => $ArNfEntrada['usuario'],
        'codigo_referencia'     => $ArNfEntrada['cod_refer'],
        'nr_pedido_entrada'     => $ArNfEntrada['num-pedido'],
        'origem'                => $ArNfEntrada['origem'],
        'sn_integrado'          => '0',
        'sn_atualizado'         => $ArNfEntrada['CE-atual'],
        'id_estoque'            => $IdEstoque
    );

    if($ArProdutoNfEntrada = farray($QryProdutoNfEntrada)){
        $Sql = AutoExecuteSql(TipoBancoDados,'is_produto_nf_entrada', $ArInsert, 'UPDATE', array('id_nota_fiscal','id_produto','id_pessoa'));
    }else{
        $Sql = AutoExecuteSql(TipoBancoDados,'is_produto_nf_entrada', $ArInsert, 'INSERT');
    }
    $qry = query($Sql);
    if (!$qry) {
        echo $SqlInsert;
    }else{
        $ArAtualizaHora = array( 'numreg' => '1',
                                 'dt_ult_atualizacao' => date("Y-m-d"),
                                 'hr_ult_atualizacao' => date("H:i:s")
                                );
        $SqlAtualizaHora = AutoExecuteSql(TipoBancoDados,'is_param_integracao_lab', $ArAtualizaHora, 'UPDATE', array('numreg'));
        $qry = query($SqlAtualizaHora);
        if (!$qry) {
            echo $SqlInsert;
        }
    }
}


?>