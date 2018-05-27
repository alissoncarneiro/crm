<?php
/*
 * class.Venda.Frete.Custom.php
 * Autor: Alex
 * 04/07/2011 09:00
 */
class VendaFreteCustom extends VendaFrete{
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