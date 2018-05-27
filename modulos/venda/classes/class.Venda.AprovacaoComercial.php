<?php
/*
 * class.Venda.AprovacaoComercial.php
 * Autor: Alex
 * 01/11/2011 09:30:25
 */
class VendaAprovacaoComercial{
    private $ObjVenda;
    private $IdUsuarioAprovador;
    private $UsuarioAprovador;

    public function __construct(Venda $ObjVenda){
        $this->ObjVenda = $ObjVenda;
        $this->IdUsuarioAprovador = $_SESSION['id_usuario'];
        $this->UsuarioAprovador = new Usuario($this->IdUsuarioAprovador);
    }

    public function PossuiPermissaoAprovar(){
        if($this->UsuarioAprovador->getPermissao('sn_permite_aprovar_venda')){
            return true;
        }
        return false;
    }

    public function ValidaPermissaoCamposDesconto(){
        $Status = true;
        $this->ObjVenda->setMensagemDebug('## Validação permissão aprovação campos de desconto ##');

        $ArCamposDeconto = $this->ObjVenda->getArrayCamposDescontos();
        $ArrayCamposDesconto = array();
        foreach($ArCamposDeconto as $DadosCampoDesconto){
            if($DadosCampoDesconto['sn_valida_desc_max_aprovador'] != '1'){
                continue;
            }
            $SqlDescontoMaximoPermitido = "SELECT pct_max_desconto_permitido FROM is_param_campo_desconto_aprovacao WHERE id_usuario = '".$this->IdUsuarioAprovador."' AND id_campo_desconto = '".$DadosCampoDesconto['numreg']."'";
            $QryDescontoMaximoPermitido = query($SqlDescontoMaximoPermitido);
            $ArDescontoMaximoPermitido = farray($QryDescontoMaximoPermitido);
            $PctMaxDescPermitido = $ArDescontoMaximoPermitido['pct_max_desconto_permitido'];
            $ArrayCamposDesconto[$DadosCampoDesconto['numreg']] = $PctMaxDescPermitido;
        }
        if(count($ArrayCamposDesconto) == 0){
            $this->ObjVenda->setMensagemDebug('Nenhum campo de desconto para validar');
            return true;
        }
        $this->ObjVenda->setMensagemDebug('Percentuais de desconto permitidos:<pre>'.print_r($ArrayCamposDesconto,true).'</pre>');
        $Itens = $this->ObjVenda->getItens();
        
        foreach($ArrayCamposDesconto as $IdCampoDesconto => $PctMaxPermitido){
            $this->ObjVenda->setMensagemDebug('-- Validando campo de desconto '.$IdCampoDesconto);
            foreach($Itens as $Item){
                $PctDesconto = $Item->getPctDescontoItemDesconto($IdCampoDesconto);
                $this->ObjVenda->setMensagemDebug('Pct Desconto: '.$PctDesconto.', Pct Max Permitido:'.$PctMaxPermitido);
                if($PctDesconto > $PctMaxPermitido){
                    $Status = false;
                    $this->ObjVenda->setMensagemDebug('Item '.$Item->getDadosVendaItem('id_sequencia').': '.$PctDesconto.'% maior que o máximo permitido de '.$PctMaxPermitido.'%');
                }
            }
        }
        return $Status;
    }

    public function PermiteAprovar(){
        if(!$this->PossuiPermissaoAprovar()){
            return false;
        }
        if(!$this->ValidaPermissaoCamposDesconto()){
            return false;
        }
        return true;
    }
}
?>