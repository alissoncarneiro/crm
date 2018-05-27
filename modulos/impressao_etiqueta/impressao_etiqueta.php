<?php
$qtd      = $_GET['qtd'];
$programa = $_GET['id_prog'];
?>
<head>
<style>
    html body{
        text-align: center;
    }
.dv_campo{
    border: 1px solid #ACC6DB;
    border-radius: 5px;    
    margin: 0 auto;
    text-align: center;
    width: 300px;
    height: 300px;
    overflow: auto;
    
};

</style>
</head>
<body>
    <h3>Inserir os números das atividades</h3>
    <form  method="POST" action="<?php echo $programa;?>.php" >
        <input type="hidden" value="<?php echo $qtd; ?>" id="qtd" name="qtd"/>
        <div id=dv_campo class=dv_campo>

        <?php

        $i = 0;
        while($i < $qtd){
            echo "<input type='text' name='campo_".$i."' id='campo_".$i."' /><br>";
            $i++;
        }
        ?> 
        </div>
        <input type="submit" value="Imprimir">
        <input type="button" onclick="javaScript:window.close();"  value="Fechar">
    </form>
</body>
