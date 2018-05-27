<?php
/*
 * calc_custom_pessoa_pendente_integracao.php
 * Autor: Alex
 * 14/02/2012 15:58:23
 */
if($id_funcao == 'pessoa_pendente_integracao'){
    if($id_campo == 'calc_trans_prospect'){
        $ret = '<img src="images/ativar.gif" onclick="javascript:TransformaClienteEmProspect(\''.$qry_cadastro['numreg'].'\');" style="cursor:pointer"/>';
    }
}
?>