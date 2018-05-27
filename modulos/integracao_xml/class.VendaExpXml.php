<?php

class VendaExpXml {

    public $CaminhoArquivo = '';
    private $Txt;
    private $MapaCampos = array();
    private $NovaLinha;
    private $VendaParametro;
    private $DataBaseExportacao;
    private $DataHoje;
    private $QuantidadeRegistroExportados = 0;

    public function getQuantidadeRegistroExportados() {
        return $this->QuantidadeRegistroExportados;
    }

    public function __construct() {
        $this->VendaParametro = new VendaParametro();
        $this->DataHoje = date('Y-m-d');

        /* Definindo a data base para exportação dos pedidos */
        if ($this->VendaParametro->getSnExportaNaDataEntrega()) {

            $DiaSemanaHoje = date('w', strtotime($this->DataHoje));

            if ($DiaSemanaHoje == '5') { /* Se hoje for sexta-feira */
                $this->DataBaseExportacao = date('Y-m-d', strtotime($this->DataHoje . ' +3 days')); /* Alterando a data para segunda-feira */
            } elseif ($DiaSemanaHoje == '6') { /* Se hoje for sábado */
                $this->DataBaseExportacao = date('Y-m-d', strtotime($this->DataHoje . ' +2 days')); /* Alterando a data para segunda-feira */
            } else { /* Se for qualquer outro dia da semana */
                $this->DataBaseExportacao = date('Y-m-d', strtotime($this->DataHoje . ' +1 day')); /* Alterando a data para segunda-feira */
            }
        }

        /* Definindo o posicionamento dos campos */
        $this->MapaCampos['01'][1] = array(1, 2, 0);    //1 - Código do Registro - 01
        $this->MapaCampos['01'][2] = array(3, 12, 2);   //2 - Nome Abreviado do Cliente
        $this->MapaCampos['01'][3] = array(15, 12, 0);  //3 - Número do Pedido do Cliente
        $this->MapaCampos['01'][4] = array(27, 9, 0);   //4 - Código do Emitente
        $this->MapaCampos['01'][5] = array(36, 19, 0);  //5 - CGC ou CIC do Cliente
        $this->MapaCampos['01'][6] = array(55, 12, 0);  //6 - Número do Pedido do Representante
        $this->MapaCampos['01'][7] = array(67, 8, 1);   //7 - Data em que o Pedido foi Emitido
        $this->MapaCampos['01'][8] = array(75, 8, 0);   //8 - Data Mínima para Atendimento do Pedido
        $this->MapaCampos['01'][9] = array(83, 8, 0);   //9 - Data Limite para Atendimento do Pedido
        $this->MapaCampos['01'][10] = array(91, 2, 0);  //10 - Tipo Pedido
        $this->MapaCampos['01'][11] = array(93, 3, 0); //11 - Código da Condição de Pagamento
        $this->MapaCampos['01'][12] = array(96, 8, 0); //12 - Tabela de Preços
        $this->MapaCampos['01'][13] = array(104, 3, 0); //13 - Número da Tabela de Financiamento
        $this->MapaCampos['01'][14] = array(107, 2, 0); //14 - Número do Índice de Financiamento
        $this->MapaCampos['01'][15] = array(109, 2, 0); //15 - Tipo de Preço
        $this->MapaCampos['01'][16] = array(111, 2, 0); //16 - Código da Moeda
        $this->MapaCampos['01'][17] = array(113, 5, 0); //17 - Desconto-1
        $this->MapaCampos['01'][18] = array(118, 5, 0); //18 - Desconto-2
        $this->MapaCampos['01'][19] = array(123, 2, 0); //19 - Prioridade
        $this->MapaCampos['01'][20] = array(125, 1, 0); //20 - Destino da Mercadoria
        $this->MapaCampos['01'][21] = array(126, 12, 0); //21 - Rota
        $this->MapaCampos['01'][22] = array(138, 12, 2); //22 - Transportador
        $this->MapaCampos['01'][23] = array(150, 12, 0); //23 - Representante
        $this->MapaCampos['01'][24] = array(162, 25, 0); //24 - Cidade CIF
        $this->MapaCampos['01'][25] = array(187, 3, 0); //25 - Mensagem
        $this->MapaCampos['01'][26] = array(190, 12, 2); //26 - Código Entrega
        $this->MapaCampos['01'][27] = array(202, 25, 0); //27 - Contato
        $this->MapaCampos['01'][28] = array(227, 154, 0); //28 - Brancos
        $this->MapaCampos['01'][29] = array(381, 6, 0); //29 - Natureza de Operação
        $this->MapaCampos['01'][30] = array(387, 5, 0); //30 - Código do Portador
        $this->MapaCampos['01'][31] = array(392, 1, 0); //31 - Modalidade
        $this->MapaCampos['01'][32] = array(393, 12, 0); //32 - Nome Transportadora Redespacho
        $this->MapaCampos['01'][33] = array(405, 50, 0); //33 - Brancos
        $this->MapaCampos['01'][34] = array(455, 4, 0); //34 - Brancos
        $this->MapaCampos['01'][35] = array(459, 3, 0); //35 - Código do Estabelecimento
        $this->MapaCampos['01'][36] = array(462, 8, 0); //36 - Data de Entrega Prevista
        $this->MapaCampos['01'][37] = array(470, 2, 0); //37 - Espécie de Pedido
        $this->MapaCampos['01'][38] = array(472, 3, 0); //38 - Estabelecimento Atendimento
        $this->MapaCampos['01'][39] = array(475, 3, 0); //39 - Estabelecimento Central
        $this->MapaCampos['01'][40] = array(478, 3, 0); //40 - Estabelecimento Destino
        $this->MapaCampos['01'][41] = array(481, 3, 0); //41 - Canal de Venda
        $this->MapaCampos['01'][42] = array(484, 7, 0); //42 - Brancos
        $this->MapaCampos['01'][43] = array(491, 1, 0); //43 - Desconto Item/Cliente
        $this->MapaCampos['01'][44] = array(492, 5, 0); //44 - Perc Desc Tabela Preços
        $this->MapaCampos['01'][45] = array(497, 50, 0); //45 - Perc Desc Informado
        $this->MapaCampos['01'][46] = array(547, 7, 0);     //46 - Perc Desc Valor
        $this->MapaCampos['01'][47] = array(554, 2, 0);     //47 - Moeda Faturamento
        $this->MapaCampos['01'][48] = array(556, 1, 0);     //48 - Concede Bonificação
        $this->MapaCampos['01'][49] = array(557, 12, 0);    //49 - Número Pedido Origem
        $this->MapaCampos['01'][50] = array(569, 12, 0);    //50 - Número Pedido Bonificação
        $this->MapaCampos['01'][51] = array(581, 5, 0);     //51 - Código do Serviço de Frete
        $this->MapaCampos['01'][52] = array(586, 12, 0);    //52 - Nome Abreviado do Cliente Remessa Triangular

        $this->MapaCampos['02'][1] = array(1, 2, 0);    //1 - Código do Registro - 02
        $this->MapaCampos['02'][2] = array(3, 2000, 0);    // - Observações das Condições Especiais

        $this->MapaCampos['03'][1] = array(1, 2, 0);    //1 - Código do Registro - 03
        $this->MapaCampos['03'][2] = array(3, 2000, 0);    // - Condições Redespacho

        $this->MapaCampos['04'][1] = array(1, 2, 0);    //1 - Código do Registro - 04
        $this->MapaCampos['04'][2] = array(3, 2000, 0);    // - Observação do Pedido

        $this->MapaCampos['07'][1] = array(1, 2, 0); //1 - Código do Registro - 07
        $this->MapaCampos['07'][2] = array(3, 14, 2); //2 - Nome Abreviado do Cliente
        $this->MapaCampos['07'][3] = array(15, 26, 0); //3 - Pedido do Cliente
        $this->MapaCampos['07'][4] = array(27, 31, 0); //4 - Número da Sequência do Item do Pedido
        $this->MapaCampos['07'][5] = array(32, 47, 0); //5 - Item
        $this->MapaCampos['07'][6] = array(48, 55, 0); //6 - Ordem de Compra
        $this->MapaCampos['07'][7] = array(56, 57, 0); //7 - Parcela
        $this->MapaCampos['07'][8] = array(58, 65, 0); //8 - Data Entrega Original
        $this->MapaCampos['07'][9] = array(66, 76, 4); //9 - Quantidade Unidade Faturamento
        $this->MapaCampos['07'][10] = array(77, 90, 3); //10 - Preço Líquido
        $this->MapaCampos['07'][11] = array(91, 140, 0); //11 - Brancos
        $this->MapaCampos['07'][12] = array(141, 144, 0); //12 - Percentual Mínimo Faturamento Parcial
        $this->MapaCampos['07'][13] = array(145, 152, 2); //13 - Referência
        $this->MapaCampos['07'][14] = array(153, 158, 0); //14 - Natureza de Operação
        $this->MapaCampos['07'][15] = array(159, 164, 0); //15 - Percentual de Desconto de ICMS
        $this->MapaCampos['07'][16] = array(165, 165, 0); //16 - ICMS Retido na Fonte
        $this->MapaCampos['07'][17] = array(166, 166, 0); //17 - Unidade Faturamento
        $this->MapaCampos['07'][18] = array(167, 174, 1); //18 - Data de Entrega Prevista
        $this->MapaCampos['07'][19] = array(175, 176, 0); //19 - Indicador Componente Produto Configurado
        $this->MapaCampos['07'][20] = array(177, 188, 0); //20 - Código de entrega do Item
        $this->MapaCampos['07'][21] = array(189, 196, 0); //21 - Tabela de Preço
        $this->MapaCampos['07'][22] = array(197, 203, 0); //22 - Perc Desc Tabela Preços
        $this->MapaCampos['07'][23] = array(204, 253, 0); //23 - Perc Desc Informado
        $this->MapaCampos['07'][24] = array(254, 264, 0); //24 - Valor Desconto Informado
        $this->MapaCampos['07'][25] = array(265, 265, 0); //25 - Concede Bonificação Quantidade
        $this->MapaCampos['07'][26] = array(266, 270, 0); //26 - Perc Bonificação
        $this->MapaCampos['07'][27] = array(271, 275, 0); //27 - Perc Desc Período
        $this->MapaCampos['07'][28] = array(276, 280, 0); //28 - Perc Desc Prazo
        $this->MapaCampos['07'][29] = array(281, 291, 0); //29 - Valor Desconto Bonificação
        $this->MapaCampos['07'][30] = array(292, 302, 0); //30 - Quantidade Bonificação
        $this->MapaCampos['07'][31] = array(303, 307, 0); //31 - Sequência Bonificação
        $this->MapaCampos['07'][32] = array(308, 321, 0); //32 - Desconto [1]
        $this->MapaCampos['07'][33] = array(322, 335, 0); //33 - Desconto [2]
        $this->MapaCampos['07'][34] = array(336, 349, 0); //34 - Desconto [3]
        $this->MapaCampos['07'][35] = array(350, 363, 0); //35 - Desconto [4]
        $this->MapaCampos['07'][36] = array(364, 377, 0); //36 - Desconto [5]
        $this->MapaCampos['07'][37] = array(378, 379, 0); //37 - Unidade de Medida Faturamento / Débito Direto
        $this->MapaCampos['07'][38] = array(380, 396, 0); //38 - Conta de Aplicação
        $this->MapaCampos['07'][39] = array(397, 408, 0); //39 - Vl Custo Contábil
        $this->MapaCampos['07'][40] = array(409, 411, 0); //40 - Código da Unidade de Negócio
        $this->MapaCampos['07'][41] = array(412, 418, 0); //41 - Branco
        $this->MapaCampos['07'][42] = array(419, 2419, 0); //42 - Observações do Item

        $this->MapaCampos['09'][1] = array(1, 2, 0);    //1 - Código do Registro - 09 Representante (ped-repre)
        $this->MapaCampos['09'][2] = array(3, 12, 0);   //2 - Pedido do Cliente
        $this->MapaCampos['09'][3] = array(15, 5, 0);  //3 - Código Representante
        $this->MapaCampos['09'][4] = array(20, 5, 0);   //4 - Percentual de Comissão
        $this->MapaCampos['09'][5] = array(25, 5, 0);   //5 - Comissão Emissão
        $this->MapaCampos['09'][6] = array(30, 1, 0);   //6 - Representante Principal
    }

    public function PreencheValor($Linha, $Campo, $Valor) {
        if ($this->MapaCampos[$Linha][$Campo][2] == 1) {//Data
            $Valor = substr($Valor, 8, 2) . substr($Valor, 5, 2) . substr($Valor, 0, 4);
        } elseif ($this->MapaCampos[$Linha][$Campo][2] == 2) {//Remove Acentos
            $Valor = retiraAcentos($Valor);
        } elseif ($this->MapaCampos[$Linha][$Campo][2] == 3) {
            $Valor = number_format($Valor, 5, '.', '');
        } elseif ($this->MapaCampos[$Linha][$Campo][2] == 4) {//Qtde
            $Valor = number_format($Valor, 4, '', '');
        }
        $Valor = str_replace("\r\n", '', $Valor);
        $this->NovaLinha .= '<' . $Campo . '>' . $Valor . '</' . $Campo . '>' . chr(13) . chr(10);
    }

    public function getSqlPedidos($Count=false) {
        $Sql = "SELECT ";

        if ($Count === false) {
            $Sql .= "   t1.*,
                        t2.id_pessoa_erp,
                        t2.fantasia_apelido,
                        t2.cnpj_cpf,
                        t3.id_cond_pagto_erp,
                        t4.id_transportadora_erp,
                        t4.nome_abrev_transportadora,
                        t5.id_destino_mercadoria_erp,
                        t6.id_endereco_erp,
                        t7.id_cfop_erp,
                        t8.id_estabelecimento_erp ";
        } else {
            $Sql .= " COUNT(*) AS CNT ";
        }
        $Sql .= " FROM is_pedido t1
                    INNER JOIN is_pessoa t2 ON t1.id_pessoa = t2.numreg
                    INNER JOIN is_cond_pagto t3 ON t1.id_cond_pagto = t3.numreg
                    INNER JOIN is_transportadora t4 ON t1.id_transportadora = t4.numreg
                    INNER JOIN is_destino_mercadoria t5 ON t1.id_destino_mercadoria = t5.numreg
                    INNER JOIN is_pessoa_endereco t6 ON t1.id_endereco_entrega = t6.numreg
                    INNER JOIN is_cfop t7 ON t1.id_cfop = t7.numreg
                    INNER JOIN is_estabelecimento t8 ON t1.id_estabelecimento = t8.numreg
                WHERE
                    t1.sn_digitacao_completa = 1
                AND
                    t1.sn_em_aprovacao_comercial = 0
                AND
                    t1.sn_importado_erp = 0
                AND
                    t1.sn_exportado_erp = 0";
        if ($this->VendaParametro->getSnExportaNaDataEntrega()) { /* Se considera a data de entrega para exportação */
            $Sql .= " AND t1.dt_entrega_desejada <= '" . $this->DataBaseExportacao . "' ";
        }

        return $Sql;
    }

    public function getQuantidadePendente() {
        $SqlCountPedidos = $this->getSqlPedidos(true);
        $QryCountPedidos = query($SqlCountPedidos);
        $ArCountPedidos = farray($QryCountPedidos);
        return $ArCountPedidos['CNT'];
    }

    public function CarregaPedidosBD() {
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
        if ($this->VendaParametro->getSnAtualizaCotacaoExportacao()) {
            $SqlPedidos = $this->getSqlPedidos();
            $QryPedidos = query($SqlPedidos);
            while ($ArPedido = farray($QryPedidos)) {
                $Pedido = new Pedido(2, $ArPedido['numreg']);
                $StatusAtualizacaoCotacao = $Pedido->AtualizaCotacaoBD();
                if (!$StatusAtualizacaoCotacao) {
                    $ArrayPedidosNaoExportar[$ArPedido['numreg']] = NULL;
                }
            }
        }
        $SqlPedidos = $this->getSqlPedidos();
        $QryPedidos = query($SqlPedidos);
        while ($ArPedido = farray($QryPedidos)) {
            /* Verificando se o pedido está na lista dos que não devem ser exportados */
            if (array_key_exists($ArPedido['numreg'], $ArrayPedidosNaoExportar)) {
                continue;
            }
            /*
             * Cabecalho
             */
            $this->NovaLinha = "<?xml version='1.0' encoding='ISO-8859-1'?>" . chr(13) . chr(10);
            $this->NovaLinha .= "<root>" . chr(13) . chr(10);
            $this->NovaLinha .= "<data_criacao>" . date("Y-m-d") . " " . date("H:i:s") . ".0</data_criacao>" . chr(13) . chr(10);
            $this->NovaLinha .= "<pedido>" . chr(13) . chr(10);
            $this->NovaLinha .= "<cabecalho>" . chr(13) . chr(10);
            $this->PreencheValor('01', 'id_pedido', $ArPedido['numreg']);
            $this->PreencheValor('01', 'id_estabelecimento', $ArPedido['id_estabelecimento_erp']);
            $this->PreencheValor('01', 'id_pedido_erp', $ArPedido['id_pedido_erp']);
            $this->PreencheValor('01', 'id_pedido_cliente', $ArPedido['id_pedido_cliente']);
            $this->PreencheValor('01', 'id_pedido_representante', $ArPedido['id_pedido_representante']);
            $this->PreencheValor('01', 'id_pessoa', $ArPedido['id_pessoa_erp']);
            $this->PreencheValor('01', 'id_tp_pedido', $ArPedido['id_tp_pedido']);
            $this->PreencheValor('01', 'id_situacao_pedido', $ArPedido['id_situacao_pedido']);
            $this->PreencheValor('01', 'id_cond_pagto', $ArPedido['id_cond_pagto_erp']);
            $this->PreencheValor('01', 'id_transportadora', $ArPedido['nome_abrev_transportadora']);
            $this->PreencheValor('01', 'id_tp_frete', $ArPedido['id_tp_frete']);
            $this->PreencheValor('01', 'dt_entrega', $ArPedido['dt_entrega']);
            $this->PreencheValor('01', 'dt_entrega_desejada', $ArPedido['dt_entrega_desejada']);
            $this->PreencheValor('01', 'id_usuario_cad', $ArPedido['id_usuario_cad']);
            $this->PreencheValor('01', 'id_origem', $ArPedido['id_origem']);
            $this->PreencheValor('01', 'sn_faturamento_parcial', $ArPedido['sn_faturamento_parcial']);
            $this->PreencheValor('01', 'sn_antecipa_entrega', $ArPedido['sn_antecipa_entrega']);
            $this->PreencheValor('01', 'vl_total_desconto', $ArPedido['vl_total_desconto']);
            $this->PreencheValor('01', 'id_destino_mercadoria', $ArPedido['id_destino_mercadoria_erp']);
            $this->PreencheValor('01', 'id_tp_preco', '1');
            $this->PreencheValor('01', 'dt_pedido', $ArPedido['dt_pedido']);
            $this->PreencheValor('01', 'dt_cadastro', $ArPedido['dt_cadastro']);
            $this->PreencheValor('01', 'dt_cancelamento', $ArPedido['dt_cancelamento']);
            $this->PreencheValor('01', 'id_usuario_cancelamento', $ArPedido['id_usuario_cancelamento']);
            $this->PreencheValor('01', 'id_motivo_cancelamento', $ArPedido['id_motivo_cancelamento']);
            $this->PreencheValor('01', 'dt_minima_faturamento', $ArPedido['dt_minima_faturamento']);
            $this->PreencheValor('01', 'dt_limite_faturamento', $ArPedido['dt_limite_faturamento']);
            $this->PreencheValor('01', 'id_cfop', $ArPedido['id_cfop_erp']);
            $this->PreencheValor('01', 'id_tab_preco', $ArPedido['id_tab_preco']);
            $this->PreencheValor('01', 'id_endereco_entrega', $ArPedido['id_endereco_erp']);
            $this->PreencheValor('01', 'obs', $ArPedido['obs']);
            $this->PreencheValor('01', 'obs_nf', $ArPedido['obs_nf']);
            $this->PreencheValor('01', 'obs_logistica', '');
            $this->PreencheValor('01', 'vl_total_bruto', $ArPedido['vl_total_bruto']);
            $this->PreencheValor('01', 'vl_total_liquido', $ArPedido['vl_total_liquido']);
            $this->PreencheValor('01', 'vl_total_ipi', $ArPedido['vl_total_ipi']);
            $this->PreencheValor('01', 'vl_total_st', $ArPedido['vl_total_st']);
            $this->PreencheValor('01', 'vl_total_icms', $ArPedido['vl_total_icms']);
            $this->PreencheValor('01', 'vl_total_frete', $ArPedido['vl_total_frete']);
            $this->PreencheValor('01', 'sn_avaliado_credito', $ArPedido['sn_avaliado_credito']);
            $this->PreencheValor('01', 'id_usuario_avaliador_credito', $ArPedido['id_usuario_avaliador_credito']);
            $this->PreencheValor('01', 'dt_avaliacao_credito', $ArPedido['dt_avaliacao_credito']);
            $this->PreencheValor('01', 'sn_aprovado_credito', $ArPedido['sn_aprovado_credito']);
            $this->PreencheValor('01', 'sn_aprovacao_parcial', $ArPedido['sn_aprovacao_parcial']);
            $this->PreencheValor('01', 'id_pedido_bonificacao', $ArPedido['id_pedido_bonificacao']);
            $this->PreencheValor('01', 'id_grupo_tab_preco', $ArPedido['id_grupo_tab_preco']);
            $this->PreencheValor('01', 'cond_pagto_especial', $ArPedido['cond_pagto_especial']);
            $this->PreencheValor('01', 'id_orcamento', $ArPedido['id_orcamento']);
            $this->PreencheValor('01', 'id_contato', $ArPedido['id_contato']);
            $this->PreencheValor('01', 'vl_total_bonificacao', $ArPedido['vl_total_bonificacao']);
            $this->PreencheValor('01', 'pct_comissao', $ArPedido['pct_comissao']);
            $this->PreencheValor('01', 'vl_total_comissao', $ArPedido['vl_total_comissao']);
            $this->PreencheValor('01', 'tp_pedido', $ArPedido['tp_pedido']);
            $this->NovaLinha .= "</cabecalho>" . chr(13) . chr(10);


            /* -----------------------------------------------------------------
             * LINHA 09 - Representantes
             */
            $this->NovaLinha .= "<representantes>" . chr(13) . chr(10);
            $SqlRepresentantesPedido = "   SELECT
                                            t1.*,
                                            t2.id_representante
                                        FROM is_pedido_representante t1
                                            INNER JOIN is_usuario t2 ON t1.id_representante = t2.numreg
                                        WHERE
                                            t1.id_pedido = " . $ArPedido['numreg'];
            $QryRepresentantesPedido = query($SqlRepresentantesPedido);
            while ($ArRepresentantePedido = farray($QryRepresentantesPedido)) {
                $this->NovaLinha .= "<representante>" . chr(13) . chr(10);
                $this->PreencheValor('09', 'id_representante', $ArRepresentantePedido['id_representante']);
                $this->PreencheValor('09', 'pct_comissao', $ArRepresentantePedido['pct_comis']);
                $this->PreencheValor('09', 'vl_comissao', '');
                $this->PreencheValor('09', 'sn_representante_principal', $ArRepresentantePedido['sn_representante_principal']);
                $this->PreencheValor('09', 'id_tp_participacao', '');
                $this->NovaLinha .= "</representante>" . chr(13) . chr(10);
            }
            $this->NovaLinha .= "</representantes>" . chr(13) . chr(10);

            /* -----------------------------------------------------------------
             * LINHA 07 - Itens
             *
             * t1 = is_pedido_item
             * t2 = is_produto
             */
            $this->NovaLinha .= "<itens>" . chr(13) . chr(10);

            $SqlItensPedido = "   SELECT
                                            t1.*,
                                            t2.id_produto_erp,
                                            t3.id_cfop_erp
                                        FROM is_pedido_item t1
                                            INNER JOIN is_produto t2 ON t1.id_produto = t2.numreg
                                            INNER JOIN is_cfop t3 ON t1.id_cfop = t3.numreg
                                        WHERE
                                            t1.id_pedido = " . $ArPedido['numreg'];
            $QryItensPedido = query($SqlItensPedido);
            while ($ArItemPedido = farray($QryItensPedido)) {
                $this->NovaLinha .= "<item>" . chr(13) . chr(10);
                $this->PreencheValor('07', 'id_sequencia', $ArItemPedido['id_sequencia']);
                $this->PreencheValor('07', 'id_produto', $ArItemPedido['id_produto_erp']);
                $this->PreencheValor('07', 'id_moeda', $ArItemPedido['id_moeda']);
                $this->PreencheValor('07', 'id_unid_medida', $ArItemPedido['id_unid_medida']);
                $this->PreencheValor('07', 'qtde', $ArItemPedido['total_unidades']);
                $this->PreencheValor('07', 'qtde_por_unid_medida', '1');
                $this->PreencheValor('07', 'total_unidades', $ArItemPedido['total_unidades']);
                $this->PreencheValor('07', 'qtde_faturada', $ArItemPedido['qtde_faturada']);
                $this->PreencheValor('07', 'id_situacao_item', $ArItemPedido['id_situacao_item']);
                $this->PreencheValor('07', 'pct_desconto_base', $ArItemPedido['pct_desconto_base']);
                $this->PreencheValor('07', 'pct_desconto_total', $ArItemPedido['pct_desconto_total']);
                $this->PreencheValor('07', 'pct_aliquota_ipi', $ArItemPedido['pct_aliquota_ipi ']);
                $this->PreencheValor('07', 'pct_aliquota_iva', $ArItemPedido['pct_aliquota_iva ']);
                $this->PreencheValor('07', 'vl_unitario_base_calculo', $ArItemPedido['vl_unitario_base_calculo']);
                $this->PreencheValor('07', 'vl_unitario_tabela_original', $ArItemPedido['vl_unitario_tabela_original ']);
                $this->PreencheValor('07', 'vl_cotacao', $ArItemPedido['vl_cotacao']);
                $this->PreencheValor('07', 'vl_unitario_com_desconto_base', $ArItemPedido['vl_unitario_com_desconto_base']);
                $this->PreencheValor('07', 'vl_unitario_com_descontos', $ArItemPedido['vl_unitario_com_descontos']);
                $this->PreencheValor('07', 'vl_unitario_ipi ', $ArItemPedido['vl_unitario_ipi ']);
                $this->PreencheValor('07', 'vl_unitario_icms', $ArItemPedido['vl_unitario_icms']);
                $this->PreencheValor('07', 'vl_unitario_convertido', $ArItemPedido['vl_unitario_convertido']);
                $this->PreencheValor('07', 'vl_unitario_st ', $ArItemPedido['vl_unitario_st ']);
                $this->PreencheValor('07', 'vl_total_bruto_base_calculo', $ArItemPedido['vl_total_bruto_base_calculo']);
                $this->PreencheValor('07', 'vl_total_liquido_base_calculo', $ArItemPedido['vl_total_liquido_base_calculo']);
                $this->PreencheValor('07', 'vl_total_bruto', $ArItemPedido['vl_total_bruto']);
                $this->PreencheValor('07', 'vl_total_liquido', $ArItemPedido['vl_total_liquido']);
                $this->PreencheValor('07', 'vl_total_ipi ', $ArItemPedido['vl_total_ipi ']);
                $this->PreencheValor('07', 'vl_total_st ', $ArItemPedido['vl_total_st ']);
                $this->PreencheValor('07', 'vl_total_bonificacao', $ArItemPedido['vl_total_bonificacao']);
                $this->PreencheValor('07', 'peso_bruto', $ArItemPedido['peso_bruto']);
                $this->PreencheValor('07', 'peso_liquido ', $ArItemPedido['peso_liquido ']);
                $this->PreencheValor('07', 'dt_cadastro', $ArItemPedido['dt_cadastro']);
                $this->PreencheValor('07', 'id_usuario_cad ', $ArItemPedido['id_usuario_cad ']);
                $this->PreencheValor('07', 'dt_entrega', $ArItemPedido['dt_entrega']);
                $this->PreencheValor('07', 'dt_minima_faturamento', $ArItemPedido['dt_minima_faturamento']);
                $this->PreencheValor('07', 'dt_cancelamento', $ArItemPedido['dt_cancelamento']);
                $this->PreencheValor('07', 'id_usuario_cancelamento', $ArItemPedido['id_usuario_cancelamento']);
                $this->PreencheValor('07', 'id_motivo_cancelamento', $ArItemPedido['id_motivo_cancelamento']);
                $this->PreencheValor('07', 'id_cfop', $ArItemPedido['id_cfop_erp']);
                $this->PreencheValor('07', 'id_referencia', $ArItemPedido['id_referencia']);
                $this->PreencheValor('07', 'obs', $ArItemPedido['obs']);
                $this->PreencheValor('07', 'pct_comissao', $ArItemPedido['pct_comissao']);
                $this->PreencheValor('07', 'vl_total_comissao', $ArItemPedido['vl_total_comissao']);
                $this->PreencheValor('07', 'id_tab_preco ', $ArItemPedido['id_tab_preco ']);
                $this->PreencheValor('07', 'sn_cotacao_fixa', $ArItemPedido['sn_cotacao_fixa']);
                $this->PreencheValor('07', 'id_kit', $ArItemPedido['id_kit']);
                $this->PreencheValor('07', 'vl_total_comissao', $ArItemPedido['vl_total_comissao']);
                // Descontos do Item
                $this->NovaLinha .= "<descontos>" . chr(13) . chr(10);
                $this->NovaLinha .= "<desconto>" . chr(13) . chr(10);
                $this->NovaLinha .= "<id_campo_desconto></id_campo_desconto>" . chr(13) . chr(10);
                $this->NovaLinha .= "<pct_desconto></pct_desconto>" . chr(13) . chr(10);
                $this->NovaLinha .= "</desconto>" . chr(13) . chr(10);
                $this->NovaLinha .= "</descontos>" . chr(13) . chr(10);

                // Descontos do Item
                $this->NovaLinha .= "<programacoes_entregas>" . chr(13) . chr(10);
                $this->NovaLinha .= "<programacao_entrega>" . chr(13) . chr(10);
                $this->NovaLinha .= "<id_endereco_entrega_erp></id_endereco_entrega_erp>" . chr(13) . chr(10);
                $this->NovaLinha .= "<qtde></qtde>" . chr(13) . chr(10);
                $this->NovaLinha .= "<dt_entrega></dt_entrega>" . chr(13) . chr(10);
                $this->NovaLinha .= "</programacao_entrega>" . chr(13) . chr(10);
                $this->NovaLinha .= "</programacoes_entregas>" . chr(13) . chr(10);

                $this->NovaLinha .= "</item>" . chr(13) . chr(10);
            }


            $this->NovaLinha .= "</itens>" . chr(13) . chr(10);
            include('VendaExpXmlXCustom.php');
            $this->NovaLinha .= "</pedido>" . chr(13) . chr(10);
            $this->NovaLinha .= "</root>" . chr(13) . chr(10);

            $this->Txt .= $this->NovaLinha . "\r\n";
            $this->QuantidadeRegistroExportados++;


            /*
             * Atualizando o pedido como exportado
             */
            $ArSqlUpdatePedido = array('numreg' => $ArPedido['numreg'], 'sn_exportado_erp' => 1, 'dt_hr_exportado_erp' => date("Y-m-d H:i:s"));
            $SqlUpdatePedido = AutoExecuteSql(getParametrosGerais('TipoBancoDados'), 'is_pedido', $ArSqlUpdatePedido, 'UPDATE', array('numreg'));
            $QryUpdatePedido = query($SqlUpdatePedido);
            
            if (file_exists($CaminhoArquivo)) {
                $MaxId = uB::getProximoMaxId(2);
            }
            $NomeArquivo = 'PEDIDO_'.$ArPedido['numreg'] . '_' . date("dmYHis") . ".xml";
            
            $Arquivo = fopen($this->CaminhoArquivo . $NomeArquivo, "w+");
            fwrite($Arquivo, $this->Txt);
            fclose($Arquivo);
            $this->Txt = '';
            $this->NovaLinha = '';
        }
    }

    public function getTxt() {
        return $this->Txt;
    }

}

?>