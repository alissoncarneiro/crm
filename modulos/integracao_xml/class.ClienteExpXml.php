<?php

class ClienteExpXml{
    public  $DiretorioArquivo = '';
    private $Txt;
    private $MapaCampos = array();
    private $NovaLinha;
    private $QuantidadeRegistroExportados = 0;

    public function getQuantidadeRegistroExportados(){
        return $this->QuantidadeRegistroExportados;
    }

    public function  __construct(){
        $this->MapaCampos['01'][1] = array(1,6,0); //1 - Brancos
        $this->MapaCampos['01'][2] = array(7,12,0); //2 - Nome abreviado do Cliente/Fornecedor
        $this->MapaCampos['01'][3] = array(19,19,0); //3 - C.G.C.M.F./C.I.C.
        $this->MapaCampos['01'][4] = array(38,1,0); //4 - Identificação
        $this->MapaCampos['01'][5] = array(39,1,0); //5 - Natureza
        $this->MapaCampos['01'][6] = array(40,40,0); //6 - Brancos
        $this->MapaCampos['01'][7] = array(80,40,0); //7 - Endereþo
        $this->MapaCampos['01'][8] = array(120,30,0); //8 - Bairro
        $this->MapaCampos['01'][9] = array(150,25,0); //9 - Cidade
        $this->MapaCampos['01'][10] = array(175,4,0); //10 - Estado
        $this->MapaCampos['01'][11] = array(179,12,0); //11 - Código Endereþamento Postal (C.E.P.)
        $this->MapaCampos['01'][12] = array(191,10,0); //12 - Caixa Postal
        $this->MapaCampos['01'][13] = array(201,20,0); //13 - País
        $this->MapaCampos['01'][14] = array(221,19,0); //14 - Inscrição Estadual
        $this->MapaCampos['01'][15] = array(240,2,0); //15 - Brancos
        $this->MapaCampos['01'][16] = array(242,5,0); //16 - Taxa Financeira
        $this->MapaCampos['01'][17] = array(247,8,0); //17 - Data Taxa Financeira
        $this->MapaCampos['01'][18] = array(255,5,0); //18 - Código Transportador Padrão
        $this->MapaCampos['01'][19] = array(260,2,0); //19 - Código do Grupo do Fornecedor
        $this->MapaCampos['01'][20] = array(262,8,0); //20 - Linha de Produtos
        $this->MapaCampos['01'][21] = array(270,12,0); //21 - Ramo de Atividade
        $this->MapaCampos['01'][22] = array(282,15,0); //22 - Telefax
        $this->MapaCampos['01'][23] = array(297,5,0); //23 - Ramal do Telefax
        $this->MapaCampos['01'][24] = array(302,15,0); //24 - Telex
        $this->MapaCampos['01'][25] = array(317,8,5); //25 - Data Implantação
        $this->MapaCampos['01'][26] = array(325,14,0); //26 - Compras no Período
        $this->MapaCampos['01'][27] = array(339,1,0); //27 - Contribuinte ICMS
        $this->MapaCampos['01'][28] = array(340,2,0); //28 - Brancos
        $this->MapaCampos['01'][29] = array(342,3,0); //29 - Categoria
        $this->MapaCampos['01'][30] = array(345,5,0); //30 - Código do Representante
        $this->MapaCampos['01'][31] = array(350,2,0); //31 - Brancos
        $this->MapaCampos['01'][32] = array(352,5,0); //32 - Bonificação (Desconto Padrão cliente)
        $this->MapaCampos['01'][33] = array(357,1,0); //33 - Abrangência da Avaliação de Crédito
        $this->MapaCampos['01'][34] = array(358,2,0); //34 - Grupo do Cliente
        $this->MapaCampos['01'][35] = array(360,11,0); //35 - Limite de Crédito
        $this->MapaCampos['01'][36] = array(371,8,0); //36 - Data Limite de Crédito
        $this->MapaCampos['01'][37] = array(379,3,0); //37 - Percentual Máximo Faturado por Período
        $this->MapaCampos['01'][38] = array(382,5,0); //38 - Portador
        $this->MapaCampos['01'][39] = array(387,2,0); //39 - Modalidade
        $this->MapaCampos['01'][40] = array(389,1,0); //40 - Aceita Faturamento Parcial
        $this->MapaCampos['01'][41] = array(390,1,0); //41 - Indicador de Crédito
        $this->MapaCampos['01'][42] = array(391,1,0); //42 - Avaliação de Crédito Para Aprovação de Pedido
        $this->MapaCampos['01'][43] = array(392,6,0); //43 - Natureza de Operação
        $this->MapaCampos['01'][44] = array(398,150,0); //44 - Observação 1
        $this->MapaCampos['01'][45] = array(548,4,0); //45 - Percentual Minimo Por Faturamento Parcial
        $this->MapaCampos['01'][46] = array(552,1,0); //46 - Meio Para Emissão de Pedido de Compra
        $this->MapaCampos['01'][47] = array(553,12,0); //47 - Nome Fantasia da Matriz do Cliente
        $this->MapaCampos['01'][48] = array(565,15,0); //48 - Telefone Modem
        $this->MapaCampos['01'][49] = array(580,5,0); //49 - Ramal do Modem
        $this->MapaCampos['01'][50] = array(585,15,0); //50 - Telefax
        $this->MapaCampos['01'][51] = array(600,5,0); //51 - Ramal do Telefax
        $this->MapaCampos['01'][52] = array(605,7,0); //52 - Agência do Cliente/Fornecedor
        $this->MapaCampos['01'][53] = array(612,1,0); //53 - Brancos
        $this->MapaCampos['01'][54] = array(613,8,0); //54 - Número de Títulos
        $this->MapaCampos['01'][55] = array(621,8,0); //55 - Número de Dias
        $this->MapaCampos['01'][56] = array(629,4,0); //56 - Percentual Máximo de cancelamento Quant. Aberto
        $this->MapaCampos['01'][57] = array(633,8,0); //57 - Data da Última Nota Fiscal Emitida
        $this->MapaCampos['01'][58] = array(641,1,0); //58 - Emite Bloquete Para Título
        $this->MapaCampos['01'][59] = array(642,1,0); //59 - Emite Etiqueta Para Correspondência
        $this->MapaCampos['01'][60] = array(643,1,0); //60 - Valores de Recebimento
        $this->MapaCampos['01'][61] = array(644,1,0); //61 - Gera Aviso de Débito
        $this->MapaCampos['01'][62] = array(645,5,0); //62 - Portador Preferencial
        $this->MapaCampos['01'][63] = array(650,2,0); //63 - Modalidade Preferencial
        $this->MapaCampos['01'][64] = array(652,3,0); //64 - Baixa Não Acatada
        $this->MapaCampos['01'][65] = array(655,10,0); //65 - Conta Corrente do Cliente/Fornecedor
        $this->MapaCampos['01'][66] = array(665,2,0); //66 - Dígito da Conta Corrente do Cliente/Fornecedor
        $this->MapaCampos['01'][67] = array(667,4,0); //67 - Condição de Pagamento
        $this->MapaCampos['01'][68] = array(671,4,0); //68 - Brancos
        $this->MapaCampos['01'][69] = array(675,2,0); //69 - Número de Cópias do Pedido de Compra
        $this->MapaCampos['01'][70] = array(677,20,0); //70 - Código Suframa
        $this->MapaCampos['01'][71] = array(697,20,0); //71 - Código Cacex
        $this->MapaCampos['01'][72] = array(717,1,0); //72 - Gera Diferença de Preço
        $this->MapaCampos['01'][73] = array(718,8,0); //73 - Tabela de Preços
        $this->MapaCampos['01'][74] = array(726,1,0); //74 - Indicador de Avaliação
        $this->MapaCampos['01'][75] = array(727,12,0); //75 - Usuário libera Crédito
        $this->MapaCampos['01'][76] = array(739,1,0); //76 - Vencimento Domingo
        $this->MapaCampos['01'][77] = array(740,1,0); //77 - Vencimento Sábado
        $this->MapaCampos['01'][78] = array(741,19,0); //78 - C.G.C. Cobrança
        $this->MapaCampos['01'][79] = array(760,12,0); //79 - C.E.P. Cobrança
        $this->MapaCampos['01'][80] = array(772,4,0); //80 - Estado Cobrança
        $this->MapaCampos['01'][81] = array(776,25,0); //81 - Cidade Cobrança
        $this->MapaCampos['01'][82] = array(801,30,0); //82 - Bairro Cobrança
        $this->MapaCampos['01'][83] = array(831,40,0); //83 - Endereço Cobrança
        $this->MapaCampos['01'][84] = array(871,10,0); //84 - Caixa Postal Cobrança
        $this->MapaCampos['01'][85] = array(881,19,0); //85 - Inscrição Estadual Cobrança
        $this->MapaCampos['01'][86] = array(900,3,0); //86 - Banco do Cliente/Fornecedor
        $this->MapaCampos['01'][87] = array(903,6,0); //87 - Próximo Aviso Débito
        $this->MapaCampos['01'][88] = array(909,1,0); //88 - Tipo do Registro
        $this->MapaCampos['01'][89] = array(910,1,0); //89 - Vencimento Feriado
        $this->MapaCampos['01'][90] = array(911,2,0); //90 - Tipo de Pagamento
        $this->MapaCampos['01'][91] = array(913,1,0); //91 - Tipo de Cobrança das Despesas
        $this->MapaCampos['01'][92] = array(914,19,0); //92 - Inscrição Municipal
        $this->MapaCampos['01'][93] = array(933,3,0); //93 - Tipo de Despesa Padrão
        $this->MapaCampos['01'][94] = array(936,3,0); //94 - Tipo de Receita Padrão
        $this->MapaCampos['01'][95] = array(939,12,0); //95 - Código de Endereçamento Postal Estrangeiro
        $this->MapaCampos['01'][96] = array(951,12,0); //96 - Micro Região
        $this->MapaCampos['01'][97] = array(963,3,0); //97 - Brancos
        $this->MapaCampos['01'][98] = array(966,15,0); //98 - Telefone[1]
        $this->MapaCampos['01'][99] = array(981,15,0); //99 - Telefone[2]
        $this->MapaCampos['01'][100] = array(996,2,0); //100 - Número de Meses Inativos
        $this->MapaCampos['01'][101] = array(998,3,0); //101 - Instrução Bancária(1)
        $this->MapaCampos['01'][102] = array(1001,3,0); //102 - Instrução Bancária(2)
        $this->MapaCampos['01'][103] = array(1004,6,0); //103 - Natureza Interestadual
        $this->MapaCampos['01'][104] = array(1010,9,0); //104 - Código do Cliente
        $this->MapaCampos['01'][105] = array(1019,9,0); //105 - Código do Cliente de Cobrança
        $this->MapaCampos['01'][106] = array(1028,1,0); //106 - Utiliza Verba de Publicidade
        $this->MapaCampos['01'][107] = array(1029,6,0); //107 - Percentual de Verba de Publicidade
        $this->MapaCampos['01'][108] = array(1035,40,0); //108 - E-mail
        $this->MapaCampos['01'][109] = array(1075,1,0); //109 - Indicador de Avaliação de Embarque
        $this->MapaCampos['01'][110] = array(1076,3,0); //110 - Canal de Venda
        $this->MapaCampos['01'][111] = array(1079,2000,0); //111 - Endereço Cobrança Completo
        $this->MapaCampos['01'][112] = array(3079,2000,0); //112 - Endereço Completo
        $this->MapaCampos['01'][113] = array(5079,20,0); //113 - País de Cobrança
        $this->MapaCampos['01'][114] = array(5099,1,0); //114 - Situação do Fornecedor
        $this->MapaCampos['01'][115] = array(5100,8,0); //115 - Data de Vigência Inicial
        $this->MapaCampos['01'][116] = array(5108,8,0); //116 - Data de Vigência Final
        $this->MapaCampos['01'][117] = array(5116,20,0); //117 - Inscrição INSS
        $this->MapaCampos['01'][118] = array(5136,1,0); //118 - Tributa COFINS
        $this->MapaCampos['01'][119] = array(5137,1,0); //119 - Tributa PIS
        $this->MapaCampos['01'][120] = array(5138,1,0); //120 - Controla Valor Máximo INSS
        $this->MapaCampos['01'][121] = array(5139,1,0); //121 - Calcula PIS/COFINS por Unidade
        $this->MapaCampos['01'][122] = array(5140,1,0); //122 - Retem Pagto
        $this->MapaCampos['01'][123] = array(5141,5,0); //123 - Portador Fornecedor
        $this->MapaCampos['01'][124] = array(5146,2,0); //124 - Modalidade Fornecedor
        $this->MapaCampos['01'][125] = array(5148,1,0); //125 - Contribuinte Substituto Intermediário
        $this->MapaCampos['01'][126] = array(5149,80,0); //126 - Nome do Cliente/Fornecedor
    }

    public function PreencheValor($Linha,$Campo,$Valor){
        if($this->MapaCampos[$Linha][$Campo][2] == 1){//Data
            $Valor = substr($Valor,8,2).substr($Valor,5,2).substr($Valor,0,4);
        }
        elseif($this->MapaCampos[$Linha][$Campo][2] == 2){//Remove Acentos
            $Valor = retiraAcentos($Valor);
        }
        elseif($this->MapaCampos[$Linha][$Campo][2] == 3){
            $Valor = number_format($Valor,5,'.','');
        }
        elseif($this->MapaCampos[$Linha][$Campo][2] == 4){//Qtde
            $Valor = number_format($Valor,4,'','');
        }
        elseif($this->MapaCampos[$Linha][$Campo][2] == 5){//Data
            $Valor = trata_dt_exp_dts(dten2br($Valor));
        }
        $Valor = str_replace("\r\n",'',$Valor);
        $this->NovaLinha .= '<'.$Campo.'>'.$Valor.'</'.$Campo.'>'.chr(13).chr(10);
    }

    public function CarregaClientesBD(){
        /* ---------------------------------------------------------------------
         * LINHA 01 - Cabeçalho
         *
         *
         * t1 = is_pessoa
         * t2 = is_
         * t3 = is_
         * t4 = is_
         * t5 = is_
         */
        $QryPessoas = query("   SELECT
                                    t1.numreg,
                                    t1.sn_estrangeiro,
                                    t1.id_tp_pessoa,
                                    t1.fantasia_apelido,
                                    t1.cnpj_cpf,
                                    t1.endereco,
                                    t1.numero,
                                    t1.complemento,
                                    t1.bairro,
                                    t1.cidade,
                                    t1.pais,
                                    t1.uf,
                                    t1.cep,
                                    t1.dt_cadastro,
                                    t1.sn_contribuinte_icms,
                                    t1.sn_aceita_faturamento_parcial,
                                    t2.id_transportadora_erp,
                                    t3.id_representante,
                                    t4.id_grupo_cliente_erp,
                                    t5.fantasia_apelido AS fantasia_apelido_grupo,
                                    t1.cod_suframa,
                                    t6.id_tab_preco_erp,
                                    t1.tel1,
                                    t1.tel2,
                                    t1.id_pessoa_erp,
                                    t1.email,
                                    t7.id_canal_venda_erp,
                                    t1.razao_social_nome
                                FROM is_pessoa t1
                                    LEFT JOIN is_transportadora t2 ON t1.id_transportadora_padrao = t2.numreg
                                    LEFT JOIN is_usuario t3 ON t1.id_representante_padrao = t3.numreg
                                    LEFT JOIN is_grupo_cliente t4 ON t1.id_grupo_cliente = t4.numreg
                                    LEFT JOIN is_pessoa t5 ON t1.id_pertence_grupo = t5.numreg
                                    LEFT JOIN is_tab_preco t6 ON t1.id_tab_preco_padrao = t6.numreg
                                    LEFT JOIN is_canal_venda t7 ON t1.id_canal_venda = t7.numreg
                                WHERE
                                    t1.sn_cliente = 1
                                AND
                                    t1.sn_importado_erp = 0
                                AND
                                    t1.sn_exportado_erp = 0");
        while($ArPessoa = farray($QryPessoas)){
            /*
             * Aplicando tratamento para linha 01
             */
            /* Se o cliente for estrangeiro exporta como natureza 3 */
            if($ArPessoa['sn_estrangeiro'] == '1'){
                $ArPessoa['id_tp_pessoa'] = '3';
            }
            /*
             * Aplicando tratamento de conversão de valores para o ERP
             */
            switch($ArPessoa['id_tp_pessoa']){
                case '2':
                    $ArPessoa['id_tp_pessoa'] = 1;
                    break;
                case '1':
                    $ArPessoa['id_tp_pessoa'] = 2;
                    break;
            }
            $ArPessoa['sn_contribuinte_icms']            = ($ArPessoa['sn_contribuinte_icms'] == 1)?1:0;
            $ArPessoa['sn_aceita_faturamento_parcial']   = ($ArPessoa['sn_aceita_faturamento_parcial'] == 1)?1:0;

            $this->NovaLinha = "<?xml version='1.0' encoding='ISO-8859-1'?>".chr(13).chr(10);
            $this->NovaLinha .= "<root>".chr(13).chr(10);
            $this->NovaLinha .= "<data_criacao>".date("Y-m-d")." 00:00:00.0</data_criacao>".chr(13).chr(10);
            $this->NovaLinha .= "<pessoa>".chr(13).chr(10);
            $this->PreencheValor('01','id_pessoa', $ArPessoa['numreg']);
            $this->PreencheValor('01','id_pessoa_erp', '');
            $this->PreencheValor('01','id_tp_pessoa', $ArPessoa['id_tp_pessoa']);
            $this->PreencheValor('01','razao_social_nome', $ArPessoa['razao_social_nome']);
            $this->PreencheValor('01','fantasia_apelido', $ArPessoa['fantasia_apelido']);
            $this->PreencheValor('01','sn_extrangeiro', $ArPessoa['sn_extrangeiro']);
            $this->PreencheValor('01','cnpj_cpf', $ArPessoa['cnpj_cpf']);
            $this->PreencheValor('01','ie_rg', $ArPessoa['ie_rg']);
            $this->PreencheValor('01','email', $ArPessoa['email']);
            $this->PreencheValor('01','endereco', $ArPessoa['endereco']);
            $this->PreencheValor('01','numero', $ArPessoa['numero']);
            $this->PreencheValor('01','complemento', $ArPessoa['complemento']);
            $this->PreencheValor('01','bairro', $ArPessoa['bairro']);
            $this->PreencheValor('01','cidade', $ArPessoa['cidade']);
            $this->PreencheValor('01','uf', $ArPessoa['uf']);
            $this->PreencheValor('01','id_cep', $ArPessoa['id_cep']);
            $this->PreencheValor('01','cep', $ArPessoa['cep']);
            $this->PreencheValor('01','pais', $ArPessoa['pais']);
            $this->PreencheValor('01','dt_cadastro', $ArPessoa['dt_cadastro']);
            $this->PreencheValor('01','id_ramo_atividade', $ArPessoa['id_ramo_atividade']);
            $this->PreencheValor('01','site', $ArPessoa['site']);
            $this->PreencheValor('01','tel1', $ArPessoa['tel1']);
            $this->PreencheValor('01','tel2', $ArPessoa['tel2']);
            $this->PreencheValor('01','fax', $ArPessoa['fax']);
            $this->PreencheValor('01','id_grupo_cliente', $ArPessoa['id_grupo_cliente']);
            $this->PreencheValor('01','id_canal_venda', $ArPessoa['id_canal_venda']);
            $this->PreencheValor('01','sn_contribuinte_icms', $ArPessoa['sn_contribuinte_icms']);
            $this->PreencheValor('01','dt_limite_credito_validade', $ArPessoa['dt_limite_credito_validade']);
            $this->PreencheValor('01','vl_limite_credito', $ArPessoa['vl_limite_credito']);
            $this->PreencheValor('01','sn_aceita_faturamento_parcial', $ArPessoa['sn_aceita_faturamento_parcial']);
            $this->PreencheValor('01','dt_ult_pedido_emitido', $ArPessoa['dt_ult_pedido_emitido']);
            $this->PreencheValor('01','id_transportadora_padrao', $ArPessoa['id_transportadora_padrao']);
            $this->PreencheValor('01','id_tab_preco_padrao', $ArPessoa['id_tab_preco_padrao']);
            $this->PreencheValor('01','id_cond_pagto_padrao', $ArPessoa['id_cond_pagto_padrao']);
            $this->PreencheValor('01','id_forma_pagto_padrao', $ArPessoa['id_forma_pagto_padrao']);
            $this->PreencheValor('01','cfop_interestadual_padrao', $ArPessoa['cfop_interestadual_padrao']);
            $this->PreencheValor('01','cfop_estadual_padrao', $ArPessoa['cfop_estadual_padrao']);
            $this->PreencheValor('01','cfop_internacional_padrao', $ArPessoa['cfop_internacional_padrao']);
            $this->PreencheValor('01','im', $ArPessoa['im']);
            $this->PreencheValor('01','dt_virou_cliente', $ArPessoa['dt_virou_cliente']);
            $this->PreencheValor('01','id_regiao', $ArPessoa['id_regiao']);
            $this->PreencheValor('01','id_sit_cred', $ArPessoa['id_sit_cred']);
            $this->PreencheValor('01','cod_suframa', $ArPessoa['cod_suframa']);
            $this->PreencheValor('01','saldo_limite_credito', $ArPessoa['saldo_limite_credito']);
            $this->PreencheValor('01','obs', $ArPessoa['obs']);
            $this->PreencheValor('01','id_segmento', $ArPessoa['id_segmento']);
            $this->PreencheValor('01','id_tp_frete_padrao', $ArPessoa['id_tp_frete_padrao']);
            $this->PreencheValor('01','id_representante_padrao', $ArPessoa['id_representante_padrao']);

            /*
             * Tratando informações fixas no cadastro de parâmetros
             */
            /*$QryCamposFixos = query("SELECT numero_campo,valor_padrao FROM is_param_campo_fixo_txt_cliente WHERE sn_ativo = 1");
            while($ArCamposFixos = farray($QryCamposFixos)){
                $this->PreencheValor('01',$ArCamposFixos[numero_campo], $ArCamposFixos['valor_padrao']);
            }*/
            $this->NovaLinha .= "</pessoa>".chr(13).chr(10);
            $this->NovaLinha .= "</root>".chr(13).chr(10);

            $this->Txt .= $this->NovaLinha."\r\n";
            $this->QuantidadeRegistroExportados++;
            /*
             * Atualizando o cliente como exportado
             */
            $ArSqlUpdatePessoa  = array('numreg' => $ArPessoa['numreg'], 'sn_exportado_erp' => 1, 'dthr_exportacao_erp' => date("Y-m-d H:i:s"));
            $SqlUpdatePessoa    = AutoExecuteSql(TipoBancoDados,'is_pessoa',$ArSqlUpdatePessoa,'UPDATE',array('numreg'));
            $QryUpdatePessoa    = query($SqlUpdatePessoa);

            $NomeArquivo = strtoupper(RemoveAcentos(trim(str_replace(" ","_",str_replace(".","",$ArPessoa['razao_social_nome']))))).date("dmYHis").".xml";

            $CaminhoArquivo = $this->DiretorioArquivo.$NomeArquivo;
            if(file_exists($CaminhoArquivo)){
                $MaxId = uB::getProximoMaxId(2);
            }

            $Arquivo = fopen($CaminhoArquivo,"w+");
            fwrite($Arquivo,$this->Txt);
            fclose($Arquivo);
            $this->Txt = '';
            $this->NovaLinha = '';
        }
    }

    public function getTxt(){
        return $this->Txt;
    }
}
?>