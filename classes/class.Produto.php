<?php
/*
 * class.Produto.php
 * Autor: Alex
 * 04/11/2010 16:39:00
 * Classe responsável por tratar os pedidos e orçamentos
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class Produto{
    protected $DadosProduto;
    protected $SaldoEmEstoque = NULL;
    protected $ArEmbalagem = false;

    /**
     * Classe para tratar as informações relacionadas ao produto
     * @param int $IdProduto Numreg do produto
     */
    public function __construct($IdProduto){
        if(!is_numeric($IdProduto)){
            echo 'Produto não informado';
            exit;
        }
        $QryProduto = query("SELECT * FROM is_produto WHERE numreg = ".$IdProduto);
        if(numrows($QryProduto) == 0){
            echo 'Produto não encontrado';
            exit;
        }
        $ArProduto = farray($QryProduto);
        $this->DadosProduto = $ArProduto;
    }

    public function getDadosProduto($IdCampo=NULL){
        if($IdCampo == NULL){
            return $this->DadosProduto;
        }
        return $this->DadosProduto[$IdCampo];
    }

    public function getNumregProduto(){
        return $this->DadosProduto['numreg'];
    }

    public function getVlUnitarioTabelaBD($IdGrupoTabPreco,$IdTabelaPreco,$IdUnidMedida=NULL,$DtCotacao=2){
        $PrecoProduto = new PrecoProduto($this->getNumregProduto(),$IdGrupoTabPreco, $IdTabelaPreco, $IdUnidMedida, $DtCotacao);
        if(!$PrecoProduto){
            return false;
        }
        return $PrecoProduto->getPreco();
    }

    public function getSaldoEmEstoque(){
        if($this->SaldoEmEstoque == NULL){// Se o saldo ainda não foi calculado, chama a função de cálculo
            $this->CalculaEstoque();
        }
        return $this->SaldoEmEstoque;
    }

    public function CalculaEstoque(){
        $this->SaldoEmEstoque = 0;
    }

    public function getIdTpProduto(){
        $SqlCFOPProduto = "SELECT id_tp_produto FROM is_produto_cfop WHERE id_produto = ".$this->getNumregProduto();
        $QryCFOPProduto = query($SqlCFOPProduto);
        $ArCFOPProduto = farray($QryCFOPProduto);
        return $ArCFOPProduto['id_tp_produto'];
    }

    public function getPossuiSimilar(){
        $QrySimilar = query("SELECT COUNT(*) AS CNT FROM is_produto_similar WHERE id_produto_pai = ".$this->getNumregProduto());
        $ArSimilar = farray($QrySimilar);
        if($ArSimilar['CNT'] > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function getPossuiCrossSelling(){
        $QryCrossSelling = query("SELECT COUNT(*) AS CNT FROM is_produto_crossselling WHERE id_produto_pai = ".$this->getNumregProduto());
        $ArCrossSelling = farray($QryCrossSelling);
        if($ArCrossSelling['CNT'] > 0){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     *
     * @return array Retorna uma array com os dados da embalagem
     */
    public function getArEmbalagem(){
        if($this->ArEmbalagem !== false){ /* Caso já tenho sido carregada anteriormente */
            return $this->ArEmbalagem;
        }
        $SqlEmbalagem = "SELECT t1.numreg, t1.qtde, t2.nome_embalagem FROM is_produto_embalagem t1
                            INNER JOIN is_embalagem t2 ON t1.id_embalagem = t2.numreg
                            WHERE (t1.id_produto = '".$this->getNumregProduto()."' OR t1.id_produto IS NULL)
                            AND (t1.id_familia = '".$this->getDadosProduto('id_familia')."' OR t1.id_familia IS NULL)
                            AND (t1.id_familia_comercial = '".$this->getDadosProduto('id_familia_comercial')."' OR t1.id_familia_comercial IS NULL)";
        $QryEmbalagem = query($SqlEmbalagem);
        $NumRowsEmbalagem = numrows($QryEmbalagem);
        if($NumRowsEmbalagem == 0){
            $this->ArEmbalagem = array();
        }
        else{
            while($ArEmbalagem = farray($QryEmbalagem)){
                $this->ArEmbalagem[] = array($ArEmbalagem['numreg'],$ArEmbalagem['qtde'],$ArEmbalagem['nome_embalagem'].' - '.($ArEmbalagem['qtde']*1).' un');
            }
        }
        return $this->ArEmbalagem;
    }
    
    public function isFaturavel($IdEstabelecimento){
        $SqlProdutoFaturavel = "SELECT sn_faturavel FROM is_produto_estabelecimento WHERE id_produto = '".$this->getNumregProduto()."' AND id_estabelecimento = '".$IdEstabelecimento."'";
        $QryProdutoFaturavel = query($SqlProdutoFaturavel);
        $ArProdutoFaturavel = farray($QryProdutoFaturavel);
        if($ArProdutoFaturavel['sn_faturavel'] == '0'){
            return false;
        }
        return true;
    }

    public function getQtdePorEmbalagem($IdProdutoEmbalagem){
        $SqlEmbalagem = "SELECT qtde FROM is_produto_embalagem t1 WHERE numreg = '".$IdProdutoEmbalagem."' AND id_produto = '".$this->getNumregProduto()."'";
        $QryEmbalagem = query($SqlEmbalagem);
        $NumRowsEmbalagem = numrows($QryEmbalagem);
        if($NumRowsEmbalagem == 0){
            return false;
        }
        else{
            $ArEmbalagem = farray($QryEmbalagem);
            return $ArEmbalagem['qtde'];
        }
    }
    
    public function getIdUnidMedidaPadrao(){
        return $this->getDadosProduto('id_unid_medida_padrao');
    }
    
    public function getIdUnidMedidaAtacadoVarejo($IdGrupoTabPreco){
        if($IdGrupoTabPreco == '1'){
            return $this->getIdUnidMedidaAtacado();
        }
        elseif($IdGrupoTabPreco == '2'){
            return $this->getIdUnidMedidaVarejo();
        }
        else{
            return false;
        }
    }

    public function getIdUnidMedidaAtacado(){
        $SqlUnidMedidaAtacado = "SELECT id_unid_medida FROM is_produto_unid_medida_atacado WHERE id_produto = ".$this->getNumregProduto();
        $QryUnidMedidaAtacado = query($SqlUnidMedidaAtacado);
        $NumRowsUnidMedidaAtacado = numrows($QryUnidMedidaAtacado);
        if($NumRowsUnidMedidaAtacado == 0){
            return false;
        }
        $ArUnidMedidaAtacado = farray($QryUnidMedidaAtacado);
        return $ArUnidMedidaAtacado['id_unid_medida'];
    }
    
    public function getIdUnidMedidaVarejo(){
        $SqlUnidMedidaVarejo = "SELECT id_unid_medida FROM is_produto_unid_medida_varejo WHERE id_produto = ".$this->getNumregProduto();
        $QryUnidMedidaVarejo = query($SqlUnidMedidaVarejo);
        $NumRowsUnidMedidaVarejo = numrows($QryUnidMedidaVarejo);
        if($NumRowsUnidMedidaVarejo == 0){
            return false;
        }
        $ArUnidMedidaVarejo = farray($QryUnidMedidaVarejo);
        return $ArUnidMedidaVarejo['id_unid_medida'];
    }

    public function getQtdePorUnidMedida($IdUnidMedida){
        if($IdUnidMedida == '' || $IdUnidMedida == $this->getIdUnidMedidaPadrao()){
            return 1;
        }
        $SqlQtdePorUnidMedida = "SELECT total_unidades FROM is_produto_fator_conversao WHERE id_produto = ".$this->getNumregProduto()." AND id_unid_medida = ".$IdUnidMedida;
        $QryQtdePorUnidMedida = query($SqlQtdePorUnidMedida);
        $ArQtdePorUnidMedida = farray($QryQtdePorUnidMedida);

        $QtdePorUnidMedida = $ArQtdePorUnidMedida['total_unidades'];
        $QtdePorUnidMedida = ($QtdePorUnidMedida == '')?1:$QtdePorUnidMedida;
        return $QtdePorUnidMedida;
    }

    public function getQtdePorQtdeInformada($IdProdutoEmbalagem){
        $QtdePorEmbalagem = $this->getQtdePorEmbalagem($IdProdutoEmbalagem);
        $IdUnidMedidaAtacado = $this->getIdUnidMedidaAtacado();
        $QtdePorUndidMedidaAtacado = $this->getQtdePorUnidMedida($IdUnidMedidaAtacado);

        $QtdePorQtdeInformada = round($QtdePorEmbalagem / $QtdePorUndidMedidaAtacado,0);

        return $QtdePorQtdeInformada;
    }

    public function MontaHTMLSelectEmbalagem($IdCampo,$ReadOnly=false,$ValorPadrao=NULL){
        $ArEmbalagem = $this->getArEmbalagem();
        $HTML = '<select name="'.$IdCampo.'" id="'.$IdCampo.'">';
        if(count($ArEmbalagem) == 0){
            $HTML .= '<option value="">--Nenhuma embalagem--</option>';
        }
        else{
            $Options = '';
            foreach($this->ArEmbalagem as $k => $v){
                $Options .= '<option value="'.$v[0].'">'.$v[2].'</option>';
            }
            $HTML .= $Options;
        }
        $HTML .= '</select>';
        return $HTML;
    }
    
    public function getSnAtivo(){
        if($this->getDadosProduto('sn_ativo') == '1'){
            return true;
        }
        return false;
    }

    /**
     * Retorna o caminho relativo das imagens do produto. Caso a imagem não exista retorna FALSE
     * @since 4.0.1.21
     * @return bool|string
     */
    public function getCaminhoImagem(){
        return false;
    }
}
?>