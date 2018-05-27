<?php
/*
 * class.Venda.Kit.php
 * Autor: Alex
 * 21/06/2011 14:28:28
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class VendaKit{
    private $ObjVenda;
    private $NumregKit;
    private $ItensKit = array();
    
    public function __construct($ObjVenda,$IdSequenciaKIT){
        $this->ObjVenda = $ObjVenda;
        $this->CarregaItens($IdSequenciaKIT);
    }
    
    public function CarregaItens($IdSequenciaKIT){
        $Itens = $this->ObjVenda->getItens();
        foreach($Itens as $IndiceItem => $Item){
            if($IdSequenciaKIT == $Item->getDadosVendaItem('id_sequencia_kit')){
                $NumregItem = $Item->getNumregItem();
                $this->ItensKit[$NumregItem] = $Item;
            }
        }
    }
    
    public function getItens(){
        return $this->ItensKit;
    }
}
?>