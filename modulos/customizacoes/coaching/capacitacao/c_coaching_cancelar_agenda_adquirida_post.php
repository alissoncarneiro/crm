<?php
/*
 * c_coaching_cancelar_agenda_adquirida_post.php
 * Autor: Alex
 * 29/11/2011 16:04:51
 */
session_start();
header("Content-Type: text/html; charset=ISO-8859-1");

require('../../../../conecta.php');
require('../../../../functions.php');

$XML = '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.'>
    <resposta>
        <status>{!STATUS!}</status>
            <acao>{!ACAO!}</acao>
            <url><![CDATA[{!URL!}]]></url>
            <mensagem><![CDATA[{!MENSAGEM!}]]></mensagem>
    </resposta>
';

$ArUpdate = array(
    'numreg'                    => $_POST['numreg_inscricao_curso'],
    'id_situacao'               => '3',
    'id_motivo_desistencia'     => $_POST['edtc_id_motivo_desistencia'],
    'id_usuario_desistencia'    => $_SESSION['id_usuario'],
    'id_tipo_desistencia'       => $_POST['edtc_id_tipo_desistencia'],
    'vl_devolucao'              => TrataFloatPost($_POST['edtc_vl_devolucao']),
    'obs_desistencia'           => urldecode($_POST['edtpagto_obs']),
    'dt_desistencia'            => dtbr2en($_POST['edtc_dt_desistencia'])
);

$SqlArUpdate = AutoExecuteSql(TipoBancoDados, 'c_coaching_inscricao_curso', $ArUpdate, 'UPDATE', array('numreg'));
if(query($SqlArUpdate)){
    /* Deletando agendas programadas */
    $SqlDeleteAgendas = "DELETE FROM c_coaching_inscricao_curso_detalhe WHERE id_inscricao = '".$_POST['id_inscricao']."' AND id_agenda = '".$_POST['id_agenda_curso']."'";
    query($SqlDeleteAgendas);

    $SqlAgendaCurso = "SELECT t1.id_pessoa,t2.nome_curso 
                        FROM 
                            c_coaching_inscricao_curso t1
                        INNER JOIN 
                            c_coaching_curso t2 ON t1.id_curso = t2.numreg 
                        WHERE 
                            t1.numreg = '".$_POST['numreg_inscricao_curso']."'";
    $QryAgendaCurso = query($SqlAgendaCurso);
    $ArAgendaCurso = farray($QryAgendaCurso);
    
    
    if($_POST['edtc_id_tipo_desistencia'] == '1'){
        /* Criando atividade */
        $ArInsertAtividade = array(
            'id_tp_atividade'   => '7',
            'id_usuario_resp'   => GetParam('C_COACHING_USU_RESP_ATIV_DESIST'),
            'assunto'           => 'Desistência',
            'id_pessoa'         => $ArAgendaCurso['id_pessoa'],
            'dt_inicio'         => date("Y-m-d"),
            'hr_inicio'         => date("H:i"),
            'dt_prev_fim'       => date("Y-m-d"),
            'hr_prev_fim'       => date("H:i"),
            'id_situacao'       => '1',
            'id_usuario_cad'    => $_SESSION['id_usuario'],
            'dt_cadastro'       => date("Y-m-d"),
            'hr_cadastro'       => date("H:i"),
            'obs'               => 'Curso: '.$ArAgendaCurso['nome_curso']."\r\n".'Valor Devolução: '.number_format(TrataFloatPost($_POST['edtc_vl_devolucao']),2,',','.')
        );

        $SqlInsertAtividade = AutoExecuteSql(TipoBancoDados, 'is_atividade', $ArInsertAtividade, 'INSERT');
        query($SqlInsertAtividade);
    }
    
    $Status = 1;
    $Mensagem = 'Agenda cancelada com sucesso!';
}
else{
    $Status = 2;
    $Mensagem = 'Erro ao cancelar agenda. Por favor tente novamente.';
}

$XML = str_replace('{!STATUS!}',$Status,$XML);
$XML = str_replace('{!ACAO!}',$Acao,$XML);
$XML = str_replace('{!MENSAGEM!}',$Mensagem,$XML);
header("Content-Type: text/xml");
echo $XML;
?>