<?php

class VendaCamposCustom extends VendaCampos{

    public function CampoCustom($IdCampo,$IdCadastro){
        return parent::CampoCustom($IdCampo,$IdCadastro);
    }

    public function ValorCustom($IdCadastro,$IdCampo,$Valor){
        return parent::ValorCustom($IdCadastro,$IdCampo,$Valor);
    }

    public function HTMLPersonalizado($IdCampo,$IdCadastro){
        if($IdCampo == 'id_cond_pagto'){
            return '<img src="../../images/payment-card.png" style="cursor:pointer" id="btn_c_coaching_id_ect_etc"/>';
        }
        return parent::HTMLPersonalizado($IdCampo,$IdCadastro);
    }

    public function ValidaTrataPOSTCustom($IdCampo){
        return parent::ValidaTrataPOSTCustom($IdCampo);
    }
}
