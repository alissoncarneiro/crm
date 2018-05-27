<?php
/*
 * class.UF.php
 * Autor: Alex
 * 28/12/2010 12:23:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class UF{

    public $DadosUF;

    /**
     * Classe para tratar os dados do cadastro de UF (is_estados_uf)
     * @param string $SiglaUF
     * @param string $Pais
     * @return bool
     */
    public function __construct($SiglaUF,$Pais){
        if(trim($SiglaUF) == '' || trim($Pais) == ''){
            return false;
        }
        $SqlUF = "SELECT * FROM is_estados_uf WHERE uf = '".$SiglaUF."' AND pais = '".$Pais."'";
        $QryUF = query($SqlUF);
        $ArUF = farray($QryUF);
        $this->DadosUF = $ArUF;
        return true;
    }

    public function getDadosUF($IdCampo=NULL){
        if($IdCampo == NULL){
            return $this->DadosUF;
        }
        else{
            return $this->DadosUF[$IdCampo];
        }
    }
}
?>