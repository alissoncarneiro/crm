<?
  @header("Pragma: no-cache");
  @header("Expires: 0");


include("../../conecta.php");

require_once("../../fpdf.php");

$busca = query("select * from is_lista_pessoa where id_lista = '".$_GET["id_lista"]."'");

$pular = $_GET["pular"];

// Variaveis de Tamanho

$mesq = "4,8"; // Margem Esquerda (mm)
$mdir = "5"; // Margem Direita (mm)
$msup = "5,7"; // Margem Superior (mm)
$leti = "74"; // Largura da Etiqueta (mm)
$aeti = "27,4"; // Altura da Etiqueta (mm)
$ehet = "0"; // Espaço horizontal entre as Etiquetas (mm)
$pdf=new FPDF('P','mm','Letter'); // Cria um arquivo novo tipo carta, na vertical.
$pdf->Open(); // inicia documento
$pdf->AddPage(); // adiciona a primeira pagina
$pdf->SetMargins('/','0'); // Define as margens do documento
$pdf->SetAuthor("OASIS"); // Define o autor
$pdf->SetFont('helvetica','',7); // Define a fonte
//$pdf->SetDisplayMode();

if ($pular > 0) {
	$linha = (int)($pular / 3);
	$coluna = ($pular % 3);
} else {
	$coluna = 0;
	$linha = 0;
}

//MONTA A ARRAY PARA ETIQUETAS
while($dados = farray($busca)) {
	$nome = $dados["razao_social_nome"];
	$nome_contato = trim($dados["nome_contato"]);
        if ($nome_contato) { $nome_contato = 'A/C '.$nome_contato; }
	$ende = $dados["endereco"].' '.$dados["numero"].' '.$dados["complemento"];
	$bairro = $dados["bairro"];
	$estado = $dados["uf"];
	$cidade = $dados["cidade"];
	$local = $bairro . " - " . $cidade . " - " . $estado;
	$cep = "CEP: " . $dados["cep"];

	if($linha == "10") {
	$pdf->AddPage();
	$linha = 0;
	}

	if($coluna == "3") { // Se for a terceira coluna
	$coluna = 0; // $coluna volta para o valor inicial
	$linha = $linha +1; // $linha é igual ela mesma +1
	}

	if($linha == "10") { // Se for a última linha da página
	$pdf->AddPage(); // Adiciona uma nova página
	$linha = 0; // $linha volta ao seu valor inicial
	}

	$posicaoV = $linha*$aeti;
	$posicaoH = $coluna*$leti;

	if($coluna == "0") { // Se a coluna for 0
	$somaH = $mesq; // Soma Horizontal é apenas a margem da esquerda inicial
	} else { // Senão
	$somaH = $mesq+$posicaoH; // Soma Horizontal é a margem inicial mais a posiçãoH
	}

	if($linha =="0") { // Se a linha for 0
	$somaV = $msup; // Soma Vertical é apenas a margem superior inicial
	} else { // Senão
	$somaV = $msup+$posicaoV; // Soma Vertical é a margem superior inicial mais a posiçãoV
	}

	//$pdf->Image('../../images/logorel.jpg',$somaH,$somaV-4,18,18);
	$pdf->Text($somaH,$somaV,$nome_contato); // Imprime o nome da pessoa de acordo com as coordenadas
	$pdf->Text($somaH,$somaV+4,$nome); // Imprime o nome do contato de acordo com as coordenadas
	$pdf->Text($somaH,$somaV+8,$ende); // Imprime o endereço da pessoa de acordo com as coordenadas
	$pdf->Text($somaH,$somaV+12,$local); // Imprime a localidade da pessoa de acordo com as coordenadas
	$pdf->Text($somaH,$somaV+16,$cep); // Imprime o cep da pessoa de acordo com as coordenadas

	$coluna = $coluna+1;
}

$pdf->Output();
?>
