<?php
    if($controle!=1) {
    @session_start();
    $id_session = $_POST['edtid_session'];
    $mode = $_SESSION[$id_session.'sessionmode'];
    require_once("../../conecta.php");
    require_once("../../functions.php");
    $exibe=0;
    $tam=1;
    $linha=1;

?>

<form id="itens" name="itens">
    <table border="0" align="center" cellpadding="2" cellspacing="2" class="bordatabela">
        <tr>
            <td bgcolor="dae8f4" class="tit_tabela" ><div align="center">#</div></td>
            <?php
            $ar_campos = mysql_query("SELECT * FROM is_gera_cad_campos WHERE id_funcao = '".$_SESSION[$id_session.'pfuncao']."' AND editavel = 'S' ORDER BY ordem ASC");
            while($campos2 = mysql_fetch_array($ar_campos,MYSQL_ASSOC)) {
                $tipos[$campos2['id_campo']] = $campos2['tipo_campo'];
                $sql[$campos2['id_campo']] = $campos2['sql_lupa'];
                $campo_lupa[$campos2['id_campo']] = $campos2['id_campo_lupa'];
                $campo_desc_lupa[$campos2['id_campo']] = $campos2['campo_descr_lupa'];
    ?>
            <td bgcolor="#dae8f4" class="tit_tabela" ><div align="center"><?php echo utf8_encode($campos2['nome_campo']);?></div></td>
    <?php } ?>
            <td bgcolor="#dae8f4" class="tit_tabela" ><div align="center">Excluir</div></td>
        </tr>
        <?php
       // print_r($_SESSION[$id_session.'campos']);
        $num = 1;
        if(count($_SESSION[$id_session.'campos'])>0){
        foreach($_SESSION[$id_session.'campos'] as $k1 => $v1) {
    ?>
        <tr id="linha<?php echo $k1?>">
            <td <?php echo $cor_bg;?>><?php echo $num;?></td>
                <?php foreach($_SESSION[$id_session.'campos'][$k1] as $k => $v) {
                    if($k!="numreg") {

            //echo $count_row-1;?>

            <td <?php echo $cor_bg;?>>

            <?php if($tipos[$k]=="sim_nao") {?>
                <select type="text" style="font-weight:bold;width:50px;text-align:right;" name="<?=$k.$session_id_prod;?>" id="<?=$k.$session_id_prod;?>" value="<?=$v;?>"  onchange="atualizar_campo_din('<?=$id_session;?>',this.id,this.value,<?=$_SESSION[$id_session.'campos'][$k1]['numreg'];?>)">
                <?php if($v=='S') { $selecteds="selected";} if($v=='N') {$selectedn="selected";}?>
                    <option></option>
                    <option <?php echo $selecteds;?> value="S">Sim</option>
                    <option <?php echo $selectedn;?> value="N">N&atilde;o</option>
                </select>
                <?php } elseif($tipos[$k]=="combobox") { //echo $sql[$k];
                    ?>

                <select type="text" style="font-weight:bold;width:50px;text-align:right;" name="<?=$k.$session_id_prod;?>" id="<?=$k.$session_id_prod;?>" value="<?=$v;?>"  onchange="atualizar_campo_din('<?=$id_session;?>',this.id,this.value,<?=$_SESSION[$id_session.'campos'][$k1]['numreg'];?>)">
                <option></option>
                <?php
                $qry = mysql_query($sql[$k]);
                                while($ar = mysql_fetch_array($qry)) {
                    if($v==$ar[$campo_lupa['id_campo_lupa']]) { $selected="selected"; }else {$selected=""; }?>
                    
                    <option <?php echo $selected;?> value="<?php echo $ar[$campo_lupa[$k]];?>"><?php echo $ar[$campo_desc_lupa[$k]];?></option>
                    <?php }?>
                </select>
                <?php } else {?>
                <input type="text" style="font-weight:bold;width:50px;text-align:right;" tabindex="<?=$num;?>" name="<?=$k.$session_id_prod;?>" id="<?=$k.$session_id_prod;?>" value="<?=$v;?>" onchange="atualizar_campo_din('<?=$id_session;?>',this.id,this.value,<?=$_SESSION[$id_session.'campos'][$k1]['numreg'];?>)" />   </td>
                <?php }?>
            <?php }
            }$num++; ?>
            <td <?=$cor_bg;?>><div align="center"><img style="cursor:pointer;" onclick="javascript:deletar_itens('<?=str_replace('.','ponto',$_SESSION[$id_session.'campos'][$k1]['numreg']);?>',document.getElementById('edtid_session').value,'');" src="images/btn_del.PNG" alt="Clique aqui para excluir..." width="15" height="15" border="0" /></div></td></tr>
    <?php }
        }?>
        <tr id="linhatoal">
            <td height="23" colspan="10" bgcolor="#CCCCCC"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td colspan="10"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>
<?php
     $controle = 1;
    }
  ?>