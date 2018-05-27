<?php
/*
 * class.CFOP.php
 * Autor: Alex
 * 28/12/2010 10:37:00
 *
 *
 * Log de Altera��es
 * yyyy-mm-dd <Pessoa respons�vel> <Descri��o das altera��es>
 */
class CFOP{

    public $DadosCFOP;
    
    /**
     * Classe para tratar os dados do cadastro de CFOP (is_cfop)
     * @param int $NumregCFOP
     * @return bool
     */
    public function __construct($NumregCFOP){
        if($NumregCFOP == '' || empty($NumregCFOP)){
            return false;
        }
        $SqlCFOP = "SELECT * FROM is_cfop WHERE numreg = ".$NumregCFOP;
        $QryCFOP = query($SqlCFOP);
        $ArCFOP = farray($QryCFOP);
        $this->DadosCFOP = $ArCFOP;
        return true;
    }

    public function getDadosCFOP($IdCampo=NULL){
        if($IdCampo == NULL){
            return $this->DadosCFOP;
        }
        else{
            return $this->DadosCFOP[$IdCampo];
        }
    }
    
    public function getSnPossuiReducaoICMS(){
        if($this->getDadosCFOP('pct_reducao_icms') > 0){
            return true;
        }
        return false;
    }
}
?>