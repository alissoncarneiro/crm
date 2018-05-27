<?php

if ($id_funcao == 'atividades_cad_lista') {
    // Na função histórico de relacionamento por contato - tratamento para recuperar a empresa na inclusão
    if ( ($_GET["psubdet"]=='357') && ($_GET["pnumreg"]=='-1')) {
        $ArContato = farray(query("select numreg, id_empresa, nome from is_contato where numreg = '".$_GET["pnpai"]."'"));
        $ArEmpresaAtividade = farray(query("select numreg, razao_social_nome from is_pessoa where numreg = '".$ArContato["id_empresa"]."'"));
        echo "<script>";
        echo "document.getElementById('edtid_pessoa').value = '".$ArEmpresaAtividade["numreg"]."'; ";
        echo "document.getElementById('edtdescrid_pessoa').value = '".$ArEmpresaAtividade["razao_social_nome"]."';";
        echo "document.getElementById('edtid_pessoa_contato').value = '".$ArContato["numreg"]."'; ";
        echo "document.getElementById('edtdescrid_pessoa_contato').value = '".$ArContato["nome"]."';";
        echo "</script>";
    }
}