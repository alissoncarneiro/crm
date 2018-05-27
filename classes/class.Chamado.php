<?php

/*
 * class.Chamado.php
 * Autor: Alex
 * 07/12/2011 09:37:48
 */

class Chamado extends RegistroOasis{

    private $IdTpAtividade;
    private $Pessoa;
    private $Resposta = array();
    private $NotaInterna = array();
    private $Anexo = array();
    public $PortalParametro;

    public function __construct($Numreg=NULL){
        $this->IdTpAtividade = 22;
        $this->NomeTabela = 'is_atividade';
        $this->PortalParametro = new PortalParametro();
        $Construct = parent::__construct($Numreg);
        if($Construct){
            $this->ArDados['id_tp_atividade'] = $this->IdTpAtividade;
        }
        else{
            return false;
        }
        if($this->getDado('id_pessoa') != ''){
            $this->Pessoa = new Pessoa($this->getDado('id_pessoa'));
        }
        $this->CarregaRespostasBD();
        $this->CarregaNotaInternaBD();
        $this->CarregaAnexoBD();
        return true;
    }

    public function setDado($Coluna, $Valor){
        if($Coluna == 'id_pessoa'){
            $this->Pessoa = new Pessoa($Valor);
        }
        parent::setDado($Coluna, $Valor);
    }

    public function isAberto(){
        return ($this->getDado('id_situacao') == '1');
    }

    public function isEncerrado(){
        return ($this->getDado('id_situacao') == '4');
    }

    public function getEmAtendimento(){
        $Sql = "SELECT numreg FROM is_atividade_chamado_atendimento WHERE id_atividade = ".$this->getNumreg()." AND sn_finalizado = 0";
        $Qry = query($Sql);
        $Ar = farray($Qry);
        if($Ar){
            return true;
        }
        return false;
    }

    public function TransfereParaUsuario($IdUsuario, $IdMotivo, $Obs){
        $this->setDado('id_usuario_resp', $IdUsuario);
        $this->GravaBD();
        $TextoLog = 'Transferência para o usuário '.DeparaCodigoDescricao('is_usuario', array('nome_usuario'), array('numreg' => $IdUsuario)).".\r\n";
        $TextoLog .= 'Motivo: '.DeparaCodigoDescricao('is_motivo_transf_chamado_portal', array('nome_motivo_transf_chamado_portal'), array('numreg' => $IdMotivo)).".\r\n";
        $TextoLog .= 'Obs: '.$Obs;
        $this->AdicionaLog($TextoLog);
    }
    
    public function TransfereParaProduto($IdProduto, $IdMotivo, $Obs){
        $this->setDado('id_produto', $IdProduto);
        $this->GravaBD();
        $TextoLog = 'Transferencia para o produto '.DeparaCodigoDescricao('is_produto', array('nome_produto'), array('numreg' => $IdProduto)).".\r\n";
        $TextoLog .= 'Motivo: '.DeparaCodigoDescricao('is_motivo_transf_chamado_portal', array('nome_motivo_transf_chamado_portal'), array('numreg' => $IdMotivo)).".\r\n";
        $TextoLog .= 'Obs: '.$Obs;
        $this->AdicionaLog($TextoLog);
    }

    public function AdicionaLog($Descricao){
        $ArInsertLog = array(
            'id_atividade' => $this->getNumreg(),
            'dthr_log' => date("Y-m-d H:i:s"),
            'id_usuario' => $_SESSION['id_usuario'],
            'descricao' => $Descricao
        );
        $SqlInsertLog = AutoExecuteSql(TipoBancoDados, 'is_log_chamado_portal', $ArInsertLog, 'INSERT');
        return query($SqlInsertLog);
    }

    public function getUsuarioAtendimentoAberto(){
        $Sql = "SELECT id_usuario FROM is_atividade_chamado_atendimento WHERE id_atividade = ".$this->getNumreg()." AND sn_finalizado = 0";
        $Qry = query($Sql);
        $Ar = farray($Qry);
        if($Ar){
            return $Ar['id_usuario'];
        }
        return false;
    }
    
    public function getUltimaResposta(){
        end($this->Resposta);
        $KeyUltimaResposta = key($this->Resposta);
        return $this->getResposta($KeyUltimaResposta);
    }

    public function getResposta($NumregReposta=NULL){
        return ($NumregReposta === NULL)?$this->Resposta:$this->Resposta[$NumregReposta];
    }

    public function getNotaInterna($NumregNotaInterna=NULL){
        return ($NumregNotaInterna === NULL)?$this->NotaInterna:$this->NotaInterna[$NumregNotaInterna];
    }

    public function CarregaRespostasBD(){
        if($this->Numreg != NULL){
            $SqlRespostas = "SELECT numreg FROM is_atividade_resposta_chamado WHERE id_atividade = '".$this->Numreg."' ORDER BY dt_inicio, hr_inicio ASC";
            $QryRespostas = query($SqlRespostas);
            while($ArRespostas = farray($QryRespostas)){
                $this->Resposta[$ArRespostas['numreg']] = new ChamadoResposta($this, $ArRespostas['numreg']);
            }
        }
    }

    public function CarregaNotaInternaBD(){
        if($this->Numreg != NULL){
            $SqlNotaInterna = "SELECT numreg FROM is_atividade_chamado_nota_interna WHERE id_atividade = '".$this->Numreg."' ORDER BY dt_nota_interna, hr_nota_interna ASC";
            $QryNotaInterna = query($SqlNotaInterna);
            while($ArNotaInterna = farray($QryNotaInterna)){
                $this->NotaInterna[$ArNotaInterna['numreg']] = new ChamadoNotaInterna($this, $ArNotaInterna['numreg']);
            }
        }
    }

    public function CarregaAnexoBD(){
        $SqlAnexo = "SELECT numreg FROM is_arquivo WHERE id_atividade = '".$this->getNumreg()."'";
        $QryAnexo = query($SqlAnexo);
        while($ArAnexo = farray($QryAnexo)){
            $this->Anexo[$ArAnexo['numreg']] = new Arquivo($ArAnexo['numreg']);
        }
    }

    public function PossuiAnexos(){
        return (count($this->Anexo) > 0);
    }

    public function getAnexo($NumregAnexo=NULL){
        return ($NumregAnexo === NULL)?$this->Anexo:$this->Anexo[$NumregAnexo];
    }

    public function SubstituiVariaveis($Texto){
        $ArrayReplace = array(
            '{NUMERO_CHAMADO}' => $this->Numreg,
            '{NOME_EMPRESA}' => $this->PortalParametro->getNomeEmpresa(),
            '{NOME_CLIENTE}' => $this->Pessoa->getDadoPessoa('razao_social_nome')
        );

        foreach($ArrayReplace as $k => $v){
            $Texto = str_replace($k, $v, $Texto);
        }
        return $Texto;
    }

    public function PossuiAcesso(){
        return true;
    }

    public function AdicionaAnexo($CaminhoTemporario, $NomeArquivo){
        $Arquivo = new Arquivo();
        $Arquivo->setDado('nome_arquivo', $NomeArquivo);
        $Arquivo->setDado('url_arquivo', $NomeArquivo);
        $Arquivo->setDado('id_arquivo_categ', 0);
        $Arquivo->setDado('dt_documento', date("Y-m-d"));
        $Arquivo->setDado('id_atividade', $this->getNumreg());
        $Arquivo->setDado('id_pessoa', $this->getDado('id_pessoa'));
        if(!$Arquivo->GravaBD()){
            return false;
        }
        $NomeArquivo = $Arquivo->getNumreg().$NomeArquivo;
        $Arquivo->setDado('url_arquivo',$NomeArquivo);
        $Arquivo->GravaBD();

        if(!$Arquivo->MoveCaminhoDefinitivo($CaminhoTemporario, $NomeArquivo)){
            $this->setMensagem('Arquivo anexo não pode ser gravado!');
            return false;
        }
        return true;
    }

    public function ExcluiChamado($Confirma=false){
        if($Confirma === true){
            $SqlDelete = "DELETE FROM is_atividade WHERE numreg = ".$this->getNumreg();
            query($SqlDelete);
        }
    }

    public function AdicionaRespostaCliente($ObsResposta){
        $Resposta = new ChamadoResposta($this);
        $Resposta->setDado('id_contato', $_SESSION['id_usuario_portal']);
        $Resposta->setDado('id_pessoa', $_SESSION['id_pessoa_portal']);
        $Resposta->setDado('obs_resposta', $ObsResposta);
        $NumregResposta = $Resposta->GravaBD();
        if(!$NumregResposta){
            return false;
        }
        $this->Resposta[$NumregResposta] = $Resposta;
        return $NumregResposta;
    }

    public function AdicionaNotaInterna($ObsNotaInterna){
        $NotaInterna = new ChamadoNotaInterna($this);
        $NotaInterna->setDado('dt_nota_interna', date("Y-m-d"));
        $NotaInterna->setDado('hr_nota_interna', date("H:i:s"));
        $NotaInterna->setDado('obs_nota_interna', $ObsNotaInterna);
        $NumregResposta = $NotaInterna->GravaBD();
        if(!$NumregResposta){
            return false;
        }
        $this->NotaInterna[$NumregResposta] = $NotaInterna;
        return $NumregResposta;
    }

    public function AdicionaRespostaAtendente($ObsResposta){
        $Resposta = new ChamadoResposta($this);
        $Resposta->setDado('id_usuario', $_SESSION['id_usuario']);
        $Resposta->setDado('obs_resposta', $ObsResposta);
        $NumregResposta = $Resposta->GravaBD();
        if(!$NumregResposta){
            return false;
        }
        $this->Resposta[$NumregResposta] = $Resposta;
        return $NumregResposta;
    }

    public function AdicionaAnexoResposta($NumregResposta, $CaminhoTemporario, $NomeArquivo){
        $Resposta = $this->Resposta[$NumregResposta];
        return $Resposta->AdicionaAnexo($CaminhoTemporario, $NomeArquivo);
    }

    public function getTextoAbertura(){
        $Texto = $this->SubstituiVariaveis($this->PortalParametro->getTextoAberturaChamado());
        $Texto = str_replace('{EMAIL_DESTINO}', $this->getDado('email_contato'), $Texto);
        return $Texto;
    }

    public function AtenderChamado($IdUsuario){
        if(!$this->isAberto()){
            return false;
        }
        $ArSqlInsert = array(
            'id_atividade' => $this->getNumreg(),
            'id_usuario' => $IdUsuario,
            'dthr_inicio' => date("Y-m-d H:i:s"),
            'dthr_fim' => date("Y-m-d H:i:s"),
            'tempo_gasto' => '00:00'
        );
        $SqlInsert = AutoExecuteSql(TipoBancoDados, 'is_atividade_chamado_atendimento', $ArSqlInsert, 'INSERT');
        $IdUsuarioResp = $this->getDado('id_usuario_resp');
        if(empty($IdUsuarioResp)){
            $this->setDado('id_usuario_resp', $IdUsuario);
        }
        if($this->getDado('dthr_atendido') == ''){
            $this->setDado('dthr_atendido', date("Y-m-d H:i:s"));
        }
        $this->setDado('sn_em_atendimento', 1);
        $this->GravaBD();
        return iquery($SqlInsert);
    }

    public function FinalizaAtendimentoChamado($IdUsuario){
        $Sql = "SELECT numreg,dthr_inicio FROM is_atividade_chamado_atendimento WHERE id_atividade = ".$this->getNumreg()." AND sn_finalizado = 0";
        $Qry = query($Sql);
        $Ar = farray($Qry);
        if(!$Ar){
            return false;
        }
        $DtHrInicio = $Ar['dthr_inicio'];
        $DtHrFim = date("Y-m-d H:i:s");
        $TsDtHrInicio = strtotime($DtHrInicio);
        $TsDtHrFim = strtotime($DtHrFim);
        $DiferencaSec = $TsDtHrFim - $TsDtHrInicio;
        $DiferencaHr = floor($DiferencaSec / 3600);
        $DiferencaSec -= $DiferencaHr * 3600;
        $DiferencaMin = floor($DiferencaSec / 60);
        $DiferencaSec -= $DiferencaMin * 60;

        $TempoGasto = sprintf('%02d:%02d:%02d', $DiferencaHr, $DiferencaMin, $DiferencaSec);

        $ArSqlUpdate = array(
            'numreg' => $Ar['numreg'],
            'dthr_fim' => $DtHrFim,
            'tempo_gasto' => $TempoGasto,
            'sn_finalizado' => 1
        );
        $SqlUpdate = AutoExecuteSql(TipoBancoDados, 'is_atividade_chamado_atendimento', $ArSqlUpdate, 'UPDATE', array('numreg'));
        if(query($SqlUpdate)){
            $this->setDado('sn_em_atendimento', 0);
            $this->GravaBD();
            $this->AtualizaTotalTempoGastoAtendimentoBD();
            return true;
        }
        return false;
    }

    public function AtualizaTotalTempoGastoAtendimentoBD(){
        $SqlAtendimentos = "SELECT tempo_gasto FROM is_atividade_chamado_atendimento WHERE id_atividade = ".$this->getNumreg();
        $QryAtendimentos = query($SqlAtendimentos);
        $TotalTempoGasto = '00:00';
        $DataHora = new DataHora();
        while($ArAtendimentos = farray($QryAtendimentos)){
            $TotalTempoGasto = $DataHora->SomaHoras($TotalTempoGasto,$ArAtendimentos['tempo_gasto']);
        }
        $this->setDado('tempo_total_atendimento', $TotalTempoGasto);
        $this->GravaBD();
    }

    /* Funções de e-mail */
    
    public function EnviaEmailAbertura(){
        $Assunto = $this->SubstituiVariaveis($this->PortalParametro->getAssuntoEmailAberturaChamado());
        $Texto = $this->SubstituiVariaveis($this->PortalParametro->getTextoEmailAberturaChamado());
        $Texto = str_replace('{EMAIL_DESTINO}', $this->getDado('email_contato'), $Texto);
        $Texto = nl2br($Texto);

        $Email = new Email();
        $Email->_AdicionaDestinatario($this->getDado('email_contato'), '');
        $Email->_Assunto($Assunto);
        $Email->_Corpo($Texto);
        return $Email->_EnviaEmail();
    }
    
    public function EnviaEmailAberturaGrupo(){
        $SqlUsuarios = "SELECT DISTINCT t2.nome_usuario,t2.email FROM is_portal_usuario_x_grupo t1 INNER JOIN is_usuario t2 ON t1.id_usuario = t2.numreg WHERE t1.id_grupo_usuario IN(SELECT id_grupo_usuario FROM is_portal_grupo_usuario_produto WHERE id_produto = '".$this->getDado('id_produto')."')";
        $QryUsuarios = query($SqlUsuarios);
        while($ArUsuarios = farray($QryUsuarios)){
            $ArUsuarios['email'] = trim($ArUsuarios['email']);
            if($ArUsuarios['email'] == ''){
                continue;
            }
            $Assunto = $this->SubstituiVariaveis($this->PortalParametro->getAssuntoGrupoAtendimento());
            $Texto = $this->SubstituiVariaveis($this->PortalParametro->getTextoGrupoAtendimento());
            $Texto = str_replace('{EMAIL_DESTINO}', $ArUsuarios['email'], $Texto);
            $Texto = nl2br($Texto);

            $Email = new Email();
            $Email->_AdicionaDestinatario($ArUsuarios['email'], $ArUsuarios['nome_usuario']);
            $Email->_Assunto($Assunto);
            $Email->_Corpo($Texto);
            $Email->_EnviaEmail();
        }
        return true;
    }

    public function EnviaEmailRespostaAtendente(){
        $Assunto = $this->SubstituiVariaveis($this->PortalParametro->getAssuntoRespostaAtendente());
        $Texto = $this->SubstituiVariaveis($this->PortalParametro->getTextoRespostaAtendente());
        $Texto = str_replace('{EMAIL_DESTINO}', $this->getDado('email_contato'), $Texto);
        $Texto = nl2br($Texto);

        $Email = new Email();
        $Email->_AdicionaDestinatario($this->getDado('email_contato'), '');
        $Email->_Assunto($Assunto);
        $Email->_Corpo($Texto);
        return $Email->_EnviaEmail();
    }

    public function EnviaEmailRespostaCliente(){
        $UsuarioResponsavel = new Usuario($this->getDado('id_usuario_resp'));
        $EmailDestino = trim($UsuarioResponsavel->getEmail());
        if($EmailDestino == ''){
            $this->setMensagem('Email de destino em branco.');
            return false;
        }
        $Assunto = $this->SubstituiVariaveis($this->PortalParametro->getAssuntoRespostaCliente());
        $Texto = $this->SubstituiVariaveis($this->PortalParametro->getTextoRespostaCliente());
        $Texto = str_replace('{EMAIL_DESTINO}', $EmailDestino, $Texto);
        $Texto = nl2br($Texto);

        $Email = new Email();
        $Email->_AdicionaDestinatario($EmailDestino, '');
        $Email->_Assunto($Assunto);
        $Email->_Corpo($Texto);
        return $Email->_EnviaEmail();
    }
    
    public function EnviaEmailAlertaChamadoPendente(){
        $SqlUsuarios = "SELECT DISTINCT t2.nome_usuario,t2.email FROM is_portal_usuario_x_grupo t1 INNER JOIN is_usuario t2 ON t1.id_usuario = t2.numreg WHERE t1.id_grupo_usuario IN(SELECT id_grupo_usuario FROM is_portal_grupo_usuario_produto WHERE id_produto = '".$this->getDado('id_produto')."')";
        $QryUsuarios = query($SqlUsuarios);
        while($ArUsuarios = farray($QryUsuarios)){
            $ArUsuarios['email'] = trim($ArUsuarios['email']);
            if($ArUsuarios['email'] == ''){
                continue;
            }
            $Assunto = $this->SubstituiVariaveis($this->PortalParametro->getAssuntoAlertaChamadoPendente());
            $Texto = $this->SubstituiVariaveis($this->PortalParametro->getTextoAlertaChamadoPendente());
            $Texto = str_replace('{EMAIL_DESTINO}', $ArUsuarios['email'], $Texto);
            $Texto = nl2br($Texto);

            $Email = new Email();
            $Email->_AdicionaDestinatario($ArUsuarios['email'], $ArUsuarios['nome_usuario']);
            $Email->_Assunto($Assunto);
            $Email->_Corpo($Texto);
            $Email->_EnviaEmail();
        }
        return true;
    }
    
    public function EnviaEmailAlertaPreencerramentoClienteEAtendente(){
        $UsuarioResponsavel = new Usuario($this->getDado('id_usuario_resp'));
        $NomeUsuarioResponsavel = '';
        $EmailUsuarioResponsavel = '';
        if($UsuarioResponsavel){
            $NomeUsuarioResponsavel = $UsuarioResponsavel->getNome();
            $EmailUsuarioResponsavel = trim($UsuarioResponsavel->getEmail());            
        }
        $Assunto = $this->SubstituiVariaveis($this->PortalParametro->getAssuntoAlertaPreEncerramento());
        $Texto = $this->SubstituiVariaveis($this->PortalParametro->getTextoAlertaPreEncerramento());
        $Texto = str_replace('{EMAIL_DESTINO}', $this->getDado('email_contato'), $Texto);
        $Texto = nl2br($Texto);

        $Email = new Email();
        $Email->_AdicionaDestinatario($this->getDado('email_contato'), $this->getDado('nome_contato'));
        if($EmailUsuarioResponsavel != ''){
            $Email->_AdicionaDestinatarioCC($EmailUsuarioResponsavel,$NomeUsuarioResponsavel);
            echo 'adicionando cópia para '.$EmailUsuarioResponsavel;
        }
        $Email->_Assunto($Assunto);
        $Email->_Corpo($Texto);
        $Email->_EnviaEmail();

        return true;
    }

    public function EncerraChamadoAtendente(){
        $this->FinalizaAtendimentosEmAberto();
        $this->AdicionaRespostaAtendente('Chamado encerrado pelo atendente');
        $this->setDado('id_situacao', 4);
        $this->setDado('id_status_chamado', $this->PortalParametro->getIdStatusEncerramento());
        $this->GravaBD();
        return true;
    }

    public function EncerraChamadoCliente(){
        $this->FinalizaAtendimentosEmAberto();
        $this->AdicionaRespostaCliente('Chamado encerrado pelo cliente');
        $this->setDado('id_situacao', 4);
        $this->setDado('id_status_chamado', $this->PortalParametro->getIdStatusEncerramento());
        $this->GravaBD();
        return true;
    }
    
    public function FinalizaAtendimentosEmAberto(){
        $Sql = "SELECT numreg,dthr_inicio FROM is_atividade_chamado_atendimento WHERE id_atividade = ".$this->getNumreg()." AND sn_finalizado = 0";
        $Qry = query($Sql);
        while($Ar = farray($Qry)){
            $DtHrInicio = $Ar['dthr_inicio'];
            $DtHrFim = date("Y-m-d H:i:s");
            $TsDtHrInicio = strtotime($DtHrInicio);
            $TsDtHrFim = strtotime($DtHrFim);
            $DiferencaSec = $TsDtHrFim - $TsDtHrInicio;
            $DiferencaHr = floor($DiferencaSec / 3600);
            $DiferencaSec -= $DiferencaHr * 3600;
            $DiferencaMin = floor($DiferencaSec / 60);
            $DiferencaSec -= $DiferencaMin * 60;

            $TempoGasto = sprintf('%02d:%02d:%02d', $DiferencaHr, $DiferencaMin, $DiferencaSec);

            $ArSqlUpdate = array(
                'numreg'            => $Ar['numreg'],
                'dthr_fim'          => $DtHrFim,
                'tempo_gasto'       => $TempoGasto,
                'sn_finalizado'     => 1
            );
            $SqlUpdate = AutoExecuteSql(TipoBancoDados, 'is_atividade_chamado_atendimento', $ArSqlUpdate, 'UPDATE', array('numreg'));
            $QryUpdate = query($SqlUpdate);
        }
        $this->setDado('sn_em_atendimento', 0);
        $this->GravaBD();
        $this->AtualizaTotalTempoGastoAtendimentoBD();
        return true;
    }

    public static function getTempoDecorridoAbertura($IdChamado){
        $SqlChamado = "SELECT t1.numreg,t1.id_pessoa,t1.dt_cadastro,t1.hr_cadastro,(t2.qtde_horas_prz*60) AS minutos_sla FROM is_atividade t1 INNER JOIN is_prioridade t2 ON t1.id_prioridade = t2.numreg WHERE t1.numreg = '".$IdChamado."'";
        $QryChamado = query($SqlChamado);
        $ArChamado = farray($QryChamado);

        $DtHrAbertura = substr($ArChamado['dt_cadastro'], 0, 10).' '.$ArChamado['hr_cadastro'].':00';
        $MinutosSLA = $ArChamado['minutos_sla'];
        $DataHora = new DataHora();
        return $DataHora->CalculaMinutosUteisDecorridos($DtHrAbertura);
    }
}
?>