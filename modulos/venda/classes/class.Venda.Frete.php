<?php
/*
 * class.Venda.Frete.php
 * Autor: Alex
 * 01/07/2011 14:18:30
 */
class VendaFrete{
    private $ObjVenda;
    private $VlTotalFrete = 0;
    
    public function __construct($ObjVenda){
        $this->ObjVenda = $ObjVenda;
    }
    
    public function getVlTotalFrete(){
        return $this->VlTotalFrete;
    }
    
    public function setVlTotalFrete($VlTotalFrete){
        $this->VlTotalFrete = $VlTotalFrete;
    }
    
    public function CalculaValorTotalFrete(){
        $this->VlTotalFrete = 0;
    }    
}
?>