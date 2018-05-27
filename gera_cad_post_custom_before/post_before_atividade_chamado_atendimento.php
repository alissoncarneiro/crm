<?php
/*
 * post_before_atividade_chamado_atendimento.php
 * Autor: Alex
 * 02/08/2012 10:10:24
 */
if($id_funcao == 'atividade_chamado_atendimento'){
    if($opc == 'excluir' || $opc == 'incluir'){
        echo 'Não é permitido '.$opc.'!';
        exit;
    }
    else{
        $DtHrInicio = trim($_POST['edtdthr_inicio']);
        $DtHrFim = trim($_POST['edtdthr_fim']);

        if($DtHrInicio == $DtHrFim){
            echo 'Data de Início deve ser diferente da data de fim!';
            exit;
        }

        $TsDtHrInicio = strtotime($DtHrInicio);
        $TsDtHrFim = strtotime($DtHrFim);
        $DiferencaSec = $TsDtHrFim - $TsDtHrInicio;
        $DiferencaHr = floor($DiferencaSec / 3600);
        $DiferencaSec -= $DiferencaHr * 3600;
        $DiferencaMin = floor($DiferencaSec / 60);
        $DiferencaSec -= $DiferencaMin * 60;

        $_POST['edttempo_gasto'] = sprintf('%02d:%02d:%02d', $DiferencaHr, $DiferencaMin, $DiferencaSec);
        $_POST['edtsn_finalizado'] = 1;

        CarregaClasse('RegistroOasis', 'classes/class.RegistroOasis.php');
        CarregaClasse('Chamado', 'classes/class.Chamado.php');
        CarregaClasse('ChamadoResposta', 'classes/class.ChamadoResposta.php');
        CarregaClasse('PortalParametro', 'classes/class.PortalParametro.php');
        CarregaClasse('DataHora', 'classes/class.DataHora.php');
        $Chamado = new Chamado($_POST['edtid_atividade']);
        $Chamado->AdicionaLog('Corrigido manualmente horário de atendimento '.uB::DataEn2Br($DtHrInicio,true).' - '.uB::DataEn2Br($DtHrFim,true));
        $Chamado->AtualizaTotalTempoGastoAtendimentoBD();
    }
}
?>