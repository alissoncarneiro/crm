<?php
require_once("../../conecta.php");
require_once '../../funcoes.php';
require_once '../../functions.php';

$ID_Atestado = $_GET['pnumreg'];
$Query_Atestado_Intervencao = mysql_fetch_array(mysql_query("SELECT * FROM is_atestado_intervencao WHERE numreg = '".$ID_Atestado."'"));

$ID_Atestado_Anterior = $Query_Atestado_Intervencao['id_atestado_anterior'];

// ******************************************************************
//                            Usuário ECF
// ******************************************************************
$ID_Razao_Social_Usuario_ECF = $Query_Atestado_Intervencao['id_pessoa_ue'];

$Query_Usuario_ECF = mysql_fetch_array(mysql_query("SELECT * FROM is_pessoa WHERE numreg = '".$ID_Razao_Social_Usuario_ECF."' LIMIT 1"));
$Razao_Social_UECF = $Query_Usuario_ECF['razao_social_nome'];
$CNPJ_UECF = $Query_Usuario_ECF['cnpj_cpf'];
$IM_UECF = $Query_Usuario_ECF['im'];
$Endereco_UECF = $Query_Usuario_ECF['endereco'];
$IE_UECF = $Query_Usuario_ECF['ie_rg'];
$Municipio_UECF = $Query_Usuario_ECF['cidade'];
$UF_UECF = $Query_Usuario_ECF['uf'];

$ID_Ramo_UECF = $Query_Usuario_ECF['id_ramo'];
$Query_Verifica_Ramo_UECF = mysql_fetch_array(mysql_query("SELECT * FROM is_ramo WHERE id_ramo = '".$ID_Ramo_UECF."' LIMIT 1"));
$CNAE_UECF = $Query_Verifica_Ramo_UECF['nome_ramo'];


$ID_Razao_Social_Usuario_RCIT = $Query_Atestado_Intervencao['id_pessoa_rcit'];

$Query_Usuario_RCIT = mysql_fetch_array(mysql_query("SELECT * FROM is_pessoa WHERE numreg = '".$ID_Razao_Social_Usuario_RCIT."' LIMIT 1"));
$Razao_Social_RCIT = $Query_Usuario_RCIT['razao_social_nome'];
$CNPJ_RCIT = $Query_Usuario_RCIT['cnpj_cpf'];
$IM_RCIT = $Query_Usuario_RCIT['im'];
$Endereco_RCIT = $Query_Usuario_RCIT['endereco'];
$IE_RCIT = $Query_Usuario_RCIT['ie_rg'];
$Municipio_RCIT = $Query_Usuario_RCIT['cidade'];
$UF_RCIT = $Query_Usuario_RCIT['uf'];

// ******************************************************************
//                            Equipamento ECF
// ******************************************************************
$ID_Equipamento_ECF = $Query_Atestado_Intervencao['id_equipamento_ecf'];

$Query_Cad_Equipamento_ECF = mysql_fetch_array(mysql_query("SELECT * FROM is_pessoa_equipamento WHERE numreg = '".$ID_Equipamento_ECF."' LIMIT 1"));
$Query_Marca_Equipamento_ECF = mysql_fetch_array(mysql_query("select * from is_produto_marca WHERE numreg = '".$Query_Cad_Equipamento_ECF['id_produto_marca']."' LIMIT 1"));
$Query_Modelo_Equipamento_ECF = mysql_fetch_array(mysql_query("select * from is_produto_modelo WHERE numreg = '".$Query_Cad_Equipamento_ECF['id_produto_modelo']."' LIMIT 1"));

$Marca_Equipamento = $Query_Marca_Equipamento_ECF['nome_produto_marca'];
$Modelo_Equipamento = $Query_Modelo_Equipamento_ECF['nome_produto_modelo'];
$Numero_Ordem = $Query_Cad_Equipamento_ECF['num_ordem'];
$Numero_Fabricacao = $Query_Cad_Equipamento_ECF['nr_serie'];
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
$nao_incidencia_api = $Query_Atestado_Intervencao['nao_incidencia_api'];
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
$Query_Tecnico_Interventor = mysql_fetch_array(mysql_query("SELECT * FROM is_usuario WHERE id_usuario = '".$ID_Tecnico_Interventor."' LIMIT 1"));
$Nome_Tecnico = $Query_Tecnico_Interventor['nome_usuario'];
$RG_Tecnico = $Query_Tecnico_Interventor['rg_usuario'];
$CPF_Tecnico = $Query_Tecnico_Interventor['cpf_usuario'];

$DATA_BD_Cadastro_Atestado = date("Y-m-d");

$Data_Cadastro_Atestado = dten2br($emissao['dt_log']);

#Emissao

$emissao = mysql_fetch_array(mysql_query('select * from is_log where operacao = \'incluir\' and id_cad = \'cad_atestado_intervencao\' and numreg_cadastro = \''.$ID_Atestado.'\''));

$emissao = dten2br($emissao['dt_log']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Untitled Document</title>
        <style type="text/css">
            body {
                margin-left: 0px;
                margin-top: 0px;
                margin-right: 0px;
                margin-bottom: 0px;
            }

        </style>
    </head>

    <body>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-size:14px">
            <tr>
          <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="13%" height="10">&nbsp;</td>
                        <td width="69%" height="10">&nbsp;</td>
                            <td width="18%" height="10">&nbsp;</td>
                        </tr>
              </table></td>
            </tr>
            <tr>
                <td height="10">&nbsp;</td>
          </tr>
            <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td height="23" colspan="3" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Razao_Social_RCIT;?>&nbsp;</td>
                    <td height="23" colspan="2" align="center" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $CNPJ_RCIT;?>&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="23" colspan="3" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Endereco_RCIT;?>&nbsp;</td>
                    <td height="23" colspan="2" align="center" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $IE_RCIT;?>&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="50%" height="23" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Municipio_RCIT;?>&nbsp;</td>
                    <td width="5%" height="23" align="right" valign="bottom"><?php echo $UF_RCIT;?>&nbsp;</td>
                    <td height="23" colspan="2" valign="bottom"><?php echo $CNAE_RCIT;?>&nbsp;</td>
                    <td width="24%" height="23" align="center" valign="bottom"><?php echo $IM_RCIT;?>&nbsp;</td>
                  </tr>
              </table></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td height="23" colspan="3" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Razao_Social_UECF;?>&nbsp;</td>
                            <td height="23" colspan="2" align="center" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $CNPJ_UECF;?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="23" colspan="3" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Endereco_UECF;?>&nbsp;</td>
                            <td height="23" colspan="2" align="center" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;<?php echo $IE_UECF;?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="50%" height="23" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Municipio_UECF;?>&nbsp;</td>
                            <td width="10%" height="23" align="right" valign="bottom"><?php echo $UF_UECF;?>&nbsp; </td>
                      <td height="23" colspan="2" valign="bottom"><?php echo $CNAE_UECF;?>&nbsp;</td>
                            <td width="24%" height="23" valign="bottom"><?php echo $IM_UECF;?>&nbsp;</td>
                        </tr>
              </table></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="38%" height="23" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Marca_Equipamento;?>&nbsp;</td>
                          <td height="23" colspan="2" align="center" valign="bottom"><?php echo $Modelo_Equipamento;?>&nbsp;</td>
                            <td width="24%" height="23" align="center" valign="bottom"><?php echo $Numero_Ordem;?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="23" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;<?php echo $Numero_Fabricacao;?>&nbsp;</td>
                          <td height="23" colspan="2" align="center" valign="bottom"><?php echo $Versao_Encontrada;?>&nbsp;</td>
                            <td height="23" align="center" valign="bottom"><?php echo $Versao_Atual;?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="23" colspan="2" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Data_Inicio_Intervencao;?>&nbsp;</td>
                            <td height="23" colspan="2" align="center" valign="bottom"><?php echo $Data_Termino_Intervencao?></td>
                        </tr>
                        <tr>
                            <td height="23" colspan="2" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Numero_MFD_Retirada;?>&nbsp;</td>
                            <td height="23" colspan="2" align="center" valign="bottom"><?php echo $Numero_MFD_Colocada;?>&nbsp;</td>
                        </tr>
              </table></td>
            </tr>
            <tr>
                <td height="26">&nbsp;</td>
            </tr>
            <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                          <td width="29%" height="24" valign="bottom">&nbsp;</td>
                        <td width="38%" height="24" align="right" valign="bottom"><?php echo $geral_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="33%" height="24" align="right" valign="bottom"><?php echo $geral_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" valign="bottom">&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $venda_bruta_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $venda_bruta_dpi;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" valign="bottom">&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $venda_liquida_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $venda_liquida_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" valign="bottom">&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $cancelamento_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $cancelamento_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" valign="bottom">&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $desconto_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $desconto_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" valign="bottom">&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $subistituicao_tributaria_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $subistituicao_tributaria_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" valign="bottom">&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $isentas_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $isentas_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" valign="bottom">&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $nao_incidencia_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $nao_incidencia_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" align="right" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $tributado_1;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $tributado_1_val_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $tributado_1_val_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" align="right" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $tributado_2;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $tributado_2_val_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $tributado_2_val_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" align="right" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $tributado_3;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $tributado_3_val_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $tributado_3_val_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" align="right" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $tributado_4;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $tributado_4_val_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $tributado_4_val_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" align="right" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $tributado_5;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $tributado_5_val_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $tributado_5_val_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" align="right" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $tributado_6;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $tributado_6_val_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $tributado_6_val_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                           <tr>
                            <td height="24" align="right" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $tributado_7;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $tributado_7_val_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $tributado_7_val_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="16" valign="bottom">&nbsp; </td>
                          <td height="16" align="right" valign="bottom">&nbsp; &nbsp;</td>
                            <td height="16" align="right" valign="bottom">&nbsp; </td>
                        </tr>
                        <tr>
                          <td height="24" valign="bottom">&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $ordem_operacao_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $ordem_operacao_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" valign="bottom">&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $contador_reducoes_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $contador_reducoes_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" valign="bottom">&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $ordem_doc_fiscais_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $ordem_doc_fiscais_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" valign="bottom">&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $docs_cancelados_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $docs_cancelados_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                          <td height="24" valign="bottom">&nbsp;</td>
                          <td height="24" align="right" valign="bottom"><?php echo $contador_reinicio_operacao_ai;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td height="24" align="right" valign="bottom"><?php echo $contador_reinicio_operacao_api;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        </tr>
              </table></td>
          </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td height="23" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Numero_Lacre_NOVO;?>&nbsp;</td>
                            <td height="23" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Numero_Lacre_NOVO;?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="23" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Numero_Lacre_Externo_ECF_CNPJ_ANTIGO;?>&nbsp;</td>
                            <td height="23" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Numero_Lacre_Externo_ECF_CNPJ_NOVO;?>&nbsp;</td>
                        </tr>
              </table></td>
            </tr>
            <tr>
                <td height="15">&nbsp; </td>
            </tr>
            <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="16%" height="19" align="center" valign="bottom">&nbsp; <?php echo $Software_Basico_PCF;?>&nbsp;</td>
                            <td width="16%" height="19" align="right" valign="bottom">&nbsp;&nbsp;<?php echo $Cabo_PCF_MF_Lado_PCF;?>&nbsp;</td>
                          <td width="16%" height="19" align="right" valign="bottom"><?php echo $Cabo_PCF_MF_Lado_MF;?>&nbsp;</td>
                          <td width="30%" height="19" align="right" valign="bottom"><?php echo $PCF_Gabinete;?>&nbsp;</td>
                          <td width="22%" height="19" align="right" valign="bottom">&nbsp;<?php echo $Mem_Fita_Detalhe;?></td>
                  </tr>
                        <tr>
                          <td height="23" align="center" valign="bottom">&nbsp;  <?php echo $Software_Basico_PCF_Apos_Intervencao;?>&nbsp;</td>
                          <td height="23" align="right" valign="bottom"><?php echo $Cabo_PCF_MF_Lado_PCF_Apos_Intervencao;?>&nbsp;</td>
                          <td height="23" align="right" valign="bottom"><?php echo $Cabo_PCF_MF_Lado_MF_Apos_Intervencao;?>&nbsp;</td>
                          <td height="23" align="right" valign="bottom"><?php echo $PCF_Gabinete_Apos_Intervencao;?>&nbsp;</td>
                          <td height="23" align="right" valign="bottom">&nbsp;<?php echo $Mem_Fita_Detalhe_Apos_Intervencao;?></td>
                  </tr>
              </table></td>
            </tr>
            <tr>
                <td height="16">&nbsp;</td>
            </tr>
            <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="60%" height="23" align="center" valign="bottom">&nbsp;<?php echo $Nome_Credenciado;?>&nbsp;</td>
                          <td width="20%" height="23" align="center" valign="bottom">&nbsp;<?php echo $Numero_Atestado;?>&nbsp;</td>
                            <td width="20%" height="23" align="center" valign="bottom">&nbsp;<?php echo $Numero_Consulta;?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="27" colspan="3" valign="bottom">&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $Motivo_Intervencao_Discriminacao_Servico_Executado;?>&nbsp;</td>
                        </tr>
              </table></td>
            </tr>
            <tr>
                <td>&nbsp; </td>
            </tr>
            <tr>
                <td height="23">&nbsp; </td>
            </tr>
            <tr>
                <td height="23"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td height="23" colspan="2" valign="bottom">&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $Nome_Tecnico;?>&nbsp;</td>
                            <td height="23" valign="bottom">&nbsp;</td>
                  </tr>
                        <tr>
                          <td height="23" valign="bottom">&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $RG_Tecnico;?>&nbsp;</td>
                          <td height="23" valign="bottom">&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $CPF_Tecnico;?>&nbsp;</td>
                          <td height="23" align="center" valign="bottom"><?php echo $emissao;?>&nbsp;</td>
                  </tr>
              </table></td>
            </tr>
        </table>
</body>
</html>