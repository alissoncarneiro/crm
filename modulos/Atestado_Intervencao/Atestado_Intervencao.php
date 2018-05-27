<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML oncontextmenu="return false" onselectstart="return false">
    <HEAD>
    </HEAD>
    <BODY style="font-family:Courier New;font-size:10px;letter-spacing:4px;">
        <?php
        require_once("../../conecta.php");

        /*
          +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-
          Tratamento das variáveis que serão exibidas na Impressão
          +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-
         */

        // ******************************************************************
        // Recebe o "pnumreg", que servirá como referência na QUERY principal
        // ******************************************************************
        $ID_Atestado = $_GET['pnumreg'];

        // ******************************************************************
        // Query principal, que servirá como base para as outras Query's
        // ******************************************************************
        $Query_Atestado_Intervencao = mysql_fetch_array(mysql_query("SELECT * FROM is_atestado_intervencao WHERE numreg = '".$ID_Atestado."'"));

        // ******************************************************************
        // ID do Atestado Anterior
        // ******************************************************************
        $ID_Atestado_Anterior = $Query_Atestado_Intervencao['id_atestado_anterior'];

        // ******************************************************************
        //                            Usuário ECF
        // ******************************************************************
        $ID_Razao_Social_Usuario_ECF = $Query_Atestado_Intervencao['id_pessoa_ue'];

        $Query_Usuario_ECF = mysql_fetch_array(mysql_query("SELECT * FROM is_pessoa WHERE id_pessoa = '".$ID_Razao_Social_Usuario_ECF."' LIMIT 1"));
        $Razao_Social_UECF = $Query_Usuario_ECF['razao_social_nome'];
        $CNPJ_UECF = $Query_Usuario_ECF['cnpj_cpf'];
        $Endereco_UECF = $Query_Usuario_ECF['endereco'];
        $IE_UECF = $Query_Usuario_ECF['ie_rg'];
        $Municipio_UECF = $Query_Usuario_ECF['cidade'];
        $UF_UECF = $Query_Usuario_ECF['uf'];

        $ID_Ramo_UECF = $Query_Usuario_ECF['id_ramo'];
        $Query_Verifica_Ramo_UECF = mysql_fetch_array(mysql_query("SELECT * FROM is_ramo WHERE id_ramo = '".$ID_Ramo_UECF."' LIMIT 1"));
        $CNAE_UECF = $Query_Verifica_Ramo_UECF['nome_ramo'];

        $IM_UECF = $Query_Usuario_ECF['im'];

        // ******************************************************************
        //                            Equipamento ECF
        // ******************************************************************
        $ID_Equipamento_ECF = $Query_Atestado_Intervencao['id_equipamento_ecf'];

        $Query_Cad_Equipamento_ECF = mysql_fetch_array(mysql_query("SELECT * FROM is_cad_equipamento_ecf WHERE id_equipamento = '".$ID_Equipamento_ECF."' LIMIT 1"));
        $Marca_Equipamento = $Query_Cad_Equipamento_ECF['marca_equipamento'];
        $Modelo_Equipamento = $Query_Cad_Equipamento_ECF['modelo_equipamento'];
        $Numero_Ordem = $Query_Cad_Equipamento_ECF['n_ordem'];
        $Numero_Fabricacao = $Query_Cad_Equipamento_ECF['n_fabricacao'];
        $Versao_Encontrada = $Query_Cad_Equipamento_ECF['versao_encontratada'];
        $Versao_Atual = $Query_Cad_Equipamento_ECF['versao_atual'];

        $DATA_BD_Inicio_Intervencao = $Query_Atestado_Intervencao['dt_inicio_intervencao'];
        $Dia_Inicio_Intervencao = substr($DATA_BD_Inicio_Intervencao,8,2);
        $Mes_Inicio_Intervencao = substr($DATA_BD_Inicio_Intervencao,5,2);
        $Ano_Inicio_Intervencao = substr($DATA_BD_Inicio_Intervencao,0,4);

        $Data_Inicio_Intervencao = $Dia_Inicio_Intervencao."/".$Mes_Inicio_Intervencao."/".$Ano_Inicio_Intervencao;

        $DATA_BD_Termino_Intervencao = $Query_Atestado_Intervencao['dt_term_intervencao'];
        $Dia_Termino_Intervencao = substr($DATA_BD_Termino_Intervencao,8,2);
        $Mes_Termino_Intervencao = substr($DATA_BD_Termino_Intervencao,5,2);
        $Ano_Termino_Intervencao = substr($DATA_BD_Termino_Intervencao,0,4);

        $Data_Termino_Intervencao = $Dia_Termino_Intervencao."/".$Mes_Termino_Intervencao."/".$Ano_Termino_Intervencao;

        $Numero_MFD_Retirada = $Query_Atestado_Intervencao['n_mfd_retirada'];
        $Numero_MFD_Colocada = $Query_Atestado_Intervencao['n_mfd_colocada'];

        // ******************************************************************
        //                 Valor Registrado ou Acumulado
        // ******************************************************************
        $geral_ai = $Query_Atestado_Intervencao['geral_ai'];
        $geral_api = $Query_Atestado_Intervencao['geral_api'];
        $venda_bruta_ai = $Query_Atestado_Intervencao['venda_bruta_ai'];
        $venda_bruta_dpi = $Query_Atestado_Intervencao['venda_bruta_dpi'];
        $venda_liquida_ai = $Query_Atestado_Intervencao['venda_liquida_ai'];
        $venda_liquida_api = $Query_Atestado_Intervencao['venda_liquida_api'];
        $cancelamento_ai = $Query_Atestado_Intervencao['cancelamento_ai'];
        $cancelamento_api = $Query_Atestado_Intervencao['cancelamento_api'];
        $desconto_ai = $Query_Atestado_Intervencao['desconto_ai'];
        $desconto_api = $Query_Atestado_Intervencao['desconto_api'];
        $subistituicao_tributaria_ai = $Query_Atestado_Intervencao['subistituicao_tributaria_ai'];
        $subistituicao_tributaria_api = $Query_Atestado_Intervencao['subistituicao_tributaria_api'];
        $isentas_ai = $Query_Atestado_Intervencao['isentas_ai'];
        $isentas_api = $Query_Atestado_Intervencao['isentas_api'];
        $nao_incidencia_ai = $Query_Atestado_Intervencao['nao_incidencia_ai'];
        $nao_incidencia_ai = $Query_Atestado_Intervencao['nao_incidencia_ai'];
        $tributado_1 = $Query_Atestado_Intervencao['tributado_1'];
        $tributado_1_val_ai = $Query_Atestado_Intervencao['tributado_1_val_ai'];
        $tributado_1_val_api = $Query_Atestado_Intervencao['tributado_1_val_api'];
        $tributado_2 = $Query_Atestado_Intervencao['tributado_2'];
        $tributado_2_val_ai = $Query_Atestado_Intervencao['tributado_2_val_ai'];
        $tributado_2_val_api = $Query_Atestado_Intervencao['tributado_2_val_api'];
        $tributado_3 = $Query_Atestado_Intervencao['tributado_3'];
        $tributado_3_val_ai = $Query_Atestado_Intervencao['tributado_3_val_ai'];
        $tributado_3_val_api = $Query_Atestado_Intervencao['tributado_3_val_api'];
        $tributado_4 = $Query_Atestado_Intervencao['tributado_4'];
        $tributado_4_val_ai = $Query_Atestado_Intervencao['tributado_4_val_ai'];
        $tributado_4_val_api = $Query_Atestado_Intervencao['tributado_4_val_api'];
        $tributado_5 = $Query_Atestado_Intervencao['tributado_5'];
        $tributado_5_val_ai = $Query_Atestado_Intervencao['tributado_5_val_ai'];
        $tributado_5_val_api = $Query_Atestado_Intervencao['tributado_5_val_api'];
        $tributado_6 = $Query_Atestado_Intervencao['tributado_6'];
        $tributado_6_val_ai = $Query_Atestado_Intervencao['tributado_6_val_ai'];
        $tributado_6_val_api = $Query_Atestado_Intervencao['tributado_6_val_api'];
        $tributado_7 = $Query_Atestado_Intervencao['tributado_7'];
        $tributado_7_val_ai = $Query_Atestado_Intervencao['tributado_7_val_ai'];
        $tributado_7_val_api = $Query_Atestado_Intervencao['tributado_7_val_api'];
        $ordem_operacao_ai = $Query_Atestado_Intervencao['ordem_operacao_ai'];
        $ordem_operacao_api = $Query_Atestado_Intervencao['ordem_operacao_api'];
        $contador_reducoes_ai = $Query_Atestado_Intervencao['contador_reducoes_ai'];
        $contador_reducoes_api = $Query_Atestado_Intervencao['contador_reducoes_api'];
        $ordem_doc_fiscais_ai = $Query_Atestado_Intervencao['ordem_doc_fiscais_ai'];
        $ordem_doc_fiscais_api = $Query_Atestado_Intervencao['ordem_doc_fiscais_api'];
        $docs_cancelados_ai = $Query_Atestado_Intervencao['docs_cancelados_ai'];
        $docs_cancelados_api = $Query_Atestado_Intervencao['docs_cancelados_api'];
        $contador_reinicio_operacao_ai = $Query_Atestado_Intervencao['contador_reinicio_operacao_ai'];
        $contador_reinicio_operacao_api = $Query_Atestado_Intervencao['contador_reinicio_operacao_api'];

        // ******************************************************************
        //                               Lacres
        // ******************************************************************
        // Pega os dados do Lacre antigo, usando o ID do Atestado Anterior como Referência ( caso não seja o primeiro Atestado )
        if($ID_Atestado_Anterior != ''){

            $Query = mysql_query("SELECT * FROM is_lacres WHERE n_atestado = '".$ID_Atestado_Anterior."'");
            $Qtd = mysql_num_rows($Query);
            $Array_Lacres_ANTIGO = array();

            while($Query_Lacres_ANTIGO_1 = mysql_fetch_array($Query)){
                $Array_Lacres_ANTIGO[] = $Query_Lacres_ANTIGO_1;
            }

            $Numero_Lacre_ANTIGO = $Array_Lacres_ANTIGO[0]['n_lacre'];
            $Numero_Lacre_Externo_ECF_CNPJ_ANTIGO = $Array_Lacres_ANTIGO[1]['n_lacre'];
        }

        // No caso de não haver um Atestado anterior, os "Lacres" antigos são mantidos como <VAZIO>
        if($ID_Atestado_Anterior == ''){
            $Numero_Lacre_ANTIGO = '';
            $Numero_Lacre_Externo_ECF_CNPJ_ANTIGO = '';
        }

        // Servem como Referência para  busca dos Lacres Novos
        $Numero_Lacre_NOVO_1 = $Query_Atestado_Intervencao['n_lacre_dispositivo_armazenamento_software_basico'];
        $Numero_Lacre_NOVO_2 = $Query_Atestado_Intervencao['n_lacre_dispositivo_armazenamento_software_basico_'];

        // Pega os dados do Lacre ( Novo )
        $Query_Lacres_NOVO_1 = mysql_fetch_array(mysql_query("SELECT * FROM is_lacres WHERE id_lacre = '".$Numero_Lacre_NOVO_1."' LIMIT 1"));
        $Numero_Lacre_NOVO = $Query_Lacres_NOVO_1['n_lacre'];

        $Query_Lacres_NOVO_2 = mysql_fetch_array(mysql_query("SELECT * FROM is_lacres WHERE id_lacre = '".$Numero_Lacre_NOVO_2."' LIMIT 1"));
        $Numero_Lacre_Externo_ECF_CNPJ_NOVO = $Query_Lacres_NOVO_2['n_lacre'];

        // ******************************************************************
        //                   Número de Etiquetas ou Selos
        // ******************************************************************

        $Software_Basico_PCF = $Query_Atestado_Intervencao['software_basico_pcf'];
        $Cabo_PCF_MF_Lado_PCF = $Query_Atestado_Intervencao['cabo_pcf_mf_lado_pcf'];
        $Cabo_PCF_MF_Lado_MF = $Query_Atestado_Intervencao['cabo_pcf_mf_lado_mf'];
        $PCF_Gabinete = $Query_Atestado_Intervencao['pcf_gabinete'];
        $Mem_Fita_Detalhe = $Query_Atestado_Intervencao['mem_fita_detalhe'];

        $Software_Basico_PCF_Apos_Intervencao = $Query_Atestado_Intervencao['software_basico_pcf_apos_intervencao'];
        $Cabo_PCF_MF_Lado_PCF_Apos_Intervencao = $Query_Atestado_Intervencao['cabo_pcf_mf_lado_pcf_apos_intervencao'];
        $Cabo_PCF_MF_Lado_MF_Apos_Intervencao = $Query_Atestado_Intervencao['cabo_pcf_mf_lado_mf_apos_intervencao'];
        $PCF_Gabinete_Apos_Intervencao = $Query_Atestado_Intervencao['pcf_gainete_apos_intervencao'];
        $Mem_Fita_Detalhe_Apos_Intervencao = $Query_Atestado_Intervencao['mem_fita_detalhe_apos_intervencao'];

        // ******************************************************************
        //                 Informações sobre o Atestado Anterior
        // ******************************************************************

        $Nome_Credenciado = $Query_Atestado_Intervencao['nome_credenciado'];
        $Numero_Atestado = $Query_Atestado_Intervencao['id_atestado'];
        $Numero_Consulta = $Query_Atestado_Intervencao['n_consulta'];
        $Motivo_Intervencao_Discriminacao_Servico_Executado = $Query_Atestado_Intervencao['motivo_intervencao_discriminacao_servico_executado'];

        // ******************************************************************
        //                        Técnico Interventor
        // ******************************************************************
        $ID_Tecnico_Interventor = $Query_Atestado_Intervencao['tecnico_interventor'];
        $Query_Tecnico_Interventor = mysql_fetch_array(mysql_query("SELECT * FROM is_tecnico_interventor WHERE id_tecnico = '".$ID_Tecnico_Interventor."' LIMIT 1"));
        $Nome_Tecnico = $Query_Tecnico_Interventor['nome'];
        $RG_Tecnico = $Query_Tecnico_Interventor['rg'];
        $CPF_Tecnico = $Query_Tecnico_Interventor['cpf'];

        $DATA_BD_Cadastro_Atestado = date("Y-m-d");
        $Dia_Cadastro_Atestado = substr($DATA_BD_Cadastro_Atestado,8,2);
        $Mes_Cadastro_Atestado = substr($DATA_BD_Cadastro_Atestado,5,2);
        $Ano_Cadastro_Atestado = substr($DATA_BD_Cadastro_Atestado,0,4);

        $Data_Cadastro_Atestado = $Dia_Cadastro_Atestado."/".$Mes_Cadastro_Atestado."/".$Ano_Cadastro_Atestado;

        /*
          +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-
         */
        ?>

        <!--
            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
            CONTEÚDO PRINCIPAL, ONDE ESTÃO ARMAZENADOS OS DIVS, QUE REPRESENTAM CADA CAMPO/CÉLULA DO FORMULÁRIO
            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
        -->

        <DIV style="">
            <DIV id="BLOCO_1">
                <div style="border:0px solid;">
                </div>
            </DIV>

            <!-- -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
                 RESPONSÁVEL PELA COMPLEMENTAÇÃO DA INTERVENÇÃO TÉCNICA
            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*- -->

            <DIV id="BLOCO_2">
                <div style="border:0px solid;">
                    <label>  </label>
                </div>
                <div style="border:0px solid;">
                    <label>  </label>
                </div>
                <div style="border:0px solid;">
                    <label> </label>
                </div>
                <div style="border:0px solid;">
                    <label> <? ?> </label>
                </div>
                <div style="border:0px solid;">
                    <label> <? ?> </label>
                </div>
                <div style="border:0px solid;">
                    <label> <? ?> </label>
                </div>
                <div style="border:0px solid;">
                    <label> <? ?> </label>
                </div>
                <div style="border:0px solid;">
                    <label> <? ?> </label>
                </div>
            </DIV>

            <!-- -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
                 USUÁRIO DO ECF
            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*- -->
            <DIV id="BLOCO_3">
                <div style="border:1px solid;">
                    <label> <? echo "Razão Social: ".$Razao_Social_UECF ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "CNPF: ".$CNPJ_UECF ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "ENDEREÇO: ".$Endereco_UECF ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "INSCRIÇÃO ESTADUAL: ".$IE_UECF ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "MUNICIPIO: ".$Municipio_UECF ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "UF: ".$UF_UECF ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "CNAE: ".$CNAE_UECF ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "INSCRIÇÃO MUNICIPIO: ".$IM_UECF ?> </label>
                </div>
            </DIV>
            <br>

            <!-- -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
                 EQUIPAMENTO ECF
            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*- -->

            <DIV id="BLOCO_4">
                <div style="border:1px solid;">
                    <label> <? echo "MARCA: ".$Marca_Equipamento ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "MODELO: ".$Modelo_Equipamento ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "Nº ORDEM: ".$Numero_Ordem ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "Nº FABRICAÇÃO: ".$Numero_Fabricacao ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "VERSÃO ENCONTRADA: ".$Versao_Encontrada ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "VERSÃO ATUAL: ".$Versao_Atual ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "DATA INICIO INTERVENÇÃO: ".$Data_Inicio_Intervencao ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "DATA TÉRMINO INTERVENÇÃO: ".$Data_Termino_Intervencao ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "MFD RETIRADA: ".$Numero_MFD_Retirada ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "MFD COLOCADA: ".$Numero_MFD_Colocada ?> </label>
                </div>
            </DIV>
            <br>
            <!-- -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*

                 VALOR REGISTRADO OU ACUMULADO

            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*- -->

            <DIV id="BLOCO_5" style="">
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$geral_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$geral_api ?> </label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$venda_bruta_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$venda_bruta_dpi ?> </label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$venda_liquida_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$venda_liquida_api ?> </label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$cancelamento_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$cancelamento_api ?> </label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$desconto_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$desconto_api ?> </label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$subistituicao_tributaria_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$subistituicao_tributaria_api ?> </label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$isentas_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$isentas_api ?> </label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$nao_incidencia_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$nao_incidencia_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$tributado_1 ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$tributado_1_val_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$tributado_1_val_api ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$tributado_2 ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$tributado_2_val_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$tributado_2_val_api ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_3 ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_3_val_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$tributado_3_val_api ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_4 ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_4_val_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_4_val_api ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_5 ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_5_val_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_5_val_api ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_6 ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_6_val_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_6_val_api ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_7 ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_7_val_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$tributado_7_val_api ?> </label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$ordem_operacao_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$ordem_operacao_api ?> </label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$contador_reducoes_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo ": ".$contador_reducoes_api ?></label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$ordem_doc_fiscais_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$ordem_doc_fiscais_api ?> </label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$docs_cancelados_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$docs_cancelados_api ?> </label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$contador_reinicio_operacao_ai ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo ": ".$contador_reinicio_operacao_api ?> </label>
                </div>
            </DIV>
            <br>

            <!-- -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*

                 LACRES
            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*- -->

            <DIV id="BLOCO_6">
                <div style="border:1px solid;">
                    <label> <? echo "LACRE SOFTWARE ( ANTIGO ): ".$Numero_Lacre_ANTIGO ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "LACRE EXTERNO ( ANTIGO ): ".$Numero_Lacre_Externo_ECF_CNPJ_ANTIGO ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "LACRE SOFTWARE ( NOVO ): ".$Numero_Lacre_NOVO ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "LACRE EXTERNO ( NOVO ): ".$Numero_Lacre_Externo_ECF_CNPJ_NOVO ?> </label>
                </div>
            </DIV>
            <br>
            <!-- -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*

                 NÚMERO DE ETIQUETAS OU SELOS

            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*- -->
            <DIV id="BLOCO_7">
                <div style="border:1px solid;">
                    <label> <? echo "SOFTWARE ( ANTIGO ): ".$Software_Basico_PCF ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "CABO PCF LADO PCF ( ANTIGO ): ".$Cabo_PCF_MF_Lado_PCF ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "CABO PCF-MF LADO MF ( ANTIGO ): ".$Cabo_PCF_MF_Lado_MF ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "PCF GABINETE ( ANTIGO ): ".$PCF_Gabinete ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "MEMORIA FITA ( ANTIGO ): ".$Mem_Fita_Detalhe ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "SOFTWARE ( NOVO ): ".$Software_Basico_PCF_Apos_Intervencao ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "CABO PCF LADO PCF ( NOVO ): ".$Cabo_PCF_MF_Lado_PCF_Apos_Intervencao ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "CABO PCF-MF LADO MF ( NOVO ): ".$Cabo_PCF_MF_Lado_MF_Apos_Intervencao ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "PCF GABINETE ( NOVO ): ".$PCF_Gabinete_Apos_Intervencao ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "MEMORIA FITA ( NOVO ): ".$Mem_Fita_Detalhe_Apos_Intervencao ?> </label>
                </div>
            </DIV>
            <DIV id="BLOCO_8">
                <div style="border:1px solid;">
                    <label> <? echo "NOME DO CREDENCIADO: ".$Nome_Credenciado ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "NUMERO ATESTADO ( ANTERIOR ): ".$Numero_Atestado ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "NUMERO CONSULTA: ".$Numero_Consulta ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <? echo "MOTIVO: ".$Motivo_Intervencao_Discriminacao_Servico_Executado ?> </label>
                </div>
            </DIV>
            <br>
            <!-- -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*

                 DECLARAÇÃO

            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*- -->
            <DIV id="BLOCO_9">
                <div style="border:1px solid;">
                    <label> <? echo "TÉCNICO: ".$Nome_Tecnico ?> </label>
                </div>
                <div style="border:1px solid;">
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo "RG: ".$RG_Tecnico ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo "CPF: ".$CPF_Tecnico ?> </label>
                </div>
                <div style="border:1px solid;">
                    <label> <?php echo "DATA DO ATESTADO ( ATUAL ): ".$Data_Cadastro_Atestado ?> </label>
                </div>
            </DIV>
        </DIV>
        <!--
            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
            -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
        -->

    </BODY>
</HTML>