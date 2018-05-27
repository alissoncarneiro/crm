<?php
 /*
  * detalhe_end_importa_estrutura.php
  * Autor: Rodrigo Piva
  * 22/06/2011 16:36:00
  */
if($id_funcao == 'equip_cad'){
?>
<script language="javascript">
    $(document).ready(function(){
        $("input[name=btn_importar_estrutura]").click(function(){
            window.open('modulos/equipamentos/equipamento_importa_estrutura.php?pnumreg=' + $("#pnumreg").val(), 'importacao','toolbar=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,fullscreen=0,width=300,height=200');
        });
    });
</script>
<?php
}
?>