<?php
# readonly_laboratorio
# Expression package is undefined on line 3, column 5 in Templates/Scripting/EmptyPHP.php.
# Autor: Rodrigo Piva
# 26/08/2011
#
# Log de Alterações
# yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
#

if ($id_funcao == 'chamado_atec_lab'){
    
    $readonly = 'readonly style="background-color:#CCCCCC" ';
    if( $_REQUEST['pnumreg'] == '-1' ){
        if(       $id_campo == 'assunto'
               || $id_campo == 'id_pessoa'
               || $id_campo == 'id_pessoa_contato'
               || $id_campo == 'id_usuario_tecnico'
               || $id_campo == 'id_tp_atendimento'
               || $id_campo == 'id_pessoa_analisa'
               || $id_campo == 'id_nao_conformidade'
               || $id_campo == 'id_fabricante'
               || $id_campo == 'id_prioridade'
               || $id_campo == 'dt_prev_fim'
               || $id_campo == 'hr_prev_fim'
               || $id_campo == 'obs'
               || $id_campo == 'nr_serie_produto'
               || $id_campo == 'id_produto'
               || $id_campo == 'qtde'
               || $id_campo == 'id_estoque'
               || $id_campo == 'id_estoque_final'
               || $id_campo == 'nr_nota'
               || $id_campo == 'dt_nota'
               ){
                $readonly = '';
            }        
    }
    
    switch ($qry_cadastro['id_status_reparo']){

        #STATUS 1 - EM ABERTO
        case '1':
            if(   $id_campo == 'assunto'
               || $id_campo == 'id_pessoa_contato'
               || $id_campo == 'id_usuario_tecnico'
               || $id_campo == 'id_tp_atendimento'
               || $id_campo == 'id_pessoa_analisa'
               || $id_campo == 'id_nao_conformidade'
               || $id_campo == 'id_fabricante'
               || $id_campo == 'id_prioridade'
               || $id_campo == 'dt_prev_fim'
               || $id_campo == 'hr_prev_fim'
               || $id_campo == 'obs'
               || $id_campo == 'nr_serie_produto'
               ){
                $readonly = '';
            }
        break;

        #STATUS 2 - EM BANCADA
        case '2':
            if(   $id_campo == 'email_cc'
               || $id_campo == 'sn_mais_informacoes'
               || $id_campo == 'desc_necessita_inf'
               || $id_campo == 'sn_perda_total'
               || $id_campo == 'id_tp_perda_total'
               || $id_campo == 'desc_perda_total'
               || $id_campo == 'id_estoque_final'
               || $id_campo == 'sn_envia_fornecedor'
               || $id_campo == 'motivo_fornecedor'
               || $id_campo == 'id_nao_conformidade'
               || $id_campo == 'sn_aguarda_peca'
               || $id_campo == 'desc_obs_peca'
            ){
                $readonly = '';
            }
        break;

        #STATUS 3 - AGUARDANDO INFORMACOES
        case '3':
            if(   $id_campo == 'email_cc'
               || $id_campo == 'sn_mais_informacoes'
               || $id_campo == 'desc_necessita_inf'
               || $id_campo == 'id_estoque_final'
               || $id_campo == 'sn_perda_total'
               || $id_campo == 'id_tp_perda_total'
               || $id_campo == 'desc_perda_total'
               || $id_campo == 'sn_envia_fornecedor'
               || $id_campo == 'motivo_fornecedor'
               || $id_campo == 'id_nao_conformidade'
	       || $id_campo == 'sn_aguarda_peca'
               || $id_campo == 'desc_obs_peca'
               ){
                $readonly = '';
            }
        break;


        #STATUS 4 - SUCATEA OU COBRA
        case '4':
            if(   $id_campo == 'sn_sucata'
               || $id_campo == 'sn_custo_cliente'
               || $id_campo == 'id_motivo_n_orcto'
               || $id_campo == 'sn_devolucao'
               ){
                $readonly = '';
            }
        break;

        #STATUS 5 - IDENTIFICAR TIPO DE CONTRATO
        case '5':
            if(   $id_campo == 'sn_sucata'
               || $id_campo == 'sn_custo_cliente'
               || $id_campo == 'id_motivo_n_orcto'
               || $id_campo == 'sn_devolucao'
               ){
               $readonly = '';
            }
        break;

        #STATUS 6 - EM FORNECEDOR
        case '6':
            if(   $id_campo == 'email_cc'
               || $id_campo == 'sn_mais_informacoes'
               || $id_campo == 'desc_necessita_inf'
               || $id_campo == 'sn_perda_total'
               || $id_campo == 'id_tp_perda_total'
               || $id_campo == 'desc_perda_total'
               || $id_campo == 'sn_envia_fornecedor'
               || $id_campo == 'motivo_fornecedor'
               || $id_campo == 'sn_aguarda_peca'
               || $id_campo == 'desc_obs_peca'
               ){
                $readonly = '';
            }
        break;

        #STATUS 7 - ATENDIMENTO EM REPARO
        case '7':
            if(   $id_campo == 'sn_reparo_concluido'
               || $id_campo == 'diagnostico_tecnico'
               || $id_campo == 'id_nao_conformidade'
               || $id_campo == 'id_estoque_final'
               ){
                $readonly = '';
            }
        break;

        #STATUS 8 - ATENDIMENTO EM ORCAMENTO
        case '8':
            if(   $id_campo == 'id_orcamento'
               || $id_campo == 'id_situacao_orcamento'
               || $id_campo == 'sn_devolucao'
               ){
                $readonly = '';
            }
        break;

        #STATUS 9 - DEVOLUÇÃO
        case '9':
            if(   $id_campo == 'nr_nota_devolucao'
               ){
                $readonly = '';
            }
        break;

        #STATUS 11 - REPARO CONCLUIDO
        case '11':
            if(   $id_campo == 'sn_custo_cliente'
               || $id_campo == 'id_motivo_n_orcto'
               ){
                $readonly = '';
            }
        break;

		#STATUS 12 - AGUARDANDO PECA
        case '12':
            if(   $id_campo == 'sn_aguarda_peca'
		  || $id_campo == 'desc_obs_peca'
               ){
                $readonly = '';
            }
        break;

    }

}

?>
