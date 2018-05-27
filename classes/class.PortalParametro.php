<?php
/*
 * class.PortalParametro.php
 * Autor: Alex
 * 09/12/2011 13:00
 */
class PortalParametro{
    private  $DadosParametro;

    public function  __construct(){
        $SqlParametro = "SELECT * FROM is_portal_parametro";
        $QryParametro = query($SqlParametro);
        $ArParametro = farray($QryParametro);
        $this->DadosParametro = $ArParametro;
    }

    public function getSnEnviaEmailAberturaChamado(){
        return ($this->DadosParametro['sn_envia_email_abertura_chamado'] == '1');
    }

    public function getTextoEmailAberturaChamado(){
        return $this->DadosParametro['texto_email_abertura_chamado'];
    }

    public function getNomeEmpresa(){
        return $this->DadosParametro['nome_empresa'];
    }

    public function getIdStatusAbertura(){
        return $this->DadosParametro['id_status_abertura'];
    }

    public function getIdStatusEncerramento(){
        return $this->DadosParametro['id_status_encerramento'];
    }

    public function getIdStatusRespondidoPeloCliente(){
        return $this->DadosParametro['id_status_resp_cli'];
    }

    public function getAssuntoEmailAberturaChamado(){
        return $this->DadosParametro['assunto_email_abertura_chamado'];
    }

    public function getTextoAberturaChamado(){
        return $this->DadosParametro['texto_abertura_chamado'];
    }

    public function getAssuntoRespostaAtendente(){
        return $this->DadosParametro['assunto_resposta_atendente'];
    }

    public function getTextoRespostaAtendente(){
        return $this->DadosParametro['texto_resposta_atendente'];
    }

    public function getAssuntoRespostaCliente(){
        return $this->DadosParametro['assunto_resposta_cliente'];
    }

    public function getTextoRespostaCliente(){
        return $this->DadosParametro['texto_resposta_cliente'];
    }
    
    public function getSnEnviaEmailGrupoAberturaChamado(){
        return ($this->DadosParametro['sn_env_email_grupo_aber_cha'] == '1');
    }
    
    public function getAssuntoGrupoAtendimento(){
        return $this->DadosParametro['assunto_email_grupo'];
    }

    public function getTextoGrupoAtendimento(){
        return $this->DadosParametro['texto_email_grupo'];
    }
    
    public function getAssuntoAlertaChamadoPendente(){
        return $this->DadosParametro['assunto_alerta_chamado_pendente'];
    }

    public function getTextoAlertaChamadoPendente(){
        return $this->DadosParametro['texto_alerta_chamado_pendente'];
    }
    
    public function getAssuntoAlertaPreEncerramento(){
        return $this->DadosParametro['assunto_pre_encerramento_cliente'];
    }

    public function getTextoAlertaPreEncerramento(){
        return $this->DadosParametro['texto_pre_encerramento_cliente'];
    }

    public function getTextoRodape(){
        return $this->DadosParametro['texto_rodape'];
    }
}
?>