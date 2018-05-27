<?php
/*
 * cores_custom_portal.php
 * Autor: Alex
 * 24/01/2012 13:45:21
 */
if($pfuncao == 'chamado_portal'){
    global $chamado_portal_NumregAtual;
    global $chamado_portal_lista_color;
    if($chamado_portal_NumregAtual != $lista_qry_cadastro['numreg']){
        $chamado_portal_NumregAtual = $lista_qry_cadastro['numreg'];
        
        global $TempoDecorridoAberturaChamado;
        global $MinutosDecorridosAberturaChamado;

        $SqlSLA = "SELECT t2.qtde_horas_prz FROM is_pessoa t1 INNER JOIN is_prioridade t2 ON t1.id_prioridade = t2.numreg WHERE t1.numreg = '".$lista_qry_cadastro['id_pessoa']."'";
        $QrySLA = query($SqlSLA);
        $ArSLA = farray($QrySLA);
        $PrazoSLA = $ArSLA['qtde_horas_prz'];
        $MinutosSLA = $PrazoSLA * 60;
        if($lista_qry_cadastro['id_situacao'] == '4'){
            $chamado_portal_lista_color = 'bgcolor="#CCCCCC"';
        }
        else{
            if($lista_qry_cadastro['sn_em_atendimento'] == '1'){
                $chamado_portal_lista_color = 'bgcolor="#E4F5FB"';
                $TempoDecorridoAberturaChamado = '-';
            }
            elseif($lista_qry_cadastro['dthr_atendido'] == ''){
                CarregaClasse('RegistroOasis', 'classes/class.RegistroOasis.php');
                CarregaClasse('Chamado', 'classes/class.Chamado.php');
                CarregaClasse('DataHora', 'classes/class.DataHora.php');
                CarregaClasse('CalendarioData', 'classes/class.CalendarioData.php');
                $DataHora = new DataHora();
                $MinutosDecorridosAberturaChamado = Chamado::getTempoDecorridoAbertura($lista_qry_cadastro['numreg']);
                $TempoDecorridoAberturaChamado = $DataHora->MinutosParaHoras($MinutosDecorridosAberturaChamado);
                if($MinutosDecorridosAberturaChamado > $MinutosSLA){
                    $chamado_portal_lista_color = 'bgcolor="#FF0000"';
                }
            }
            else{
                $TempoDecorridoAberturaChamado = '-';
            }
        }
    }
    $lista_color = $chamado_portal_lista_color;
}
?>