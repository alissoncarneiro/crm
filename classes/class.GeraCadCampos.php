<?php
/*
 * class.GeraCadCampos.php
 * Autor: Alex
 * 26/10/2010 09:28
 * -
 *
 * Log de Alterações
 * 2011-01-14 Alex Adicionado método RemoveCampo()
 */
class GeraCadCampos{

    public    $ArrayCampos = array();
    protected $IdCadastro;
    protected $DadosRegistro = array();
    protected $UrlRetorno;
    protected $ArDadosPostBack = array();
    protected $CaminhoBase = '';
    public    $PrefixoCampo = 'edt';
    protected $POST;
    protected $IdPostBack;
    public $OcultaCodLupaPopUp;
    public $ArGET;

    /**
     *
     * @param type $IdCadastro
     * @param type $ArGET
     */
    public function  __construct($IdCadastro,$ArGET){
        $this->IdCadastro           = $IdCadastro;
        $this->ArGET                = $ArGET;
        $this->OcultaCodLupaPopUp   = GetParam('oculta_codigo_lupa_popup');
        $this->loadArrayCampos();
    }

    public function setDadosRegistro($DadosRegistro){
        $this->DadosRegistro = $DadosRegistro;
    }

    public function setUrlRetorno($UrlRetorno){
        $this->UrlRetorno = $UrlRetorno;
    }

    public function setCaminhoBase($CaminhoBase){
        $this->CaminhoBase = $CaminhoBase;
    }

    public function setPOST($POST){
        $this->POST = $POST;
        $this->loadArrayCampos();
    }

    public function setIdPostBack($IdPostBack){
        $this->IdPostBack = $IdPostBack;
    }

    private function AplicaRegrasCamposBloqueios($IdCampo){
        if($_SESSION['id_perfil'] == ''){
            return;
        }
        $IdPerfil = $_SESSION['id_perfil'];
        $SqlBloqueiosCampos = "SELECT sn_bloqueio_ver,sn_bloqueio_editar FROM is_perfil_funcao_bloqueio_campos WHERE id_perfil = '".$IdPerfil."' AND id_cad = '".$this->getIdCadastro()."' AND id_campo = '".$IdCampo."'";
        $QryBloqueiosCampos = query($SqlBloqueiosCampos);
        $ArBloqueiosCampos = farray($QryBloqueiosCampos);

        if($ArBloqueiosCampos['sn_bloqueio_ver'] == '1'){
            $this->setPropriedadeCampo($IdCampo, 'exibe_formulario',0);
        }

        if($ArBloqueiosCampos['sn_bloqueio_editar'] == '1'){
            $this->setPropriedadeCampo($IdCampo, 'editavel',0);
        }
    }

    private function loadArrayCampos(){
        $QryCampos = query("(SELECT * FROM is_gera_cad_campos WHERE id_funcao = '".$this->IdCadastro."')
                        UNION
                            (SELECT * FROM is_gera_cad_campos_custom WHERE id_funcao = '".$this->IdCadastro."')
                        ORDER BY
                            nome_aba ASC,nome_grupo ASC,ordem ASC");
        while($ArCampos = farray($QryCampos)){
            foreach($ArCampos as $k => $v){
                if(is_int($k)){
                    unset($ArCampos[$k]);
                }
            }
            $ArCampos['sn_ver_detalhes'] = 1;
            $this->ArrayCampos[$ArCampos['id_campo']] = $ArCampos;
            /*
             * Aplicando as regras definidas no bloqueios custom
             */
            $this->AplicaRegrasCamposBloqueios($ArCampos['id_campo']);
            /*
             * Aplicando as regras customizadas do campo
             */
            $this->CampoCustom($ArCampos['id_campo'],$this->IdCadastro);
        }
    }
    /*
     * POSTBACK
     */
    public function loadPostBack($NumregPostBack){
        if($NumregPostBack == ''){
            return false;
        }
        $QryPostBack = query("SELECT * FROM is_postback WHERE valido_ate > '".date("Y-m-d H:i:s")."' AND numreg = ".strsadds($NumregPostBack));
        if(numrows($QryPostBack) == 0){
            return false;
        }
        $ArPostBack = farray($QryPostBack);
        $this->ArDadosPostBack = unserialize($ArPostBack['post']);
    }

    public function getIdPostBack(){
        return $this->IdPostBack;
    }

    public function getIdCadastro(){
        return $this->IdCadastro;
    }

    public function getPostBack($IdCampo){
        return $this->ArDadosPostBack[$IdCampo];
    }

    public function getPrefixoCampo(){
        return $this->PrefixoCampo;
    }

    public function getDadosRegistro($IdCampo){
        return $this->DadosRegistro[$IdCampo];
    }

    public function replaceFiltroFixo($String){
        $String = str_replace('@gfi',"' + $('#",$String);
        $String = str_replace('@gff',"').val() + '",$String);
        return $String;
    }

    public function getArraySQL(){
        $ArraySQL = array();
        foreach($this->ArrayCampos as $k => $v){
                $ArraySQL[$k] = $this->decodeValor($k,$this->POST[$this->PrefixoCampo.$k]);
        }
        return $ArraySQL;
    }

    public function getArrayCampos(){
        return $this->ArrayCampos;
    }

    public function ValidaCamposObrigatorios(){

    }

    public function ValidaTrataPOST(){
        foreach($this->ArrayCampos as $k => $v){
            $this->ValidaTrataPOSTCustom($k);
        }
    }

    public function ValidaTrataPOSTCustom($IdCampo){}

    public function ValorCustom($IdCadastro,$IdCampo,$Valor){
        return $Valor;
    }

    public function getLabelCampo($IdCampo){
        $StringRetorno = '';
        $Classes = array();
        if($this->ArrayCampos[$IdCampo]['sn_obrigatorio'] == 1){
            $Classes[] = 'campo_obrigatorio';
        }
        if(substr($IdCampo, 0, 4) == 'wcp_'){
            $Classes[] = 'campo_verde';
        }
        elseif($this->ArrayCampos[$IdCampo]['sn_obrigatorio'] == 1){
            $Classes[] = 'campo_azul';
        }

        if(count($Classes) > 0){
            $StringRetorno .= '<span class="'.implode(' ', $Classes).'">';
        }
        $StringRetorno .= $this->ArrayCampos[$IdCampo]['nome_campo'];
        if(count($Classes) > 0){
            $StringRetorno .= (($this->ArrayCampos[$IdCampo]['sn_obrigatorio'] == 1)?'*':'').'</span>';
        }
        return $StringRetorno;
    }

    public function replaceValorPadrao($String){
        $ArrayReplace = array(
            '@vs_id_usuario'    => $_SESSION['id_usuario'],
            '@s'                => "'",
            '@vs_dt_hoje'       => date("Y-m-d"),
            '@vs_max_numreg'    => NULL
        );
        foreach($ArrayReplace as $k => $v){
            $ArrayReplace1[] = $k;
            $ArrayReplace2[] = $v;
        }

        $String = str_replace($ArrayReplace1,$ArrayReplace2,$String);

        foreach($this->ArrayCampos as $k => $v){
            $String = str_replace('@vs_cad_'.$this->ArrayCampos[$k]['id_campo'],$this->DadosRegistro[$this->ArrayCampos[$k]['id_campo']],$String);
        }

        /* Tratamento para campos @mestre_ */
        $PrefixoVlPadrao = substr($String,0,8);
        if($PrefixoVlPadrao == '@mestre_'){//pnpai
            $psubdet = trim($this->ArGET['psubdet']);
            $pnpai = trim($this->ArGET['pnpai']);
            if($psubdet == '' || $pnpai == ''){
                return '';
            }
            $SqlGeraCadSub = "SELECT id_funcao_mestre,campo_mestre,campo_detalhe FROM is_gera_cad_sub WHERE numreg = ".$psubdet;
            $QryGeraCadSub = query($SqlGeraCadSub);
            $ArGeraCadSub = farray($QryGeraCadSub);

            $IdFuncaoMestre = $ArGeraCadSub['id_funcao_mestre'];
            $CampoMestre = $ArGeraCadSub['campo_mestre'];
            $CampoDetalhe = $ArGeraCadSub['campo_detalhe'];

            $SqlGeraCad = "SELECT nome_tabela FROM is_gera_cad WHERE id_cad = '".$IdFuncaoMestre."'";
            $QryGeraCad = query($SqlGeraCad);
            $ArGeraCad = farray($QryGeraCad);
            $NomeTabela = $ArGeraCad['nome_tabela'];

            $SqlDados = "SELECT ".$CampoMestre." FROM ".$NomeTabela." WHERE numreg = ".$pnpai;
            $QryDados = query($SqlDados);
            $ArDados = farray($QryDados);

            $String = $ArDados[$CampoMestre];
        }
        return $String;
    }

    public function CampoCustom($IdCampo,$IdCadastro){}

    public function RemoveCampo($IdCampo){
        unset($this->ArrayCampos[$IdCampo]);
    }

    public function setPropriedadeCampo($IdCampo,$Propriedade,$Valor){
        $this->ArrayCampos[$IdCampo][$Propriedade] = $Valor;
    }

    public function getPropriedadeCampo($IdCampo,$Propriedade){
        return $this->ArrayCampos[$IdCampo][$Propriedade];
    }

    public function encodeValor($IdCampo,$Valor){
        $ArCampo = $this->ArrayCampos[$IdCampo];
        switch($ArCampo['tipo_campo']){
            case 'varchar':
                return trim($Valor);
                break;
            case 'int':
                return trim($Valor);
                break;
            case 'combobox':
                return trim($Valor);
                break;
            case 'date':
                return ($Valor != '')?dten2br($Valor):NULL;
                break;
            case 'memo':
                return trim($Valor);
                break;
            case 'sim_nao':
                return trim($Valor);
                break;
            case 'lupa_popup':
                return trim($Valor);
                break;
            case 'money':
                return trim(number_format_min($Valor,2,',','.'));
                break;
            case 'float':
                return trim(number_format_min($Valor,2,',','.'));
                break;
            default:
                return trim($Valor);
                break;
        }
    }

    public function decodeValor($IdCampo,$Valor){
        $ArCampo = $this->ArrayCampos[$IdCampo];
        switch($ArCampo['tipo_campo']){
            case 'varchar':
                return trim($Valor);
                break;
            case 'int':
                return trim($Valor);
                break;
            case 'combobox':
                return trim($Valor);
                break;
            case 'date':
                return ($Valor != '')?dtbr2en($Valor):NULL;
                break;
            case 'memo':
                return trim($Valor);
                break;
            case 'sim_nao':
                return trim($Valor);
                break;
            case 'lupa_popup':
                return trim($Valor);
                break;
            case 'money':
                return trim(str_replace(',','.',str_replace('.','',$Valor)));
                break;
            case 'float':
                return trim(str_replace(',','.',str_replace('.','',$Valor)));
                break;
            default:
                return trim($Valor);
                break;
        }
    }

    public function TrataValorPadrao($IdCampo,$ValorPadrao){
        switch($IdCampo){
            case 'date':
                $ValorPadrao = ($ValorPadrao != '')?dten2br($ValorPadrao):'';
                return $ValorPadrao;
                break;
            case 'memo':
                //$ValorPadrao = ($ValorPadrao != '')?dten2br($ValorPadrao):'';
                return $ValorPadrao;
                break;
            default :
                return $ValorPadrao;
        }
    }

    private function getValorPadraoCampo($IdCampo){
        return $this->ArrayCampos[$IdCampo]['valor_padrao'];
    }

    private function getValorPadraoCampoBloqueio($IdPerfil,$IdCampo){
        if($IdPerfil == '' || $IdCampo == 'numreg'){
            return '';
        }
        $SqlBloqueiosCampos = "SELECT valor_padrao FROM is_perfil_funcao_bloqueio_campos WHERE id_perfil = '".$IdPerfil."' AND id_cad = '".$this->getIdCadastro()."' AND id_campo = '".$IdCampo."'";
        $QryBloqueiosCampos = query($SqlBloqueiosCampos);
        $ArBloqueiosCampos = farray($QryBloqueiosCampos);
        return $ArBloqueiosCampos['valor_padrao'];
    }

    public function getValorPadrao($IdPerfil,$IdCampo){
        $ValorPadrao = $this->getValorPadraoCampoBloqueio($IdPerfil,$IdCampo);
        if($ValorPadrao == ''){
            $ValorPadrao = $this->getValorPadraoCampo($IdCampo);
        }
        return $this->replaceValorPadrao($ValorPadrao);
    }

    public function HTMLPersonalizado($ArCampo,$ValorPadrao=''){
        return '';
    }

    public function getHTMLCampo($IdCampo,$ValorPadrao = ''){
        $ArCampo = $this->ArrayCampos[$IdCampo];
        switch($ArCampo['tipo_campo']){
            case 'varchar':
                return $this->HTMLVarchar($ArCampo,$ValorPadrao);
                break;
            case 'int':
                return $this->HTMLInteiro($ArCampo,$ValorPadrao);
                break;
            case 'combobox':
                return $this->HTMLCombobox($ArCampo,$ValorPadrao);
                break;
            case 'date':
                return $this->HTMLDate($ArCampo,$ValorPadrao);
                break;
            case 'memo':
                return $this->HTMLMemo($ArCampo,$ValorPadrao);
                break;
            case 'sim_nao':
                return $this->HTMLSimNao($ArCampo,$ValorPadrao);
                break;
            case 'money':
                return $this->HTMLMonetario($ArCampo,$ValorPadrao);
                break;
            case 'float':
                return $this->HTMLPrecisao($ArCampo,$ValorPadrao);
                break;
            case 'lupa_popup':
                return $this->HTMLLupaPopUp($ArCampo,$ValorPadrao);
                break;
        }
    }

    /*
     * VARCHAR
     */
    public function HTMLVarchar($ArCampo,$ValorPadrao = ''){
        if($ArCampo['exibe_formulario'] == 0){
            $StringRetorno = '<input type="hidden" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" value="'.$ValorPadrao.'" />';
            return $StringRetorno;
        }

        $StringRetorno = '<div id="div'.$this->PrefixoCampo.$ArCampo['id_campo'].'" class="venda_div_campo">';
        $StringRetorno .= '<input type="text" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" size="'.$ArCampo['tamanho_campo'].'" maxlength="'.$ArCampo['max_carac'].'" value="'.$ValorPadrao.'"';

        if($ArCampo['editavel'] == 0){
            $StringRetorno .= ' readonly="readonly" class="venda_input_readonly"';
        }

        $StringRetorno .= ' />';
        $StringRetorno .= '</div>';

        $StringRetorno .= $this->HTMLPersonalizado($ArCampo['id_campo'],$this->IdCadastro);

        return $StringRetorno;
    }

    /*
     * INTEIRO
     */
    public function HTMLInteiro($ArCampo,$ValorPadrao = ''){
        if($ArCampo['exibe_formulario'] == 0){
            $StringRetorno = '<input type="hidden" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" value="'.$ValorPadrao.'" />';
            return $StringRetorno;
        }

        $StringRetorno = '<div id="div'.$this->PrefixoCampo.$ArCampo['id_campo'].'" class="venda_div_campo">';
        $StringRetorno .= '<input type="text" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" size="'.$ArCampo['tamanho_campo'].'" maxlength="'.$ArCampo['max_carac'].'" value="'.$ValorPadrao.'"';

        if($ArCampo['editavel'] == 0){
            $StringRetorno .= ' readOnly="readOnly" class="venda_input_readonly textright"';
        }

        $StringRetorno .= ' />';
        $StringRetorno .= '</div>';

        $StringRetorno .= $this->HTMLPersonalizado($ArCampo['id_campo'],$this->IdCadastro);

        return $StringRetorno;
    }

    /*
     * COMBOBOX
     */
    public function HTMLCombobox($ArCampo,$ValorPadrao = ''){
        $ArCampo['sql_lupa'] = $this->replaceValorPadrao($ArCampo['sql_lupa']);

        if($ArCampo['exibe_formulario'] == 0){
            $StringRetorno = '<input type="hidden" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" value="'.$ValorPadrao.'" />';
            return $StringRetorno;
        }

        if(trim($ArCampo['sql_lupa']) == '' || trim($ArCampo['id_campo_lupa']) == '' || trim($ArCampo['campo_descr_lupa']) == ''){
            return false;
        }

        $StringRetorno = '<div id="div'.$this->PrefixoCampo.$ArCampo['id_campo'].'" class="venda_div_campo">';
        $StringRetorno .= '<select name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" '.$ArCampo['evento_change'].(($ArCampo['editavel'] == 0)?' class="venda_input_readonly"':'').'>';
        $StringRetorno .= ($ArCampo['editavel'] == 0)?'':'<option value=""'.$disabled.'></option>';
        $QryCombobox = query($ArCampo['sql_lupa']." ORDER BY ".$ArCampo['campo_descr_lupa']);

        while($ArCombobox = farray($QryCombobox)){
            if($ArCampo['editavel'] == 0 && $ValorPadrao != $ArCombobox[$ArCampo['id_campo_lupa']]){
                continue;
            }
            $selected = ($ValorPadrao == $ArCombobox[$ArCampo['id_campo_lupa']])?' selected="selected" ':'';
            $StringRetorno .= '<option value="'.$ArCombobox[$ArCampo['id_campo_lupa']].'"'.$selected.$disabled.'>'.$ArCombobox[$ArCampo['campo_descr_lupa']].'</option>';
        }

        $StringRetorno .= '</select>';
        $StringRetorno .= '</div>';

        $StringRetorno .= $this->HTMLPersonalizado($ArCampo['id_campo'],$this->IdCadastro);

        return $StringRetorno;
    }

    /*
     * DATE
     */
    public function HTMLDate($ArCampo,$ValorPadrao = ''){
        if($ArCampo['exibe_formulario'] == 0){
            $StringRetorno = '<input type="hidden" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" value="'.$ValorPadrao.'" />';
            return $StringRetorno;
        }

        $StringRetorno = '<div id="div'.$this->PrefixoCampo.$ArCampo['id_campo'].'" class="venda_div_campo">';
        $StringRetorno .= '<input type="text" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" maxlength="10" readOnly="readOnly"';
        if($ArCampo['editavel'] == 0){
            $StringRetorno .= ' class="venda_campo_data venda_input_readonly"';
        }
        else{
            $StringRetorno .= ' class="venda_campo_data"';
        }

        $StringRetorno .= ' value="'.$ValorPadrao.'"/>';

        if($ArCampo['editavel'] == 1){
            $DataMaxima = ($ArCampo['maxdate'] != '')?',maxDate:'.$ArCampo['maxdate']:'';
            $DataMinima = ($ArCampo['mindate'] != '')?',minDate:'.$ArCampo['mindate']:'';
            $StringRetorno .= '<script language="JavaScript"> $(document).ready(function(){ $("#'.$this->PrefixoCampo.$ArCampo['id_campo'].'").datepicker({showOn: "button",buttonImage: "'.$this->CaminhoBase.'images/agenda.gif",buttonImageOnly: true,changeMonth:true, changeYear:true '.$DataMinima.$DataMaxima.'}); $("#'.$this->PrefixoCampo.$ArCampo['id_campo'].'").val(\''.$ValorPadrao.'\'); $("#btn_limpar'.$this->PrefixoCampo.$ArCampo['id_campo'].'").click(function(){ $("#'.$this->PrefixoCampo.$ArCampo['id_campo'].'").val(\'\'); }); }); </script>';
            if($ArCampo['sn_obrigatorio'] == 0){
                $StringRetorno .= '&nbsp;';
                $StringRetorno .= '<img border="0" width="15" height="15" style="cursor:pointer;" id="btn_limpar'.$this->PrefixoCampo.$ArCampo['id_campo'].'" src="'.$this->CaminhoBase.'images/btn_eraser.PNG" alt="Limpar" title="Limpar">';
            }
        }

        $StringRetorno .= '</div>';

        $StringRetorno .= $this->HTMLPersonalizado($ArCampo['id_campo'],$this->IdCadastro);

        return $StringRetorno;
    }

    /*
     * MEMO
     */
    public function HTMLMemo($ArCampo,$ValorPadrao = ''){
        if($ArCampo['exibe_formulario'] == 0){
            $StringRetorno = '<input type="hidden" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" value="'.$ValorPadrao.'" />';
            return $StringRetorno;
        }

        $ReadOnly = ($ArCampo['editavel'] == 0)?' readOnly="readOnly" ':'';
        $ClassReadOnly = ($ArCampo['editavel'] == 0)?' venda_input_readonly':'';

        $StringRetorno = '<div id="div'.$this->PrefixoCampo.$ArCampo['id_campo'].'" class="venda_div_campo">';
        $StringRetorno .= '<textarea type="text" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" cols="70" rows="7" class="venda_campo_textarea'.$ClassReadOnly.'"'.$ReadOnly.'>'.$ValorPadrao.'</textarea>';
        $StringRetorno .= '</div>';

        $StringRetorno .= $this->HTMLPersonalizado($ArCampo['id_campo'],$this->IdCadastro);

        return $StringRetorno;
    }

    /*
     * SIM NÃO
     */
    public function HTMLSimNao($ArCampo,$ValorPadrao = ''){
        if($ArCampo['exibe_formulario'] == 0){
            $StringRetorno = '<input type="hidden" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" value="'.$ValorPadrao.'" />';
            return $StringRetorno;
        }

        $StringRetorno = '<div id="div'.$this->PrefixoCampo.$ArCampo['id_campo'].'" class="venda_div_campo">';
        $StringRetorno .= '<select name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'"'.(($ArCampo['editavel'] == 0)?' class="venda_input_readonly"':'').'>';
        if($ArCampo['editavel'] == 1){
            $StringRetorno .= '<option value=""></option>';
            $StringRetorno .= '<option value="1"'.(($ValorPadrao == 1)?' selected="selected" ':'').'>Sim</option>';
            $StringRetorno .= '<option value="0"'.(($ValorPadrao == 0)?' selected="selected" ':'').'>N&atilde;o</option>';
        }
        elseif($ValorPadrao == 1){
            $StringRetorno .= '<option value="1">Sim</option>';
        }
        elseif($ValorPadrao == 0){
            $StringRetorno .= '<option value="0">N&atilde;o</option>';
        }
        $StringRetorno .= '</select>';
        $StringRetorno .= '</div>';

        $StringRetorno .= $this->HTMLPersonalizado($ArCampo['id_campo'],$this->IdCadastro);

        return $StringRetorno;
    }

    /*
     * MONETÁRIO
     */
    public function HTMLMonetario($ArCampo,$ValorPadrao = ''){
        if($ArCampo['exibe_formulario'] == 0){
            $StringRetorno = '<input type="hidden" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" value="'.$ValorPadrao.'" />';
            return $StringRetorno;
        }

        $StringRetorno = '<div id="div'.$this->PrefixoCampo.$ArCampo['id_campo'].'" class="venda_div_campo">';
        $StringRetorno .= '<input type="text" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" size="'.$ArCampo['tamanho_campo'].'" maxlength="'.$ArCampo['max_carac'].'" value="'.$ValorPadrao.'"';

        if($ArCampo['editavel'] == 0){
            $StringRetorno .= ' readonly="readonly" class="venda_input_readonly"';
        }

        $StringRetorno .= ' />';
        $StringRetorno .= '</div>';

        $StringRetorno .= $this->HTMLPersonalizado($ArCampo['id_campo'],$this->IdCadastro);

        return $StringRetorno;
    }

    /*
     * PRECISÃO
     */
    public function HTMLPrecisao($ArCampo,$ValorPadrao = ''){
        if($ArCampo['exibe_formulario'] == 0){
            $StringRetorno = '<input type="hidden" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" value="'.$ValorPadrao.'" />';
            return $StringRetorno;
        }

        $StringRetorno = '<div id="div'.$this->PrefixoCampo.$ArCampo['id_campo'].'" class="venda_div_campo">';
        $StringRetorno .= '<input type="text" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" size="'.$ArCampo['tamanho_campo'].'" maxlength="'.$ArCampo['max_carac'].'" value="'.$ValorPadrao.'"';

        $StringRetorno .= ' style="text-align:right;"';

        if($ArCampo['editavel'] == 0){
            $StringRetorno .= ' readonly="readonly" class="venda_input_readonly"';
        }

        $StringRetorno .= ' />';
        $StringRetorno .= '</div>';

        $StringRetorno .= $this->HTMLPersonalizado($ArCampo['id_campo'],$this->IdCadastro);

        return $StringRetorno;
    }

    /*
     * LUPA POPUP
     */
    public function HTMLLupaPopUp($ArCampo,$ValorPadrao = ''){
        if($ArCampo['exibe_formulario'] == 0){
            $StringRetorno = '<input type="hidden" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" value="'.$ValorPadrao.'" />';
            return $StringRetorno;
        }
        /*
         * Obtendo valor da descrição do campo de lupa
         */
        $custom_descr_lupa = '';

        $custom_qry_gera_cad_campo = query("(SELECT numreg,id_campo,id_funcao_lupa,id_campo_lupa,campo_descr_lupa,filtro_fixo FROM is_gera_cad_campos WHERE id_funcao = '".$this->IdCadastro."' AND id_campo = '".$ArCampo['id_campo']."') UNION (SELECT numreg,id_campo,id_funcao_lupa,id_campo_lupa,campo_descr_lupa,filtro_fixo FROM is_gera_cad_campos_custom WHERE id_funcao = '".$this->IdCadastro."' AND id_campo = '".$ArCampo['id_campo']."')");
        $custom_ar_gera_cad_campo = farray($custom_qry_gera_cad_campo);

        $custom_qry_gera_cad = query("SELECT nome_tabela FROM is_gera_cad WHERE id_cad = '".$custom_ar_gera_cad_campo['id_funcao_lupa']."'");
        $custom_ar_gera_cad = farray($custom_qry_gera_cad);

        $custom_qry_gera_cad_lupa = query("SELECT nome_tabela FROM is_gera_cad WHERE id_cad = '".$custom_ar_gera_cad_campo['id_funcao_lupa']."'");
        $custom_ar_gera_cad_lupa = farray($custom_qry_gera_cad_lupa);

        if($ValorPadrao != ''){
            $custom_qry_gera_cad_dados = query("SELECT ".$custom_ar_gera_cad_campo['id_campo_lupa']." FROM ".$custom_ar_gera_cad['nome_tabela']." WHERE ".$custom_ar_gera_cad_campo['id_campo_lupa']." = '".$ValorPadrao."'");

            $custom_ar_gera_cad_dados = farray($custom_qry_gera_cad_dados);

            $custom_qry_gera_cad_dados_lupa = query("SELECT numreg,".$custom_ar_gera_cad_campo['id_campo_lupa'].",".$custom_ar_gera_cad_campo['campo_descr_lupa']." FROM ".$custom_ar_gera_cad['nome_tabela']." WHERE ".$custom_ar_gera_cad_campo['id_campo_lupa']." = '".$custom_ar_gera_cad_dados[$custom_ar_gera_cad_campo['id_campo_lupa']]."'");

            $custom_ar_gera_cad_dados_lupa = farray($custom_qry_gera_cad_dados_lupa);

            $custom_descr_lupa = $custom_ar_gera_cad_dados_lupa[$custom_ar_gera_cad_campo['campo_descr_lupa']];
        }

        $StringRetorno = '<div id="div'.$this->PrefixoCampo.$ArCampo['id_campo'].'" class="venda_div_campo">';
        if($this->OcultaCodLupaPopUp != 1){
            $StringRetorno .= '<input type="text" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" readOnly="readOnly" class="venda_campo_lupapopup'.(($ArCampo['editavel'] == 0)?' venda_input_readonly':'').'" value="'.str_replace('"','\"',$ValorPadrao).'">';
            $StringRetorno .= ' - ';
        }
        else{
            $StringRetorno .= '<input type="hidden" name="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.$ArCampo['id_campo'].'" value="'.str_replace('"','\"',$ValorPadrao).'">';
        }
        $StringRetorno .= '<input type="text" name="'.$this->PrefixoCampo.'descr'.$ArCampo['id_campo'].'" id="'.$this->PrefixoCampo.'descr'.$ArCampo['id_campo'].'" readOnly="readOnly" class="venda_campo_lupapopupdescr'.(($ArCampo['editavel'] == 0)?' venda_input_readonly':'').'" value="'.str_replace('"','\"',$custom_descr_lupa).'">';
        $StringRetorno .= "\n";
        if($ArCampo['editavel'] == 1){
        $StringRetorno .= '&nbsp;';
        $StringRetorno .= '<img border="0" width="15" height="15" id="btn_lupa_busca'.$this->PrefixoCampo.$ArCampo['id_campo'].'" src="'.$this->CaminhoBase.'images/btn_busca.PNG" alt="Buscar" title="Buscar" style="cursor:pointer">';

        $StringRetorno .= '&nbsp;';
        $StringRetorno .= '<img border="0" width="15" height="15" id="btn_lupa_apagar'.$this->PrefixoCampo.$ArCampo['id_campo'].'" src="'.$this->CaminhoBase.'images/btn_eraser.PNG" alt="Limpar" title="Limpar" style="cursor:pointer">';
        }
        if($ArCampo['sn_ver_detalhes'] == 1){
        $StringRetorno .= '&nbsp;';
        $StringRetorno .= '<img border="0" width="15" height="15" id="btn_lupa_det'.$this->PrefixoCampo.$ArCampo['id_campo'].'" src="'.$this->CaminhoBase.'images/btn_det.PNG" alt="Ver Detalhes" title="Ver Detalhes" style="cursor:pointer">';
        }
        $StringRetorno .= '</div>';

        if($ArCampo['editavel'] == 1 || $ArCampo['sn_ver_detalhes'] == 1){
        /*
         * Montando JS dos campos
         */
        $StringRetorno .= "\n<script>\n";
        $StringRetorno .= '$(document).ready(function(){';
        $StringRetorno .= "\n";
        }
        if($ArCampo['editavel'] == 1){
             // Verificando se é possível reaproveitar o filtro fixo para extrair o pnpai
            $pnpai_lupa_filtro_fixo = '';
            $pnpai_lupa_filtro_fixo = $ArCampo["filtro_fixo"];
            $pnpai_lupa_filtro_fixo_pos_ini = strpos($pnpai_lupa_filtro_fixo, '@gfi');
            if (!($pnpai_lupa_filtro_fixo_pos === false)) {
                $pnpai_lupa_filtro_fixo_pos_fim = strpos($pnpai_lupa_filtro_fixo, '@gff');
                $pnpai_lupa_filtro_fixo = substr($pnpai_lupa_filtro_fixo, $pnpai_lupa_filtro_fixo_pos_ini + 4, $pnpai_lupa_filtro_fixo_pos_fim - ($pnpai_lupa_filtro_fixo_pos_ini + 4));
                if ($pnpai_lupa_filtro_fixo) {
                    $pnpai_lupa_filtro_fixo_campo_det = substr($ArCampo["filtro_fixo"], 0, strpos($ArCampo["filtro_fixo"], '@igual'));
                    $a_pnpai_lupa_filtro_fixo_sub = farray(query("select numreg from is_gera_cad_sub where id_funcao_detalhe = '" . $ArCampo["id_funcao_lupa"] . "' and campo_detalhe = '" . $pnpai_lupa_filtro_fixo_campo_det . "'"));
                    $pnpai_lupa_filtro_fixo = "&psubdet=".$a_pnpai_lupa_filtro_fixo_sub["numreg"]."&pnpai=' + $('#".$pnpai_lupa_filtro_fixo . "').val() + '";
                }
            }
            // fim do processo

            $BloqueioIncluir = ($ArCampo['sn_lupa_bloqueia_incluir'] == '1')?1:0;

            //Abrir popup(busca)
            $StringRetorno .= '$("#btn_lupa_busca'.$this->PrefixoCampo.$ArCampo['id_campo'].'").click(function(){window.open(\''.$this->CaminhoBase.'gera_cad_lista.php?pfuncao='.$custom_ar_gera_cad_campo['id_funcao_lupa'].'&pfixo='.$this->replaceFiltroFixo($custom_ar_gera_cad_campo['filtro_fixo']).'&pdrilldown=1&plupa='.$custom_ar_gera_cad_campo['numreg'].$pnpai_lupa_filtro_fixo.'&pbloqincluir='.$BloqueioIncluir.'\',\''.$this->IdCadastro.$ArCampo['id_campo'].'\',\'location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=810,height=550,top=250,left=250\').focus();});';
            $StringRetorno .= "\n";

            //Limpar campos(apagar)
            $StringRetorno .= '$("#btn_lupa_apagar'.$this->PrefixoCampo.$ArCampo['id_campo'].'").click(function(){ $("#'.$this->PrefixoCampo.$ArCampo['id_campo'].'").val(\'\'); $("#'.$this->PrefixoCampo.'descr'.$ArCampo['id_campo'].'").val(\'\'); });';
            $StringRetorno .= "\n";
        }
        if($ArCampo['sn_ver_detalhes'] == 1){
            //Abrir popup detalhes(det)
            $StringRetorno .= '$("#btn_lupa_det'.$this->PrefixoCampo.$ArCampo['id_campo'].'").click(function(){if($("#'.$this->PrefixoCampo.$ArCampo['id_campo'].'").val() != \'\'){ window.open(\''.$this->CaminhoBase.'gera_cad_detalhe.php?pfuncao='.$custom_ar_gera_cad_campo['id_funcao_lupa'].'&pnumreg=\' + $("#'.$this->PrefixoCampo.$ArCampo['id_campo'].'").val() + \'&pidlupa='.$custom_ar_gera_cad_campo['id_campo_lupa'].'&pdrilldown=1&plupa='.$custom_ar_gera_cad_campo['numreg'].'&pfixo=&pbloqincluir='.$BloqueioIncluir.'\',\''.$this->IdCadastro.$ArCampo['id_campo'].'\',\'location=0,menubar=0,resizable=1,status=1,toolbar=0,scrollbars=1,width=810,height=550,top=250,left=250\').focus();} else { alert(\'Nenhum registro selecionado!\'); } });';
            $StringRetorno .= "\n";
        }

        if($ArCampo['editavel'] == 1 || $ArCampo['sn_ver_detalhes'] == 1){
            $StringRetorno .= '});';

            $StringRetorno .= "\n";
            $StringRetorno .= "\n</script>\n";
        }
        $StringRetorno .= $this->HTMLPersonalizado($ArCampo['id_campo'],$this->IdCadastro);

        return $StringRetorno;
    }
}
?>