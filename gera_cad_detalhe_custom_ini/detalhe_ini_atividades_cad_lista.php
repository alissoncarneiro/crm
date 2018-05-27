<?php

if(!class_exists('VendaParametro')){
    require_once('modulos/venda/classes/class.Venda.Parametro.php');
}else{
 $VendaParametro = new VendaParametro();
}



if($_GET['pfuncao'] == 'atividades_cad_lista'){
    require_once('conecta.php');
    $SqlAtividade = "SELECT id_tp_atividade, id_formulario_workflow FROM is_atividade WHERE numreg = '" . $_GET['pnumreg'] . "'";
    $QryAtividade = query($SqlAtividade);
    $ArAtividade = farray($QryAtividade);
    $TipoAtividade = $ArAtividade['id_tp_atividade'];

    if($TipoAtividade == '1'){/* Atendimento */
        $_GET['pfuncao'] = 'assist_cad_chamados';
    }
    elseif($TipoAtividade == '3'){/* Visita Comercial */
        $_GET['pfuncao'] = 'visitas_cad_lista';
    }
    elseif($TipoAtividade == '4'){/* Visita Técnica */
        $_GET['pfuncao'] = 'visitastec_cad_lista';
    }
    elseif($TipoAtividade == '5'){/* Telemarketing */
        $_GET['pfuncao'] = 'telemarketing_cad';
    }
    elseif($TipoAtividade == '8'){/* Telecobrança */
        $_GET['pfuncao'] = 'cobrancas_cad';
    } 
    elseif($TipoAtividade == '9'){/* Televendas */
        $_GET['pfuncao'] = 'televendas_cad';
    }
    elseif($TipoAtividade == '19'){/* Assistência Técnica */
        $_GET['pfuncao'] = 'chamado_atec';
    }
    elseif($TipoAtividade == '20'){/* CRC - Ouvidoria */
        $_GET['pfuncao'] = 'is_ativ_crc';
    }
    elseif($TipoAtividade == '21'){/* Ações ATEC (Serviços Internos) */
        $_GET['pfuncao'] = 'acoes_atec';
    }
    elseif($TipoAtividade == '22'){/* Help Desk (Suporte) */
        $_GET['pfuncao'] = 'is_help_desk';

    }
    elseif($TipoAtividade == '13' && $VendaParametro->getSnControlaAtividade()){/* Envio de Orçamento */
        $_GET['pfuncao'] = 'atividadexorcamento';
    }
    elseif($TipoAtividade == '23' && $VendaParametro->getSnControlaAtividade()){/* Envio de Orçamento */
        $_GET['pfuncao'] = 'atividadexorcamento';
    }
     elseif($ArAtividade['id_formulario_workflow']){/* Workflow Avancado */
        $_GET['pfuncao'] = $ArAtividade['id_formulario_workflow'];
    }
    elseif($TipoAtividade == '55'){/* Envio de Orçamento */
        $_GET['pfuncao'] = 'chamado_atec_lab';
    }    
}