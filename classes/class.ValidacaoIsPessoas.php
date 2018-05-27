<?php

/*
 * class.ValidacaoIsPessoas.php
 * Autor: Alex
 * 23/09/2010 14:30:00
 * Este classe deve ser utilizada para validar o CFP/CNPJ de uma pessoa, e também consistir se já existe algum cnpj/cpf no banco de dados
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class ValidacaoIsPessoas{

    public $pnumreg = NULL;
    
    public $opc = NULL;

    public $arrayDados = NULL;

    public function TranformarEmCliente(){
        $sql_update = "UPDATE is_pessoa SET sn_cliente = 1, sn_prospect = 0, sn_exportado_erp = 0, sn_importado_erp = 0 WHERE numreg = ".$this->pnumreg;
        $qry_update = query($sql_update);
        if($qry_update){
            return array(true,'Prospect transformado em cliente com sucesso!');
        }
        else{
            return array(true,getError(1));
        }
    }

    public function CheckDadosProspectParaCliente(){
        if(trim($this->arrayDados['id_tp_pessoa']) == ''){
            return array(false,getError('0010020001',1));
        } elseif(trim($this->arrayDados['razao_social_nome']) == ''){
            return array(false,getError('0010020002',1));
        } elseif(trim($this->arrayDados['cnpj_cpf']) == ''){
            return array(false,getError('0010020003',1));
        } elseif(trim($this->arrayDados['ie_rg']) == '' && trim($this->arrayDados['id_tp_pessoa']==1)){
            return array(false,getError('0010020004',1));
        } elseif(trim($this->arrayDados['id_tab_preco_padrao']) == ''){
            return array(false,getError('0010020005',1));
        } elseif(trim($this->arrayDados['id_grupo_cliente']) == ''){
            return array(false,getError('0010020006',1));
        } elseif(trim($this->arrayDados['id_origem']) == ''){
            return array(false,getError('0010020007',1));
        } elseif(trim($this->arrayDados['sn_contribuinte_icms']) == ''){
            return array(false,getError('0010020008',1));
        } elseif(trim($this->arrayDados['tel1']) == ''){
            return array(false,getError('0010020009',1));
        } elseif(trim($this->arrayDados['email']) == ''){
            return array(false,getError('0010020010',1));
        } elseif(trim($this->arrayDados['cep']) == ''){
            return array(false,getError('0010020011',1));
        } elseif(trim($this->arrayDados['uf']) == ''){
            return array(false,getError('0010020013',1));
        } elseif(trim($this->arrayDados['cidade']) == ''){
            return array(false,getError('0010020014',1));
        } elseif(trim($this->arrayDados['numero']) == ''){
            return array(false,getError('0010020015',1));
        } elseif(trim($this->arrayDados['bairro']) == ''){
            return array(false,getError('0010020016',1));
        } elseif(trim($this->arrayDados['endereco']) == ''){
            return array(false,getError('0010020017',1));
        } elseif(trim($this->arrayDados['pais']) == ''){
            return array(false,getError('0010020018',1));
        } elseif(trim($this->arrayDados['cod_suframa']) != '' && trim($this->arrayDados['uf']) != 'AM'){
            return array(false,getError('001002020',1));
        } else{
            return array(true,'');
        }
    }

    public function CheckDuplicidadeCNPJCPF(){
        $sql_cnt_pessoa = "SELECT COUNT(*) AS CNT FROM is_pessoa WHERE cnpj_cpf = '".addslashes(trim($this->arrayDados['cnpj_cpf']))."'";
        $sql_cnt_pessoa = (!$this->pnumreg)?$sql_cnt_pessoa:$sql_cnt_pessoa." AND numreg != ".$this->pnumreg;
        $qry_cnt_pessoa = query($sql_cnt_pessoa);
        $ar_cnt_pessoa = farray($qry_cnt_pessoa);

        if($ar_cnt_pessoa['CNT'] != '' && $ar_cnt_pessoa['CNT'] >= 1){
            if($this->arrayDados['id_tp_pessoa'] == 1){
                $string_documento = 'CNPJ';
            } elseif($this->arrayDados['id_tp_pessoa'] == 2){
                $string_documento = 'CPF';
            } elseif($this->arrayDados['id_tp_pessoa'] == 3){
                $string_documento = 'Documento de Identificação';
            }
            return array(true,'Erro: '.$string_documento.' já está cadastrado!');
        } else{
            return array(false,'');
        }
    }
     
    public function CheckCNPJCPF(){
        $status = false;
        //Se for pessoa Jurídica
        if($this->arrayDados['id_tp_pessoa'] == 1){
            $status = CheckCNPJ($this->arrayDados['cnpj_cpf']);
            $Msg = ($status == false)?'CNPJ inválido!':'';
            return array($status,$Msg);
        }
        //Se for pessoa Física
        elseif($this->arrayDados['id_tp_pessoa'] == 2){
            $status = CheckCPF($this->arrayDados['cnpj_cpf']);
            $Msg = ($status == false)?'CPF inválido!':'';
            return array($status,$Msg);
        }
        //Se for pessoa estrangeira retorna verdadeiro
        elseif($this->id_tp_pessoa == 3){
            return array(true,'');
        }
        //Se não for pessoa física, jurídica estrangeira retorna falso
        else{
            return array(false,'Tipo de Pessoa não informado!');
        }
    }

    public function CheckIE(){
        if($this->arrayDados['id_tp_pessoa'] == 1){
            $CheckIE = CheckIE($this->arrayDados['ie_rg'],$this->arrayDados['uf']);
            switch($CheckIE){
                 case 0:
                    $status = false;
                    $Msg = 'IE inválida!';
                    break;
                case 1:
                    $status = true;
                    $Msg = '';
                    break;
                case 2:
                    $status = false;
                    $Msg = 'IE inválida!';
                    break;
                case 3:
                    $status = false;
                    $Msg = 'IE inválida! Estado não informado!';
                    break;
            }
            return array($status,$Msg);
        }
        //Se for pessoa Física
        elseif($this->arrayDados['id_tp_pessoa'] == 2){
            return array(true,'');
        }
        //Se for pessoa estrangeira retorna verdadeiro
        elseif($this->id_tp_pessoa == 3){
            return array(true,'');
        }
        else{
            return array(true,'');
        }
    }
}
?>