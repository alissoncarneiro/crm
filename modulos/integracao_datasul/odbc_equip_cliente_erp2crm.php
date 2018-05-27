<?php
@header("Content-Type:text/html; charset=iso-8859-1;");

echo "*============================================================*<br>";
echo "Carga de Equipamento x Cliente Datasul ERP via ODBC (Especifico Alpha)<br>";
echo "*============================================================*<br>";

$odbc_c = true;

require("../../conecta.php");
include "../../funcoes.php";

// Carregando Parâmetros
$ap = farray(query("select * from is_dm_param"));

$tabela_erp = 'pub."equiptoxcliente"';
$campo_chave_erp = 'num-serie';

$tabela_crm = 'is_pessoa_equipamento';
$campo_chave_crm = 'nr_serie';

query("delete from " . $tabela_crm);


$id_usuario = 'IMPORT';
//Conecta com os bancos ODBC
/* =========================================================================================================== */
// Importa Clientes
/* =========================================================================================================== */
echo 'Importando Registros<br>';

$ar_depara = array(
    'nr_serie' => 'num-serie',
    'id_pessoa' => 'cod-emitente',
    'id_produto' => 'cd-equipto',
    'id_produto_modelo' => 'cd-equipto',
    'id_pom_erp' => 'c-pom',
    'dt_fabricacao' => 'ano-fabricacao',
    'informacao_complementar' => 'complemento',
    'dt_instalacao' => 'dt-instalacao',
    'obs' => 'cd-equipe',
    'endereco' => 'endereco',
    'bairro' => 'bairro',
    'cidade' => 'cidade',
    'uf' => 'estado',
    'cep' => 'cep'
);

$ar_fixos = array(
);

$campos = '';
foreach ($ar_depara as $k => $v) {
    $campos .= $k . ', ';
}
foreach ($ar_fixos as $k => $v) {
    $campos .= $k . ', ';
}
$campos = substr($campos, 0, strlen($campos) - 2);

$sql = 'select * from ' . $tabela_erp . ' where "num-serie" <> \'\' order by "' . $campo_chave_erp . '"';

echo "Buscando Registros " . date("H:i:s") . '<br>';
$q_erp = odbc_exec($cnx3, $sql);

$u = 0;
$i = 0;

while ($a_erp = odbc_fetch_array($q_erp)) {

    $q_existe = farray(query("select numreg from " . $tabela_crm . " where " . $campo_chave_crm . " = '" . $a_erp[$campo_chave_erp] . "'"));
    $pnumreg = $q_existe["numreg"];
    $q_max = farray(query("select (max(numreg)+1) as ultimo from " . $tabela_crm));

    // UPDATE
    if ($pnumreg) {
        $conteudos = '';
        foreach ($ar_depara as $k => $v) {
            $vl_trat = trata_valores_carga($k, $a_erp, $v);
            $conteudos .= $k . " = " . $vl_trat . ", ";
        }
        $conteudos = substr($conteudos, 0, strlen($conteudos) - 2);
        $sql = 'UPDATE ' . $tabela_crm . ' SET ' . $conteudos . " where numreg = '" . $pnumreg . "'";
        $u = $u + 1;
    } else {
        // INSERT
        $conteudos = '';
        foreach ($ar_depara as $k => $v) {
            $vl_trat = trata_valores_carga($k, $a_erp, $v);
            $conteudos .= $vl_trat . ', ';
        }
        foreach ($ar_fixos as $k => $v) {
            $conteudos .= $v . ', ';
        }
        $conteudos = substr($conteudos, 0, strlen($conteudos) - 2);
        $sql = 'INSERT INTO ' . $tabela_crm . ' ( ' . $campos . ' ) VALUES (' . $conteudos . ')';
        $i = $i + 1;
    }
    $rq = query($sql);

    
}

function trata_valores_carga($k, $a_erp, $v) {
    switch ($k) {
        case "id_pessoa" :
            $a_busca = farray(query('SELECT numreg FROM is_pessoa WHERE id_pessoa_erp = \'' . $a_erp[$v] . '\''));
            if ($a_busca["numreg"]*1>0) {
                $vl_trat = "'" . $a_busca['numreg'] . "'";
            } else {
                $vl_trat = "NULL";
            }
            break;
        case "id_produto" :
            $a_busca = farray(query('SELECT numreg FROM is_produto WHERE id_produto_erp = \'' . $a_erp[$v] . '\''));
            if ($a_busca["numreg"]*1>0) {
                $vl_trat = "'" . $a_busca['numreg'] . "'";
            } else {
                $vl_trat = "NULL";
            }
            break;
        case "id_produto_modelo" :
            $a_busca = farray(query('SELECT numreg FROM is_produto_modelo WHERE id_produto_modelo_erp = \'' . $a_erp[$v] . '\''));
            if ($a_busca["numreg"]*1>0) {
                $vl_trat = "'" . $a_busca['numreg'] . "'";
            } else {
                $vl_trat = "NULL";
            }
            break;
        case "dt_fabricacao" :
            if ($a_erp[$v]) {
                $vl_trat = "'" . $a_erp[$v] . "-01-01'";
            } else {
                $vl_trat = "NULL";
            }
            break;
        case "id_pom_erp" :
            if ($a_erp[$v]) {
                $vl_trat = "'" . $a_erp[$v] . "-01-01'";
            } else {
                $vl_trat = "NULL";
            }
            break;
        case "obs" :
            $vl_trat = "'Equipe : " . $a_erp["cd-equipe"] . " - Contato : ".$a_erp["contato"]." - Telefone : ".$a_erp["telefone"]." - e-mail : ".$a_erp["email"]."'";
            break;
        default:
            if (trim($a_erp[$v]) == "") {
                $vl_trat = "NULL";
            } else {
                $vl_trat = "'" . str_replace(';', " ", str_replace('"', " ", str_replace("'", " ", $a_erp[$v]))) . "'";
            }
            break;
    }
    return $vl_trat;
}

/* =========================================================================================================== */
// Fecha Conexões
/* =========================================================================================================== */

odbc_close($cnx_erp);

echo 'Fim do Processamento : Total' . ($u + $i) . ' Inclusões : ' . $i . ' Atualizações : ' . $u . ' ' . date("H:i:s");
?>