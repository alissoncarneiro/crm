<?php

/*
 * gera_modelo_orcamento.php
 * Autor: Monica
 * 19/11/2010
 * Arquivo inicial do gera modelo de orçamento
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

header("Content-Type: text/html; charset=ISO-8859-1");
session_start();

require('../../../conecta.php');
require('../../../classes/class.GeraCadCampos.php');
require('../../../classes/class.Url.php');
require('../../../classes/class.Usuario.php');
require('../../../classes/class.Produto.php');
require('../../../classes/class.Pessoa.php');
require('../../../functions.php');

require('../classes/class.Venda.php');
require('../classes/class.Venda.Orcamento.php');
require('../classes/class.Venda.Pedido.php');
require('../classes/class.Venda.Item.php');
require('../classes/class.Venda.Representante.php');
require('../classes/class.Venda.Campos.php');
require('../classes/class.Venda.CamposCustom.php');
require('../classes/class.Venda.Parametro.php');

/*if(empty($_REQUEST['pnumreg'])){
    echo getError('0040010001',getParametrosGerais('RetornoErro'));
    exit;
}
else{
    if($_REQUEST['ptp_venda'] == 1){
        $Venda = new Orcamento($_REQUEST['ptp_venda'],$_REQUEST['pnumreg']);
    }
    else{
        $Venda = new Pedido($_REQUEST['ptp_venda'],$_REQUEST['pnumreg']);
    }
}*/

 /*
  * Verifica qual o modelo de Orçamento foi escolhido para ser preenchido
  */

  ###Modelos###
  $sql_modelo = "select * from is_modelo_orcamento where numreg='".$_GET['id_modelo']."'";
  $qry_modelo = query($sql_modelo);
  $ar_modelo = farray($qry_modelo);



  $arquivo = $ar_modelo['caminho_modelo_orcamento'];
  $fp = fopen($arquivo, 'r');
  $texto = fread($fp, filesize($arquivo));
  fclose($fp);


/*
 * Verifica se a váriável de tipo da venda foi preenchida.
 */
    $Venda = new Orcamento(1, $_GET['pnumreg']);

    
    ###Contato###
    $sql_contato = "select * from is_contato where numreg = '".$Venda->getDadosVenda('id_contato')."'";
    $qry_contato = query($sql_contato);
    $ar_contato = farray($qry_contato);

    /*Cargo do contato
    $sql_cargo_contato = "select * from is_cargo numreg='".$ar_contato['id_cargo']."'";
    $qry_cargo_contato = query($sql_cargo_contato);
    $ar_cargo_contato = farray($qry_cargo_contato);*/
    

    # Data de Cadastro do Orçamento
    $dt_cadastro_orc = $Venda->getDadosVenda('dt_cadastro');// Tratando datas
    $texto = (str_replace("VS_DT_ATUAL", utf8_encode(DataBD2DMMY($dt_cadastro_orc)), $texto));

    # Razão Social do Cliente
    $texto = (str_replace("VS_NOME_CLIENTE", utf8_encode($Venda->getPessoa()->getDadoPessoa('razao_social_nome')), $texto));

    #Informações do Contato
    $texto = (str_replace("VS_NOME_CONTATO", utf8_encode($ar_contato['nome']), $texto)); //Nome
    //$texto = (str_replace("VS_CARGO_CONTATO", utf8_encode($ar_cargo_contato['nome_cargo']), $texto)); //Cargo

    ###Itens do Orçamento###
    $info_item_orcamento = NULL;
    $info_item_modelo3 = NULL;
    $num_item = 1;
    $num_item_modelo3 = 1;
    $nome_familia = '';
    $nome_produto_linha = '';

    $sql_item_orcamento = ("SELECT a.*,b.*,c.nome_familia_comercial,c.numreg as numreg_familia FROM is_orcamento_item as a INNER JOIN is_produto as b ON a.id_produto = b.numreg INNER JOIN is_familia_comercial as c ON b.id_familia = c.numreg where a.id_orcamento = '".$_GET['pnumreg']."' ORDER BY c.nome_familia_comercial ASC");
    $qry_item_orcamento = query($sql_item_orcamento);


    $sql_produto_linha=("SELECT c.*,d.numreg as numreg_linha,d.nome_produto_linha FROM is_familia_comercial as c INNER JOIN is_produto_linha as d ON c.id_produto_linha = d.numreg where c.numreg = '".$ar_item_orcamento['numreg_familia']."'");
    $qry_produto_linha = query($sql_produto_linha);
    $ar_produto_linha = farray($qry_produto_linha);
   
    $cnt_item_orcamento = numrows($qry_item_orcamento);
    if($cnt_item_orcamento > 0){
            while ($ar_item_orcamento = farray($qry_item_orcamento)) {
                if($ar_item_orcamento['sn_item_perdido']==1){
                    continue;
                }
                    if($nome_familia!=$ar_item_orcamento['nome_familia_comercial']){
                        #Família do Item
                        $info_item_orcamento.='<w:p wsp:rsidR="003400E8" wsp:rsidRPr="00E24039" wsp:rsidRDefault="003400E8" wsp:rsidP="003400E8"><w:pPr><w:listPr><w:ilvl w:val="0"/><w:ilfo w:val="'.$num_item.'"/><wx:t wx:val="'.$num_item.'"/><wx:font wx:val="Times New Roman"/></w:listPr><w:jc w:val="both"/><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="365F91"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:u w:val="single"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="365F91"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:u w:val="single"/></w:rPr><w:t>'.$ar_item_orcamento['nome_familia_comercial'].'</w:t></w:r></w:p>';
                    }

                    $nome_familia = $ar_item_orcamento['nome_familia_comercial'];

                    #Quebra de linha
                    $info_item_orcamento.='<w:p wsp:rsidR="003400E8" wsp:rsidRPr="00E24039" wsp:rsidRDefault="003400E8" wsp:rsidP="003400E8"><w:pPr><w:jc w:val="both"/><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="365F91"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr></w:pPr></w:p>';

                    #Produto (Formato)
                    $info_item_orcamento.='<w:p wsp:rsidR="003400E8" wsp:rsidRDefault="003400E8" wsp:rsidP="003400E8"><w:pPr><w:ind w:left="348"/><w:jc w:val="both"/><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr></w:pPr><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:t>Formato: </w:t></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:tab/></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:tab/></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:tab/></w:r><w:r><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:t>'.$ar_item_orcamento['nome_produto'].'</w:t></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:t> </w:t></w:r></w:p>';

                    #Qtde(Inserções)
                    $info_item_orcamento.='<w:p wsp:rsidR="003400E8" wsp:rsidRDefault="003400E8" wsp:rsidP="003400E8"><w:pPr><w:ind w:left="348"/><w:jc w:val="both"/><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr></w:pPr><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:t>Inserções:</w:t></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:tab/></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:tab/></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:tab/></w:r><w:r><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:t>'.$ar_item_orcamento['qtde'].'</w:t></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:t> </w:t></w:r></w:p>';

                    #Valor Unitário de Tabela
                    $info_item_orcamento.='<w:p wsp:rsidR="003400E8" wsp:rsidRDefault="003400E8" wsp:rsidP="003400E8"><w:pPr><w:ind w:left="348"/><w:jc w:val="both"/><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr></w:pPr><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:t>Valor total - tabela: </w:t></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:tab/></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:tab/></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:tab/></w:r><w:r><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:t>R$ '.$ar_item_orcamento['vl_unitario_tabela'].'</w:t></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:t> </w:t></w:r></w:p>';

                    #Sub-Total
                    $info_item_orcamento.='<w:p wsp:rsidR="003400E8" wsp:rsidRPr="00E24039" wsp:rsidRDefault="003400E8" wsp:rsidP="003400E8"><w:pPr><w:ind w:left="348"/><w:jc w:val="both"/><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr></w:pPr><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:t>Valor total especial para '.$Venda->getPessoa()->getDadoPessoa('fantasia_apelido').'</w:t></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:tab/></w:r><w:r><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:tab/></w:r><w:r wsp:rsidRPr="00E24039"><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:t>R$ </w:t></w:r><w:r><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="000000"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr><w:t>'.$ar_item_orcamento['vl_total_liquido'].'</w:t></w:r></w:p><w:p wsp:rsidR="003400E8" wsp:rsidRDefault="003400E8" wsp:rsidP="008F2FBA"><w:pPr><w:jc w:val="both"/><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="365F91"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr></w:pPr></w:p>';

                    


                    

                    #Preenche itens Modelo 3

                    if($nome_produto_linha!=$ar_produto_linha['nome_produto_linha']){
                        #Linha do Item
                        $info_item_orcamento.='<w:p wsp:rsidR="003400E8" wsp:rsidRPr="00E24039" wsp:rsidRDefault="003400E8" wsp:rsidP="003400E8"><w:pPr><w:listPr><w:ilvl w:val="0"/><w:ilfo w:val="'.$num_item.'"/><wx:t wx:val="'.$num_item.'"/><wx:font wx:val="Times New Roman"/></w:listPr><w:jc w:val="both"/><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="365F91"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:u w:val="single"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="365F91"/><w:sz w:val="20"/><w:sz-cs w:val="20"/><w:u w:val="single"/></w:rPr><w:t>'.$ar_produto_linha['nome_produto_linha'].'</w:t></w:r></w:p>';
                    }
                    $nome_produto_linha = $ar_produto_linha['nome_produto_linha'];

                    # Quebra de Linha
                    $info_item_modelo3.='<w:p w:rsidR="00745BA0" w:rsidRDefault="00745BA0" w:rsidP="007F761D"><w:pPr><w:spacing w:after="0"/><w:rPr><w:b/><w:color w:val="4F81BD" w:themeColor="accent1"/><w:u w:val="single"/></w:rPr></w:pPr></w:p>';

                    # Produto - Informações de Narrativa
                    $info_item_modelo3.='<w:p w:rsidR="005C4F22" w:rsidRPr="005C4F22" w:rsidRDefault="005C4F22" w:rsidP="007F761D"><w:pPr><w:spacing w:after="0"/><w:rPr><w:b/></w:rPr></w:pPr><w:r w:rsidRPr="005C4F22"><w:rPr><w:b/></w:rPr><w:t>Produto: '.$ar_item_orcamento['nome_produto'].'</w:t></w:r></w:p>';

                    # Narrativa
                    //$info_item_modelo3.='<w:p w:rsidR="005C4F22" w:rsidRPr="005C4F22" w:rsidRDefault="005C4F22" w:rsidP="007F761D"><w:pPr><w:spacing w:after="0"/></w:pPr><w:r w:rsidRPr="005C4F22"><w:rPr><w:b/></w:rPr><w:t>Informações:</w:t></w:r><w:r><w:rPr><w:b/></w:rPr><w:t xml:space="preserve"> </w:t></w:r><w:r w:rsidRPr="005C4F22"><w:t>'.$ar_item_orcamento['narrativa'].'</w:t></w:r></w:p>';

                    # Produto - Valor unitário de tabela
                    $info_item_modelo3.='<w:p w:rsidR="00A475FD" w:rsidRPr="00A475FD" w:rsidRDefault="00A475FD" w:rsidP="007F761D"><w:pPr><w:spacing w:after="0"/></w:pPr><w:r w:rsidRPr="00A475FD"><w:t xml:space="preserve">Valor unitário de tabela: R$ '.$ar_item_orcamento['vl_unitario_tabela'].'</w:t></w:r></w:p>';
             
                    # Período
                    //$info_item_modelo3.='<w:p w:rsidR="00A475FD" w:rsidRPr="00A475FD" w:rsidRDefault="00A475FD" w:rsidP="007F761D"><w:pPr><w:spacing w:after="0"/></w:pPr><w:r w:rsidRPr="00A475FD"><w:t xml:space="preserve">Período: '.periodo.'</w:t></w:r></w:p>';

                    # Texto 2
                    $qtde_item = $ar_item_orcamento['qtde'];
                    $info_item_modelo3.='<w:p w:rsidR="00A475FD" w:rsidRPr="00A475FD" w:rsidRDefault="00A475FD" w:rsidP="007F761D"><w:pPr><w:spacing w:after="0"/></w:pPr><w:r w:rsidRPr="00A475FD"><w:t xml:space="preserve">Quantidade: '.$qtde_item.'</w:t></w:r></w:p>';

                    # Tópico
                    $info_item_modelo3.='<w:p w:rsidR="00943413" w:rsidRDefault="00943413" w:rsidP="00943413"><w:pPr><w:pStyle w:val="PargrafodaLista"/><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr><w:spacing w:after="0"/><w:rPr><w:b/></w:rPr></w:pPr><w:r><w:rPr><w:b/></w:rPr><w:t>'.$ar_item_orcamento['nome_produto'].'</w:t></w:r></w:p>';

                    # Valor x Item
                    $info_item_modelo3.='<w:p w:rsidR="00943413" w:rsidRPr="00943413" w:rsidRDefault="00943413" w:rsidP="00943413"><w:pPr><w:pStyle w:val="PargrafodaLista"/><w:spacing w:after="0"/><w:ind w:firstLine="696"/></w:pPr><w:r w:rsidRPr="00943413"><w:t>'.$qtde_item.' x R$ '.$ar_item_orcamento['vl_unitario_tabela'].' = '.$ar_item_orcamento['vl_total_bruto'].'</w:t></w:r></w:p>';

                    # Título Destacado
                    $info_item_modelo3.='<w:p w:rsidR="005C4F22" w:rsidRPr="005C4F22" w:rsidRDefault="00F73973" w:rsidP="007F761D"><w:pPr><w:spacing w:after="0"/><w:rPr><w:b/><w:color w:val="4F81BD" w:themeColor="accent1"/></w:rPr></w:pPr><w:r w:rsidRPr="005C4F22"><w:rPr><w:b/><w:color w:val="4F81BD" w:themeColor="accent1"/><w:highlight w:val="yellow"/></w:rPr><w:t>Valor Especial para '.$Venda->getPessoa()->getDadoPessoa('razao_social_nome').': R$ '.$ar_item_orcamento['vl_total_liquido'].'</w:t></w:r></w:p>';

                    $num_item_modelo3 = $num_item_modelo3 ++;
                    $num_item = $num_item ++;


            }
    } else {
        # Retorna em branco
        //modelos 1e2
        $info_item_orcamento ='<w:p wsp:rsidR="003400E8" wsp:rsidRPr="00E24039" wsp:rsidRDefault="003400E8" wsp:rsidP="003400E8"><w:pPr><w:jc w:val="both"/><w:rPr><w:rFonts w:ascii="Maiandra GD" w:h-ansi="Maiandra GD"/><wx:font wx:val="Maiandra GD"/><w:b/><w:b-cs/><w:color w:val="365F91"/><w:sz w:val="20"/><w:sz-cs w:val="20"/></w:rPr></w:pPr></w:p>';

        //Modelo 3
        $info_item_modelo3 ='<w:p w:rsidR="00745BA0" w:rsidRDefault="00745BA0" w:rsidP="007F761D"><w:pPr><w:spacing w:after="0"/><w:rPr><w:b/><w:color w:val="4F81BD" w:themeColor="accent1"/><w:u w:val="single"/></w:rPr></w:pPr></w:p>';

    }

    # Exibe os dados dos itens
    $texto=(str_replace("VS_DADOS_ITEM_MODELO3",$info_item_modelo3,$texto));

    $texto=(str_replace("VS_DADOS_ITEM",$info_item_orcamento,$texto));

    # Valor Total Negociado
    $texto=(str_replace("VS_TOTAL_VL_ESPECIAL",$Venda->getVlTotalVendaLiquido(),$texto));

    # Condição de Pagamento
    $id_cond_pagto_especial = $Venda->getDadosVenda('cond_pagto_especial');
    $sql_cond_pagto = ("select * from is_cond_pagto where numreg='".$id_cond_pagto_especial."'");
    $qry_cond_pagto = query($sql_cond_pagto);
    $ar_cond_pagto = farray($qry_cond_pagto);

    $texto=(str_replace("VS_COND_PAGTO",$ar_cond_pagto['nome_cond_pagto'],$texto));

    # Não há data de validade informado no formulário de Orçamento
    //$texto = (str_replace("VS_DT_VALIDADE", utf8_encode(DataBD2DMMY('$dt_cadastro_orc')), $texto));

    /*
     * Usuários (Participantes) envolvidos com a venda
     *
     * Aguardando o término do desenvolvimento dos Participantes da Venda
     *
     */

    if ($texto) {

        header("Content-Type: application/vnd.ms-word");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("content-disposition: attachment;filename=modelo_orcamento".gmdate("Y")."-".gmdate("m")."-".gmdate("d").".doc");

        echo $texto;
    }





/* Funções */

# Tratamento de Datas
function DataBD2DMY($data) {
    return substr($data, 8, 2) . '/' . substr($data, 5, 2) . '/' . substr($data, 0, 4);
}

function DataBD2DMMY($data) {
    $mes_name = array("", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    return substr($data, 8, 2) . ' de ' . $mes_name[(substr($data, 5, 2) * 1)] . ' de ' . substr($data, 0, 4);
}
?>