<?php
/*
 * gera_atividade_laboratorio.php
 * Autor: Rodrigo Piva
 * 02/08/2011 11:00
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

include_once('../../conecta.php');
include_once('../../functions.php');
require('functions_laboratorio.php');

$QtdAtualizados = 0;
$QtdGerados = 0;

$SqlNfEntrada = "SELECT *
                 FROM is_produto_nf_entrada
                 WHERE sn_integrado = '0'";
$QryNfEntrada = query($SqlNfEntrada);
while($ArNfEntrada = farray($QryNfEntrada)){

    $SqlAtividade = "SELECT *
                     FROM is_atividade
                     WHERE nr_nota = '".$ArNfEntrada['id_nota_fiscal']."'
                     AND   id_produto = '".$ArNfEntrada['id_produto']."'
                     AND   id_pessoa  = '".$ArNfEntrada['id_pessoa']."'
                     AND   id_tp_atividade = '".getParametroLab('id_tp_atividade_laboratorio')."'
                     ";
    $QryAtividade = query($SqlAtividade);
    $QuantidadeDeAtividades = numrows($QryAtividade);

    /*verificar se o produto gera multiplos atendimento*/
    $SqlEMultiplosAtendimentos = "select * from is_produto_lab where id_produto = '".$ArNfEntrada['id_produto']."'";
    $QryEMultiplosAtendimentos = query($SqlEMultiplosAtendimentos);
    $ArEMultiplosAtendimentos = farray($QryEMultiplosAtendimentos);

    if($ArEMultiplosAtendimentos['sn_gera_mutiplos_atendimentos'] == '0'){
        $QtdeAtendimentos = 1;
        $qtde_produto_lab = $ArNfEntrada['quantidade'];
    }
    else{
        $QtdeAtendimentos = $ArNfEntrada['quantidade'];
        $qtde_produto_lab = 1;
    }

    if($QuantidadeDeAtividades > 0){
        while( $ArAtividade = farray($QryAtividade)){
            $ArUpdate = array(
                        'numreg'            => $ArAtividade['numreg'],
                        'nr_nota'           => $ArNfEntrada['id_nota_fiscal'],
                        'dt_nota'           => $ArNfEntrada['dt_emissao'],
                        'nr_serie'          => $ArNfEntrada['serie'],
                        'id_produto'        => $ArNfEntrada['id_produto'],
                        'qtde'              => $qtde_produto_lab,
                        'sn_atualizado'     => $ArNfEntrada['sn_atualizado']
            );
            $Sql = AutoExecuteSql(TipoBancoDados,'is_atividade', $ArUpdate, 'UPDATE', array('numreg'));
            $qry = query($Sql);
            if (!$qry) {
                echo $SqlInsert;
            }else{
                $QtdAtualizados++;
            }
        }
    }else{
        for( $i = 1; $i <= $QtdeAtendimentos; $i++){

            // Calcula e atualiza último protocolo do SAC, CRC, Help Desk e O.S para exibir no momento da inclusão.
            $tipo_protocolo = 'LABORATORIO';
            $a_param_ultimo_protocolo = farray(query("select parametro from is_parametros_sistema where id_parametro = 'ULTIMO_PROTOCOLO_" . $tipo_protocolo . "'"));
            $ultimo_protocolo = ($a_param_ultimo_protocolo["parametro"]+1);
            query("update is_parametros_sistema set parametro = '" . $ultimo_protocolo . "' where id_parametro = 'ULTIMO_PROTOCOLO_" . $tipo_protocolo . "'");

            $IdProdErp = deparaIdErpCrm($ArNfEntrada['id_produto'],'id_produto_erp','numreg','is_produto');
            $ArInsert = array(
                        'id_atividade'      => $ultimo_protocolo,
                        'id_tp_atividade'   => getParametroLab('id_tp_atividade_laboratorio'),
                        'assunto'           => getParametroLab('assunto_atividade_lab'),
                        'id_pessoa'         => $ArNfEntrada['id_pessoa'],
                        'id_usuario_resp'   => getParametroLab('id_usuario_triagem'),
                        'dt_inicio'         => date("Y-m-d"),
                        'hr_inicio'         => '',
                        'dthr_inicio'       => date("Y-m-d H:i:s"),
                        'dt_prev_fim'       => date("Y-m-d",strtotime('+'.getParametroLab('prazo_atividade').' days')),
                        'hr_prev_fim'       => '',
                        'dthr_prev_fim'     => '',
                        'dt_real_fim'       => '',
                        'hr_real_fim'       => '',
                        'id_situacao'       => '1',
                        'nr_nota'           => $ArNfEntrada['id_nota_fiscal'],
                        'dt_nota'           => $ArNfEntrada['dt_emissao'],
                        'nr_serie'          => $ArNfEntrada['serie'],
                        'id_status_reparo'  => '1',
                        'id_produto'        => $ArNfEntrada['id_produto'],
                        'qtde'              => $qtde_produto_lab,
                        'id_tp_atendimento' => '',
                        'id_estoque'        => $ArNfEntrada['id_estoque'],
                        'sn_atualizado'     => $ArNfEntrada['sn_atualizado'],
                        'id_produto_erp'    => $IdProdErp
            );

            $Sql = AutoExecuteSql(TipoBancoDados,'is_atividade', $ArInsert, 'INSERT');
            $qry = query($Sql);
            if (!$qry) {
                echo $SqlInsert;
            }else{
                $QtdGerados++;
            }
        }
    }
	$ArAtualizaIntegracao = array( 'numreg'        => $ArNfEntrada['numreg'],
                               'sn_integrado' => '1');
	$Sql = AutoExecuteSql(TipoBancoDados,'is_produto_nf_entrada', $ArAtualizaIntegracao, 'UPDATE', array('numreg'));
	$qry = query($Sql);
	if (!$qry) {
		echo $SqlInsert;
	}
}

echo "Quantidade de registros gerados: ".$QtdGerados;
echo "Quantidade de registros atualizados".$QtdAtualizados;

?>
