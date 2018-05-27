<?php
/*
 * class.GeraPontuacaoParametro.php
 * Autor: Alex
 * 30/12/2010 16:10:00
 *
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */

class GeraPontuacaoParametro{

    private $Tabela;
    private $CampoPontuacao = array();

    /**
     * Classe para calcular a pontuação dos registros dos cadastro de parâmetros
     * @param int $IdCadastro  param_politica_comercial_campo_desconto | param_politica_comercial_desc_item | param_politica_comercial_desc | param_politica_comercial_comis
     */
    public function  __construct($IdCadastro){
        $this->Tabela = 'is_'.$IdCadastro;
        $SqlCampoPontuacao = "SELECT campo,nr_pontos FROM is_param_pontuacao_parametros WHERE id_cadastro = '".$IdCadastro."'";
        $QryCampoPontuacao = query($SqlCampoPontuacao);
        while($ArCampoPontuacao = farray($QryCampoPontuacao)){
            $this->CampoPontuacao[$ArCampoPontuacao['campo']] = $ArCampoPontuacao['nr_pontos'];
        }
    }
    /**
     * Calcula a pontuação de todos os registros do cadastro
     */
    public function CalculaPontuacaoGeralBD(){
        $SqlRegistros = "SELECT * FROM ".$this->Tabela." ORDER BY numreg";
        $QryRegistros = query($SqlRegistros);
        while($ArRegistros = farray($QryRegistros)){
            $ArSqlUpdate = array();
            $ArSqlUpdate['numreg']      = $ArRegistros['numreg'];
            $ArSqlUpdate['nr_pontos']   = $this->CalculaPontuacao($ArRegistros);
            $SqlUpdate = AutoExecuteSql(TipoBancoDados, $this->Tabela, $ArSqlUpdate, 'UPDATE', array('numreg'));
            query($SqlUpdate);
        }
    }

    /**
     * Calcula a pontuação de um registro
     * @param array $ArDados Array com os dados do registro array[id_campo] => valor_campo
     * @return int Número de pontos
     */
    public function CalculaPontuacao($ArDados) {
        $NrPontos = 0;
        foreach ($ArDados as $IdCampo => $Valor) {
            if (!is_int($IdCampo)) {
                if (array_key_exists($IdCampo, $this->CampoPontuacao) && trim($Valor) != '') {
                    $NrPontos += $this->CampoPontuacao[$IdCampo];
                }
            }
        }
        return $NrPontos;
    }
}
?>