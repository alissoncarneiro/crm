<?php

/*
 * c_coaching_grade_pagto_custom_post.php
 * Autor: Alisson
 * 18/12/2012 17:14:09
 */

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
if($_SESSION['id_usuario'] == ''){
    echo 'Usuário não logado.';
    exit;
}

require('../../conecta.php');
require('../../functions.php');
require('../../classes/class.uB.php');


if(!$_POST){
    BloqueiaAcessoDireto();
}

$_POST = uB::UrlDecodePost($_POST);

$IdRequisicao   = trim($_POST['pagto_id_requisicao']);
$numreg    = trim($_POST['numreg']);
$id_parcela	= trim($_POST['id_parcela']);
$ptp_venda = trim($_POST['ptp_venda']);
$ptp_venda = '1' ? 'id_orcamento' :'id_pedido';


if($IdRequisicao == 1){
    $ArSqlInsert = array();

    $ArSqlInsert[$ptp_venda]            = $numreg;
    $ArSqlInsert['vl_parcela']              = round(TrataFloatPost($_POST['edtpagto_vl_parcela']),2);
    $ArSqlInsert['id_forma_pagto']          = $_POST['edtpagto_id_forma_pagto'];
    $ArSqlInsert['id_cond_pagto']           = $_POST['edtpagto_id_cond_pagto'];
    $ArSqlInsert['dt_primeiro_pagto']       = dtbr2en($_POST['edtpagto_dt_primeiro_pagto']);
    $ArSqlInsert['id_tp_pagto']             = $_POST['edtpagto_id_tp_pagto'];
    $ArSqlInsert['obs']                     = $_POST['edtpagto_obs'];

    $QtdeParcelas = 1;
    if($ArSqlInsert['id_cond_pagto'] != ''){
        $QryCondPagto = query("SELECT qtde_parcelas FROM is_cond_pagto WHERE numreg = ".$ArSqlInsert['id_cond_pagto']);
        $ArCondPagto = farray($QryCondPagto);
        if($ArCondPagto){
            $QtdeParcelas = $ArCondPagto['qtde_parcelas'];
        }
    }
    $ArSqlInsert['qtde_parcelas'] = $QtdeParcelas;
    $ArSqlInsert['vl_pagto'] = $ArSqlInsert['vl_parcela'] * $QtdeParcelas;

    $SqlInsert = AutoExecuteSql(TipoBancoDados, 'c_coaching_inscricao_pagto_orcamento_pedido', $ArSqlInsert, 'INSERT');
    $QryInsert = query($SqlInsert);
    if($QryInsert){
        echo 'Registro inserido com sucesso!';
    }
    else{
        echo 'Erro ao inserir registro. Tente novamente, caso o problema persista, entre em contato com o administrador';
    }
}
elseif($IdRequisicao == 2){
    $ArSqlUpdate = array();
    $ArSqlUpdate['numreg']                  = $id_parcela;
    $ArSqlUpdate['vl_parcela']              = TrataFloatPost($_POST['edtpagto_vl_parcela']);
    $ArSqlUpdate['id_forma_pagto']          = $_POST['edtpagto_id_forma_pagto'];
    $ArSqlUpdate['id_cond_pagto']           = $_POST['edtpagto_id_cond_pagto'];
    $ArSqlUpdate['dt_primeiro_pagto']       = dtbr2en($_POST['edtpagto_dt_primeiro_pagto']);
    $ArSqlUpdate['id_tp_pagto']             = $_POST['edtpagto_id_tp_pagto'];
    $ArSqlUpdate['obs']                     = $_POST['edtpagto_obs'];

    $QtdeParcelas = 1;
    if($ArSqlUpdate['id_cond_pagto'] != ''){
        $QryCondPagto = query("SELECT qtde_parcelas FROM is_cond_pagto WHERE numreg = ".$ArSqlUpdate['id_cond_pagto']);
        $ArCondPagto = farray($QryCondPagto);
        if($ArCondPagto){
            $QtdeParcelas = $ArCondPagto['qtde_parcelas'];
        }
    }

    $ArSqlUpdate['qtde_parcelas'] = $QtdeParcelas;
    $ArSqlUpdate['vl_pagto'] = $ArSqlUpdate['vl_parcela'] * $QtdeParcelas;

    $SqlUpdate = AutoExecuteSql(TipoBancoDados, 'c_coaching_inscricao_pagto_orcamento_pedido', $ArSqlUpdate, 'UPDATE', array('numreg'));
    $QryUpdate = query($SqlUpdate);
    if($QryUpdate){
        echo 'Registro atualizado com sucesso!';
    }
    else{
        echo 'Erro ao atualizar registro. Tente novamente, caso o problema persista, entre em contato com o administrador';
    }
}
elseif($IdRequisicao == 3){
    $SqlDelete = "DELETE FROM c_coaching_inscricao_pagto_orcamento_pedido WHERE numreg = ".$id_parcela;
    $QryDelete = query($SqlDelete);
    if($QryDelete){
        echo 'Registro excluido com sucesso!';
    }
    else{
        echo 'Erro ao excluir registro. Tente novamente, caso o problema persista, entre em contato com o administrador';
    }
}
?>