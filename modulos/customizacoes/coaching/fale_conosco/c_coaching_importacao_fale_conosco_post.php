<?php
/*
* c_coaching_importacao_fale_conosco_post.php
* Autor: Alex
* 25/08/2011 10:47:01
*/
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=ISO-8859-1");

require('../../../../conecta.php');
require('../../../../functions.php');
require('../../../../classes/class.Pessoa.php');
$Erro = false;
$SqlFaleConosco = "SELECT * FROM c_coaching_fale_conosco WHERE sn_importado = 0 ORDER BY numreg ASC";
$QryFaleConosco = query($SqlFaleConosco);
                  
while($ArFaleConosco = farray($QryFaleConosco)){
    if($_POST['chk_fc_'.$ArFaleConosco['numreg']] != '1'){
        continue;
    }   
    
    $arContatoTreinamentos = array();
    if($_POST['edtid_usuario_resp_'.$ArFaleConosco['numreg']] == 146){
        $arContatoTreinamentos[$ArFaleConosco['numreg']] = $ArFaleConosco;
        continue;
    }
    
    if(
		(isset($_POST['edtid_pessoa_'.$ArFaleConosco['numreg']]) && $_POST['edtid_usuario_resp_'.$ArFaleConosco['numreg']] != '') 
		|| 
		(isset($_POST['edtid_pessoa_'.$ArFaleConosco['numreg']]) && $_POST['edtid_usuario_resp_hiden_'.$ArFaleConosco['numreg']] != '')
		){
                    $IdPessoaGerada = false;
	 	if(isset($_POST['edtid_usuario_resp_hiden_'.$ArFaleConosco['numreg']])){
			$IdUsuarioResp = $_POST['edtid_usuario_resp_hiden_'.$ArFaleConosco['numreg']];
		}else{
	   		$IdUsuarioResp = $_POST['edtid_usuario_resp_'.$ArFaleConosco['numreg']];
		}
		
	   if($_POST['edtid_pessoa_'.$ArFaleConosco['numreg']] == '' ){
            $RazaoSocialNome = (trim($ArFaleConosco['contato_nome']) == '')?$ArFaleConosco['contato_email']:$ArFaleConosco['contato_nome'];
            $Pessoa = new Pessoa(false);
            $Pessoa->setArDados('sn_prospect', '1');
            $Pessoa->setArDados('razao_social_nome', $RazaoSocialNome);
            $Pessoa->setArDados('id_tp_pessoa', '2');
            $Pessoa->setArDados('tel1', '('.$ArFaleConosco['contato_ddd'].')'.$ArFaleConosco['contato_tel']);
            $Pessoa->setArDados('id_origem_conta', $ArFaleConosco['contato_site']);
            $Pessoa->setArDados('id_tp_campanha', $ArFaleConosco['contato_pagina']);
            $Pessoa->setArDados('email', trim($ArFaleConosco['contato_email']));
            $Pessoa->setArDados('id_vendedor_padrao', $IdUsuarioResp);
            $Pessoa->setArDados('id_representante_padrao', $IdUsuarioResp);
            $Pessoa->setArDados('id_operador_padrao', $IdUsuarioResp);
            $Pessoa->setArDados('wcp_campanha_google', $ArFaleConosco['wcp_campanha_google']);
            $Pessoa->setArDados('wcp_grupo_google', $ArFaleConosco['wcp_grupo_google']);
            $InsertPessoa = $Pessoa->InserePessoaBD();
            if(!$InsertPessoa){
                $Erro = true;
                continue;
            }
            $IdPessoaGerada = $InsertPessoa;
            $sqlLog = "insert into is_log (id_cad,numreg_cadastro, dt_log, hr_log, id_usuario, operacao, texto_log) values 
										  ('pessoa','".$IdPessoaGerada."','".date("Y-m-d")."','".date("H:i")."','".$_SESSION['id_usuario']."','incluir', 
											'Prospect: => ".$RazaoSocialNome."
											 Dia e Hora: => ".date("d-m-Y H:i:s")."
											 Vendedor: => ".$IdUsuarioResp."
											')";
			$qryLog = query($sqlLog);	
        }else{
	   $SqlUpdatePessoa = "UPDATE is_pessoa SET id_vendedor_padrao=$IdUsuarioResp ,id_representante_padrao=$IdUsuarioResp ,id_operador_padrao=$IdUsuarioResp 
						   WHERE numreg='".$_POST['edtid_pessoa_'.$ArFaleConosco['numreg']]."'";
	   $QrySqlUpdatePessoa = query($SqlUpdatePessoa);                                                  
		}
        $IdPessoa = ($IdPessoaGerada === false)?$_POST['edtid_pessoa_'.$ArFaleConosco['numreg']]:$IdPessoaGerada;
        
        $ArSqlInsertAtividade = array(
            'id_tp_atividade'       => '1',
            'assunto'               => 'Fale conosco',
            'id_pessoa'             => $IdPessoa,
            'id_usuario_resp'       => $IdUsuarioResp,
            'id_tp_ativ_rec'        => '2',
            'id_situacao'           => '1',
            'dt_inicio'             => date("Y-m-d"),
            'dt_cadastro'           => date("Y-m-d"),
            'atend_id_forma_contato'=> $ArFaleConosco['contato_pagina'],
            'atend_id_origem'       => $ArFaleConosco['contato_site'],
            'obs'                   => 'Telefone: ('.$ArFaleConosco['contato_ddd'].')'.$ArFaleConosco['contato_tel'].' Mensagem: '.$ArFaleConosco['contato_obs']
        );
        $SqlInsertAtividade = AutoExecuteSql(TipoBancoDados, 'is_atividade', $ArSqlInsertAtividade, 'INSERT');
        $QryInsertAtividade = iquery($SqlInsertAtividade);
        if(!$QryInsertAtividade){
            $Erro = true;
        }
        else{
            query("UPDATE is_atividade SET id_atividade = '".$QryInsertAtividade."' WHERE numreg = '".$QryInsertAtividade."'");
            $ArSqlUpdateFaleConosco = array(
                'numreg' => $ArFaleConosco['numreg'],
                'sn_importado' => '1',
                'id_usuario_importacao' => $_SESSION['id_usuario'],
                'dt_importacao' => date("Y-m-d"),
                'hr_importacao' => date("H:i:s"),
                'id_atividade_gerada' => $QryInsertAtividade,
                'id_pessoa_gerada' => (($IdPessoaGerada !== false)?$IdPessoaGerada:'')
            );
            $SqlUpdateFaleConosco = AutoExecuteSql(TipoBancoDados, 'c_coaching_fale_conosco', $ArSqlUpdateFaleConosco, 'UPDATE', array('numreg'));
            query($SqlUpdateFaleConosco);
        }
    }
}

if(count($arContatoTreinamentos) > 0){
    // conecta com a kinghost
    if(!$con = mysql_connect('mysql.sbcoaching.com.br','sbcoaching','ALS0215')){
        echo mysql_error();exit;
        $Erro = true;
    }
    if(!$bd = mysql_select_db('sbcoaching')){
        echo mysql_error();exit;
        $Erro = true;
    }
}
foreach($arContatoTreinamentos as $numreg => $arContatoTreinamento){
    $contato_dt_inclusao = $arContatoTreinamento['contato_dt_inclusao'];
    $contato_nome = $arContatoTreinamento['contato_nome'];
    $contato_ddd = $arContatoTreinamento['contato_ddd'];
    $contato_tel = $arContatoTreinamento['contato_tel'];
    $contato_email = $arContatoTreinamento['contato_email'];
    $contato_obs = $arContatoTreinamento['contato_obs'];
    $contato_pagina = $arContatoTreinamento['contato_pagina'];
    $contato_site = $arContatoTreinamento['contato_site'];
    $wcp_campanha_google = $arContatoTreinamento['wcp_campanha_google'];
    $wcp_grupo_google = $arContatoTreinamento['wcp_grupo_google'];

    $sqlInsertContato = "INSERT INTO `contato_fale_conosco`
        (`contato_dt_inclusao`,
        `contato_nome`,
        `contato_ddd`,
        `contato_tel`,
        `contato_email`,
        `contato_obs`,
        `contato_pagina`,
        `contato_site`,
        `wcp_campanha_google`,
        `wcp_grupo_google`
)
    VALUES
        ('$contato_dt_inclusao',
        '$contato_nome',
        '$contato_ddd',
        '$contato_tel',
        '$contato_email',
        '$contato_obs',
        '$contato_pagina',
        '$contato_site',
        '$wcp_campanha_google',
        '$wcp_grupo_google'
    )";
    
    if(!$qryInsertContato = mysql_query($sqlInsertContato)){
        echo mysql_error();exit;
        $Erro = true;
    }    
    $arConfirma[] = $numreg;
}

if(count($arConfirma) > 0){
    require('../../../../conecta.php');
    foreach($arConfirma as $numreg){
        $sqlUpdateContato = "
                        UPDATE `c_coaching_fale_conosco`
                        SET
                        `sn_importado` = 1,
                        `id_usuario_importacao` = ".$_SESSION['id_usuario'].",
                        `dt_importacao` = '".date("Y-m-d")."',
                        `hr_importacao` = '".date("H:i:s")."'
                        WHERE numreg = ".$numreg;       
    }
    if(!$qryUpdateContato = mysql_query($sqlUpdateContato)){
        echo mysql_error();exit;
        $Erro = true;
    }
}

if($Erro === true){
    echo alert("Os registros foram importados, porém houveram erros ao gerar alguns registros (estes não foram importados).",true);
    echo windowlocationhref('c_coaching_tela_importacao_fale_conosco.php',true);
}
else{
    echo alert("Os registros foram importados com sucesso!",true);
    echo windowlocationhref('c_coaching_tela_importacao_fale_conosco.php',true);
}
?>