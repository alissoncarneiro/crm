<?php
@header("Content-Type: text/html;  charset=ISO-8859-1",true);
set_time_limit(0);
ini_set('upload_max_filesize','64M');
include('../../conecta.php');
    include('../../functions.php');
    include('../../classes/class.ImportacaoModeloCSV.php');
    $obrigatorio = array('razao_social_nome','id_mailing');
    if($_POST['edtchktelefone_obrigatorio'] == 'on'){
        $obrigatorio[] = 'tel1';
    }

    if($_POST['edtchkrazao_social_cnpj_cpf_obrigatorio']){
        $obrigatorio[] = 'cnpj_cpf';
    }

    $duplicado = array('cnpj_cpf','id_mailing');

    if(!isset($_POST['edtchkrazao_social_nome_duplicidade'])){
        $duplicado[] = 'razao_social_nome';
    }
    /*if(!isset($_POST['edtchkemail_duplicidade'])){
        $duplicado[] = 'razao_social_nome';
    }*/
    if(!isset($_POST['edtchktelefone_duplicidade'])){
        $duplicado[] = 'tel1';
    }
    copy($_FILES['edtarquivo']['tmp_name'],'../'.date('dmY').'.csv');
    $CSV = new ImportacaoModeloCSV();
    $CSV->ArquivoCSV = '../'.date('dmY').'.csv';
    $CSV->SnPrintrArray = 0;
    $CSV->ArCampoChave = array('id_mailing');
    $CSV->TabelaImportacao = 'is_suspect_importacao';
    $CSV->ArCampoImportacao = array('id_mailing',
                                    'id_tp_pessoa',
                                    'razao_social_nome',
                                    'fantasia_apelido',
                                    'cnpj_cpf',
                                    'ie_rg',
                                    'id_segmento',
                                    'id_ramo_atividade',
                                    'tel1',
                                    'tel2',
                                    'fax',
                                    'site',
                                    'cep',
                                    'endereco',
                                    'numero',
                                    'complemento',
                                    'bairro',
                                    'cidade',
                                    'uf',
                                    'pais',
                                    'id_regiao',
                                    'obs',
                                    'id_vendedor_padrao',
                                    'id_representante_padrao',
                                    'id_operador_padrao',
                                    'id_origem_conta',
                                    'email',
                                    'qtde_func_filhos',
                                    'nome_cont1',
                                    'tel_cont11',
                                    'tel_cont12',
                                    'email_cont1',
                                    'nome_cont2',
                                    'tel_cont21',
                                    'tel_cont22',
                                    'email_cont2',
                                    'nome_cont3',
                                    'tel_cont31',
                                    'tel_cont32',
                                    'email_cont3');
    $CSV->ArCamposObrigatorios = $obrigatorio;
    $CSV->ArCamposDuplicados = $duplicado;
    $CSV->TabelaValidacao = 'is_pessoa';
    $CSV->CampoRelatorio = 'razao_social_nome';


    $CSV->QuebraArrayEm = 3;
    $CSV->ValoresQuebraArray = array(28,32,36);
    $CSV->ArTabelaQuebra[0] = 'is_contato';
    $CSV->ArTabelaQuebra[1] = 'is_contato';
    $CSV->ArTabelaQuebra[2] = 'is_contato';
    $CSV->ArQuebraCampos[0] = array('nome','tel1','tel2','email_profissional');
    $CSV->ArQuebraCampos[1] = array('nome','tel1','tel2','email_profissional');
    $CSV->ArQuebraCampos[2] = array('nome','tel1','tel2','email_profissional');
    $CSV->ArQuebraCamposObrigatorio[0] = array('nome');
    $CSV->ArQuebraCamposObrigatorio[1] = array('nome');
    $CSV->ArQuebraCamposObrigatorio[2] = array('nome');
    $CSV->ArQuebraCamposExtra[0] = array('dt_cadastro'=>date('Y-m-d'));
    $CSV->ArQuebraCamposExtra[1] = array('dt_cadastro'=>date('Y-m-d'));
    $CSV->ArQuebraCamposExtra[2] = array('dt_cadastro'=>date('Y-m-d'));

    $CSV->CampoChaveQuebra = 'id_empresa';


    $CSV->ArCamposExtra = array('sn_suspect'=>1,
                                     'sn_cliente'=>0,
                                     'sn_prospect'=>0,
                                     'sn_consumidor_final'=>0,
                                     'sn_inadimplente'=>0,
                                     'sn_contato'=>0,
                                     'sn_grupo_inadimplente'=>0,
                                     'sn_representante'=>0,
                                     'sn_fornecedor'=>0,
                                     'sn_parceiro'=>0,
                                     'sn_concorrente'=>0,
                                     'dt_cadastro'=>date('Y-m-d')
                                    );
    $CSV->ArTroca[1]['J'] = 1;
    $CSV->ArTroca[1]['F'] = 2;

    $CSV->ArBusca[6][] = 'is_segmento';
    $CSV->ArBusca[6][] = 'numreg';
    $CSV->ArBusca[6][] = 'nome_segmento';

    $CSV->ArBusca[7][] = 'is_ramo';
    $CSV->ArBusca[7][] = 'numreg';
    $CSV->ArBusca[7][] = 'nome_ramo';

    $CSV->ArBusca[20][] = 'is_regiao';
    $CSV->ArBusca[20][] = 'numreg';
    $CSV->ArBusca[20][] = 'nome_regiao';

    $CSV->ArBusca[22][] = 'is_usuario';
    $CSV->ArBusca[22][] = 'numreg';
    $CSV->ArBusca[22][] = 'id_usuario';

    $CSV->ArBusca[23][] = 'is_usuario';
    $CSV->ArBusca[23][] = 'numreg';
    $CSV->ArBusca[23][] = 'id_usuario';

    $CSV->ArBusca[24][] = 'is_usuario';
    $CSV->ArBusca[24][] = 'numreg';
    $CSV->ArBusca[24][] = 'id_usuario';

    $CSV->ArBusca[25][] = 'is_origem_conta';
    $CSV->ArBusca[25][] = 'numreg';
    $CSV->ArBusca[25][] = 'nome_origem_conta';
    $RetornoErros = $CSV->getRelatorioErro();
if(isset($_POST['edtconfirmacao']) && $_POST['edtconfirmacao'] == 'true'){
            //echo 'Rodar query normal validando para importação sn_importa = 1<hr>';
            $CSV->EfetuaImportacaoDireta = 1;
            $CSV->EfetuaImportacaoSemCSV = 1;
        } else {
            $mimetype = $_FILES['edtarquivo']['type'];
            if($mimetype != 'application/vnd.ms-excel' && $mimetype != 'text/csv'){
                echo '<span>O arquivo deve ser um CSV de Excel v&aacute;lido. => '.$_FILES['edtarquivo']['type'].'</span>';
                exit;
            }
            $CSV->EfetuaImportacaoDireta = 1;
            $CSV->EfetuaImportacaoSemCSV = 0;
        }
$CSV->ImportaDados();
$Resultado = $CSV->getResultadoDaImportacao();

echo '<p>Total de Registros: <strong>'.$Resultado['TotalDeRegistos'].'</strong></p>
<p>Foram Importados: <strong>'.$Resultado['RegistrosInseridos'].'</strong></p>
<p>Não Foram Importados: <strong>'.$Resultado['RegistrosNaoInseridos'].'</strong></p>';
?>