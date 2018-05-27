<?php

/*
 * class.impcli.php
 * Autor: Alex
 * 23/09/2010 14:20:00
 * 
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class Cliente{

    private $qtde_clientes_total = 0;
    private $qtde_clientes_importados = 0;
    private $qtde_clientes_erro = 0;
    private $ar_cliente = NULL;
    private $Log = NULL;

    public function setLog($p){
        if(is_object($p)){
            $this->Log = $p;
        }
    }

    public function getNumCliImp(){
        $qry = mysql_query("SELECT COUNT(*) AS CNT FROM is_int_pessoas WHERE log_processado = 0 AND log_integrado = 0");
        $ar = mysql_fetch_array($qry);
        return $ar['CNT'];
    }

    public function get_ar_cliente($id_pessoa,$cnpj_cpf){
        if($id_pessoa == ''){
            $sql = "SELECT * FROM is_pessoas WHERE id_pessoa = '".$id_pessoa."'";
        } else{
            $sql = "SELECT * FROM is_pessoas WHERE cnpj_cpf = '".$cnpj_cpf."'";
        }
        $qry = mysql_query($sql);
        $ar = mysql_fetch_array($qry);
        return $ar;
    }

    public function impTodosCliente(){
        $this->qtde_clientes_total = $this->getNumCliImp();
        $qry = mysql_query("SELECT * FROM is_int_pessoas WHERE log_processado = 0 AND log_integrado = 0");
        while($ar_cliente = mysql_fetch_array($qry)){
            $this->Log->sep(100);
            $this->Log->w('Iniciando processo do cliente '.$ar_cliente['numreg'].' - '.$ar_cliente['id_pessoa'].' - '.$ar_cliente['razao_social_nome']);
            if($this->consisteCliente($ar_cliente)){
                $grava_cliente = $this->gravaCliente($ar_cliente);
                if($grava_cliente){
                    $this->atualizaId_pessoa($grava_cliente);
                    $this->atualizaProcessado($ar_cliente['numreg'],true);
                    $this->qtde_clientes_importados = $this->qtde_clientes_importados + 1;
                } else{
                    $this->atualizaProcessado($ar_cliente['numreg'],false);
                }
            } else{
                $this->atualizaProcessado($ar_cliente['numreg'],false);
                $this->qtde_clientes_erro = $this->qtde_clientes_erro + 1;
            }
        }
    }

    public function atualizaProcessado($numreg,$p){
        if($p == true){
            mysql_query("UPDATE is_int_pessoas SET log_processado = 1, log_integrado = 1, dt_processado = NOW() WHERE numreg = ".$numreg);
        } else{
            mysql_query("UPDATE is_int_pessoas SET log_processado = 1, log_integrado = 0, dt_processado = NOW() WHERE numreg = ".$numreg);
        }
    }

    public function gravaCliente($ar_cliente){
        $ar_cliente = trataFetchType($ar_cliente);
        $ar_cliente = $this->executaDepara($this->ar_depara,$ar_cliente);
        $ar_cliente['qtde_func_filhos'] = ($ar_cliente['qtde_func_filhos'] == '')?0:$ar_cliente['qtde_func_filhos'];
        if(!$this->verificaSeExiste($ar_cliente['cnpj_cpf'],$ar_cliente['id_pessoa_erp'])){
            $sql = autoExecuteSql('is_pessoas',$ar_cliente,'INSERT','',array('`',"'"),'');
            if(mysql_query($sql)){
                $this->Log->w('Cliente '.$ar_cliente['id_pessoa'].' - '.$ar_cliente['razao_social_nome'].' criado.');
                return mysql_insert_id();
            } else{
                $this->Log->w('Cliente '.$ar_cliente['id_pessoa'].' - '.$ar_cliente['razao_social_nome'].' não criado.');
                $this->Log->w('SQL:'.$sql);
                $this->Log->w('ERRO SQL:'.mysql_error());
                return false;
            }
        } else{
            $chave = ($ar_cliente['cnpf_cpf'] != '')?'cnpf_cpf':'id_pessoa_erp';
            $sql = autoExecuteSql('is_pessoas',$ar_cliente,'UPDATE',$chave,array('`',"'"),'');
            if(mysql_query($sql)){
                $this->Log->w('Cliente '.$ar_cliente['id_pessoa'].' - '.$ar_cliente['razao_social_nome'].' integrado.');
                return true;
            } else{
                $this->Log->w('Cliente '.$ar_cliente['id_pessoa'].' - '.$ar_cliente['razao_social_nome'].' não atualizado.');
                $this->Log->w('SQL:'.$sql);
                $this->Log->w('ERRO SQL:'.mysql_error());
                return false;
            }
        }
    }

    public function verificaSeExiste($cnpj_cpf,$id_pessoa_erp){
        /*
         * Verifica se o cliente existe no banco do CRM.
         * Caso já exista retorna true, caso não, false.
         */
        if($cnpj_cpf != ''){
            $sql = "SELECT COUNT(*) AS CNT FROM is_pessoas WHERE cnpj_cpf = '".$cnpj_cpf."'";
        } else{
            $sql = "SELECT COUNT(*) AS CNT FROM is_pessoas WHERE id_pessoa_erp = '".$id_pessoa_erp."'";
        }
        $qry = mysql_query($sql);
        $ar = mysql_fetch_array($qry);
        if($ar['CNT'] >= 1){
            return true;
        } else{
            return false;
        }
    }

    public function consisteCliente($ar_cliente){
        return true;
        #Verificando se CNPJ já existe
        $sem_erro = true;
        $qry = mysql_query("SELECT COUNT(*) AS CNT FROM is_pessoas WHERE cnpj_cpf = '".$ar_cliente['cnpj_cpf']."'");
        $ar = mysql_fetch_array($qry);
        if($ar['CNT'] >= 1){
            $this->Log->w('Cliente com CNPJ/CPF:'.$ar_cliente['cnpj_cpf'].' já cadastrado.');
            $sem_erro = false;
        }
        return $sem_erro;
    }

    public function executaDepara($arDepara,$arDados){
        #Executa depara para sincronizar arrays de dados
        $na = array();
        foreach($arDepara as $k => $v){
            $na[$k] = $arDados[$v];
        }
        return $na;
    }

    public function atualizaId_pessoa($numreg){
        $sql = "UPDATE is_pessoas SET id_pessoa = numreg WHERE numreg = ".$numreg;
        if(!mysql_query($sql)){
            $this->Log->w('ERRO: coluna id_pessoa não atualizada no CRM.');
        }
    }

    public function finalizaIntegracao(){
        $this->Log->sep(100);
        $this->Log->w('Total de Registros para importar: '.$this->qtde_clientes_total);
        $this->Log->w('Total de Registros para importados: '.$this->qtde_clientes_importados);
        $this->Log->w('Total de Registros com erro: '.$this->qtde_clientes_erro);
    }

    private $ar_depara = array(
        'dt_cadastro' => 'dt_cadastro',
        'hr_cadastro' => 'hr_cadastro',
        'id_usuario_cad' => 'id_usuario_cad',
        'dt_alteracao' => 'dt_alteracao',
        'hr_alteracao' => 'hr_alteracao',
        'id_usuario_alt' => 'id_usuario_alt',
        'id_pessoa_erp' => 'id_pessoa',
        'razao_social_nome' => 'razao_social_nome',
        'id_relac' => 'id_relac',
        'cnpj_cpf' => 'cnpj_cpf',
        'ie_rg' => 'ie_rg',
        'email_prof' => 'email_prof',
        'endereco' => 'endereco',
        'numero' => 'numero',
        'complemento' => 'complemento',
        'bairro' => 'bairro',
        'cidade' => 'cidade',
        'uf' => 'uf',
        'pais' => 'pais',
        'cep' => 'cep',
        'obs' => 'obs',
        'tipo_pessoa' => 'tipo_pessoa',
        'fantasia_apelido' => 'fantasia_apelido',
        'faturamento_renda' => 'faturamento_renda',
        'qtde_func_filhos' => 'qtde_func_filhos',
        'id_ramo' => 'id_ramo',
        'endereco_cob' => 'endereco_cob',
        'numero_cob' => 'numero_cob',
        'complemento_cob' => 'complemento_cob',
        'bairro_cob' => 'bairro_cob',
        'cidade_cob' => 'cidade_cob',
        'uf_cob' => 'uf_cob',
        'pais_cob' => 'pais_cob',
        'cep_cob' => 'cep_cob',
        'id_representante' => 'id_representante',
        'id_origem' => 'id_origem',
        'site' => 'site',
        'tel1' => 'tel1',
        'tel2' => 'tel2',
        'fax' => 'fax',
        'nome_abreviado' => 'nome_abreviado',
        'id_grupo_cliente' => 'id_grupo_cliente',
        'id_usuario_gc' => 'id_usuario_gc',
        'cnpj_cpf_cob' => 'cnpj_cpf_cob',
        'ie_rg_cob' => 'ie_rg_cob',
        'frequencia' => 'frequencia',
        'valor' => 'valor',
        'id_vendedor' => 'id_vendedor',
        'ativo' => 'ativo',
        'id_tp_mot_inat_cli' => 'id_tp_mot_inat_cli',
        'id_tab_preco' => 'id_tab_preco',
        'cep_ent' => 'cep_ent',
        'endereco_ent' => 'endereco_ent',
        'numero_ent' => 'numero_ent',
        'complemento_ent' => 'complemento_ent',
        'bairro_ent' => 'bairro_ent',
        'cidade_ent' => 'cidade_ent',
        'uf_ent' => 'uf_ent',
        'nome_pessoa_contato' => 'nome_pessoa_contato',
        'tel_pessoa_contato' => 'tel_pessoa_contato',
        'email_pessoa_contato' => 'email_pessoa_contato'
    );

}

?>