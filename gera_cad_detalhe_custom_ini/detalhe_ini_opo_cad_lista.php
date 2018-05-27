<?php
/*
 * detalhe_ini_opo_cad_lista.php
 * Autor: Alex
 * 20/04/2011 10:30
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
if($_GET['pfuncao'] == 'opo_cad_lista'){
    /* Tratamento para bloquear a manutenção do cadastro quando a situaçãoo for perdida ou fechada */
    $QryOportunidade = query("SELECT id_situacao FROM is_oportunidade WHERE numreg = '".$_GET["pnumreg"]."'");
    $ArOportunidade = farray($QryOportunidade);

    if($ArOportunidade['id_situacao'] == 3 || $ArOportunidade['id_situacao'] == 4){
        $_GET['pread'] = '1';
        if($_SESSION['id_perfil'] == 12){
            $_GET['pread'] = '0';

        }
    }
}