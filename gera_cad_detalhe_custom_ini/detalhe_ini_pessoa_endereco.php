<?php
/*
 * detalhe_ini_pessoa_endereco.php
 * Autor: Alex
 * 08/12/2010 09:56
 * -
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
if($_GET['pfuncao'] == 'pessoa_endereco'){
    /*
     * Tratamento para bloquear a manutenção de endereçõs que foram importados do ERP
     */
    $QryPessoaEndereco = query("SELECT id_endereco_erp FROM is_pessoa_endereco WHERE numreg = '".$_GET["pnumreg"]."'");
    $ArPessoaEndereco = farray($QryPessoaEndereco);

    if($ArPessoaEndereco['id_endereco_erp'] != ''){
        $_GET['pread'] = '1';
    }
}
?>