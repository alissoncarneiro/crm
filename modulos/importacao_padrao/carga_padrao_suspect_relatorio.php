<?php
@header("Content-Type: text/html;  charset=ISO-8859-1",true);
set_time_limit(0);
ini_set('upload_max_filesize','64M');
if($_POST && $_FILES && (isset($_POST['edtconfirmacao']) && $_POST['edtconfirmacao'] == 'on')){
    $mimetype = $_FILES['edtarquivo']['type'];
    if($mimetype != 'application/vnd.ms-excel' && $mimetype != 'text/csv'){
        echo '<span>O arquivo deve ser um CSV de Excel v&aacute;lido. => '.$_FILES['edtarquivo']['type'].'</span>';
        exit;
    }
    include('../../conecta.php');
    include('../../functions.php');
    include('../../classes/class.ImportacaoModeloCSV.php');

    query("delete from is_suspect_importacao");

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
    $CSV->EfetuaImportacaoDireta = 0;
    $CSV->EfetuaImportacaoSemCSV = 0;
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

    $CSV->ImportaDados();

    $RetornoErros = $CSV->getRelatorioErro();
?>
<html>
    <head>
        <title>Importação de Suspects</title>
    </head>
    <body>
        <h1>Registros que não serão importados.</h1>
    <table border="0" width="100%" cellspacing="2" cellpadding="2" id="minhatabela">
        <thead>
            <tr bgcolor="#DAE8F4">
                <th>Raz&atilde;o Social / Nome</th>
                <th>Em Branco</th>
                <th>Duplicidade</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($RetornoErros as $k => $v){?>
                <tr>
                    <td><?php echo $k;?></td>
                    <td align="center"><?php echo (count($v[0])>0)?implode(', ',$v[0]):'';?></td>
                    <td align="center"><?php echo (count($v[1])>0)?implode(', ',$v[1]):'';?><!--<img src="../../images/btn_del.PNG" />--></td>
                </tr>
            <?php
            }?>
                <tr>
                    <th colspan="3">Total de clientes que não serão importados: <?php echo str_pad(count($RetornoErros), 2, 0, 'STR_PAD_LEFT');?></th>
                </tr>
        </tbody>
    </table>
    <hr size="1"/>
    <input name="Submit" type="button" id="btn_confirmar" class="botao_form" value="Confirmar Importa&ccedil;&atilde;o" />
    <hr size="1"/>
    <script>zebra('minhatabela','zb');</script>
    <script language="javascript">
        $(document).ready(function(){
            $('#btn_confirmar').click(function() {
              $('#edtconfirmacao').val('true');
              $('#form_carga').submit();
            });
        });
    </script>
    </body>
</html>
<?php
}?>