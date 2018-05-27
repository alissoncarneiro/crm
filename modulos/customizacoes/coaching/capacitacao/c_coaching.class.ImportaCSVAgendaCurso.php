<?php
/*
 * c_coaching.class.ImportaCSVAgendaCurso
 * Autor: Alex
 * 25/08/2011 15:14:14
 */
class c_coachingImportaCSVAgendaCurso{
    private $CaminhoArquivo;
    private $Mensagem = array();
    private $LinhaAtual;
    private $UltimaAgendaInserida;

    public $QtdeRegistrosImportados = 0;

    public function __construct($CaminhoArquivo){
        if(!file_exists($CaminhoArquivo)){
            return false;
        }
        $this->CaminhoArquivo = $CaminhoArquivo;
    }

    private function setMensagem($Mensagem){
        $this->Mensagem[] = $Mensagem;
    }

    public function getMensagem($RetornaEmArray=false,$Separador = ' | '){
        if($RetornaEmArray == true){
            return $this->Mensagem;
        }
        return implode($Separador,$this->Mensagem);
    }

    public function ExecutaDeparaLinha($Linha){
        $ArrayCNPJCPF = array(1,29,30,31,32,33,34);
        $ArrayReplaceCNPJCPFDe = array(' ','-','/','.','\\');
        $ArrayReplaceCNPJCPFPara = array('','','','','');
        foreach($Linha as $Chave => $Valor){
            if(array_search($Chave, $ArrayCNPJCPF) !== false){
                $Linha[$Chave] = str_replace($ArrayReplaceCNPJCPFDe,$ArrayReplaceCNPJCPFPara, $Valor);
            }
        }
        $Linha[0] = DeparaCodigoDescricao('is_estabelecimento',array('numreg'),array('nome_estabelecimento' => $Linha[0])); /* Estabelecimento */
        $Linha[1] = DeparaCodigoDescricao('is_pessoa',array('numreg'),array('cnpj_cpf' => $Linha[1])); /* SBC / Licenciado */
        $Linha[2] = DeparaCodigoDescricao('c_coaching_curso',array('numreg'),array('nome_curso' => $Linha[2])); /* Curso */
        $Linha[3] = DeparaCodigoDescricao('c_coaching_parte',array('numreg'),array('id_curso' => $Linha[2], 'nome_parte' => $Linha[3])); /* Parte */
        $Linha[4] = DeparaCodigoDescricao('c_coaching_modulo',array('numreg'),array('nome_modulo' => $Linha[4], 'id_parte' => $Linha[3], 'id_curso' => $Linha[2])); /* Módulo */
        $Linha[5] = DeparaCodigoDescricao('is_usuario',array('numreg'),array('id_usuario' => $Linha[5])); /* Instrutor */
        $Linha[6] = DeparaCodigoDescricao('c_coaching_local_curso',array('numreg'),array('nome_local_curso' => $Linha[6])); /* Local do Curso */
        $Linha[7] = DeparaCodigoDescricao('c_coaching_hotel',array('numreg'),array('nome_hotel' => $Linha[7])); /* Hotel */

        $Linha[10] = dtbr2en($Linha[10]); /* Dt. Lim. Insc. */
        $Linha[11] = dtbr2en($Linha[11]); /* Data 1 */
        $Linha[14] = dtbr2en($Linha[14]); /* Data 2 */
        $Linha[17] = dtbr2en($Linha[17]); /* Data 3 */
        $Linha[20] = dtbr2en($Linha[20]); /* Data 4 */
        $Linha[23] = dtbr2en($Linha[23]); /* Data 5 */
        $Linha[26] = dtbr2en($Linha[26]); /* Data 6 */

        $Linha[29] = DeparaCodigoDescricao('is_pessoa',array('numreg'),array('cnpj_cpf' => $Linha[29])); /* Staff 1 */
        $Linha[30] = DeparaCodigoDescricao('is_pessoa',array('numreg'),array('cnpj_cpf' => $Linha[30])); /* Staff 2 */
        $Linha[31] = DeparaCodigoDescricao('is_pessoa',array('numreg'),array('cnpj_cpf' => $Linha[31])); /* Staff 3 */
        $Linha[32] = DeparaCodigoDescricao('is_pessoa',array('numreg'),array('cnpj_cpf' => $Linha[32])); /* Staff 4 */
        $Linha[33] = DeparaCodigoDescricao('is_pessoa',array('numreg'),array('cnpj_cpf' => $Linha[33])); /* Staff 5 */
        $Linha[34] = DeparaCodigoDescricao('is_pessoa',array('numreg'),array('cnpj_cpf' => $Linha[34])); /* Staff 6 */
        return $Linha;
    }

    public function ValidaObrigatorios($Linha){
        if($Linha[0] == ''){
            $this->setMensagem('Linha:'.$this->LinhaAtual.' Estabelecimento vazio ou não encontrado!');
            return false;
        }
        elseif($Linha[1] == ''){
            $this->setMensagem('Linha:'.$this->LinhaAtual.' SBC / Licenciado vazio ou não encontrado!');
            return false;
        }
        elseif($Linha[2] == ''){
            $this->setMensagem('Linha:'.$this->LinhaAtual.' Curso vazio ou não encontrado!');
            return false;
        }
        elseif($Linha[3] == ''){
            $this->setMensagem('Linha:'.$this->LinhaAtual.' Parte vazio ou não encontrado!');
            return false;
        }
        elseif($Linha[4] == ''){
            $this->setMensagem('Linha:'.$this->LinhaAtual.' Módulo vazio ou não encontrado!');
            return false;
        }
        elseif($Linha[5] == ''){
            $this->setMensagem('Linha:'.$this->LinhaAtual.' Instrutor vazio ou não encontrado!');
            return false;
        }
        elseif($Linha[6] == ''){
            $this->setMensagem('Linha:'.$this->LinhaAtual.' Local do Curso vazio ou não encontrado!');
            return false;
        }
        elseif($Linha[7] == ''){
            $this->setMensagem('Linha:'.$this->LinhaAtual.' Hotel vazio ou não encontrado!');
            return false;
        }
        return true;
    }

    public function ImportaLinha($Linha){
        $Linha = $this->ExecutaDeparaLinha($Linha);
        if($this->ValidaObrigatorios($Linha) === false){
            return false;
        }

        $ArSqlInsertAgendaCurso = array(
            'id_estabelecimento'        => $Linha[0],
            'id_pessoa_licenciado'      => $Linha[1],
            'id_curso'                  => $Linha[2],
            'id_parte'                  => $Linha[3],
            'id_modulo'                 => $Linha[4],
            'id_instrutor'              => $Linha[5],
            'id_local_curso'            => $Linha[6],
            'id_hotel'                  => $Linha[7],
            'qtde_min_inscricao'        => $Linha[8],
            'qtde_max_inscricao'        => $Linha[9],
            'dt_limite_inscricao'       => $Linha[10],
            'id_situacao'               => '1'
        );

        $SqlInsertAgendaCurso = AutoExecuteSql(TipoBancoDados, 'c_coaching_agenda_curso', $ArSqlInsertAgendaCurso, 'INSERT');
        $QryInsertAgendaCurso = iquery($SqlInsertAgendaCurso);
        
        if(!$QryInsertAgendaCurso){
            $this->setMensagem('Linha: '.$this->LinhaAtual.' Erro ao inserir agenda no banco.');
            return false;
        }
        $this->UltimaAgendaInserida = $QryInsertAgendaCurso;
        for($i=11;$i<=28;$i++){
            if(trim($Linha[$i]) != ''){
                $DataAgenda = $Linha[$i];
                $ArSqlInsertAgendaCursoDetalhe = array(
                    'id_agenda_curso'   => $QryInsertAgendaCurso,
                    'dt_curso'          => $DataAgenda,
                    'hr_inicio'         => $Linha[($i+1)],
                    'hr_fim'            => $Linha[($i+2)]
                );
                $i = $i + 2;
                $SqlInsertAgendaCursoDetalhe = AutoExecuteSql(TipoBancoDados, 'c_coaching_agenda_curso_detalhe', $ArSqlInsertAgendaCursoDetalhe, 'INSERT');
                $QryInsertAgendaCursoDetalhe = query($SqlInsertAgendaCursoDetalhe);
                
                if(!$QryInsertAgendaCursoDetalhe){
                    $this->setMensagem('Linha: '.$this->LinhaAtual.' Erro ao inserir detalhe agenda no banco.');
                }
            }
        }

        for($i=29;$i<=34;$i++){
            if(trim($Linha[$i]) != ''){
                $IdPessoaStaff = $Linha[$i];
                if($IdPessoaStaff == ''){
                    $this->setMensagem('Linha: '.$this->LinhaAtual.' Staff '.($i+11).' não encontrado');
                }
                else{
                    $ArSqlInsertAgendaCursoStaff = array(
                        'id_agenda'         => $QryInsertAgendaCurso,
                        'id_pessoa_staff'   => $IdPessoaStaff
                    );
                    $SqlInsertAgendaCursoStaff = AutoExecuteSql(TipoBancoDados, 'c_coaching_agenda_curso_staff', $ArSqlInsertAgendaCursoStaff, 'INSERT');
                    $QryInsertAgendaCursoStaff = query($SqlInsertAgendaCursoStaff);
                    
                    if(!$QryInsertAgendaCursoStaff){
                        $this->setMensagem('Linha: '.$this->LinhaAtual.' Erro ao inserir staff '.($i+11).' no banco.');
                    }
                }
            }
        }
        return true;
    }

    public function ImportaCSV(){
        $Arquivo = fopen($this->CaminhoArquivo,"r");
        $PrimeiraLinha = true;
        $i = 0;
        while($Linha = fgetcsv($Arquivo,filesize($this->CaminhoArquivo),";")){
            if(count($Linha) != 35){
                $this->setMensagem('Arquivo CSV inválido!');
                return false;
            }
            $i++;
            $this->LinhaAtual = $i;
            if($PrimeiraLinha === true){$PrimeiraLinha = false; continue;}
            foreach($Linha as $Key => $Value){
                $Linha[$Key] = trim($Value);
            }
            $ImportaLinha = $this->ImportaLinha($Linha);
            if($ImportaLinha){
                c_coachingRecriaListaPresencaStaff($this->UltimaAgendaInserida);
                $this->QtdeRegistrosImportados++;
            }
        }
        return true;
    }
}
?>