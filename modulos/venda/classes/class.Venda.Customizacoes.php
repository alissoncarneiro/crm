<?php
/*
 * class.Venda.Customizacoes
 * Autor: Alex
 * 15/06/2012 15:12:06
 */
class VendaCustomizacoes{
    protected $ObjVenda;
    public function __construct(Venda $ObjVenda){
        $this->ObjVenda = $ObjVenda;
    }
    
    public function getAnexoEnvioEmail(){
        
    }
    
    public function getHtmlLinhaRodapeItens($QtdeColunasTabelaItens){
        
    }
}
?>