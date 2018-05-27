<?php

/*
 * c_coaching_form_pagto_post.php
 * Autor: Alex
 * 11/08/2011 17:14:09
 */

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");
session_start();
if($_SESSION['id_usuario'] == ''){
    echo 'Usuário não logado.';
    exit;
}

require('../../../../conecta.php');
require('../../../../functions.php');
require('../../../../classes/class.uB.php');
require('c_coaching.class.Inscricao.php');
require('c_coaching.class.InscricaoCurso.php');

if(!$_POST){
    BloqueiaAcessoDireto();
}

$_POST = uB::UrlDecodePost($_POST);

$IdRequisicao   = trim($_POST['pagto_id_requisicao']);
$IdInscricao    = trim($_POST['id_inscricao']);
/*$id_estabelecimento = trim($_POST['edtpagto_id_estabelecimento']);*/

if($IdRequisicao == 1){
    $ArSqlInsert = array();
    $ArSqlInsert['id_inscricao']            = $IdInscricao;
    $ArSqlInsert['vl_parcela']              = round(TrataFloatPost($_POST['edtpagto_vl_parcela']),2);
    $ArSqlInsert['id_forma_pagto']          = $_POST['edtpagto_id_forma_pagto'];
    $ArSqlInsert['id_cond_pagto']           = $_POST['edtpagto_id_cond_pagto'];
    $ArSqlInsert['dt_primeiro_pagto']       = dtbr2en($_POST['edtpagto_dt_primeiro_pagto']);
    $ArSqlInsert['id_tp_pagto']             = $_POST['edtpagto_id_tp_pagto'];
    /*$ArSqlInsert['id_estabelecimento']      = $_POST['edtpagto_id_estabelecimento'];*/
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

    $SqlInsert = AutoExecuteSql(TipoBancoDados, 'c_coaching_inscricao_pagto', $ArSqlInsert, 'INSERT');
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
    $ArSqlUpdate['numreg']                  = $_POST['id_inscricao_pagto'];
    $ArSqlUpdate['id_inscricao']            = $IdInscricao;
    $ArSqlUpdate['vl_parcela']              = TrataFloatPost($_POST['edtpagto_vl_parcela']);
    $ArSqlUpdate['id_forma_pagto']          = $_POST['edtpagto_id_forma_pagto'];
    $ArSqlUpdate['id_cond_pagto']           = $_POST['edtpagto_id_cond_pagto'];
    $ArSqlUpdate['dt_primeiro_pagto']       = dtbr2en($_POST['edtpagto_dt_primeiro_pagto']);
    $ArSqlUpdate['id_tp_pagto']             = $_POST['edtpagto_id_tp_pagto'];
    $ArSqlUpdate['id_estabelecimento']      = $_POST['edtpagto_id_estabelecimento'];
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

    $SqlUpdate = AutoExecuteSql(TipoBancoDados, 'c_coaching_inscricao_pagto', $ArSqlUpdate, 'UPDATE', array('numreg'));
    $QryUpdate = query($SqlUpdate);
    if($QryUpdate){
        echo 'Registro atualizado com sucesso!';
    }
    else{
        echo 'Erro ao atualizar registro. Tente novamente, caso o problema persista, entre em contato com o administrador';
    }
}
elseif($IdRequisicao == 3){
    $SqlDelete = "DELETE FROM c_coaching_inscricao_pagto WHERE numreg = ".$_POST['id_inscricao_pagto'];
    $QryDelete = query($SqlDelete);
    if($QryDelete){
        echo 'Registro excluido com sucesso!';
    }
    else{
        echo 'Erro ao excluir registro. Tente novamente, caso o problema persista, entre em contato com o administrador';
    }
}
?>