<?php
/*
 * class.ChamadoResposta.php
 * Autor: Alex
 * 08/12/2011 11:03:18
 */
class ChamadoResposta extends RegistroOasis{
    public $ObjChamado;
    private $Anexo = array();

    public function __construct(Chamado $ObjChamado,$Numreg = NULL){
        $this->NomeTabela = 'is_atividade_resposta_chamado';
        $this->ObjChamado = $ObjChamado;
        parent::__construct($Numreg);
        if($Numreg == NULL){
            $this->ArDados['id_atividade']  = $this->ObjChamado->getNumreg();
            $this->ArDados['dt_inicio']     = date("Y-m-d");
            $this->ArDados['hr_inicio']     = date("H:i:s");
            $this->ArDados['dt_fim']        = date("Y-m-d");
            $this->ArDados['hr_fim']        = date("H:i:s");
        }
        $this->CarregaAnexoBD();
    }

    public function AdicionaAnexo($CaminhoTemporario,$NomeArquivo){
        $Arquivo = new Arquivo();
        $Arquivo->setDado('nome_arquivo', $NomeArquivo);
        $Arquivo->setDado('url_arquivo', $NomeArquivo);
        $Arquivo->setDado('id_arquivo_categ', 0);
        $Arquivo->setDado('dt_documento', date("Y-m-d"));
        $Arquivo->setDado('id_atividade', $this->ObjChamado->getNumreg());
        $Arquivo->setDado('id_pessoa', $this->ObjChamado->getDado('id_pessoa'));
        $Arquivo->setDado('id_resposta_chamado', $this->getNumreg());
        $NumregAnexo = $Arquivo->GravaBD();
        if(!$NumregAnexo){
            return false;
        }
        $NomeArquivo = $Arquivo->getNumreg().$NomeArquivo;
        $Arquivo->setDado('url_arquivo',$NomeArquivo);
        $Arquivo->GravaBD();
        if($NumregAnexo){
            $this->Anexo[$NumregAnexo] = $Arquivo;
        }
        if(!$Arquivo->MoveCaminhoDefinitivo($CaminhoTemporario,$NomeArquivo)){
            $this->setMensagem('Arquivo anexo não pode ser gravado!');
            return false;
        }
        return true;
    }

    public function isAtendente(){
        return ($this->getDado('id_usuario') != '');
    }

    public function isCliente(){
        return ($this->getDado('id_usuario') == '');
    }

    public function CarregaAnexoBD(){
        $SqlAnexo = "SELECT numreg FROM is_arquivo WHERE id_resposta_chamado = '".$this->getNumreg()."'";
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
}
?>