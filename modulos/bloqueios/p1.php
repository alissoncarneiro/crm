<form>

    <input type="button" value="Confirmar Permissões do Perfil" class="botao_form" style="font-size:18px;color:#FF0000;" onClick="javascript:bloqueios_custom_p2();">
    <table width="98%" align="center" border="0" style="border: 1px solid #0066CC;" cellspacing="2" cellpadding="0" >
      <tr style="background-color:#006699;">
        <td width="475">&nbsp;</td>
        <td width="52" align="center"><div align="center"><strong style="color:#FFFFFF;">Ver</strong></div></td>
        <td width="65" align="center"><div align="center"><strong style="color:#FFFFFF;">Editar</strong></div></td>
      </tr>
    <?php
    require('../../conecta.php');
    require('../../functions.php');
    $qry_funcoes = query("SELECT * FROM is_funcoes t1 LEFT JOIN is_modulos t2 ON t1.id_modulo = t2.id_modulo WHERE t2.id_sistema LIKE '%CRM%'  AND t1.id_sistema LIKE '%CRM%' AND t1.nome_grupo <> 'Estrutura' ORDER BY t1.id_modulo,t1.nome_grupo,t1.ordem ASC");
    $count_row = 0;
    $num_rows_funcoes = numrows($qry_funcoes);
    while($ar_funcao = farray($qry_funcoes)){
            $checkall_ver_all .= "document.getElementById('ver_".$ar_funcao['id_funcao']."').checked = this.checked; ";
            $checkall_editar_all .= "document.getElementById('editar_".$ar_funcao['id_funcao']."').checked = this.checked; ";

            $bgcolor = ($count_row % 2 == 0)?'#CCCCCC':'#FFFFFF';
            if($last_modulo != $ar_funcao['id_modulo'] && $count_row > 0){
            ?>
            <tr bgcolor="<?=$bgcolor;?>">
              <td>&nbsp;</td>
              <td><input type="checkbox" onclick="javascript:<?=$checkall_ver;?>" checked="checked"><strong>Marcar Todos</strong></td>
              <td><input type="checkbox" onclick="javascript:<?=$checkall_editar;?>" checked="checked"><strong>Marcar Todos</strong></td>
            </tr>
            <?php
            $checkall_ver = '';
            $checkall_editar = '';
            $checkall_ver .= "document.getElementById('ver_".$ar_funcao['id_funcao']."').checked = this.checked; ";
            $checkall_editar .= "document.getElementById('editar_".$ar_funcao['id_funcao']."').checked = this.checked; ";
            }
            else{
                    $checkall_ver .= "document.getElementById('ver_".$ar_funcao['id_funcao']."').checked = this.checked; ";
                    $checkall_editar .= "document.getElementById('editar_".$ar_funcao['id_funcao']."').checked = this.checked; ";
            }
            if($last_modulo != $ar_funcao['id_modulo']){
            ?>
              <tr>
                    <td colspan="3"><div style="background-color:#375C79; color:#FFFFFF; font-weight:bold; height:20px;"><?=search_name('is_modulos','id_modulo','nome_modulo',$ar_funcao['id_modulo']);?></div></td>
              </tr>
            <?php
            }
            if($last_grupo != $ar_funcao['nome_grupo']){
            ?>
                    <tr>
                      <td colspan="3"><div style="background-color:#3067A0; color:#FFFFFF; font-weight:bold;"><?=$ar_funcao['nome_grupo'];?></div></td>
                    </tr>
            <?php
            }
            $qry_bloqueio = query("SELECT * FROM is_perfil_funcao_bloqueio WHERE id_funcao = '".$ar_funcao['id_funcao']."' AND id_perfil = '".$_POST['edtid_perfil']."'");
            $num_rows = numrows($qry_bloqueio);
            if($num_rows > 0){
                    $ar_bloqueio = farray($qry_bloqueio);
                    $check_ver = ($ar_bloqueio['sn_bloqueio_abrir'] != 1)?' checked="checked" ':'';
                    $check_editar = ($ar_bloqueio['sn_bloqueio_editar'] != 1)?' checked="checked" ':'';
            }
            else{
                    $check_ver = ' checked="checked" ';
                    $check_editar = ' checked="checked" ';
            }
            ?>
              <tr bgcolor="<?=$bgcolor;?>">
                    <td align="right"><?=$ar_funcao['nome_funcao'];?></td>
                    <td><input type="checkbox" id="ver_<?=$ar_funcao['id_funcao'];?>" name="ver_<?=$ar_funcao['id_funcao'];?>" <?=$check_ver;?> value="S" onclick="javascript:if(this.checked == false){document.getElementById('editar_<?=$ar_funcao['id_funcao'];?>').checked = false;}"></td>
                    <td><input type="checkbox" id="editar_<?=$ar_funcao['id_funcao'];?>" name="editar_<?=$ar_funcao['id_funcao'];?>" <?=$check_editar;?> value="S" onclick="javascript:if(this.checked == true){document.getElementById('ver_<?=$ar_funcao['id_funcao'];?>').checked = true;}"></td>
              </tr>
            <?php
            $last_modulo = $ar_funcao['id_modulo'];
            $last_grupo = $ar_funcao['nome_grupo'];
            $count_row = $count_row + 1;
            if($count_row == $num_rows_funcoes){?>
            <tr bgcolor="<?=$bgcolor;?>">
              <td>&nbsp;</td>
              <td><input type="checkbox" onclick="javascript:<?=$checkall_ver;?>" checked="checked"><strong>Marcar Todos</strong></td>
              <td><input type="checkbox" onclick="javascript:<?=$checkall_editar;?>" checked="checked"><strong>Marcar Todos</strong></td>
            </tr>
    <?php
            }
    }
    ?>
            <tr bgcolor="<?=$bgcolor;?>">
              <td>&nbsp;</td>
              <td><input type="checkbox" onclick="javascript:<?=$checkall_ver_all;?>" checked="checked"><strong>Marcar Tudo</strong></td>
              <td><input type="checkbox" onclick="javascript:<?=$checkall_editar_all;?>" checked="checked"><strong>Marcar Tudo</strong></td>
            </tr>
    </table>
    <input type="button" value="Confirmar Permissões do Perfil" class="botao_form" style="font-size:18px;color:#FF0000;" onClick="javascript:bloqueios_custom_p2();">
</form>