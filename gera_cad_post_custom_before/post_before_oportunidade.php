<?php

// Cabeçalho da Oportunidade
if(($id_funcao == 'opo_cad_lista') && ($opc != 'excluir')){
    // Consistências
    $dt_inicio_trat = DataSetBD($_POST["edtdt_inicio"]);
    $dt_prev_fim_trat = DataSetBD($_POST["edtdt_prev_fim"]);
    $dt_real_fim_trat = DataSetBD($_POST["edtdt_real_fim"]);
    $msg_erro_oport = "";
    if($dt_inicio_trat > $dt_prev_fim_trat){
        $msg_erro_oport = "Dt.Prev. Fechamento não pode ser menor que Data de Abertura ! ";
    }
    if(($dt_real_fim_trat) && ($dt_inicio_trat > $dt_real_fim_trat)){
        $msg_erro_oport = "Dt.Real Fechamento não pode ser menor que Data de Abertura ! ";
    }
    if($msg_erro_oport){
        $Url->AlteraParam('ppostback',$numreg_postback);
        $url_retorno = $Url->getUrl();
        echo "<script language=\"javascript\">alert('".$msg_erro_oport."'); window.location.href = '".$url_retorno."';</script>";
        exit;
    }
    // Recuperando Cidade, UF e Região para Gravar na Oportunidade facilitando Gestão posterior
    $a_pessoa_oport = farray(query("select cidade, uf, id_regiao from is_pessoa WHERE numreg = ".$_POST['edtid_pessoa']));
    $_POST['edtcidade'] = $a_pessoa_oport['cidade'];
    $_POST['edtuf'] = $a_pessoa_oport['uf'];
    $_POST['edtid_regiao'] = $a_pessoa_oport['id_regiao'];

    // Calculo de % de probabilidade real
    $_POST["edtpct_sucesso_real"] = str_replace(".",",",round($_POST["edtpct_sucesso"] * $_POST["edtpct_sucesso_vend"] / 100,2));
}

// Itens da Oportunidade
if($id_funcao == 'opor_itens'){
    // Consistencia na Inclusao
    if($opc == 'incluir'){
        $a_existe_opor_produto = farray(query("SELECT COUNT(*) AS CNT FROM is_opor_produto WHERE id_oportunidade = ".$_POST['edtid_oportunidade']." AND id_produto = ".$_POST['edtid_produto']));
        if(($a_existe_opor_produto['CNT'] * 1) >= 1){
            $Url->AlteraParam('ppostback',$numreg_postback);
            $url_retorno = $Url->getUrl();
            echo "<script language=\"javascript\">alert('O produto não pode ser incluido mais de uma vez!'); window.location.href = '".$url_retorno."';</script>";
            exit;
        }
    }
    if($opc != 'excluir'){
        // Consistencia no Desconto
        if(($_POST['edtpct_desc'] * 1) > 100){
            $Url->AlteraParam('ppostback',$numreg_postback);
            $url_retorno = $Url->getUrl();
            echo "<script language=\"javascript\">alert('O desconto não pode ser maior que 100% !'); window.location.href = '".$url_retorno."';</script>";
            exit;
        }else{
            if((NumeroSetBD($_POST['edtqtde'])) * 1 < 1){
                $Url->AlteraParam('ppostback',$numreg_postback);
                $url_retorno = $Url->getUrl();
                echo "<script language=\"javascript\">alert('Campo Qtde deve ser maior que 1 !'); window.location.href = '".$url_retorno."';</script>";
                exit;
            }
            $op_item_qtde = NumeroSetBD($_POST['edtqtde']) * 1;
            $op_item_valor = NumeroSetBD($_POST['edtvalor']) * 1;
            $op_item_pct_desc = NumeroSetBD($_POST['edtpct_desc']) * 1;
            $_POST['edtvalor_total'] = number_format($op_item_qtde * ($op_item_valor - ($op_item_pct_desc * $op_item_valor / 100)),2,',','.');
        }
        $opor_itens_id_opor = $_POST['edtid_oportunidade'];
        // Recuperando Fornecedor, Linha e familia
        $a_prod_oport = farray(query("select id_fornecedor, id_linha, id_familia_comercial from is_produto WHERE numreg = ".$_POST['edtid_produto']));
        $_POST['edtid_fornecedor'] = $a_prod_oport['id_fornecedor'];
        $_POST['edtid_linha'] = $a_prod_oport['id_linha'];
        $_POST['edtid_familia_comercial'] = $a_prod_oport['id_familia_comercial'];
    }else{
        // No caso de exclusão deve-se guardar o id_oportunidade para usar no post_after_oportunidade.php
        $a_bck_opor_itens_id_opor = farray(query("SELECT id_oportunidade FROM is_opor_produto WHERE numreg = ".$_GET['pnumreg']));
        $opor_itens_id_opor = $a_bck_opor_itens_id_opor["id_oportunidade"];
    }
}

if(($id_funcao== 'opo_cad_lista') && (($_REQUEST['edtid_situacao']=='4') || ($_REQUEST['edtid_situacao']=='3')) && ($opc != 'excluir')){
    if($_REQUEST['edtid_opor_ciclo_fase'] == 9){
        $url_retorno = $Url->getUrl();
        echo "<script language=\"javascript\">alert('Favor informar a administração!!!'); window.location.href = '".$url_retorno."';</script>";
        exit;
    }
}

// Consistir o preenchimento da data de fechamento caso a situacao seja perdida ou fechada
if(($id_funcao== 'opo_cad_lista') && (($_REQUEST['edtid_situacao']=='4') || ($_REQUEST['edtid_situacao']=='3')) && ($opc != 'excluir')){
  if (empty($_POST["edtdt_real_fim"])) {
        $Url->AlteraParam('ppostback',$numreg_postback);
        $url_retorno = $Url->getUrl();
        echo "<script language=\"javascript\">alert('Por favor preencher o campo Dt. Real fechamento!'); window.location.href = '".$url_retorno."';</script>";
        exit;
  }
      $sqlCountOportProduto = "select count(1) as total from followcrm.is_opor_produto where id_oportunidade = ".$_REQUEST['pnumreg'];
    //  echo $sqlCountOportProduto;die;
      $qryCountOportProduto = query($sqlCountOportProduto);
      $arrCountOportProduto = farray($qryCountOportProduto);

  if($arrCountOportProduto['total'] == 0){
      $Url->AlteraParam('ppostback',$numreg_postback);
      $url_retorno = $Url->getUrl();
      echo "<script language=\"javascript\">alert('Por favor insira um imóvel, e o valor!'); window.location.href = '".$url_retorno."';</script>";
      exit;
  }
}

