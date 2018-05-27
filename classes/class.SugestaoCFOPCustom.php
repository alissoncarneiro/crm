<?php
/*
 * class.SugestaoCFOPCustom.php
 * Autor: Alex
 * 31/05/2011 18:00:45
 */
class SugestaoCFOPCustom{
    private $ObjVenda;
    private $ObjVendaItem;
    private $Mensagem = array();
    private $MensagemLog = array();
    
    public function  __construct(Venda $ObjVenda,VendaItem $ObjVendaItem){
        $this->ObjVenda = $ObjVenda;
        $this->ObjVendaItem = $ObjVendaItem;        
    }
    private function setMensagem($Mensagem){
        $this->Mensagem[] = $Mensagem;
    }
    private function setMensagemLog($MensagemLog){
        $this->MensagemLog[] = $MensagemLog;
    }
    public function getMensagem($RetornaEmArray=false,$Separador = ' | '){
        if($RetornaEmArray == true){
            return $this->Mensagem;
        }
        return implode($Separador,$this->Mensagem);
    }
    public function getMensagemLog($RetornaEmArray=false,$Separador = ' | '){
        if($RetornaEmArray == true){
            return $this->MensagemLog;
        }
        return implode($Separador,$this->MensagemLog);
    }
    public function SugereCFOP(){
        return 0;
    }
}
?>