<?php

/*
 * class.OrdemFaturamentoExpTxt.php
 * Autor: Lucas
 * 25/11/2010 09:40
 *
 * Esta classe foi baseada na class.VendaExpTxt.php
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class OrdemFaturamentoExpTxt{

    private $Txt;
    private $MapaCampos = array();
    private $NovaLinha;

    public function __construct(){
        $this->MapaCampos['01'][1] = array(1,2,0);      //1 - Código do Registro - 01
        $this->MapaCampos['01'][2] = array(3,12,0);     //2 - Nome Abreviado do Cliente
        $this->MapaCampos['01'][3] = array(15,12,0);    //3 - Número do Pedido do Cliente
        $this->MapaCampos['01'][4] = array(27,9,0);     //4 - Código do Emitente
        $this->MapaCampos['01'][5] = array(36,19,0);    //5 - CGC ou CIC do Cliente
        $this->MapaCampos['01'][6] = array(55,12,0);    //6 - Número do Pedido do Representante
        $this->MapaCampos['01'][7] = array(67,8,0);     //7 - Data em que o Pedido foi Emitido
        $this->MapaCampos['01'][8] = array(75,8,0);     //8 - Data Mínima para Atendimento do Pedido
        $this->MapaCampos['01'][9] = array(83,8,0);     //9 - Data Limite para Atendimento do Pedido
        $this->MapaCampos['01'][10] = array(91,2,0);    //10 - Tipo Pedido
        $this->MapaCampos['01'][11] = array(93,3,0);    //11 - Código da Condição de Pagamento
        $this->MapaCampos['01'][12] = array(96,8,0);    //12 - Tabela de Preços
        $this->MapaCampos['01'][13] = array(104,3,0);   //13 - Número da Tabela de Financiamento
        $this->MapaCampos['01'][14] = array(107,2,0);   //14 - Número do Índice de Financiamento
        $this->MapaCampos['01'][15] = array(109,2,0);   //15 - Tipo de Preço
        $this->MapaCampos['01'][16] = array(111,2,0);   //16 - Código da Moeda
        $this->MapaCampos['01'][17] = array(113,5,0);   //17 - Desconto-1
        $this->MapaCampos['01'][18] = array(118,5,0);   //18 - Desconto-2
        $this->MapaCampos['01'][19] = array(123,2,0);   //19 - Prioridade
        $this->MapaCampos['01'][20] = array(125,1,0);   //20 - Destino da Mercadoria
        $this->MapaCampos['01'][21] = array(126,12,0);  //21 - Rota
        $this->MapaCampos['01'][22] = array(138,12,0);  //22 - Transportador
        $this->MapaCampos['01'][23] = array(150,12,0);  //23 - Representante
        $this->MapaCampos['01'][24] = array(162,25,0);  //24 - Cidade CIF
        $this->MapaCampos['01'][25] = array(187,3,0);   //25 - Mensagem
        $this->MapaCampos['01'][26] = array(190,12,2);  //26 - Código Entrega
        $this->MapaCampos['01'][27] = array(202,25,0);  //27 - Contato
        $this->MapaCampos['01'][28] = array(227,154,0); //28 - Brancos
        $this->MapaCampos['01'][29] = array(381,6,0);   //29 - Natureza de Operação
        $this->MapaCampos['01'][30] = array(387,5,0);   //30 - Código do Portador
        $this->MapaCampos['01'][31] = array(392,1,0);   //31 - Modalidade
        $this->MapaCampos['01'][32] = array(393,12,0);  //32 - Nome Transportadora Redespacho
        $this->MapaCampos['01'][33] = array(405,50,0);  //33 - Brancos
        $this->MapaCampos['01'][34] = array(455,4,0);   //34 - Brancos
        $this->MapaCampos['01'][35] = array(459,3,0);   //35 - Código do Estabelecimento
        $this->MapaCampos['01'][36] = array(462,8,0);   //36 - Data de Entrega Prevista
        $this->MapaCampos['01'][37] = array(470,2,0);   //37 - Espécie de Pedido
        $this->MapaCampos['01'][38] = array(472,3,0);   //38 - Estabelecimento Atendimento
        $this->MapaCampos['01'][39] = array(475,3,0);   //39 - Estabelecimento Central
        $this->MapaCampos['01'][40] = array(478,3,0);   //40 - Estabelecimento Destino
        $this->MapaCampos['01'][41] = array(481,3,0);   //41 - Canal de Venda
        $this->MapaCampos['01'][42] = array(484,7,0);   //42 - Brancos
        $this->MapaCampos['01'][43] = array(491,1,0);   //43 - Desconto Item/Cliente
        $this->MapaCampos['01'][44] = array(492,5,0);   //44 - Perc Desc Tabela Preços
        $this->MapaCampos['01'][45] = array(497,50,0);  //45 - Perc Desc Informado
        $this->MapaCampos['01'][46] = array(547,7,0);   //46 - Perc Desc Valor
        $this->MapaCampos['01'][47] = array(554,2,0);   //47 - Moeda Faturamento
        $this->MapaCampos['01'][48] = array(556,1,0);   //48 - Concede Bonificação
        $this->MapaCampos['01'][49] = array(557,12,0);  //49 - Número Pedido Origem
        $this->MapaCampos['01'][50] = array(569,12,0);  //50 - Número Pedido Bonificação
        $this->MapaCampos['01'][51] = array(581,5,0);   //51 - Código do Serviço de Frete
        $this->MapaCampos['01'][52] = array(586,12,0);  //52 - Nome Abreviado do Cliente Remessa Triangular

        $this->MapaCampos['02'][1] = array(1,2,0);      // 1 - Código do Registro - 02
        $this->MapaCampos['02'][2] = array(3,2000,0);   // 2 - Observações das Condições Especiais

        $this->MapaCampos['03'][1] = array(1,2,0);      // 1 - Código do Registro - 03
        $this->MapaCampos['03'][2] = array(3,2000,0);   // 2 - Condições Redespacho

        $this->MapaCampos['04'][1] = array(1,2,0);      // 1 - Código do Registro - 04
        $this->MapaCampos['04'][2] = array(3,2000,0);   // 2 - Observação do Pedido

        $this->MapaCampos['06'][1] = array(1,2,0);      // 1 - Código do Registro - 06
        $this->MapaCampos['06'][2] = array(3,9,0);      // 2 - Numero do Pedido Condição de Pagamento
        $this->MapaCampos['06'][3] = array(12,3,0);     // 3 - Sequencia
        $this->MapaCampos['06'][4] = array(15,1,0);     // 4 - Tipo da Condição de Pagamento (1 - Datas 2 - Dias)
        $this->MapaCampos['06'][5] = array(16,8,0);     // 5 - Data do Pagamento
        $this->MapaCampos['06'][6] = array(24,5,0);     // 6 - Percentual do Pagamento
        $this->MapaCampos['06'][7] = array(29,11,0);    // 7 - Valor da Parcela
        $this->MapaCampos['06'][8] = array(40,2,0);     // 8 - Codigo Vencimento
        $this->MapaCampos['06'][9] = array(42,3,0);     // 9 - Numero Dias Vecimento
        $this->MapaCampos['06'][10] = array(45,2000,0); // 10 - Observações das Condições Especiais de Pagto

        $this->MapaCampos['07'][1] = array(1,2,0);      //1 - Código do Registro - 07
        $this->MapaCampos['07'][2] = array(3,14,0);     //2 - Nome Abreviado do Cliente
        $this->MapaCampos['07'][3] = array(15,26,0);    //3 - Pedido do Cliente
        $this->MapaCampos['07'][4] = array(27,31,0);    //4 - Número da Sequência do Item do Pedido
        $this->MapaCampos['07'][5] = array(32,47,0);    //5 - Item
        $this->MapaCampos['07'][6] = array(48,55,0);    //6 - Ordem de Compra
        $this->MapaCampos['07'][7] = array(56,57,0);    //7 - Parcela
        $this->MapaCampos['07'][8] = array(58,65,0);    //8 - Data Entrega Original
        $this->MapaCampos['07'][9] = array(66,76,0);    //9 - Quantidade Unidade Faturamento
        $this->MapaCampos['07'][10] = array(77,90,3);   //10 - Preço Líquido
        $this->MapaCampos['07'][11] = array(91,140,0);  //11 - Brancos
        $this->MapaCampos['07'][12] = array(141,144,0); //12 - Percentual Mínimo Faturamento Parcial
        $this->MapaCampos['07'][13] = array(145,152,0); //13 - Referência
        $this->MapaCampos['07'][14] = array(153,158,0); //14 - Natureza de Operação
        $this->MapaCampos['07'][15] = array(159,164,0); //15 - Percentual de Desconto de ICMS
        $this->MapaCampos['07'][16] = array(165,165,0); //16 - ICMS Retido na Fonte
        $this->MapaCampos['07'][17] = array(166,166,0); //17 - Unidade Faturamento
        $this->MapaCampos['07'][18] = array(167,174,0); //18 - Data de Entrega Prevista
        $this->MapaCampos['07'][19] = array(175,176,0); //19 - Indicador Componente Produto Configurado
        $this->MapaCampos['07'][20] = array(177,188,0); //20 - Código de entrega do Item
        $this->MapaCampos['07'][21] = array(189,196,0); //21 - Tabela de Preço
        $this->MapaCampos['07'][22] = array(197,203,0); //22 - Perc Desc Tabela Preços
        $this->MapaCampos['07'][23] = array(204,253,0); //23 - Perc Desc Informado
        $this->MapaCampos['07'][24] = array(254,264,0); //24 - Valor Desconto Informado
        $this->MapaCampos['07'][25] = array(265,265,0); //25 - Concede Bonificação Quantidade
        $this->MapaCampos['07'][26] = array(266,270,0); //26 - Perc Bonificação
        $this->MapaCampos['07'][27] = array(271,275,0); //27 - Perc Desc Período
        $this->MapaCampos['07'][28] = array(276,280,0); //28 - Perc Desc Prazo
        $this->MapaCampos['07'][29] = array(281,291,0); //29 - Valor Desconto Bonificação
        $this->MapaCampos['07'][30] = array(292,302,0); //30 - Quantidade Bonificação
        $this->MapaCampos['07'][31] = array(303,307,0); //31 - Sequência Bonificação
        $this->MapaCampos['07'][32] = array(308,321,0); //32 - Desconto [1]
        $this->MapaCampos['07'][33] = array(322,335,0); //33 - Desconto [2]
        $this->MapaCampos['07'][34] = array(336,349,0); //34 - Desconto [3]
        $this->MapaCampos['07'][35] = array(350,363,0); //35 - Desconto [4]
        $this->MapaCampos['07'][36] = array(364,377,0); //36 - Desconto [5]
        $this->MapaCampos['07'][37] = array(378,379,0); //37 - Unidade de Medida Faturamento / Débito Direto
        $this->MapaCampos['07'][38] = array(380,396,0); //38 - Conta de Aplicação
        $this->MapaCampos['07'][39] = array(397,408,0); //39 - Vl Custo Contábil
        $this->MapaCampos['07'][40] = array(409,411,0); //40 - Código da Unidade de Negócio
        $this->MapaCampos['07'][41] = array(412,418,0); //41 - Branco
        $this->MapaCampos['07'][42] = array(419,2419,0); //42 - Observações do Item

        $this->MapaCampos['09'][1] = array(1,2,0);    // 1 - Código do Registro - 09 Representante (ped-repre)
        $this->MapaCampos['09'][2] = array(3,12,0);   // 2 - Pedido do Cliente
        $this->MapaCampos['09'][3] = array(15,5,0);   // 3 - Código Representante
        $this->MapaCampos['09'][4] = array(20,5,0);   // 4 - Percentual de Comissão
        $this->MapaCampos['09'][5] = array(25,5,0);   // 5 - Comissão Emissão
        $this->MapaCampos['09'][6] = array(30,1,0);   // 6 - Representante Principal
    }

    public function PreencheValor($Linha,$Campo,$Valor){
        if($this->MapaCampos[$Linha][$Campo][2] == 1){//Data
            $Valor = substr($Valor,8,2).substr($Valor,5,2).substr($Valor,0,4);
        }elseif($this->MapaCampos[$Linha][$Campo][2] == 2){//Remove Acentos
            $Valor = retiraAcentos($Valor);
        }elseif($this->MapaCampos[$Linha][$Campo][2] == 3){
            $Valor = number_format($Valor,5,'.','');
        }elseif($this->MapaCampos[$Linha][$Campo][2] == 4){//Qtde
            //echo $Valor;
            $Valor = number_format($Valor,4,'','');
            //echo "/".$Valor;
        }
        $Valor = str_replace("\r\n",' | ',$Valor);
        $this->NovaLinha->AdicionaValor($this->MapaCampos[$Linha][$Campo][0] - 1,$this->MapaCampos[$Linha][$Campo][1],$Valor);
    }

    public function CarregaOrdemFaturamentoBD(){
        /* ---------------------------------------------------------------------
         * LINHA 01 - Cabeçalho
         *
         * t1 = is_ordem_faturamento
         * t2 = is_pessoa
         * t3 = is_cond_pagto
         * t4 = is_transportadora
         * t5 = is_destino_mercadoria
         */
        $QryOFaturamento = query("   SELECT
                                    t1.numreg,
                                    t1.id_contrato,
                                    t1.nr_pedido_exporta,
                                    t1.dt_ordem,
                                    t1.valor,
                                    t1.dt_vencimento,
                                    t1.obs_item,
                                    t2.id_pessoa_erp,
                                    t2.fantasia_apelido,
                                    t2.cnpj_cpf,
                                    t7.id_cfop_erp,
                                    t8.id_estabelecimento_erp,
                                    t9.id_produto_erp
                                FROM is_ordem_faturamento t1
                                    INNER JOIN is_pessoa t2 ON t1.id_pessoa = t2.numreg
                                    INNER JOIN is_cfop t7 ON t1.id_cfop = t7.numreg
                                    INNER JOIN is_estabelecimento t8 ON t1.id_estabelecimento = t8.numreg
                                    INNER JOIN is_produto t9 ON t1.id_produto = t9.numreg
                                WHERE
                                    t1.sn_exportado_erp = 0");
        while($ArOFaturamento = farray($QryOFaturamento)){
            /*
             * Aplicando tratamento para linha 01
             */
            $this->NovaLinha = new GeraLinhaTxt(598);
            $this->PreencheValor('01','1','01');
            $this->PreencheValor('01','2',$ArOFaturamento['fantasia_apelido']);
            $this->PreencheValor('01','3',$ArOFaturamento['nr_pedido_exporta']);
            $this->PreencheValor('01','4',$ArOFaturamento['id_pessoa_erp']);
            $this->PreencheValor('01','5',$ArOFaturamento['cnpj_cpf']);
            $this->PreencheValor('01','6',''); //Em branco
            $this->PreencheValor('01','7',date('dmY'));
            $this->PreencheValor('01','8',''); //Em branco
            $this->PreencheValor('01','9',''); //Em branco
            $this->PreencheValor('01','10',''); //Em branco
            $this->PreencheValor('01','11','0'); //Condição de pagamento especial
            $this->PreencheValor('01','13','1');
            $this->PreencheValor('01','14','1');
            $this->PreencheValor('01','29',$ArOFaturamento['id_cfop_erp']);
            $this->PreencheValor('01','15','1'); // Fixo 1 - Preço Informado
            //$this->PreencheValor('01','16','1'); // Fixo 1 - R$ Real | Se este valor é preenchido, é obrigatório estar cadastrada a cotação da moeda no ERP
            $this->PreencheValor('01','20','');
            $this->PreencheValor('01','22','');
            $this->PreencheValor('01','24','');

            $this->PreencheValor('01','23','');
            $this->PreencheValor('01','24','');
            $this->PreencheValor('01','26','');
            $this->PreencheValor('01','31','1'); // Fixo 1 - Modalidade

            $this->PreencheValor('01','34',$ArOFaturamento['id_estabelecimento_erp']);

            $this->PreencheValor('01','37','01'); // Fixo 01 - Espécie do Pedido

            $this->Txt .= $this->NovaLinha->CriaTxt().'L1'.chr(10);

            /* -----------------------------------------------------------------
             * LINHA 02 - Observações das Condições Especiais
             */
            $this->NovaLinha = new GeraLinhaTxt(2002);
            $this->PreencheValor('02','1','02');
            $this->PreencheValor('02','2','');

            $this->Txt .= $this->NovaLinha->CriaTxt().chr(10);

            /* -----------------------------------------------------------------
             * LINHA 04 - Observações
             */
            $this->NovaLinha = new GeraLinhaTxt(2002);
            $this->PreencheValor('04','1','04');
            $this->PreencheValor('04','2','');

            $this->Txt .= $this->NovaLinha->CriaTxt().chr(10);

            /* -----------------------------------------------------------------
             * LINHA 06 - Condição de Pagamento Especial
             */
            $this->NovaLinha = new GeraLinhaTxt(2002);
            $this->PreencheValor('06','1','06');
            $this->PreencheValor('06','2','0');         // 2 - Numero do Pedido Condição de Pagamento
            $this->PreencheValor('06','3','10');        // 3 - Sequencia
            $this->PreencheValor('06','4','3');         // 4 - Tipo da Condição de Pagamento (1 - Datas 2 - Dias)
            $this->PreencheValor('06','5',trata_dt_exp_dts(dten2br($ArOFaturamento['dt_vencimento'])));
            $this->PreencheValor('06','6','');          // 6 - Percentual do Pagamento
            $this->PreencheValor('06','7',$ArOFaturamento['valor']);
            $this->PreencheValor('06','8','01');        // 8 - Codigo Vencimento
            $this->PreencheValor('06','9','0');         // 9 - Numero Dias Vecimento
            $this->PreencheValor('06','10','');         // 10 - Observações das Condições Especiais de Pagto

            $this->Txt .= $this->NovaLinha->CriaTxt().chr(10);


            /* -----------------------------------------------------------------
             * LINHA 07 - Itens
             *
             */
            $this->NovaLinha = new GeraLinhaTxt(2419);
            $this->PreencheValor('07','1','07');
            $this->PreencheValor('07','2',$ArOFaturamento['fantasia_apelido']);
            $this->PreencheValor('07','3',$ArOFaturamento['nr_pedido_exporta']);
            $this->PreencheValor('07','4','10'); //Sequencia fixa devido o contrato enviar apenas 1 item
            $this->PreencheValor('07','5',$ArOFaturamento['id_produto_erp']);
            $this->PreencheValor('07','9','10000');
            $this->PreencheValor('07','10',$ArOFaturamento['valor']);
            $this->PreencheValor('07','14',$ArOFaturamento['id_cfop_erp']);
            $this->PreencheValor('07','42',$ArOFaturamento['obs_item']);
            $this->Txt .= $this->NovaLinha->CriaTxt().chr(10);

            /* -----------------------------------------------------------------
             * LINHA 09 - Representantes

            $this->NovaLinha = new GeraLinhaTxt(30);
            $this->PreencheValor('09','1','09');
            $this->PreencheValor('09','2','');
            $this->PreencheValor('09','3','');
            $this->PreencheValor('09','4','');
            $this->PreencheValor('09','6','');

            $this->Txt .= $this->NovaLinha->CriaTxt().chr(10);

            /*
             * Atualizando o pedido como exportado
             */

            $ArSqlUpdatePedido = array('numreg' => $ArOFaturamento['numreg'],'sn_exportado_erp' => 1,'dt_hr_exportado_erp' => date("Y-m-d H:i:s"));
            $SqlUpdatePedido = AutoExecuteSql(getParametrosGerais('TipoBancoDados'),'is_ordem_faturamento',$ArSqlUpdatePedido,'UPDATE',array('numreg'));
            $QryUpdatePedido = query($SqlUpdatePedido);
        }
    }

    public function getTxt(){
        return $this->Txt;
    }

}

?>