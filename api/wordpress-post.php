<?php
header('Content-Type: text/html; charset=utf-8');

/**
 * Created by PhpStorm.
 * User: Alisson Carneiro
 * Date: 28/09/2017
 * Time: 13:54
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once("../functions.php");

Class WordpressIcasa{

    private $serverApi = "45.126.210.98";
    private $bdApi     = "aps598f9b4a78c76";
    private $userApi   = "aps598f9b4a78d47";
    private $passApi   = "403f66517b2a2ad4";
    private $connApi   = null;
    private $dataBase  = null;

    private $serverCrm    = "45.126.210.83";
    private $bdCrm        = "followcrm";
    private $userCrm      = "followcrm";
    private $passCrm      = "E3j6kEkZ#FuLt";

    private $connCrm      = null;
    private $dataBaseCrm  = null;

    private $arrayPost  = array();
    private $arrayCaracteristica = array();
    private $arrCategoria = array();
    private $arrEndereco = array();
    private $arrDetalhes = array();

    private $postId;
    private $imovel;
    private $precoImovel;

    public function __construct(){

        if(!$_GET['id']) die("O ID do imóvel não foi informado");

        $this->getConnectApi();
        $this->getConnectCrm();
        $this->getPostWordpress();

        #Pegar Cidade & bairro
        // $this->getTaxonomy();
        //$fields = $this->getCustomFields();
        #Garagem Coberta - 1
        //$this->getOptionsGaragemCoberta($fields['wp_estate_add_dropdown_order']['option_value']['1']);
        #Garagem DesCoberta - 2
        //$this->getOptionsGaragemDescoberta($fields['wp_estate_add_dropdown_order']['option_value']['2']);
        #Categoria do imovel - 3
        //$this->getOptionsCategoriaDoImovel($fields['wp_estate_add_dropdown_order']['option_value']['3']);
        //#Condição do imovel - 5
        //$this->getOptionsOcupacao($fields['wp_estate_add_dropdown_order']['option_value']['5']);
        //#Andares do Imovel - 6
        //$this->getOptionsAndares($fields['wp_estate_add_dropdown_order']['option_value']['6']);
        #Tags imoveis
        //$this->getOptionsStatusList();
    }

    private function getConnectApi(){
        $this->connApi =  mysqli_connect($this->serverApi, $this->userApi, $this->passApi) or die(false);
        $this->dataBase = mysqli_select_db($this->connApi, $this->bdApi) or die(false);
        $this->charset();
    }

    private function getConnectCrm(){
        $this->connCrm =  mysqli_connect($this->serverCrm, $this->userCrm, $this->passCrm) or die("Erro Conect Server");
        $this->dataBase = mysqli_select_db($this->connCrm, $this->bdCrm) or die("Erro Conect Banco");
        $this->charset();
    }

    private function charset(){
        mysqli_set_charset($this->connApi,'utf8');
        mysqli_query($this->connApi,'SET character_set_connection=utf8');
        mysqli_query($this->connApi,'SET character_set_client=utf8');
        mysqli_query($this->connApi,'SET character_set_results=utf8');
    }

    private function getPostWordpress(){

        $this->postId = $_GET["id"];

        $sqlPostId = "SELECT * FROM wp_posts where id =".$this->postId;

        $qryPostId = $this->query($sqlPostId, $this->connApi);
        $numPostId = $this->numrows($qryPostId);

        if($numPostId > 0){
            $arrPost = $this->farray($qryPostId);
            $this->imovel = $arrPost;
            $this->getPost($arrPost);
            $this->getPostsCaracteristicas();
            $this->storeImovel();
            $this->storePreco();
            $this->storeEndereco();
        }
        else {
            die("Imóvel informado não encontrado");
        }

    }

    /**
     * Persiste o valor do Imóvel
     */
    private function storePreco() {
        $checkExiste = "select numreg from is_tab_preco_valor where id_produto=".$this->postId;
        $resultado = $this->query($checkExiste, $this->connCrm);

        $preco = array();
        $preco["id_produto"]        = $this->postId;
        $preco["id_produto_erp"]    = $this->postId; 
        $preco["vl_unitario"]       = sprintf('%0.2f', $this->precoImovel);
        $preco["id_unid_medida"]    = 3;
        $preco["id_tab_preco"]      = 1;
        $preco["dt_validade_ini"]   = $this->imovel["post_date"];

        if( $this->numrows($resultado) == 0) {
            $queryPreco = AutoExecuteSql('mysql','is_tab_preco_valor', 
                    $preco, "INSERT");
        }
        else {
            $precoId = $this->farray($resultado);
            $preco["numreg"] = $precoId["numreg"];
            $queryPreco = AutoExecuteSql('mysql','is_tab_preco_valor',
                $preco, "UPDATE", array("numreg") );
        }

        $resultadoPreco = $this->query($queryPreco, $this->connCrm);

    }

    /**
     * Persiste o imóvel
     */
    private function storeImovel() {

        // Verifica se existe imóvel salvo com esta id
        $queryVerificaImovel = "SELECT numreg FROM is_produto WHERE numreg=".$this->postId;

        $verificaImovel = $this->query($queryVerificaImovel, $this->connCrm);

        $this->arrDetalhesDoImovel["numreg"] = $this->postId;
        $this->arrDetalhesDoImovel["nome_produto"] = utf8_decode($this->imovel['post_title']);
        $this->arrDetalhesDoImovel["permalink"] = $this->imovel['guid'];
        $this->arrDetalhesDoImovel["id_produto_erp"] = $this->postId;

        // Condição
        if(!empty($this->arrDetalhesDoImovel['property_status'])) {
            $condicao =  $this->query("SELECT numreg FROM is_imoveis_tags_condicao WHERE tags_condicao_taxonomy='".$this->arrDetalhesDoImovel['property_status']."'", $this->connCrm);
            if($this->numrows($condicao) > 0) {
                $condId = $this->farray($condicao);
                $this->arrDetalhesDoImovel['property_status'] = $condId["numreg"];
            }
            else {
                unset($this->arrDetalhesDoImovel['property_status']);
            }
        }

        if( $this->numrows($verificaImovel) == 0) {
            $queryImovel = AutoExecuteSql('mysql','is_produto', 
                    $this->arrDetalhesDoImovel, "INSERT");
        }
        else {
            $queryImovel = AutoExecuteSql('mysql','is_produto', 
                $this->arrDetalhesDoImovel, "UPDATE", array("numreg") );
        }

        $resultadoImovel = $this->query($queryImovel, $this->connCrm);
    }

    /**
     * Persiste o endereço
     */
    private function storeEndereco() {

        // Verifica se existe endereço salvo para este imóvel
        $queryVerificaEndereco = "SELECT numreg FROM is_pessoa_endereco WHERE id_produto=".$this->postId;

        $verificaEndereco = $this->query($queryVerificaEndereco, $this->connCrm);

        if( $this->numrows($verificaEndereco) == 0) {
        $queryEndereco = AutoExecuteSql('mysql','is_pessoa_endereco', 
                $this->arrEndereco, "INSERT");
        }
        else {
            $endereco = mysqli_fetch_array($verificaEndereco);
            $this->arrEndereco["numreg"] = $endereco["numreg"];
            $queryEndereco = AutoExecuteSql('mysql','is_pessoa_endereco', 
                $this->arrEndereco, "UPDATE", array("numreg") );
        }

        $resultadoEndereco = $this->query($queryEndereco, $this->connCrm);
    }

    private function getPostsCaracteristicas(){

        $sqlPostMeta = "SELECT * FROM wp_postmeta where post_id =". $this->arrayPost['ID']." AND meta_value != '' AND meta_key not like '%wpseo%'";
        $qryPostMeta = $this->query($sqlPostMeta,$this->connApi);

        $arrCaracteristica = $this->getCaracteristicas();
        $arrCaracteristica = array_flip($arrCaracteristica);

        $this->arrDetalhesDoImovel  = $this->buildDetalhesImovelArray();
        $this->arrEndereco          = $this->buildEnderecoArray();
        $this->getCategoria($this->arrayPost['ID']);

        WHILE($ax = $this->farray($qryPostMeta)){
            // echo $ax["meta_key"] . " - ".$ax["meta_value"]."<br>";
            /*
             * Caracteristicas tipo CheckBox
             */
            if(isset($arrCaracteristica[$ax['meta_key']])){
                $this->arrayCaracteristica[$ax['meta_key']] = $ax['meta_value'];
            }

            // Complementa o Endereço do Imovel com as infos que vem dos PostsMeta
            if(isset($this->arrEndereco[$this->checkEnderecoDePara($ax['meta_key'])])){
                $this->arrEndereco[$this->checkEnderecoDePara($ax['meta_key'])] = utf8_decode($ax['meta_value']);
            }

            /*
             * Detalhes Do Imovel
             */
            if(isset($this->arrDetalhesDoImovel[$this->checkImovelDePara($ax['meta_key'])])){
                $this->arrDetalhesDoImovel[$this->checkImovelDePara($ax['meta_key'])] = $ax['meta_value'];
            }

            // preco do imóvel
            if($ax['meta_key'] == 'property_price') {
                $this->precoImovel = $ax['meta_value'];
            }

        }

        $taxonomies = $this->getTaxonomies();

        while($ax = $this->farray($taxonomies)){

            // Complementa o Endereço do Imovel com as infos que podem vir de taxonomias
            if(@isset($this->arrEndereco[$this->checkEnderecoDePara($ax['taxonomy'])])){
                $this->arrEndereco[$this->checkEnderecoDePara($ax['taxonomy'])] = utf8_decode($ax['name']);
            }

        }

        // o campo vem como região/uf, então quebramos para usar apenas a UF
        if(isset($this->arrEndereco['uf']) && $this->arrEndereco['uf'] != "") {
             $explode = explode("/", $this->arrEndereco['uf']);
             $this->arrEndereco['uf'] = $explode[1];
        }

    }

    private function getPost($arrPost){ 

        $this->arrayPost = array(
            'post_content' => $arrPost['post_content'],
            'post_title' => $arrPost['post_title'],
            'post_status' => $arrPost['post_status'],
            'post_name' => $arrPost['post_name'],
            'post_date' => $arrPost['post_date'],
            'ID' => $arrPost['ID']
        );

    }

    private function getCaracteristicas(){

        $arrayCaracteristica= array(
            'adega',
            'alarme',
            'aquecimento_central',
            'ar_central',
            'ar_condicionado',
            'area_de_servico',
            'armario_de_cozinha',
            'armarios_embutidos',
            'banheiro_de_empregada',
            'banheiro_social',
            'bicicletario',
            'brinquedoteca',
            'churrasqueira',
            'churrasqueira_coletiva',
            'circuito_de_tv',
            'closet',
            'condominio_fechado',
            'cozinha',
            'cozinha_americana',
            'cozinha_azulejada',
            'cozinha_montanda',
            'cozinha_planejada',
            'deck',
            'dependencia_de_empregada',
            'deposito',
            'despensa',
            'dormitorios_com_armarios',
            'edicula',
            'elevador_de_servico',
            'elevador',
            'empresa_de_monitoramento',
            'entrada_de_servico',
            'escritorio',
            'espaco_gourmet',
            'estacionamento_visitantes',
            'estacionamento',
            'gas_central',
            'gramado',
            'hall',
            'heliponto',
            'hidromassagem',
            'horta',
            'interfone',
            'jardim_de_inverno',
            'jardim',
            'lareira',
            'lavabo',
            'lavanderia',
            'mezanino',
            'mobiliado',
            'pilotis',
            'piscina',
            'piscina_aquecida',
            'piscina_coletiva',
            'piso_elevado',
            'playground',
            'porao',
            'portaria_24hs',
            'portaria',
            'porteiro_eletronico',
            'quadra_de_esportes',
            'quadra_de_tenis',
            'quintal',
            'quiosque',
            'rancho',
            'sacada',
            'sacada_gourmet',
            'sala_de_estar',
            'sala_de_jantar',
            'sala_de_jogos',
            'sala_de_tv',
            'sala_fitness',
            'salao_de_festas',
            'sauna',
            'sauna_coletiva',
            'seguranca',
            'spa',
            'suite',
            'terraco_coletivo',
            'varanda',
            'varanda_gourmet',
            'ventilador_de_teto',
            'vigilancia_24h',
            'vista_panoramica',
            'vista_para_o_mar',
            'zelador',
            'moveis_planejados',
            'forno_a_lenha'
        );

        return $arrayCaracteristica;

    }

    /**
     * Pega todas a taxonomias relacionadas ao Post
     */
    private function getTaxonomies(){

        $sqlCategoria = "
                        select
                            wtt.taxonomy as taxonomy,
                            wt.name as name,
                            wt.slug as slug
                        from 
                            wp_term_relationships as wtr
                     
                        inner join 
                            wp_term_taxonomy as wtt 
                        on wtt.term_taxonomy_id = wtr.term_taxonomy_id

                        inner join 
                            wp_terms as wt 
                        on wt.term_id = wtt.term_id

                        where 
                            wtr.object_id=".$this->postId;

        $qryCategoria = $this->query($sqlCategoria, $this->connApi);

        return $qryCategoria;
    }

    private function getCategoria($id){

        $sqlCategoria = "select
                          wt.term_id,
                          wtt.taxonomy,
                          wt.name,
                          wt.slug
                            from wp_term_relationships as wtr
                              inner join wp_term_taxonomy as wtt
                              on wtt.term_taxonomy_id = wtr.term_taxonomy_id
                       
                              inner join wp_terms as wt
                              on wt.term_id = wtt.term_id
                        where wtr.object_id= ".$id;
        $qryCategoria = $this->query($sqlCategoria,$this->connApi);
        $arrCats = array();
        while($arrCat = $this->farray($qryCategoria)){
            $arrCats[$arrCat['taxonomy']] = $arrCat['term_id'];
        }
        $this->arrDetalhesDoImovel["id_familia_comercial"] = $arrCats['property_action_category'];
        $this->arrDetalhesDoImovel["id_linha"] = $arrCats['property_category'];

    }

    private function buildDetalhesImovelArray(){
         return array(
            'property_year'         => '',
            'property_rooms'        => '',
            'property_bedrooms'     => '',
            'property_bathrooms'    => '',
            'n_suite'               => '',
            'vl_condominio'         => '',
            'n_condominio'          => '',
            'r_condominio'          => '',
            'garagem_coberta'       => '',
            'garagem_descoberta'    => '',
            'property_size'         => '',
            'property_lot_size'     => '',
            'id_pessoa'             => '',
            'id_unid_medida_padrao' => '3',
            'property_status'       => '',
            'ocupacao'              => '',
            'id_familia_comercial'  => '',
            'id_linha'              => '',
            'referencia_codigo_wp'  => '',
            'id_produto_erp'        => '',
            'permalink'             => '',
            'id_tab_preco_padrao'   => '1',
            'nome_produto_detalhado'=> ''
         );
    }

    /**
     * Recebe um argumento e se o array dePara possuir o argumento informado, ele devolve 
     * um valor relacionado ao argumento
     */
    private function checkImovelDePara($de) {
        $dePara = array(
                    'n-suite'               => 'n_suite',
                    'vl-condominio'         => 'vl_condominio',
                    'n-condominio'          => 'n_condominio',
                    'r-condominio'          => 'r_condominio',
                    'garagem-coberta'       => 'garagem_coberta',
                    'garagem-descoberta'    => 'garagem_descoberta',
                    'proprietariocrm'       => 'id_pessoa',
                    'mls'                   => 'referencia_codigo_wp',
                    'owner_notes'           => 'nome_produto_detalhado'
                );
        if(isset($dePara[$de]))
            return $dePara[$de];
        else
            return $de;
    }

    private function buildEnderecoArray() {
        return array(
            "id_produto"        => $this->postId,
            "id_pessoa"         => 0,
            "id_tp_endereco"    => 1,
            "bairro"            => "",
            "cidade"            => "",
            "uf"                => "",
            "pais"              => "",
            "cep"               => "",
            "endereco"          => "",
            'numero'            => "",
            'complemento'       => "",
        );
    }

    /**
     * Recebe um argumento e se o array dePara possuir o argumento informado, ele devolve 
     * um valor relacionado ao argumento
     */
    private function checkEnderecoDePara($de) {
        $dePara = array(
                    "property_county_state" => "uf",
                    "property_country"      => "pais",
                    "property_zip"          => "cep",
                    "property_city"         => "cidade",
                    "property_area"         => "bairro",
                    "property_address"      => "endereco",
                    "n-imovel"              => "numero",
                    "ncomplemento"          => "complemento",
                );
        if(isset($dePara[$de]))
            return $dePara[$de];
        else
            return $de;
    }

    private function getTaxonomy(){

        $sqlTaxonomy ="SELECT * FROM wp_options WHERE option_value like '%property_area%' and option_name like 'taxonomy_%' and option_value like '%cityparent%'  ";
        $qryTaxonomy = $this->query($sqlTaxonomy, $this->connApi);
        $numTaxonomy = $this->numrows($qryTaxonomy);
        if($numTaxonomy > 0){
            while($arrTaxonomy = $this->farray($qryTaxonomy)){
                $value = unserialize($arrTaxonomy['option_value']);
                $term_id = explode('_',$arrTaxonomy['option_name']);
                $arrData[$value['cityparent']][$term_id[1]] = array(
                    'term_id'       => $term_id[1],
                    'cityparent'    => $value['cityparent']
                );
                $arrDataTermId[] = $term_id[1];
            }
            $this->getBairro($arrData, $arrDataTermId);
        }
    }

    private function getBairro($arrData, $arrDataTermId){
        $arrDataTermId = $this->getTermid(implode(",", $arrDataTermId));
        $arrDataCidade = $this->getCidadeid();
        $arrBairroInsert = array();
        $cont = 0;
        foreach($arrData as $keyCidade => $valCidade){
            foreach($valCidade as $keyValCidade => $valValCidade) {
                $arrBairroInsert = array(
                    'numreg'      => $valValCidade['term_id'],
                    'nome_bairro' => utf8_decode($arrDataTermId[$valValCidade['term_id']]['name']),
                    'slug'        => $arrDataTermId[$valValCidade['term_id']]['slug'],
                    'id_cidade'   => $arrDataCidade[$valValCidade['cityparent']]['term_id']
                );
               $queryBairro = AutoExecuteSql('mysql','is_bairro_imoveis',$arrBairroInsert,'INSERT');
                if($this->query($queryBairro,$this->connCrm))
                   $cont ++;
            }
        }
        die();
    }

    private function getCustomFields(){

        $sqlFields= "SELECT * FROM wp_options  where option_name in('wp_estate_add_field_name','wp_estate_add_dropdown_order');";
        $qryFields = $this->query($sqlFields,$this->connApi);
        $numFields = $this->numrows($qryFields);
        if($numFields > 0) {
            while($arrFields = $this->farray($qryFields)){
                $arrFieldsDropdownOrder[$arrFields['option_name']] = array(
                    'option_value' => unserialize($arrFields['option_value'])
                );
            }
        }
        return $arrFieldsDropdownOrder;
    }

    private function getOptionsGaragemCoberta($data = null){

        $cont = 1;
        $data = explode(",",$data);
        unset($data[0]);
        foreach($data as $keyGaragemCoberta) {
            $arrInsert = array(
                'numreg'      => $keyGaragemCoberta,
                'garagem_coberta' => utf8_decode($keyGaragemCoberta)
            );
            $query = AutoExecuteSql('mysql','is_imoveis_garagem_coberta',$arrInsert,'INSERT');
            if($this->query($query,$this->connCrm))
                $cont ++;
        }
    }

    private function getOptionsGaragemDescoberta($data = null){

        $cont = 1;
        $data = explode(",",$data);
        unset($data[0]);
        foreach($data as $keyGaragemCoberta) {
            $arrInsert = array(
                'numreg'      => $keyGaragemCoberta,
                'garagem_descoberta' => utf8_decode($keyGaragemCoberta)
            );
            $query = AutoExecuteSql('mysql','is_imoveis_garagem_descoberta',$arrInsert,'INSERT');
            if($this->query($query,$this->connCrm))
                $cont ++;
        }
    }

    private function getOptionsAndares($data = null){
        $cont = 1;
        $data = explode(",",$data);
        unset($data[0]);
        foreach($data as $key) {
            $arrInsert = array(
                'numreg'      => $key,
                'stories_number' => utf8_decode($key)
            );
            $query = AutoExecuteSql('mysql','is_imoveis_stories_number',$arrInsert,'INSERT');
            if($this->query($query,$this->connCrm))
                $cont ++;
        }
    }

    private function getOptionsOcupacao($data = null){
        $cont = 1;
        $data = explode(",",$data);
        unset($data['0']);
        foreach($data as $key => $val) {
            $arrInsert = array(
                'numreg'      => $key,
                'condicao_imovel' => utf8_decode($val),
                'condicao_imovel_taxonomy' => utf8_decode($val)
            );
            $query = AutoExecuteSql('mysql','is_imoveis_property_status',$arrInsert,'INSERT');
            if($this->query($query,$this->connCrm))
               $cont ++;
        }
    }

    private function getOptionsCategoriaDoImovel($data = null){
        $cont = 1;
        $data = explode(",",$data);
        unset($data['0']);

        foreach($data as $key => $val) {
            $arrInsert = array(
                'numreg'      => $key,
                'imoveis_categoria' => utf8_decode($val),
                'imoveis_categoria_taxonomy' => utf8_decode($val)
            );
            $query = AutoExecuteSql('mysql','is_imoveis_categoria_imovel',$arrInsert,'INSERT');
            if($this->query($query,$this->connCrm))
                $cont ++;
        }
    }

    private function getOptionsStatusList(){
        $sqlFields= "SELECT * FROM wp_options  where option_name in('wp_estate_status_list');";
        $qryFields = $this->query($sqlFields,$this->connApi);
        $numFields = $this->numrows($qryFields);
        $cont = 0;
        if($numFields > 0) {
            $arrFields = $this->farray($qryFields);
            $arrFieldss = explode(",", $arrFields['option_value']);

            foreach($arrFieldss as $key => $val) {
                $arrInsert = array(
                    'tags_condicao' => utf8_decode($val),
                    'tags_condicao_taxonomy' => utf8_decode($val)
                );
                $query = AutoExecuteSql('mysql','is_imoveis_tags_condicao',$arrInsert,'INSERT');
                if($this->query($query,$this->connCrm))
                    $cont ++;
            }
        }
    }

    private function getCidadeid(){
        $sqlCidade = "select
                      te.term_id,
                      tx.taxonomy,
                      te.name,
                      te.slug
                     from wp_term_taxonomy as tx
                        inner join wp_terms as te
                        on te.term_id = tx.term_id
                        where tx.taxonomy = 'property_city'
                      order by te.name";
        $qryCidade = $this->query($sqlCidade, $this->connApi);
        $numCidade = $this->numrows($qryCidade);
        if($numCidade > 0){
            while($arr = $this->farray($qryCidade)){
                $arrCidade[$arr['name']] = array(
                    'term_id'   => $arr['term_id'],
                    'name'      => $arr['name'],
                    'slug'      => $arr['slug']
                );
            }
        }
        return $arrCidade;
    }

    private function getTermid($arrDataTermId){

        $sqlTerm = "SELECT * FROM wp_terms  where term_id in($arrDataTermId)";
        $qryTerm = $this->query($sqlTerm, $this->connApi);
        $numTerm = $this->numrows($qryTerm);
        if($numTerm > 0){
            while($arrTerm = $this->farray($qryTerm)){
               $arrDataTerm[$arrTerm['term_id']]= array(
                    'term_id'  => $arrTerm['term_id'],
                    'name'  => $arrTerm['name'],
                    'slug'    => $arrTerm['slug']
                );
            }
           return $arrDataTerm;
        }
    }

    private function query($queryparam, $conexao){

        $queryparam = str_replace(", ,",", NULL,",$queryparam);
        $queryparam = str_replace(",,",", NULL,",$queryparam);
        $queryparam = str_replace(",)",", NULL)",$queryparam);
        $queryparam = str_replace(", )",", NULL)",$queryparam);
        $queryparam = str_replace("(,","( NULL,",$queryparam);
        $queryparam = str_replace("( ,","( NULL,",$queryparam);
        $queryparam = str_replace("()","( NULL)",$queryparam);
        $queryparam = str_replace("( )","( NULL)",$queryparam);

        echo $queryparam . "<hr>";

        $queryexec = mysqli_query($conexao, $queryparam);

        if(!$queryexec){

            if(QueryDebug == 0){
                echo mysqli_error($conexao);
            }

            return false;

        }

        return $queryexec;
    }

    private function farray($queryparam) {
        $queryexec = mysqli_fetch_assoc($queryparam);
        return $queryexec;
    }

    private function numrows($queryparam) {
        $queryexec = mysqli_num_rows($queryparam);
        return $queryexec;
    }

}