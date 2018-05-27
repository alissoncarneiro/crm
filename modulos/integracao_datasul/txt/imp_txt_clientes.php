<?php
header('Content-Type: text/html; charset=iso-8859-1');
include('../../../conecta.php');
include('../../../functions.php');
include('../../../funcoes.php');
include('../../../classes/class.txtimport.php');
$importa = new txtimport;

$importa->ProcuraArquivo('emitente');
$importa->TabelaName('is_pessoa');

$ar_chave = array('cnpj_cpf', 'id_pessoa_erp');

$importa->troca_valor_fixo = array('id_vendedor_padrao'=>'id_representante_padrao');

$importa->SetArrayChaves($ar_chave);

$importa->tratamento_especial = array('razao_social_nome', 'endereco');

$importa->tratamento_float = array('vl_limite_credito');

$importa->trata_data = array('dt_cadastro');

$ar_nega['tp_identific']['vl_valido'] = '2';
$ar_nega['tp_identific']['importa_info'] = '0';
$importa->nega_importacao_inverso = $ar_nega;

$importa->Getnumreg = array(
                            'id_transportadora_padrao' 	=>'SELECT numreg FROM is_transportadora WHERE id_transportadora_erp = \'!id_transportadora_padrao!\'',
                            'id_representante_padrao' 	=>'SELECT numreg FROM is_usuario WHERE id_representante = \'!id_representante_padrao!\'',
                            'id_grupo_cliente'		=>'SELECT numreg FROM is_grupo_cliente WHERE id_grupo_cliente_erp = \'!id_grupo_cliente!\'',
                            'id_tab_preco_padrao'	=>'SELECT numreg FROM is_tab_preco WHERE id_tab_preco_erp = \'!id_tab_preco_padrao!\'',
                            'id_ramo_atividade'		=>'SELECT numreg FROM is_grupo_cliente WHERE id_grupo_cliente_erp = \'!id_ramo_atividade!\''
                    );

$importa->sim_nao = array('sn_contribuinte_icms');

$ar_campos = array(
                    'data-implant' 		=> 'dt_cadastro',
                    'cod-emitente' 		=> 'id_pessoa_erp',
                    'nome-emit' 		=> 'razao_social_nome',
                    'cgc' 			=> 'cnpj_cpf',
                    'ins-estadual' 		=> 'ie_rg',
                    'e-mail' 			=> 'email',
                    'endereco' 			=> 'endereco',
                    'bairro' 			=> 'bairro',
                    'cidade' 			=> 'cidade',
                    'estado' 			=> 'uf',
                    'pais' 			=> 'pais',
                    'cep' 			=> 'cep',
                    'observacoes' 		=> 'obs',
                    'natureza' 			=> 'id_tp_pessoa',
                    'nome-abrev'		=> 'fantasia_apelido',
                    'atividade' 		=> 'id_ramo_atividade',
                    'cod-rep' 			=> 'id_representante_padrao',
                    'home-page' 		=> 'site',
                    'telefone[1]'		=> 'tel1',
                    'telefax' 			=> 'fax',
                    //'nome-abrev' 		=> 'nome_abreviado',
                    'cod-gr-cli' 		=> 'id_grupo_cliente',
                    'nr-tabpre' 		=> 'id_tab_preco_padrao',
                    'nat-operacao'		=> 'cfop_estadual_padrao',
                    'nat-ope-ext'		=> 'cfop_interestadual_padrao',
                    'lim-credito'		=> 'vl_limite_credito',
                    //'ind-cre-cli'		=> 'id_sit_cred',
                    'nr-titulo'			=> 'qtde_max_titulos_em_atraso',
                    //'nr-dias-atraso'          => 'maior_qt_dias_atraso',
                    //'contato[1]' 		=> 'nome_pessoa_contato',
                    'contrib-icms'              => 'sn_contribuinte_icms',
                    'cod-suframa'               => 'cod_suframa',
                    'ind-cre-cli' 		=> 'sn_ativo',
                    'cod-transp'  		=> 'id_transportadora_padrao',
                    'identific'                 => 'tp_identific',
		);
$ar_default =	array(
                    'sn_cliente'            => '1',
                    'sn_prospect'           => '0',
                    'sn_suspect'            => '0',
                    'sn_concorrente'        => '0',
                    'sn_parceiro'           => '0',
                    'sn_fornecedor'         => '0',
                    'sn_representante'      => '0',
                    'sn_grupo_inadimplente' => '0',
                    'sn_contato'            => '0',
                    'sn_inadimplente'       => '0',
                    'sn_importado_erp'      => '1',
                    'sn_exportado_erp'      => '1',
                    'sn_socio'              => '0',
                    'sn_contato_principal'  => '0',
                    'qtde_min_validade_produto_dias'	=> '0'
				);
$importa->SetArrayDefault($ar_default);
//$importa->NewFieldName('id_pessoa');//CASO PRECISE INSERIR UM ID (AUTO INCREMENTO) AO REGISTRO
$importa->ImportaDados($ar_campos);
$importa->ShowResult();
?>