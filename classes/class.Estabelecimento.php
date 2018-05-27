<?php
/*
 * class.Estabelecimento.php
 * Autor: Alex
 * 24/03/2011 11:00:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class Estabelecimento{

    public $DadosEstabelecimento;

    /**
     * Classe para tratar os dados do cadastro de Estabelecimento (is_estabelecimento)
     * @param int $NumregEstabelecimento
     * @return bool
     */
    public function __construct($NumregEstabelecimento){
        if($NumregEstabelecimento == '' || empty($NumregEstabelecimento)){
            return false;
        }
        $SqlEstabelecimento = "SELECT * FROM is_estabelecimento WHERE numreg = ".$NumregEstabelecimento;
        $QryEstabelecimento = query($SqlEstabelecimento);
        $ArEstabelecimento = farray($QryEstabelecimento);
        $this->DadosEstabelecimento = $ArEstabelecimento;
        return true;
    }

    public function getDadosEstabelecimento($IdCampo=NULL){
        if($IdCampo == NULL){
            return $this->DadosEstabelecimento;
        }
        else{
            return $this->DadosEstabelecimento[$IdCampo];
        }
    }
}
?>