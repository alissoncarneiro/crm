<?php

/*

 * detalhe_sub_pessoa.php

 * Autor: Alex

 * 20/04/2011 14:01:26

 *

 * Log de Alterações

 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>

 */

if($_GET['pfuncao'] == 'pessoa'){

    if($_GET['ptpec'] != 1 && ($qry_cadastro['sn_suspect'] == 1 || $qry_cadastro['sn_prospect'] == 1 || $_GET['pgetcustom'] == 'suspect' || $_GET['pgetcustom'] == 'prospect')){

        $ArraySubExibirSomenteSeCliente = array( 43 /* Pedidos */, 88 /* Class. Unid. Negoc. */, 116 /* Contratos */, 118 /* Notas Fiscais */, 293 /* Títulos */);

        if(is_int(array_search($qry_gera_cad_sub['numreg'],$ArraySubExibirSomenteSeCliente))){

            $exibe_mestre_detalhe = '0';

        }

        else{

            $exibe_mestre_detalhe = '1';

        }

    }

}

?>