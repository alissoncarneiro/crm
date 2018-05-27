<?php
/*
 * VendaExpTxt.php
 * Autor: Alex
 * 16/11/2010 14:58
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class VendaExpTxt{
    protected $Txt;
    protected $MapaCampos = array();
    protected $NovaLinha;
    protected $VendaParametro;
    protected $DataBaseExportacao;
    protected $DataHoje;

    public function  __construct(){
        $this->VendaParametro = new VendaParametro();
        $this->DataHoje = date('Y-m-d');

        /* Definindo a data base para exportação dos pedidos */
        if($this->VendaParametro->getSnExportaNaDataEntrega()){

            $DiaSemanaHoje = date('w',strtotime($this->DataHoje));

            if($DiaSemanaHoje == '5'){ /* Se hoje for sexta-feira */
                $this->DataBaseExportacao = date('Y-m-d',strtotime($this->DataHoje.' +3 days')); /* Alterando a data para segunda-feira */
            }
            elseif($DiaSemanaHoje == '6'){ /* Se hoje for sábado */
                $this->DataBaseExportacao = date('Y-m-d',strtotime($this->DataHoje.' +2 days')); /* Alterando a data para segunda-feira */
            }
            else{ /* Se for qualquer outro dia da semana */
                $this->DataBaseExportacao = date('Y-m-d',strtotime($this->DataHoje.' +1 day')); /* Alterando a data para segunda-feira */
            }
        }

        /* Definindo o posicionamento dos campos */
        $this->MapaCampos['01'][1] = array(1,2,0);    //1 - Código do Registro - 01
        $this->MapaCampos['01'][2] = array(3,12,2);   //2 - Nome Abreviado do Cliente
        $this->MapaCampos['01'][3] = array(15,12,0);  //3 - Número do Pedido do Cliente
        $this->MapaCampos['01'][4] = array(27,9,0);   //4 - Código do Emitente
        $this->MapaCampos['01'][5] = array(36,19,0);  //5 - CGC ou CIC do Cliente
        $this->MapaCampos['01'][6] = array(55,12,0);  //6 - Número do Pedido do Representante
        $this->MapaCampos['01'][7] = array(67,8,1);   //7 - Data em que o Pedido foi Emitido
        $this->MapaCampos['01'][8] = array(75,8,1);   //8 - Data Mínima para Atendimento do Pedido
        $this->MapaCampos['01'][9] = array(83,8,1);   //9 - Data Limite para Atendimento do Pedido
        $this->MapaCampos['01'][10] = array(91,2,0);  //10 - Tipo Pedido
        $this->MapaCampos['01'][11] = array(93,3,0); //11 - Código da Condição de Pagamento
        $this->MapaCampos['01'][12] = array(96,8,0); //12 - Tabela de Preços
        $this->MapaCampos['01'][13] = array(104,3,0); //13 - Número da Tabela de Financiamento
        $this->MapaCampos['01'][14] = array(107,2,0); //14 - Número do Índice de Financiamento
        $this->MapaCampos['01'][15] = array(109,2,0); //15 - Tipo de Preço
        $this->MapaCampos['01'][16] = array(111,2,0); //16 - Código da Moeda
        $this->MapaCampos['01'][17] = array(113,5,33); //17 - Desconto-1
        $this->MapaCampos['01'][18] = array(118,5,0); //18 - Desconto-2
        $this->MapaCampos['01'][19] = array(123,2,0); //19 - Prioridade
        $this->MapaCampos['01'][20] = array(125,1,0); //20 - Destino da Mercadoria
        $this->MapaCampos['01'][21] = array(126,12,0); //21 - Rota
        $this->MapaCampos['01'][22] = array(138,12,2); //22 - Transportador
        $this->MapaCampos['01'][23] = array(150,12,0); //23 - Representante
        $this->MapaCampos['01'][24] = array(162,25,0); //24 - Cidade CIF
        $this->MapaCampos['01'][25] = array(187,3,0); //25 - Mensagem
        $this->MapaCampos['01'][26] = array(190,12,2); //26 - Código Entrega
        $this->MapaCampos['01'][27] = array(202,25,0); //27 - Contato
        $this->MapaCampos['01'][28] = array(227,154,0); //28 - Brancos
        $this->MapaCampos['01'][29] = array(381,6,0); //29 - Natureza de Operação
        $this->MapaCampos['01'][30] = array(387,5,0); //30 - Código do Portador
        $this->MapaCampos['01'][31] = array(392,1,0); //31 - Modalidade
        $this->MapaCampos['01'][32] = array(393,12,0); //32 - Nome Transportadora Redespacho
        $this->MapaCampos['01'][33] = array(405,50,0); //33 - Brancos
        $this->MapaCampos['01'][34] = array(455,4,0); //34 - Brancos
        $this->MapaCampos['01'][35] = array(459,3,0); //35 - Código do Estabelecimento
        $this->MapaCampos['01'][36] = array(462,8,1); //36 - Data de Entrega Prevista
        $this->MapaCampos['01'][37] = array(470,2,0); //37 - Espécie de Pedido
        $this->MapaCampos['01'][38] = array(472,3,0); //38 - Estabelecimento Atendimento
        $this->MapaCampos['01'][39] = array(475,3,0); //39 - Estabelecimento Central
        $this->MapaCampos['01'][40] = array(478,3,0); //40 - Estabelecimento Destino
        $this->MapaCampos['01'][41] = array(481,3,0); //41 - Canal de Venda
        $this->MapaCampos['01'][42] = array(484,7,0); //42 - Brancos
        $this->MapaCampos['01'][43] = array(491,1,0); //43 - Desconto Item/Cliente
        $this->MapaCampos['01'][44] = array(492,5,32); //44 - Perc Desc Tabela Preços
        $this->MapaCampos['01'][45] = array(497,50,0); //45 - Perc Desc Informado
        $this->MapaCampos['01'][46] = array(547,7,0); //46 - Perc Desc Valor
        $this->MapaCampos['01'][47] = array(554,2,0); //47 - Moeda Faturamento
        $this->MapaCampos['01'][48] = array(556,1,0); //48 - Concede Bonificação
        $this->MapaCampos['01'][49] = array(557,12,0); //49 - Número Pedido Origem
        $this->MapaCampos['01'][50] = array(569,12,0); //50 - Número Pedido Bonificação
        $this->MapaCampos['01'][51] = array(581,5,0); //51 - Código do Serviço de Frete
        $this->MapaCampos['01'][52] = array(586,12,0); //52 - Nome Abreviado do Cliente Remessa Triangular
        $this->MapaCampos['01'][53] = array(599,8,0); //53 - Safra
        $this->MapaCampos['01'][54] = array(607,5,0); //54 - Código da Condição de Pagamento

        $this->MapaCampos['02'][1] = array(1,2,0);    //1 - Código do Registro - 02
        $this->MapaCampos['02'][2] = array(3,2000,0);    // - Observações das Condições Especiais

        $this->MapaCampos['03'][1] = array(1,2,0);    //1 - Código do Registro - 03
        $this->MapaCampos['03'][2] = array(3,2000,0);    // - Condições Redespacho

        $this->MapaCampos['04'][1] = array(1,2,0);    //1 - Código do Registro - 04
        $this->MapaCampos['04'][2] = array(3,2000,0);    // - Observação do Pedido

        $this->MapaCampos['07'][1] = array(1,2,0); //1 - Código do Registro - 07
        $this->MapaCampos['07'][2] = array(3,14,2); //2 - Nome Abreviado do Cliente
        $this->MapaCampos['07'][3] = array(15,26,0); //3 - Pedido do Cliente
        $this->MapaCampos['07'][4] = array(27,31,0); //4 - Número da Sequência do Item do Pedido
        $this->MapaCampos['07'][5] = array(32,47,0); //5 - Item
        $this->MapaCampos['07'][6] = array(48,55,0); //6 - Ordem de Compra
        $this->MapaCampos['07'][7] = array(56,57,0); //7 - Parcela
        $this->MapaCampos['07'][8] = array(58,65,1); //8 - Data Entrega Original
        $this->MapaCampos['07'][9] = array(66,76,34); //9 - Quantidade Unidade Faturamento
        $this->MapaCampos['07'][10] = array(77,90,35); //10 - Preço Líquido
        $this->MapaCampos['07'][11] = array(91,140,0); //11 - Brancos
        $this->MapaCampos['07'][12] = array(141,144,0); //12 - Percentual Mínimo Faturamento Parcial
        $this->MapaCampos['07'][13] = array(145,152,2); //13 - Referência
        $this->MapaCampos['07'][14] = array(153,158,0); //14 - Natureza de Operação
        $this->MapaCampos['07'][15] = array(159,164,0); //15 - Percentual de Desconto de ICMS
        $this->MapaCampos['07'][16] = array(165,165,0); //16 - ICMS Retido na Fonte
        $this->MapaCampos['07'][17] = array(166,166,0); //17 - Unidade Faturamento
        $this->MapaCampos['07'][18] = array(167,174,1); //18 - Data de Entrega Prevista
        $this->MapaCampos['07'][19] = array(175,176,0); //19 - Indicador Componente Produto Configurado
        $this->MapaCampos['07'][20] = array(177,188,0); //20 - Código de entrega do Item
        $this->MapaCampos['07'][21] = array(189,196,0); //21 - Tabela de Preço
        $this->MapaCampos['07'][22] = array(197,203,35); //22 - Perc Desc Tabela Preços
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

        $this->MapaCampos['09'][1] = array(1,2,0);    //1 - Código do Registro - 09 Representante (ped-repre)
        $this->MapaCampos['09'][2] = array(3,12,0);   //2 - Pedido do Cliente
        $this->MapaCampos['09'][3] = array(15,5,0);  //3 - Código Representante
        $this->MapaCampos['09'][4] = array(20,5,0);   //4 - Percentual de Comissão
        $this->MapaCampos['09'][5] = array(25,5,0);   //5 - Comissão Emissão
        $this->MapaCampos['09'][6] = array(30,1,0);   //6 - Representante Principal
    }

    public function getDataBaseExportacao(){
        return $this->DataBaseExportacao;
    }

    public function setDataBaseExportacao($Data){
        $this->DataBaseExportacao = $Data;
    }

    public function getVendaParametro(){
        return $this->VendaParametro;
    }

    public function PreencheValor($Linha,$Campo,$Valor){
        if($this->MapaCampos[$Linha][$Campo][2] == 1){
            $Valor = substr($Valor,8,2).substr($Valor,5,2).substr($Valor,0,4);
        }
        elseif($this->MapaCampos[$Linha][$Campo][2] == 2){
            $Valor = retiraAcentos($Valor);
        }
        elseif($this->MapaCampos[$Linha][$Campo][2] == 3){
            $Valor = number_format($Valor,5,'','');
        }
        elseif($this->MapaCampos[$Linha][$Campo][2] == 4){
            $Valor = number_format($Valor,4,'','');
        }
        elseif($this->MapaCampos[$Linha][$Campo][2] == 32){
            $Valor = number_format_min($Valor,2,'','');
        }
        elseif($this->MapaCampos[$Linha][$Campo][2] == 33){
            $Valor = number_format_min($Valor,3,'','');
        }
        elseif($this->MapaCampos[$Linha][$Campo][2] == 34){
            $Valor = number_format_min($Valor,4,'','');
        }
        elseif($this->MapaCampos[$Linha][$Campo][2] == 35){
            $Valor = number_format_min($Valor,5,'','');
        }
        $Valor = str_replace("\r\n",' | ',$Valor);
        $this->NovaLinha->AdicionaValor($this->MapaCampos[$Linha][$Campo][0]-1,$this->MapaCampos[$Linha][$Campo][1],$Valor);
    }

    public function PreencheValorCustom($Linha,$ArPedido){}

    public function PreencheValorCustomItem($ArPedido,$ArPedidoItem){}

    public function PreencheValorCustomRepresentante($ArPedido,$ArPedidoRepresentante){}

    public function getSqlPedidos($Count=false){
        $Sql = "SELECT ";

        if($Count === false){
            $Sql .= "   t1.numreg,
                        t1.id_pedido_cliente,
                        t1.id_pedido_representante,
                        t1.obs,
                        t1.obs_nf,
                        t1.dt_pedido,
                        t1.id_tp_frete,
                        t1.dt_entrega,
                        t1.dt_entrega_desejada,
                        t1.id_tp_preco,
                        t1.pct_desconto_pessoa,
                        t1.pct_desconto_tab_preco,
                        t1.pct_desconto_informado,
                        t1.sn_antecipa_entrega,
                        t1.id_pessoa_triangular,
                        t2.id_pessoa_erp,
                        t2.fantasia_apelido,
                        t2.cnpj_cpf,
                        t3.id_cond_pagto_erp,
                        t4.id_transportadora_erp,
                        t4.nome_abrev_transportadora,
                        t5.id_destino_mercadoria_erp,
                        t6.id_endereco_erp,
                        t6.cidade,
                        t7.id_cfop_erp,
                        t8.id_estabelecimento_erp,
                        t9.id_tab_preco_erp,
                        t10.id_canal_venda_erp
                        ";
        }
        else{
            $Sql .= " COUNT(*) AS CNT ";
        }
        $Sql .= " FROM is_pedido t1
                    INNER JOIN is_pessoa t2 ON t1.id_pessoa = t2.numreg
                    INNER JOIN is_transportadora t4 ON t1.id_transportadora = t4.numreg
                    INNER JOIN is_destino_mercadoria t5 ON t1.id_destino_mercadoria = t5.numreg
                    INNER JOIN is_pessoa_endereco t6 ON t1.id_endereco_entrega = t6.numreg
                    INNER JOIN is_cfop t7 ON t1.id_cfop = t7.numreg
                    INNER JOIN is_estabelecimento t8 ON t1.id_estabelecimento = t8.numreg
                    LEFT JOIN is_cond_pagto t3 ON t1.id_cond_pagto = t3.numreg
                    LEFT JOIN is_tab_preco t9 ON t1.id_tab_preco = t9.numreg
                    LEFT JOIN is_canal_venda t10 ON t1.id_canal_venda = t10.numreg
                WHERE
                    t1.id_situacao_pedido = 1
                AND
                    t1.sn_digitacao_completa = 1
                AND
                    t1.sn_em_aprovacao_comercial = 0
                AND
                    t1.sn_importado_erp = 0
                AND
                    t1.sn_exportado_erp = 0";
        if($this->VendaParametro->getSnExportaNaDataEntrega()){ /* Se considera a data de entrega para exportação */
            $Sql .= " AND t1.dt_entrega_desejada <= '".$this->DataBaseExportacao."' ";
        }

        return $Sql;
    }

    public function getQuantidadePendente(){
        $SqlCountPedidos = $this->getSqlPedidos(true);
        $QryCountPedidos = query($SqlCountPedidos);
        $ArCountPedidos = farray($QryCountPedidos);
        return $ArCountPedidos['CNT'];
    }

    public function CarregaPedidosBD(){
        /* ---------------------------------------------------------------------
         * LINHA 01 - Cabeçalho
         *
         *
         * t1 = is_pedido
         * t2 = is_pessoa
         * t3 = is_cond_pagto
         * t4 = is_transportadora
         * t5 = is_destino_mercadoria
         */
        $ArrayPedidosNaoExportar = array(); /* Armazena os numreg de pedidos que não devem ser exportados */
        /* Se recalcula as cotações no momento da exportação */
        if($this->VendaParametro->getSnAtualizaCotacaoExportacao()){
            $SqlPedidos = $this->getSqlPedidos();
            $QryPedidos = query($SqlPedidos);
            while($ArPedido = farray($QryPedidos)){
                $Pedido = new Pedido(2,$ArPedido['numreg']);
                $StatusAtualizacaoCotacao = $Pedido->AtualizaCotacaoBD();
                if(!$StatusAtualizacaoCotacao){
                    $ArrayPedidosNaoExportar[$ArPedido['numreg']] = NULL;
                }
            }
        }
        $SqlPedidos = $this->getSqlPedidos();
        $QryPedidos = query($SqlPedidos);
        while($ArPedido = farray($QryPedidos)){
            /* Verificando se o pedido está na lista dos que não devem ser epxortados */
            if(array_key_exists($ArPedido['numreg'], $ArrayPedidosNaoExportar)){
                continue;
            }
            if($ArPedido['sn_antecipa_entrega'] == '1'){
                $DtMinAtendimentoPed = '';
                $DtMaxAtendimentoPed = '';
            }
            else{
                $DtMinAtendimentoPed = $ArPedido['dt_entrega_desejada'];
                $DtMaxAtendimentoPed = '2099-12-31';
            }

            /*
             * Aplicando tratamento para linha 01
             */
            $this->NovaLinha = new GeraLinhaTxt(611);
            $this->PreencheValor('01','1','01');
            $this->PreencheValor('01','2', $ArPedido['fantasia_apelido']);
            $this->PreencheValor('01','3', $ArPedido['id_pedido_cliente']);
            $this->PreencheValor('01','4', $ArPedido['id_pessoa_erp']);
            $this->PreencheValor('01','5', $ArPedido['cnpj_cpf']);
            $this->PreencheValor('01','6', $ArPedido['id_pedido_representante']);
            $this->PreencheValor('01','7', $ArPedido['dt_pedido']);
            $this->PreencheValor('01','8', $DtMinAtendimentoPed);
            $this->PreencheValor('01','9', $DtMaxAtendimentoPed);
            $this->PreencheValor('01','11',$ArPedido['id_cond_pagto_erp']);
            $this->PreencheValor('01','54',$ArPedido['id_cond_pagto_erp']);
            $this->PreencheValor('01','29',$ArPedido['id_cfop_erp']);
            if($this->VendaParametro->getSnExportaPrecoInformado() || $ArPedido['id_tp_preco'] == '1'){
                $this->PreencheValor('01','15','1');
            }
            else{
                $this->PreencheValor('01','12',$ArPedido['id_tab_preco_erp']);
                $this->PreencheValor('01','15',$ArPedido['id_tp_preco']);
                /* Enviando os descontos */
                $this->PreencheValor('01','17',$ArPedido['pct_desconto_pessoa']);
                $this->PreencheValor('01','44',$ArPedido['pct_desconto_tab_preco']);
                $this->PreencheValor('01','45',number_format_min($ArPedido['pct_desconto_informado'],0,',',''));
            }
            //$this->PreencheValor('01','16','1'); // Fixo 1 - R$ Real | Se este valor é preenchido, é obrigatório estar cadastrada a cotação da moeda no ERP
            $this->PreencheValor('01','20',$ArPedido['id_destino_mercadoria_erp']);
            $this->PreencheValor('01','22',$ArPedido['nome_abrev_transportadora']);

            $QryRepresentantePrincipal = query("SELECT id_representante FROM is_pedido_representante WHERE id_pedido = ".$ArPedido['numreg']." AND sn_representante_principal = 1");
            $ArRepresentantePrincipal = farray($QryRepresentantePrincipal);
            if(!empty($ArRepresentantePrincipal['id_representante'])){
                $RepresentantePrincipal = new Usuario($ArRepresentantePrincipal['id_representante']);
                $NomeAbreviadoRepresentantePrincipal = $RepresentantePrincipal->getNomeAbreviado();
            }
            else{
                $NomeAbreviadoRepresentantePrincipal = '';
            }

            $this->PreencheValor('01','23',$NomeAbreviadoRepresentantePrincipal);

            $CidadeCIF = ($ArPedido['id_tp_frete'] == 1)?$ArPedido['cidade']:'';
            $this->PreencheValor('01','24',$CidadeCIF);

            $this->PreencheValor('01','26',$ArPedido['id_endereco_erp']);
            $this->PreencheValor('01','31','1'); // Fixo 1 - Modalidade

            $this->PreencheValor('01','35', $ArPedido['id_estabelecimento_erp']);
            $this->PreencheValor('01','36', $ArPedido['dt_entrega']);

            $this->PreencheValor('01','37','01'); // Fixo 01 - Espécie do Pedido
            $this->PreencheValor('01','41', $ArPedido['id_canal_venda_erp']);

            if($ArPedido['id_pessoa_triangular'] != ''){
                $sqlTriangular = "SELECT fantasia_apelido FROM is_pessoa WHERE numreg = '".$ArPedido['id_pessoa_triangular']."'";
                $QryTriangular = query($sqlTriangular);
                $ArTriangular = farray($QryTriangular);

                $this->PreencheValor('01','52', $ArTriangular['fantasia_apelido']);
            }

            /* Tratando informações fixas no cadastro de parâmetros */
            $QryCamposFixos = query("SELECT numero_campo,valor_padrao FROM is_param_campo_fixo_txt_pedido WHERE sn_ativo = 1 AND numero_linha = 1");
            while($ArCamposFixos = farray($QryCamposFixos)){
                $this->PreencheValor('01', $ArCamposFixos['numero_campo'], $ArCamposFixos['valor_padrao']);
            }

            $this->PreencheValorCustom('01',$ArPedido);

            $this->Txt .= $this->NovaLinha->CriaTxt().chr(10);


            /* -----------------------------------------------------------------
             * Observações
             */
            $IdLinhaObs = '0'.$this->VendaParametro->getIntTxtErpDatasulObs();
            $this->NovaLinha = new GeraLinhaTxt(2002);
            $this->PreencheValor('0'.$IdLinhaObs,'1','0'.$IdLinhaObs);
            $this->PreencheValor('0'.$IdLinhaObs,'2', $ArPedido['obs']);

            $this->PreencheValorCustom($IdLinhaObs,$ArPedido);

            $this->Txt .= $this->NovaLinha->CriaTxt().chr(10);

            /* -----------------------------------------------------------------
             * Observações NF
             */
            $IdLinhaObsNF = '0'.$this->VendaParametro->getIntTxtErpDatasulObsNF();
            $this->NovaLinha = new GeraLinhaTxt(2002);
            $this->PreencheValor('0'.$IdLinhaObsNF,'1','0'.$IdLinhaObsNF);
            $this->PreencheValor('0'.$IdLinhaObsNF,'2', $ArPedido['obs_nf']);

            $this->PreencheValorCustom($IdLinhaObsNF,$ArPedido);

            $this->Txt .= $this->NovaLinha->CriaTxt().chr(10);

            /* -----------------------------------------------------------------
             * LINHA 07 - Itens
             *
             * t1 = is_pedido_item
             * t2 = is_produto
             */
            $SqlItensPedido = "   SELECT
                                            t1.numreg,
                                            t1.id_sequencia,
                                            t1.qtde_base_calculo,
                                            t1.obs,
                                            t1.vl_unitario_convertido,
                                            t1.id_referencia,
                                            t1.dt_entrega,
                                            t1.pct_desconto_tab_preco,
                                            t1.sn_possui_st,
                                            t1.total_unidades,
                                            t2.id_produto_erp,
                                            t3.id_cfop_erp,
                                            t4.id_unid_medida_erp
                                        FROM is_pedido_item t1
                                            INNER JOIN is_produto t2 ON t1.id_produto = t2.numreg
                                            INNER JOIN is_cfop t3 ON t1.id_cfop = t3.numreg
                                            LEFT JOIN is_unid_medida t4 ON t1.id_unid_medida = t4.numreg
                                        WHERE
                                            t1.id_pedido = ".$ArPedido['numreg'];
            $QryItensPedido = query($SqlItensPedido);
            while($ArItemPedido = farray($QryItensPedido)){
                $this->NovaLinha = new GeraLinhaTxt(2419);
                $this->PreencheValor('07','1','07');
                $this->PreencheValor('01','2',$ArPedido['fantasia_apelido']);
                $this->PreencheValor('01','3',$ArPedido['id_pedido_cliente']);
                $this->PreencheValor('07','4',$ArItemPedido['id_sequencia']);
                $this->PreencheValor('07','5',$ArItemPedido['id_produto_erp']);
                $this->PreencheValor('07','8',$ArPedido['dt_entrega_desejada']);
                $this->PreencheValor('07','9',$ArItemPedido['qtde_base_calculo']);
                if($this->VendaParametro->getSnExportaPrecoInformado() || $ArPedido['id_tp_preco'] == '1'){
                    $this->PreencheValor('07','10',$ArItemPedido['vl_unitario_convertido']);
                }
                else{
                    $this->PreencheValor('07','21',$ArPedido['id_tab_preco_erp']);
                    $this->PreencheValor('07','22',$ArItemPedido['pct_desconto_tab_preco']);
                    $ArrayDescontos = array();
                    $QryDescontos = query("SELECT pct_desconto FROM is_pedido_item_desconto WHERE id_pedido_item = ".$ArItemPedido['numreg']." ORDER BY id_campo_desconto ASC");
                    while($ArDescontos = farray($QryDescontos)){
                        $ArrayDescontos[] = number_format_min($ArDescontos['pct_desconto'],0,',','');
                    }
                    if(count($ArrayDescontos) > 0){
                        $this->PreencheValor('07','23',implode('+',$ArrayDescontos));
                    }
                }
                $this->PreencheValor('07','13',$ArItemPedido['id_referencia']);
                $this->PreencheValor('07','14',$ArItemPedido['id_cfop_erp']);
                $this->PreencheValor('07','16',$ArItemPedido['sn_possui_st']);
                $this->PreencheValor('07','18',$ArItemPedido['dt_entrega']);
                $this->PreencheValor('07','37',$ArItemPedido['id_unid_medida_erp']);
                $this->PreencheValor('07','42',$ArItemPedido['obs']);

                /* Tratando informações fixas no cadastro de parâmetros */
                $QryCamposFixos = query("SELECT numero_campo,valor_padrao FROM is_param_campo_fixo_txt_pedido WHERE sn_ativo = 1 AND numero_linha = 7");
                while($ArCamposFixos = farray($QryCamposFixos)){
                    $this->PreencheValor('07', $ArCamposFixos['numero_campo'], $ArCamposFixos['valor_padrao']);
                }

                $this->PreencheValorCustomItem($ArPedido,$ArItemPedido);

                $this->Txt .= $this->NovaLinha->CriaTxt().chr(10);
            }

            /* -----------------------------------------------------------------
             * LINHA 09 - Representantes
             */
            $SqlRepresentantesPedido = "   SELECT
                                            t1.*,
                                            t2.id_representante
                                        FROM is_pedido_representante t1
                                            INNER JOIN is_usuario t2 ON t1.id_representante = t2.numreg
                                        WHERE
                                            t1.id_pedido = ".$ArPedido['numreg'];
            $QryRepresentantesPedido = query($SqlRepresentantesPedido);
            while($ArRepresentantePedido = farray($QryRepresentantesPedido)){
                $this->NovaLinha = new GeraLinhaTxt(30);
                $this->PreencheValor('09','1','09');
                $this->PreencheValor('09','2', $ArPedido['id_pedido_cliente']);
                $this->PreencheValor('09','3', $ArRepresentantePedido['id_representante']);
                $this->PreencheValor('09','4', $ArRepresentantePedido['pct_comissao']);
                $this->PreencheValor('09','6', $ArRepresentantePedido['sn_representante_principal']);

                /* Tratando informações fixas no cadastro de parâmetros */
                $QryCamposFixos = query("SELECT numero_campo,valor_padrao FROM is_param_campo_fixo_txt_pedido WHERE sn_ativo = 1 AND numero_linha = 9");
                while($ArCamposFixos = farray($QryCamposFixos)){
                    $this->PreencheValor('09', $ArCamposFixos['numero_campo'], $ArCamposFixos['valor_padrao']);
                }

                $this->PreencheValorCustomRepresentante($ArPedido,$ArRepresentantePedido);

                $this->Txt .= $this->NovaLinha->CriaTxt().chr(10);
            }

            /*
             * Atualizando o pedido como exportado
             */
            $ArSqlUpdatePedido  = array('numreg' => $ArPedido['numreg'], 'sn_exportado_erp' => 1, 'dt_hr_exportado_erp' => date("Y-m-d H:i:s"));
            $SqlUpdatePedido    = AutoExecuteSql(getParametrosGerais('TipoBancoDados'),'is_pedido',$ArSqlUpdatePedido,'UPDATE',array('numreg'));
            $QryUpdatePedido    = query($SqlUpdatePedido);
        }
    }

    public function getTxt(){
        return $this->Txt;
    }
}
?>