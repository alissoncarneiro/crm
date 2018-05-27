<?php
if($id_funcao == 'pessoa_endereco'){?>
    <script language="javascript">
    $(document).ready(function(){
        if($('#edtid_cep').val() != ''){
            cep_trava_campos('',true,true);
        }
    });
</script>
<?php
}
?>