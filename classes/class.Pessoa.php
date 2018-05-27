<?php
/*
 * class.Pessoa.php
 * Autor: Alex
 * 18/10/2010 15:02:00
 *
 * Dependências
 * class.uB.php
 * functions.php
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 * 2010-11-30 10:00:00 Alex - Coluna id_tp_pessoa corrigida para id_tp_cliente/ Variável $SqlCFOPessoa corrigida para $SqlCFOPPessoa
 * 2011-01-12 14:40:00 Lucas - Coluna sn_consumidor_final adicionada nas funções TranformarEmProspect e TranformarEmCliente
 * 2011-03-14 14:36:00 Eduardo - Criado metodo de duplicidade de nome fantasia / abrev
 * 2011-04-06 14:00:00 Alex - Adicionado suporte/tratamento para clientes estrangeiros
 */

class Pessoa{
    private $NumregPessoa;
    private $ArDados;
    private $Opc;
    private $SnIntegradoERP = true;

    public function  __construct($NumregPessoa){
        $this->SnIntegradoERP = (GetParam('INT_ERP') == 1)?true:false;
        if($NumregPessoa === false){
            $this->Opc = 'incluir';

            $this->ArDados['sn_inadimplente']       = 0;
            $this->ArDados['sn_suspect']            = 0;
            $this->ArDados['sn_cliente']            = 0;
            $this->ArDados['sn_contato']            = 0;
            $this->ArDados['sn_grupo_inadimplente'] = 0;
            $this->ArDados['sn_representante']      = 0;
            $this->ArDados['sn_fornecedor']         = 0;
            $this->ArDados['sn_parceiro']           = 0;
            $this->ArDados['sn_concorrente']        = 0;
            $this->ArDados['sn_consumidor_final']   = 0;			
        }
        elseif(!is_numeric($NumregPessoa)){
            echo getError('0010030001',getParametrosGerais('RetornoErro'));
        }
        else{
            $SqlPessoa = "SELECT * FROM is_pessoa WHERE numreg = ".$NumregPessoa;
            $QryPessoa = query($SqlPessoa);
            if(numrows($QryPessoa) == 0){
                echo getError('0010030002',getParametrosGerais('RetornoErro'));
            }
            $ArPessoa  = farray($QryPessoa);
            $this->NumregPessoa = $NumregPessoa;
            $this->ArDados = $ArPessoa;
            unset($this->ArDados['numreg']);
            $this->Opc = 'alterar';
        }
    }

    public function getNumregPessoa(){
        return $this->NumregPessoa;
    }

    public function getDadoPessoa($IdCampo=NULL){
        if($IdCampo == NULL){
            return $this->ArDados;
        }
        return $this->ArDados[$IdCampo];
    }
    /**
     * Verifica se é um suspect na flag sn_suspect
     * @return bool
     */
    public function isSuspect(){
        if($this->getDadoPessoa('sn_suspect') == 1){
            return true;
        }
        return false;
    }
    /**
     * Verifica se é um prospect na flag sn_prospect
     * @return bool
     */
    public function isProspect(){
        if($this->getDadoPessoa('sn_prospect') == 1){
            return true;
        }
        return false;
    }
    /**
     * Verifica se é um cliente na flag sn_cliente
     * @return bool
     */
    public function isCliente(){
        if($this->getDadoPessoa('sn_cliente') == 1){
            return true;
        }
        return false;
    }

    public function getPermiteFazerPedido(){
        return true;
    }

    public function getAtendimentosPendentes(){
        $QryAtendimentos = query("SELECT * FROM is_atividade");
        return numrows($QryAtendimentos);
    }

    public function setArDados($IdCampo,$Valor){
        $this->ArDados[$IdCampo] = $Valor;
    }

    public function InserePessoaBD(){
        if($this->Opc != 'incluir'){
            return false;
        }
        $DuplicidadeCNPJCPF = $this->CheckCNPJCPF();
        if($DuplicidadeCNPJCPF[0] === true){
            return false;
        }
        $DuplicidadeEmail = $this->CheckDuplicidadeEmail();
        if($DuplicidadeEmail[0] === true){
            return false;
        }		
        $DuplicidadeTelefone = $this->CheckDuplicidadeTelefone();
        if($DuplicidadeTelefone[0] === true){




            return false;
        }		
        $CheckIE = $this->CheckIE();
        if($CheckIE[0] === false){
            return false;
        }
        $DuplicidadeNomeFantasia = $this->CheckDuplicidadeNomeFantasia();
        if($DuplicidadeNomeFantasia[0] === true){
            return false;
        }
        unset($this->ArDados['numreg']);
        $SqlInsertPessoa = AutoExecuteSql(TipoBancoDados, 'is_pessoa', $this->ArDados, 'INSERT');
        $QryInsert = iquery($SqlInsertPessoa);
        if(!$QryInsert){
            return false;
        }
        $this->ArDados['numreg'] = $QryInsert;
        $this->Opc = 'alterar';
        return $QryInsert;
    }

    public function CheckDadosSuspectParaProspect(){
        if(trim($this->ArDados['id_tp_pessoa']) == ''){
            return array(false,getError('0010020001',1));
        } elseif(trim($this->ArDados['razao_social_nome']) == ''){
            return array(false,getError('0010020002',1));
        } elseif(trim($this->ArDados['email']) == ''){
            return array(false,getError('0010020010',1));
        } else{
            return array(true,'');
        }
    }

    public function TranformarEmProspect(){
        $ArUpdate = array(
            'sn_consumidor_final' => 0,
            'sn_suspect' => 0,
            'sn_prospect' => 1,
            'sn_cliente' => 0,
            'sn_exportado_erp' => 0,
            'sn_importado_erp' => 0,
            'dt_virou_prospect' => date("Y-m-d"),
            'numreg' => $this->NumregPessoa
        );
        $SqlUpdate = AutoExecuteSql(getParametrosGerais('TipoBancoDados'),'is_pessoa',$ArUpdate,'UPDATE',array('numreg'));

        $QryUpdate = query($SqlUpdate);
        if($QryUpdate){
            $this->ArDados['sn_consumidor_final'] = $ArUpdate['sn_consumidor_final'];
            $this->ArDados['sn_suspect'] = $ArUpdate['sn_suspect'];
            $this->ArDados['sn_prospect'] = $ArUpdate['sn_prospect'];
            $this->ArDados['sn_cliente'] = $ArUpdate['sn_cliente'];
            $this->ArDados['sn_exportado_erp'] = $ArUpdate['sn_exportado_erp'];
            $this->ArDados['sn_importado_erp'] = $ArUpdate['sn_importado_erp'];
            $this->ArDados['dt_virou_prospect'] = $ArUpdate['dt_virou_prospect'];
            return array(true,'Suspect transformado em Prospect com sucesso!');
        }
        else{
            return array(false,getError('0010020021',getParametrosGerais('RetornoErro')));
        }
    }

    public function CheckDadosProspectParaCliente(){
        if(!$this->SnIntegradoERP){
            return true;
        }
        if(trim($this->ArDados['id_tp_pessoa']) == ''){
            return array(false,getError('0010020001',1));
        } elseif(trim($this->ArDados['razao_social_nome']) == ''){
            return array(false,getError('0010020002',1));
        } elseif($this->ArDados['sn_estrangeiro'] != '1' && trim($this->ArDados['cnpj_cpf']) == ''){
            return array(false,getError('0010020003',1));
        } elseif($this->ArDados['sn_estrangeiro'] != '1' && trim($this->ArDados['ie_rg']) == '' && trim($this->ArDados['id_tp_pessoa'] == 1)){
            return array(false,getError('0010020004',1));
        } elseif(trim($this->ArDados['id_tab_preco_padrao']) == ''){
            return array(false,getError('0010020005',1));
        } elseif(trim($this->ArDados['id_grupo_cliente']) == ''){
            return array(false,getError('0010020006',1));
        } elseif(trim($this->ArDados['id_origem_conta']) == ''){
            return array(false,getError('0010020007',1));
        } elseif(trim($this->ArDados['sn_contribuinte_icms']) == ''){
            return array(false,getError('0010020008',1));
        } elseif(trim($this->ArDados['tel1']) == ''){
            return array(false,getError('0010020009',1));
        } elseif(trim($this->ArDados['email']) == ''){
            return array(false,getError('0010020010',1));
        } elseif(trim($this->ArDados['cep']) == ''){
            return array(false,getError('0010020011',1));
        } elseif(trim($this->ArDados['uf']) == ''){
            return array(false,getError('0010020013',1));
        } elseif(trim($this->ArDados['cidade']) == ''){
            return array(false,getError('0010020014',1));
        } elseif(trim($this->ArDados['numero']) == ''){
            return array(false,getError('0010020015',1));
        } elseif(trim($this->ArDados['bairro']) == ''){
            return array(false,getError('0010020016',1));
        } elseif(trim($this->ArDados['endereco']) == ''){
            return array(false,getError('0010020017',1));
        } elseif(trim($this->ArDados['pais']) == ''){
            return array(false,getError('0010020018',1));
        } elseif(trim($this->ArDados['cod_suframa']) != '' && trim($this->ArDados['uf']) != 'AM'){
            return array(false,getError('001002020',1));
        } else{
            return array(true,'');
        }
    }

    public function TranformarEmCliente(){
        $ArUpdate = array(
            'sn_consumidor_final'   => 0,
            'sn_suspect'            => 0,
            'sn_prospect'           => 0,
            'sn_cliente'            => 1,
            'sn_exportado_erp'      => 0,
            'sn_importado_erp'      => 0,
            'dt_virou_cliente'      => date("Y-m-d"),
            'numreg'                => $this->NumregPessoa
        );

        if($SnIntegradoERP){
            $ArUpdate['fantasia_apelido'] = ($this->getDadoPessoa('fantasia_apelido') != '')?$this->getDadoPessoa('fantasia_apelido'):$this->NumregPessoa;
        }

        if(!$this->SnIntegradoERP){
            $ArUpdate['sn_exportado_erp'] = 1;
            $ArUpdate['sn_importado_erp'] = 1;
        }
        $ArUpdate['id_pessoa_erp'] = ($this->getDadoPessoa('id_pessoa_erp') == '')?uB::getProximoMaxId(3):$this->getDadoPessoa('id_pessoa_erp');
        $SqlUpdate = AutoExecuteSql(getParametrosGerais('TipoBancoDados'),'is_pessoa',$ArUpdate,'UPDATE',array('numreg'));
        $QryUpdate = query($SqlUpdate);
        if($QryUpdate){
            $this->CriaEnderecoPadrao();
            $this->ArDados['sn_consumidor_final']   = $ArUpdate['sn_consumidor_final'];
            $this->ArDados['sn_suspect']            = $ArUpdate['sn_suspect'];
            $this->ArDados['sn_prospect']           = $ArUpdate['sn_prospect'];
            $this->ArDados['sn_cliente']            = $ArUpdate['sn_cliente'];
            $this->ArDados['sn_exportado_erp']      = $ArUpdate['sn_exportado_erp'];
            $this->ArDados['sn_importado_erp']      = $ArUpdate['sn_importado_erp'];
            $this->ArDados['dt_virou_cliente']      = $ArUpdate['dt_virou_cliente'];
            return array(true,'Prospect transformado em Cliente com sucesso!');
        }
        else{
            return array(false,getError('0010020021',getParametrosGerais('RetornoErro')));
        }
    }

    public function CriaEnderecoPadrao(){
        $QryEndereco = query("SELECT numreg FROM is_pessoa_endereco WHERE id_pessoa = ".$this->getNumregPessoa()." AND id_endereco_erp = 'PADRAO'");
        $ArEndereco = farray($QryEndereco);
        if(!$ArEndereco){
            $ArInsertEndereco = array(
                'id_pessoa'         => $this->getNumregPessoa(),
                'endereco'          => $this->getDadoPessoa('endereco'),
                'numero'            => $this->getDadoPessoa('numero'),
                'complemento'       => $this->getDadoPessoa('complemento'),
                'bairro'            => $this->getDadoPessoa('bairro'),
                'cidade'            => $this->getDadoPessoa('cidade'),
                'uf'                => $this->getDadoPessoa('uf'),
                'pais'              => ($this->getDadoPessoa('pais') == '')?'BRASIL':$this->getDadoPessoa('pais'),
                'id_cep'            => $this->getDadoPessoa('id_cep'),
                'cep'               => $this->getDadoPessoa('cep'),
                'id_tp_endereco'    => $this->getDadoPessoa('id_tp_endereco'),
                'referencia'        => $this->getDadoPessoa('referencia'),
                'id_endereco_erp'   => 'PADRAO',
                'id_tp_endereco'    => 1
            );
            $SqlInsertEndereco = AutoExecuteSql(TipoBancoDados, 'is_pessoa_endereco', $ArInsertEndereco, 'INSERT');
            $QryInsertEndereco = iquery($SqlInsertEndereco);
            return $QryInsertEndereco;
        }
        return $ArEndereco['numreg'];;
    }

    public function getSnPossuiSuframa(){
        if(trim($this->getDadoPessoa('cod_suframa')) != ''){
            return true;
        }
        return false;
    }

    public function getSnContribuinteICMS(){
        if($this->getDadoPessoa('sn_contribuinte_icms') == '1'){
            return true;
        }
        return false;
    }

    public function CheckDuplicidadeCNPJCPF(){
        $SqlCntPessoa = "SELECT COUNT(*) AS CNT FROM is_pessoa WHERE cnpj_cpf = '".addslashes(trim($this->ArDados['cnpj_cpf']))."'";
        $SqlCntPessoa = (!$this->NumregPessoa)?$SqlCntPessoa:$SqlCntPessoa." AND numreg != ".$this->NumregPessoa;
        $QryCntPessoa = query($SqlCntPessoa);
        $ArCntPessoa = farray($QryCntPessoa);

        if($ArCntPessoa['CNT'] != '' && $ArCntPessoa['CNT'] >= 1){
            if($this->ArDados['id_tp_pessoa'] == 1){
                $StringDocumento = 'CNPJ';
            } elseif($this->ArDados['id_tp_pessoa'] == 2){
                $StringDocumento = 'CPF';
            }
            return array(true,'Erro: '.$StringDocumento.' já está cadastrado!');
        } else{
            return array(false,'');
        }
    }

    public function CheckDuplicidadeNomeFantasia(){
        if($this->ArDados['fantasia_apelido'] == ''){
            return array(false,'');
        }
        $SqlCntPessoa = "SELECT COUNT(*) AS CNT FROM is_pessoa WHERE fantasia_apelido = '".TrataApostrofoBD($this->ArDados['fantasia_apelido'])."'";
        $SqlCntPessoa = (!$this->NumregPessoa)?$SqlCntPessoa:$SqlCntPessoa." AND numreg != ".$this->NumregPessoa;
        $QryCntPessoa = query($SqlCntPessoa);
        $ArCntPessoa = farray($QryCntPessoa);

        if($ArCntPessoa['CNT'] != '' && $ArCntPessoa['CNT'] >= 1){
            $StringDocumento = 'Nome Fantasia/Abrev';
            return array(true,'Erro: '.$StringDocumento.' já está cadastrado!');
        } else{
            return array(false,'');
        }
    }
	
    public function CheckDuplicidadeEmail(){
		$email = str_replace(" ", "", $this->ArDados['email']);
		$email = trim($email);
        $SqlCntPessoa = "SELECT COUNT(*) AS CNT FROM is_pessoa WHERE trim(REPLACE(email, ' ', '')) = '".$email."'";
        $SqlCntPessoa = (!$this->NumregPessoa)?$SqlCntPessoa:$SqlCntPessoa." AND numreg != ".$this->NumregPessoa;
        $QryCntPessoa = query($SqlCntPessoa);
        $ArCntPessoa = farray($QryCntPessoa);

        if($ArCntPessoa['CNT'] != '' && $ArCntPessoa['CNT'] >= 1){
            $StringDocumento = 'Email';
            return array(true,'Erro: '.$StringDocumento.' já está cadastrado!',$SqlCntPessoa);
        } else{
            return array(false,'',$SqlCntPessoa);
        }
    }	

    public function CheckDuplicidadeTelefone($arTel){
		
        $filtroCntPessoa = (!$this->NumregPessoa)?"":$SqlCntPessoa." AND numreg != ".$this->NumregPessoa;
		foreach($arTel as $tel){
			if($tel != ""){
				$telOriginal = $tel;
				$tel = str_replace(" ", "", $tel);
				$tel = str_replace("-", "", $tel);
				$tel = str_replace("(", "", $tel);
				$tel = str_replace(")", "", $tel);
				$tel = trim($tel);
				
				$SqlCntTelefone = "SELECT COUNT(*) AS CNT FROM is_pessoa WHERE 
						(trim(REPLACE(REPLACE(REPLACE(REPLACE(tel1,')',''),'(',''),'-',''),' ','')) = ".$tel." $filtroCntPessoa) OR 
						(trim(REPLACE(REPLACE(REPLACE(REPLACE(fax,')',''),'(',''),'-',''),' ','')) = ".$tel." $filtroCntPessoa) OR 
						(trim(REPLACE(REPLACE(REPLACE(REPLACE(wcp_tel3,')',''),'(',''),'-',''),' ','')) = ".$tel." $filtroCntPessoa)";		
		
				$QryCntTelefone = query($SqlCntTelefone);
				$ArCntTelefone = farray($QryCntTelefone);
	
				if($ArCntTelefone['CNT'] != '' && $ArCntTelefone['CNT'] >= 1){
					$arTelefoneExiste[] = $telOriginal;
					$arSqlCntTelefone[] = $SqlCntTelefone;
				}
			}
		}
		if(count($arTelefoneExiste) > 0){
            $StringDocumento = count($arTelefoneExiste) == 1 ? 'Telefone ' : 'Telefones: ';
            $StringDocumento2 = count($arTelefoneExiste) == 1 ? ' já está cadastrado!' : ' já estão cadastrados!';
			$telefonesExistentes = implode(" | ",$arTelefoneExiste);
            return array(true,'Erro: '.$StringDocumento.$telefonesExistentes.$StringDocumento2,$arSqlCntTelefone);
		}else{
            return array(false,'',$SqlCntTelefone);
			
		}
    }	


    public function CheckCNPJCPF(){
        $Status = false;
        //Se for pessoa Jurídica
        if($this->ArDados['id_tp_pessoa'] == 1){
            $Status = CheckCNPJ($this->ArDados['cnpj_cpf']);
            $Msg = ($Status == false)?'CNPJ inválido!':'';
            return array($Status,$Msg);
        }
        //Se for pessoa Física
        elseif($this->ArDados['id_tp_pessoa'] == 2){
            $Status = CheckCPF($this->ArDados['cnpj_cpf']);
            $Msg = ($Status == false)?'CPF inválido!':'';
            return array($Status,$Msg);
        }
        //Se não for pessoa física ou jurídica
        else{
            return array(false,'Tipo de Pessoa não informado!');
        }
    }

    public function CheckIE(){
        /* Desativado Temporariamente */
        return array(true,'');
        //TODO: Desenvolver as fórmulas de cálculo do digito verificador com as regras atualizadas
        if($this->ArDados['id_tp_pessoa'] == 1){
            $CheckIE = CheckIE($this->ArDados['ie_rg'],$this->ArDados['uf']);
            switch($CheckIE){
                 case 0:
                    $Status = false;
                    $Msg = 'IE inválida!';
                    break;
                case 1:
                    $Status = true;
                    $Msg = '';
                    break;
                case 2:
                    $Status = false;
                    $Msg = 'IE inválida!';
                    break;
                case 3:
                    $Status = false;
                    $Msg = 'IE inválida! Estado não informado!';
                    break;
                default :
                    $Status = false;
                    break;
            }
            return array($Status,$Msg);
        }
        //Se for pessoa Física
        elseif($this->ArDados['id_tp_pessoa'] == 2){
            return array(true,'');
        }
        else{
            return array($Status,'');
        }
    }

    public function getIdTpPessoa(){
        $SqlCFOPPessoa = "SELECT id_tp_cliente FROM is_pessoa_cfop WHERE id_pessoa = ".$this->getNumregPessoa();
        $QryCFOPPessoa = query($SqlCFOPPessoa);
        $ArCFOPPessoa = farray($QryCFOPPessoa);
        return $ArCFOPPessoa['id_tp_cliente'];
    }

    public function getNumregContatoPadrao(){
        $SqlContatoPadrao = "SELECT numreg FROM is_contato WHERE sn_padrao = 1 AND id_empresa = '".$this->getNumregPessoa()."'";
        $QryContatoPadrao = query($SqlContatoPadrao);
        $ArContatoPadrao = farray($QryContatoPadrao);
        return ($ArContatoPadrao)?$ArContatoPadrao['numreg']:false;
    }

    /**
     * Converte um cliente em um prospect em casos de erro de integração
     * @return array(bool,string)
     */
    public function TransformaClienteEmProspect(){
        if($this->getDadoPessoa('sn_cliente') != '1'){
            return array(false,'Conta não é cliente!');
        }
        elseif($this->getDadoPessoa('sn_exportado_erp') != '1'){
            return array(false,'Não permitido, cliente ainda não foi exportado!');
        }
        $Usuario = new Usuario($_SESSION['id_usuario']);
        if(!$Usuario || !$Usuario->getPermissao('sn_trans_cliente_prospect')){
            return array(false,'Usuário sem permissão!');
        }
        $ArUpdate = array(
            'numreg'            => $this->getNumregPessoa(),
            'sn_suspect'        => 0,
            'sn_prospect'       => 1,
            'sn_cliente'        => 0,
            'sn_exportado_erp'  => 0
        );
        $SqlUpdate = AutoExecuteSql(getParametrosGerais('TipoBancoDados'),'is_pessoa',$ArUpdate,'UPDATE',array('numreg'));
        $QryUpdate = query($SqlUpdate);
        if($QryUpdate){
            GravaLogEvento(600, true, 'Cliente '.$this->getDadoPessoa('razao_social_nome').' transformado em prospect','Numreg:'.$this->getNumregPessoa());
            return array(true,'Cliente transformado em Prospect com sucesso!');
        }
        else{
            return array(false,'Erro SQL ao transformar cliente em prospect.');
        }
    }
}
?>