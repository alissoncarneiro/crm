<?php

header("Content-Type: text/html; charset=ISO-8859-1");
echo '*============================================================*<br>';
echo 'Carga de Clientes Datasul ERP via ODBC<br>';
echo '*============================================================*<br>';

$odbc_c = true;

include('../../conecta.php');
include('../../funcoes.php');

// Carregando Parâmetros
$ap = farray(query("select * from is_dm_param"));


$tabela_erp = 'pub.emitente';
$campo_chave_erp = 'cod-emitente';
$campo_descr_erp = 'nome-emit';

$tabela_crm = 'is_pessoa';
$campo_chave_crm = 'id_pessoa_erp';

//query("delete from ".$tabela_crm);


/* =========================================================================================================== */
// Importa Clientes
/* =========================================================================================================== */
echo 'Importando Registros<br>';

$ar_depara = array(
    'dt_cadastro' => 'data-implant',
    'id_pessoa_erp' => '',
    'razao_social_nome' => '',
    'cnpj_cpf' => 'cgc',
    'ie_rg' => '',
    'email' => '',
    'endereco' => 'endereco',
    'bairro' => 'bairro',
    'cidade' => 'cidade',
    'uf' => 'estado',
    'pais' => 'pais',
    'cep' => 'cep',
    'obs' => '',
    'id_tp_pessoa' => '',
    'fantasia_apelido' => '',
    //'id_ramo_atividade'		=> 'atividade',
    'id_representante_padrao' => '',
    'site' => 'home-page',
    'tel1' => 'telefone',
    'fax' => '',
    'id_grupo_cliente' => '',
    'id_vendedor_padrao' => '',
    'id_tab_preco_padrao' => '',
    'dt_virou_cliente' => '',
    //'natureza_operacao'       => 'nat-operacao', !
    'vl_limite_credito' => '',
    //'id_sit_cred'		=> 'ind-cre-cli', !
    'qtde_max_titulos_em_atraso' => 'nr-titulo',
    //'maior_qt_dias_atraso'    => 'nr-dias-atraso',
    //'nome_pessoa_contato'     => 'contato',
    'cod_suframa' => '',
    'sn_ativo' => 'ind-cre-cli',
    //'id_trans_redespacho'     => 'nome-tr-red',
    'id_transportadora_padrao' => ''
);

$ar_fixos = array(
    'sn_cliente'            => '1',
    'sn_prospect'           => '0',
    'sn_suspect'            => '0',
    'sn_concorrente'        => '0',
    'sn_parceiro'           => '0',
    'sn_fornecedor'         => '0',
    'sn_representante'      => '0',
    'sn_contato'            => '0',
    'sn_importado_erp'      => '1',
    'sn_exportado_erp'      => '1'
);

$campos = '';
foreach($ar_depara as $k => $v){
    if($k == 'tel1'){
        $campos .= $k.', ';
        $campos .= 'tel2'.', ';
    } else{
        $campos .= $k.', ';
    }
}
foreach($ar_fixos as $k => $v){
    $campos .= $k.', ';
}
$campos = substr($campos,0,strlen($campos) - 2);

$sql = 'select * from '.$tabela_erp.' WHERE identific != 2 and natureza < 3 order by "'.$campo_chave_erp.'"';

echo "Buscando Registros ".date("H:i:s").'<br>';
$q_erp = odbc_exec($cnx1,$sql);

$u = 0;
$i = 0;

while($a_erp = odbc_fetch_array($q_erp)){

    $q_existe = farray(query("select numreg from ".$tabela_crm." where ".$campo_chave_crm." = '".$a_erp[$campo_chave_erp]."' and sn_cliente = 1"));
    $pnumreg = $q_existe["numreg"];
    echo $a_erp[$campo_chave_erp]." ".$a_erp[$campo_descr_erp]." - ".$q_existe["numreg"].'<br>';
    $q_max = farray(query("select (max(numreg)+1) as ultimo from ".$tabela_crm));

    // UPDATE
    if($pnumreg){
        $conteudos = '';
        foreach($ar_depara as $k => $v){
            if($k == 'sn_ativo'){
                switch($a_erp[$v]){
                    case 4:
                        $new_value = '0';
                        break;
                    default:
                        $new_value = '1';
                        break;
                }
                $conteudos .= $k." = '".str_replace(';'," ",str_replace('"'," ",str_replace("'"," ",$new_value)))."', ";
            } else if($k == 'id_vendedor_padrao' || $k == 'id_representante_padrao'){
                $sql_representante = 'SELECT numreg FROM is_usuario WHERE id_representante = \''.$a_erp[$v].'\'';
                $qry_representante = query($sql_representante);
                $nrows = numrows($qry_representante);
                if($nrows > 0){
                    $ar_representante = farray($qry_representante);
                    $conteudos .= $k." = '".addslashes($ar_representante['numreg'])."', ";
                } else{
                    $conteudos .= $k." = NULL, ";
                }
            } else if($k == 'id_transportadora_padrao'){
                $sql = 'SELECT numreg FROM is_transportadora WHERE id_transportadora_erp = \''.$a_erp[$v].'\'';
                $qry = query($sql);
                $nrows = numrows($qry);
                if($nrows > 0){
                    $ar_sql = farray($qry);
                    $conteudos .= $k." = '".addslashes($ar_sql['numreg'])."', ";
                } else{
                    $conteudos .= $k." = NULL, ";
                }
            } else if($k == 'id_grupo_cliente'){
                $sql = 'SELECT numreg FROM is_grupo_cliente WHERE id_grupo_cliente_erp = \''.$a_erp[$v].'\'';
                $qry = query($sql);
                $nrows = numrows($qry);
                if($nrows > 0){
                    $ar_sql = farray($qry);
                    $conteudos .= $k." = '".addslashes($ar_sql['numreg'])."', ";
                } else{
                    $conteudos .= $k." = NULL, ";
                }
            } else if($k == 'id_tp_pessoa'){
                if(trim($a_erp[$v]) < 3){
                    if($a_erp[$v] == '1'){
                        $valor_real = '2';
                    } else if($a_erp[$v] == '2'){
                        $valor_real = '1';
                    }
                    $conteudos .= $k." = '".addslashes($valor_real)."', ";
                } else{
                    $conteudos .= $k." = NULL, ";
                }
            } else if($k == 'id_tab_preco_padrao'){
                $sql = 'SELECT numreg FROM is_tab_preco WHERE id_tab_preco_erp = \''.$a_erp[$v].'\'';
                $qry = query($sql);
                $nrows = numrows($qry);
                if($nrows > 0){
                    $ar_sql = farray($qry);
                    $conteudos .= $k." = '".addslashes($ar_sql['numreg'])."', ";
                } else{
                    $conteudos .= $k." = NULL, ";
                }
            } else if($k == 'tel1'){
                $telefone = explode(';',$a_erp[$v]);
                if(count($telefone) < 2){
                    $ar_sql = farray($qry);
                    $conteudos .= $k." = '".addslashes($a_erp[$v])."', ";
                } else if(count($telefone) > 1){
                    $conteudos .= $k." = '".addslashes($telefone[0])."', ";
                    $conteudos .= 'tel2'." = '".addslashes($telefone[1])."', ";
                } else{
                    $conteudos .= $k." = NULL, ";
                }
            } else{
                $conteudos .= $k." = '".str_replace(';'," ",str_replace('"'," ",str_replace("'"," ",$a_erp[$v])))."', ";
            }
        }
        $conteudos = substr($conteudos,0,strlen($conteudos) - 2);
        $sql = 'UPDATE '.$tabela_crm.' SET '.$conteudos." where numreg = '".$pnumreg."' and sn_cliente = '1'";
        $u = $u + 1;
    } else{
        // INSERT
        $conteudos = '';
        foreach($ar_depara as $k => $v){
            if($k == 'sn_ativo'){
                switch($a_erp[$v]){
                    case 4:
                        $new_value = '0';
                        break;
                    default:
                        $new_value = '1';
                        break;
                }
                $conteudos .= "'".str_replace(';'," ",str_replace('"'," ",str_replace("'"," ",$new_value)))."', ";
            } else if($k == 'id_vendedor_padrao' || $k == 'id_representante_padrao'){
                $sql_representante = 'SELECT numreg FROM is_usuario WHERE id_representante = \''.$a_erp[$v].'\'';
                $qry_representante = query($sql_representante);
                $nrows = numrows($qry_representante);
                if($nrows > 0){
                    $ar_representante = farray($qry_representante);
                    $conteudos .= "'".addslashes($ar_representante['numreg'])."', ";
                } else{
                    $conteudos .= " NULL, ";
                }
            } else if($k == 'id_transportadora_padrao'){
                $sql = 'SELECT numreg FROM is_transportadora WHERE id_transportadora_erp = \''.$a_erp[$v].'\'';
                $qry = query($sql);
                $nrows = numrows($qry);
                if($nrows > 0){
                    $ar_sql = farray($qry);
                    $conteudos .= "'".addslashes($ar_sql['numreg'])."', ";
                } else{
                    $conteudos .= " NULL, ";
                }
            } else if($k == 'id_grupo_cliente'){
                $sql = 'SELECT numreg FROM is_grupo_cliente WHERE id_grupo_cliente_erp = \''.$a_erp[$v].'\'';
                $qry = query($sql);
                $nrows = numrows($qry);
                if($nrows > 0){
                    $ar_sql = farray($qry);
                    $conteudos .= "'".addslashes($ar_sql['numreg'])."', ";
                } else{
                    $conteudos .= " NULL, ";
                }
            } else if($k == 'id_tp_pessoa'){
                if(trim($a_erp[$v]) < 3){
                    if($a_erp[$v] == '1'){
                        $valor_real = '2';
                    } else if($a_erp[$v] == '2'){
                        $valor_real = '1';
                    }
                    $conteudos .= "'".addslashes($valor_real)."', ";
                } else{
                    $conteudos .= " NULL, ";
                }
            } else if($k == 'id_tab_preco_padrao'){
                $sql = 'SELECT numreg FROM is_tab_preco WHERE id_tab_preco_erp = \''.$a_erp[$v].'\'';
                $qry = query($sql);
                $nrows = numrows($qry);
                if($nrows > 0){
                    $ar_sql = farray($qry);
                    $conteudos .= "'".addslashes($ar_sql['numreg'])."', ";
                } else{
                    $conteudos .= " NULL, ";
                }
            } else if($k == 'tel1'){
                $telefone = explode(';',$a_erp[$v]);
                if(count($telefone) < 2){
                    $ar_sql = farray($qry);
                    $conteudos .= "'".addslashes($a_erp[$v])."', ";
                } else if(count($telefone) > 1){
                    $conteudos .= "'".addslashes($telefone[0])."', ";
                    $conteudos .= "'".addslashes($telefone[1])."', ";
                } else{
                    $conteudos .= $k." = NULL, ";
                }
            } else{
                $conteudos .= "'".str_replace(';'," ",str_replace('"'," ",str_replace("'"," ",$a_erp[$v])))."', ";
            }
        }
        foreach($ar_fixos as $k => $v){
            $conteudos .= $v.', ';
        }
        $conteudos = substr($conteudos,0,strlen($conteudos) - 2);
        $sql = 'INSERT INTO '.$tabela_crm.' ( '.$campos.' ) VALUES ('.$conteudos.')';
        $i = $i + 1;
    }

    $rq = query($sql);

    if($rq != "1"){
        echo $sql;
    }
}



/* =========================================================================================================== */
// Fecha Conexões 
/* =========================================================================================================== */

odbc_close($cnx1);

echo '<strong>Fim do Processamento: <br />Total: </strong>',($u + $i),'<br /><strong>Inclusões:</strong> ',$i,'<br /><strong>Atualizações:</strong> ',$u,' '.date("H:i:s");
?>