<?php
/*
 * class.PessoaEndereco.php
 * Autor: Alex
 * 24/03/2011 11:00:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class PessoaEndereco{

    public $DadosPessoaEndereco;

    /**
     * Classe para tratar os dados do cadastro de Endereco das Contas (is_pessoa_endereco)
     * @param int $NumregPessoaEndereco
     * @return bool
     */
    public function __construct($NumregPessoaEndereco){
        if($NumregPessoaEndereco == '' || empty($NumregPessoaEndereco)){
            return false;
        }
        $SqlPessoaEndereco = "SELECT * FROM is_pessoa_endereco WHERE numreg = ".$NumregPessoaEndereco;
        $QryPessoaEndereco = query($SqlPessoaEndereco);
        $ArPessoaEndereco = farray($QryPessoaEndereco);
        $this->DadosPessoaEndereco = $ArPessoaEndereco;
        return true;
    }

    public function getDadosPessoaEndereco($IdCampo=NULL){
        if($IdCampo == NULL){
            return $this->DadosPessoaEndereco;
        }
        else{
            return $this->DadosPessoaEndereco[$IdCampo];
        }
    }
}
?>