<?php
/*
 * detalhe_ini_pessoa_endereco.php
 * Autor: Alex
 * 08/12/2010 09:56
 * -
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
if($_GET['pfuncao'] == 'pessoa_endereco'){
    /*
     * Tratamento para bloquear a manuten��o de endere��s que foram importados do ERP
     */
    $QryPessoaEndereco = query("SELECT id_endereco_erp FROM is_pessoa_endereco WHERE numreg = '".$_GET["pnumreg"]."'");
    $ArPessoaEndereco = farray($QryPessoaEndereco);

    if($ArPessoaEndereco['id_endereco_erp'] != ''){
        $_GET['pread'] = '1';
    }
}
?>