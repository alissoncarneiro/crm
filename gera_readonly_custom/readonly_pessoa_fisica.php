<?php
//Desenvolvido por ALisson Carneiro
// 08/05/2012 19:20
//Se for pessoa física obriga campos obrigatorios definidos na array ArrayCamposObrigatoriosPfSbc

if($id_funcao == 'pessoa'){
	if($qry_cadastro['id_tp_pessoa'] == 2){
		$ArrayCamposObrigatoriosPfSbc = array('id_sexo');
		if(is_int(array_search($id_campo,$ArrayCamposObrigatoriosPfSbc)) && $id_campo == 'id_sexo'){
			$qry_gera_cad_campos['sn_obrigatorio'] = 1;
		}
	}
    if($pnumreg == '-1'){
        if(is_int(array_search($id_campo,$ArrayCamposObrigatoriosPfSbc)) && $id_campo == 'id_sexo'){
            $qry_gera_cad_campos['sn_obrigatorio'] = 1;
        }
    }
}