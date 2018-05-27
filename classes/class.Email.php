<?php
/*
 * class.Email.php
 * Autor: Alex
 * 24/02/2011 10:00:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class Email extends PHPMailer{

    private $ParametrosEmail;
    
    /**
     * Classe simplificada para enviar e-mail. Esta classe utiliza os dados de conta cadastrados em Parâmetros-->Parâmetros de Email.
     * Utilizar Apenas os métodos iniciados com "_".
     * @subpackage PHPMailer
     * @subpackage SMTP
     * @param type $IdConta
     */
    public function  __construct($IdConta=NULL){
                    
        if($_SESSION['id_usuario'] == ''){
            $SnUsaEmailUsuario = false;
        } 
        else {
            $SqlUsuario = "SELECT sn_utiliza_conta_email, sn_smtp_exige_autenticacao,smtp_servidor ,smtp_porta, smtp_login,smtp_senha, smtp_email, smtp_email_nome
                        FROM is_usuario WHERE numreg = ".$_SESSION['id_usuario']."";
            $QryUsuario = query($SqlUsuario);
            $ArUsuario = farray($QryUsuario);
            
            if($ArUsuario['sn_utiliza_conta_email'] == 0){
                $SnUsaEmailUsuario = false;
            }
            elseif ($ArUsuario['smtp_servidor'] == ''
                    || $ArUsuario['smtp_porta'] == ''
                    || $ArUsuario['smtp_login'] == ''
                    || $ArUsuario['smtp_senha'] == ''
                    || $ArUsuario['smtp_email'] == '') {
                
                $SnUsaEmailUsuario = false;
            }
            else{
              $SnUsaEmailUsuario = true;
            }
        }

        $this->ParametrosEmail = getDadosParametroEmail();

        if($SnUsaEmailUsuario){
            $this->ParametrosEmail['smtp_porta'] = $ArUsuario['smtp_porta'];
            $this->ParametrosEmail['smtp_servidor'] = $ArUsuario['smtp_servidor'];
            $this->ParametrosEmail['sn_smtp_exige_autenticacao'] = $ArUsuario['sn_smtp_exige_autenticacao'];
            $this->ParametrosEmail['smtp_login'] = $ArUsuario['smtp_login'];
            $this->ParametrosEmail['smtp_senha'] = $ArUsuario['smtp_senha'];
            $this->ParametrosEmail['smtp_email'] = $ArUsuario['smtp_email'];
            $this->ParametrosEmail['smtp_email_nome'] = $ArUsuario['smtp_email_nome'];
        }

        $this->WordWrap = 50;
        $this->IsSMTP();
        $this->IsHTML(true);
        $this->Port = $this->ParametrosEmail['smtp_porta'];
        $this->Host = $this->ParametrosEmail['smtp_servidor'];
        $this->SMTPAuth = (($this->ParametrosEmail['sn_smtp_exige_autenticacao'] == '1')?true:false);
        $this->Username = $this->ParametrosEmail['smtp_login'];
        $this->Password = $this->ParametrosEmail['smtp_senha'];
        $this->From = $this->ParametrosEmail['smtp_email'];
        $this->FromName = $this->ParametrosEmail['smtp_email_nome'];
        $this->AltBody = '';
        
        /* Definições das mensagens de erro */
        $this->language['authenticate']         = 'Erro de SMTP: Não foi possível autenticar.';
        $this->language['connect_host']         = 'Erro de SMTP: Não foi possível conectar com o servidor SMTP.';
        $this->language['data_not_accepted']    = 'Erro de SMTP: Dados não aceitos.';
        $this->language['empty_message']        = 'Message body empty';
        $this->language['encoding']             = 'Codificação desconhecida: ';
        $this->language['execute']              = 'Não foi possível executar: ';
        $this->language['file_access']          = 'Não foi possível acessar o arquivo: ';
        $this->language['file_open']            = 'Erro de Arquivo: Não foi possível abrir o arquivo: ';
        $this->language['from_failed']          = 'Os endereços de rementente a seguir falharam: ';
        $this->language['instantiate']          = 'Não foi possível instanciar a função mail.';
        $this->language['invalid_email']        = 'Email de envio inválido: ';
        $this->language['mailer_not_supported'] = ' mailer não suportado.';
        $this->language['provide_address']      = 'Você deve fornecer pelo menos um endereço de destinatário de email.';
        $this->language['recipients_failed']    = 'Erro de SMTP: Os endereços de destinatário a seguir falharam: ';
        $this->language['signing']              = 'Signing Error: ';
        $this->language['smtp_connect_failed']  = 'SMTP Connect() failed.';
        $this->language['smtp_error']           = 'SMTP server error: ';
        $this->language['variable_set']         = 'Cannot set or reset variable: ';
    }

    /**
     * Preenche o assunto do e-mail
     * @param string $Assunto
     */
    public function _Assunto($Assunto){
        $this->Subject = $Assunto;
    }

    /**
     * Preenche o corpo do e-mail
     * @param string $Corpo 
     */
    public function _Corpo($Corpo){
        $this->Body = $Corpo;
    }

    /**
     * Altera o remetente e o nome de máscara do e-mail
     * @param string $Email
     * @param string $Nome
     */
    public function _setRemetente($Email,$Nome){
        $this->From = ($Email != '')?$Email:$this->From;
        $this->FromName = ($Nome != '')?$Nome:$this->FromName;
    }

    public function _setRementeComoUsuarioLogado(){
        $Usuario = new Usuario($_SESSION['id_usuario']);
        $this->_setRemetente($Usuario->getDadosUsuario('email'), $Usuario->getDadosUsuario('nome_usuario'));
    }

    private function _AdicionaDestinatariosFixos(){
        if(trim($this->ParametrosEmail['smtp_enviar_copia_para']) != ''){
            $ArEmails = explode(';',$this->ParametrosEmail['smtp_enviar_copia_para']);
            foreach($ArEmails as $Email){
                $this->_AdicionaDestinatarioCCO($Email);
            }
        }
    }

    /**
     *  Adiciona um destinatário para envio
     * @param string $Email Endereço de e-mail
     * @param string $Nome Nome de máscara para endereço de e-mail
     * @return boolean
     */
    public function _AdicionaDestinatario($Email,$Nome=NULL){
        $Nome = ($Nome == NULL)?$Email:$Nome;
        return $this->AddAddress($Email,$Nome);
    }

    /**
     *  Adiciona um destinatário em cópia para envio
     * @param string $Email Endereço de e-mail
     * @param string $Nome Nome de máscara para endereço de e-mail
     * @return boolean
     */
    public function _AdicionaDestinatarioCC($Email,$Nome=NULL){
        $Nome = ($Nome == NULL)?$Email:$Nome;
        return $this->AddCC($Email,$Nome);
    }

    /**
     *  Adiciona um destinatário em cópia oculta para envio
     * @param string $Email Endereço de e-mail
     * @param string $Nome Nome de máscara para endereço de e-mail
     * @return boolean
     */
    public function _AdicionaDestinatarioCCO($Email,$Nome=NULL){
        $Nome = ($Nome == NULL)?$Email:$Nome;
        return $this->AddBCC($Email,$Nome);
    }

    /**
     * Adiciona um arquivo anexo ao e-mail
     * @param string $Caminho Caminho físico do arquivo a ser anexado
     * @param string $Nome Novo nome para o arquivo
     * @return boolean
     */
    public function _AdicionaAnexo($Caminho,$Nome=NULL){
        if($Nome == NULL){
            return $this->AddAttachment($Caminho, $Nome);
        }
        else{
            return $this->AddAttachment($Caminho);
        }
    }

    /**
     * Executa o envio do e-mail
     * @return boolean
     */
    public function _EnviaEmail(){
        if($this->ParametrosEmail['sn_envia_apenas_teste'] == '1'){ /* Se está ativado o envio para testes apenas */
            $ArrayDestinatariosOriginais = array();
            foreach($this->to as $Destinatario){
                $ArrayDestinatariosOriginais[] = $Destinatario[0];
            }
            $ArrayDestinatariosOriginaisCC = array();
            foreach($this->cc as $Destinatario){
                $ArrayDestinatariosOriginaisCC[] = $Destinatario[0];
            }
            $this->ClearAllRecipients();
            switch ($this->ParametrosEmail['id_tp_envio_teste']){
                case 1: /* Envio para e-mail específico */
                    if($this->ParametrosEmail['smtp_email_teste'] != ''){
                        $ArEmails = explode(';',$this->ParametrosEmail['smtp_email_teste']);
                        foreach($ArEmails as $Email){
                            $this->_AdicionaDestinatario($Email);
                        }
                    }
                    else{
                        $Mail->ErrorInfo = 'Email para teste não parametrizado.';
                        return false;
                    }
                    $this->_Corpo($this->Body.'<hr/> Este e-mail seria enviado para ('.implode(' ; ',$ArrayDestinatariosOriginais).') com cópia para ('.implode(' ; ',$ArrayDestinatariosOriginaisCC).')');
                    break;
                case 2: /* Envio para e-mail do usuário logado */
                    if($_SESSION['id_usuario'] == ''){
                        return false;
                    }
                    else{
                        $Usuario = new Usuario($_SESSION['id_usuario']);
                        $EmailUsuario = $Usuario->getDadosUsuario('email');
                        if(trim($EmailUsuario) == ''){
                            $Mail->ErrorInfo = 'Usuário logado não possui e-mail.';
                            return false;
                        }
                        else{
                            $this->_AdicionaDestinatario($EmailUsuario,$Usuario->getDadosUsuario('nome_usuario'));
                        }
                    }
                    break;
                case 3: /* Caso não envie e-mail para ninguém, retorna true, como se o e-mail foi enviado */
                    return true;
                    break;
            }
        }
        else{ /* Se não for teste, adiciona os destinatários fixos */
            $this->_AdicionaDestinatariosFixos();
        }
        
        /* Envio do e-mail */
        $Envio = $this->Send();
        
        $Emails = array();
        $EmailsCC = array();
        $EmailsCCO = array();
        $Anexos = array();
        foreach($this->to as $Email){
            $Emails[] = ($Email[1] != '' && $Email[1] != $Email[0])?$Email[1].'<'.$Email[0].'>':$Email[0];
        }
        foreach($this->cc as $Email){
            $EmailsCC[] = ($Email[1] != '' && $Email[1] != $Email[0])?$Email[1].'<'.$Email[0].'>':$Email[0];
        }
        foreach($this->bcc as $Email){
            $EmailsCCO[] = ($Email[1] != '' && $Email[1] != $Email[0])?$Email[1].'<'.$Email[0].'>':$Email[0];
        }
        foreach($this->attachment as $Anexo){
            $Anexos[] = $Anexo[1];
        }
        
        $ArSqlInsertLog = array(
            'data'          => date("Y-m-d"),
            'hora'          => date("H:i:s"),
            'id_usuario'    => $_SESSION['id_usuario'],
            'login_smtp'    => $this->Username,
            'email'         => implode(', ',$Emails),
            'email_cc'      => implode(', ',$EmailsCC),
            'email_cco'     => implode(', ',$EmailsCCO),
            'corpo'         => $this->Body,
            'anexo'         => implode(', ',$Anexos),
            'sn_ok'         => 1
        );
        if(!$Envio){
            $ArSqlInsertLog['sn_ok'] = 0;
        }
        $SqlInsertLog = AutoExecuteSql(TipoBancoDados, 'is_log_email_enviado', $ArSqlInsertLog, 'INSERT');
        query($SqlInsertLog);
        return $Envio;
    }     
}
?>