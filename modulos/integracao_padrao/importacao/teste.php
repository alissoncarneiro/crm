<?php
$CnxODBC = odbc_connect('crm', 'crm', 'crm');
if(!$CnxODBC){
    echo 'Erro na conexão com o banco de dados';
}

$QryVwUnidMedida = odbc_exec($CnxODBC,"SELECT * FROM vw_is_int_produtos");
while($ArVwUnidMedida = odbc_fetch_array($QryVwUnidMedida)){
    echo '<pre>';
    print_r($ArVwUnidMedida);
    echo '</pre>';
}

$SqlViews = "SELECT name FROM sysobjects WHERE xtype = 'U'";
$QryViews = odbc_exec($CnxODBC,$SqlViews);
echo '<h3>Listando as views da base de dados</h3>';
while($ArViews = odbc_fetch_array($QryViews)){
    echo $ArViews['name'].'<br/>';
}
?>