<?php
/*
 * class.impTable.php
 * Autor: Alex
 * 23/09/2010 14:20:00
 * 
 *
 * Log de Alterações
 * yyyy-mm-dd <Pessoa responsável> <Descrição das alterações>
 */
class impTable{
	public $TabelaOrigem = NULL;
	public $TabelaDestino = NULL;
	public $Chaves = array();
	public $CamposObrigatorios = array();
	
	public function Importa(){
		
$result = mysql_query("SELECT * FROM `".$this->TabelaOrigem."` ORDER BY numreg ASC");
if (!$result) {
    die('Query failed: ' . mysql_error());
}
/* get column metadata */
$i = 0;
while ($i < mysql_num_fields($result)) {
    echo "Information for column $i:<br />\n";
    $meta = mysql_fetch_field($result, $i);
    if (!$meta) {
        echo "No information available<br />\n";
    }
    echo "<pre>
blob:         $meta->blob
max_length:   $meta->max_length
multiple_key: $meta->multiple_key
name:         $meta->name
not_null:     $meta->not_null
numeric:      $meta->numeric
primary_key:  $meta->primary_key
table:        $meta->table
type:         $meta->type
default:      $meta->def
unique_key:   $meta->unique_key
unsigned:     $meta->unsigned
zerofill:     $meta->zerofill
</pre>";
    $i++;
}
mysql_free_result($result);		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$sql = "SELECT * FROM `".$this->TabelaOrigem."` ORDER BY numreg ASC";
		$qry = mysql_query($sql);
		$numrows = mysql_num_rows($qry);
		$ar_campos = array();
		if($numrows >= 1){
			#Definindo metadados
			$i = 0;
			while ($i < mysql_num_fields($qry)){
				$meta = mysql_fetch_field($qry, $i);
				$ar_campos[] = $meta->name;
			}
			#Removendo da array de Metadados as Colunas que nÃ£o serÃ£o utilizadas
			unset($ar_campos['numreg']);
			unset($ar_campos['log_processado']);
			unset($ar_campos['log_integrado']);
			unset($ar_campos['log_acao']);
			unset($ar_campos['dt_processado']);
			unset($ar_campos['log_erro']);
			
			while($ar = mysql_fetch_array($qry)){
				$ar_sql_insert = array();
				foreach($ar_campos as $k => $v){
					$ar_sql_insert[$v] = $ar[$v];
				}
				if($ar['log_acao']){}
				
				
			}
		}		
	}



}
?>