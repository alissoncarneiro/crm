<?php
# functions_laboratorio
# Expression package is undefined on line 3, column 5 in Templates/Scripting/EmptyPHP.php.
# Autor: Rodrigo Piva
# 16/08/2011
#
# Log de Alterações
# yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
#

function getUsuarioResponsavel($id_status_reparo){
    $SqlUsuario = "SELECT * FROM is_usuario_status_lab WHERE id_status_reparo = '".$id_status_reparo."'";
    $QryUsuario = query($SqlUsuario);
    $ArUsuario = farray($QryUsuario);

    return $ArUsuario['id_usuario'];
}

function criaAtividade($id_usuario,$id_tp_atividade,$id_atividade_pai,$id_pessoa,$id_pessoa_contato,$assunto,$obs,$prazo){
    $ArInsert = array( 'id_usuario_resp'    => $id_usuario,
                       'id_tp_atividade'    => $id_tp_atividade,
                       'id_atividade_pai'   => $id_atividade_pai,
                       'id_pessoa'          => $id_pessoa,
                       'dt_inicio'          => date("Y-m-d"),
                       'hr_inicio'          => date("H:i"),
                       'dt_prev_fim'        => date("Y-m-d",strtotime('+'.$prazo.' days')),
                       'id_pessoa_contato'  => $id_pessoa_contato,
                       'assunto'            => $assunto,
                       'obs'                => $obs

                      );
    $Sql = AutoExecuteSql(TipoBancoDados,'is_atividade', $ArInsert, 'INSERT');
    $qry = iquery($Sql);
    if (!$qry) {
        echo $SqlInsert;
    }
    echo alert('Atividade '.$qry.' gerada');

}

function envia_email($EmailDestino,$Assunto,$Texto){
    $email = new Email();
    $email->_AdicionaDestinatario($EmailDestino);
    $email->_Assunto($Assunto);
    $email->_Corpo($Texto);
    $email->_EnviaEmail();
 }

function getParametroLab($coluna_parametro){
    $SqlParametro = "select * from is_parametros_lab where numreg = 1";
    $QryParametro = query($SqlParametro);
    $ArParametro = farray($QryParametro);
    return $ArParametro[$coluna_parametro];
}


?>
