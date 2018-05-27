<?php
include_once('class.phpmailer.php');

# Criando classe para envio de e-mail
class SendMail extends PHPMailer {
	private $NoError = true;
	private $Description = NULL;
	public $Emails = array();
	public $ComCopia = array();
	public $CopiaOculta = array();
	public $destinatarios = array();
	
	public function __construct() {
		$this->WordWrap = 50; //QUEBRA DE LINHA
		//$this->SetLanguage('br');//TIPO DE IDIOMA UTILIZADO
	}
        public function getDescription(){
            return $this->Description;
        }
	//TEXTO COM FORMATO HTML
	public function CriaBody($MyBody) {
		$this->Body = $MyBody;
	}
	//ENVIA TEXTO AOS EMAILS QUE NAO SUPORTAM O FORMATO HTML
	public function BodyNoHTML($NoHTML) {
		$this->AltBody = $NoHTML;
	}
	//ADIIONA ENDEREÇOS NO PARA(TO)
	public function RecebeEmails() {
		if(is_array($this->Emails)) {
			foreach($this->Emails as $k => $v) {
				$this->AddAddress($k,$v);
			}
                        foreach($this->ComCopia as $cc => $cc2) {
				$this->AddCC($cc,$cc2);
			}
                        foreach($this->CopiaOculta as $bcc => $bcc2) {
				$this->AddBCC($bcc,$bcc2);
			}
		} else {
			$this->NoError = false;
			$this->Description = "Nenhum destinatário foi encontrado para o envio do e-mail.";
		}
	}
	//ADICIONA ENDEREÇOS EM COPIA
	public function RecebeComCopia($CCMails) {
		if(is_array($CCMails)){
			$this->ComCopia = $CCMails;
		}
	}
	//ADICIONA ENDEREÇOS NO COPIA OCULTA
	public function RecebeCopiaOculta($BCCMails) {
		if(is_array($BCCMails)){
			$this->CopiaOculta = $BCCMails;
		}
	}
	//ADICIONA ANEXOS AO DOCUMENTO
	public function AddAnexo($caminho, $novonome = NULL){
		if(!is_null($novonome)) {
			$this->AddAttachment($caminho, $novonome);
		} else {
			$this->AddAttachment($caminho);
		}
	}
	
	public function EnviarMail() {
		if($this->NoError) {
			if($this->Send()) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
?>