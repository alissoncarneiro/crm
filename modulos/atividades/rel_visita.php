<?

require_once("../../conecta.php");

$dias = array("Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado");

$numreg = $_GET["numreg"];

$a_ativ = farray(query("select * from is_atividade where numreg = '$numreg'"));
$a_empr = farray(query("select * from is_pessoa where numreg = '".$a_ativ["id_pessoa"]."'"));

$PROTOCOLO = $numreg;
$PARTICIPANTES_CLIENTE = "";
$PARTICIPANTES_IPARTNER = "";

$q_part = query("select * from is_atividade_participante_ext where id_atividade = '".$a_ativ["numreg"]."'");
while ($a_part = farray($q_part)) {
     $a1 = farray(query("select nome, email from is_contato where numreg = '".$a_part["id_pessoa_contato"]."'"));
     $PARTICIPANTES_CLIENTE .= $a1["nome"]." ".$a1["email"]."<br>";
}

$q_part = query("select * from is_atividade_participante_ext where id_atividade = '".$a_ativ["numreg"]."'");
while ($a_part = farray($q_part)) {
     $a2 = farray(query("select nome_usuario from is_usuario where numreg = '".$a_part["id_usuario"]."'"));
     $PARTICIPANTES_IPARTNER .= $a2["nome_usuario"]."<br>";
}


$CLIENTE = $a_empr["razao_social_nome"]." ( ".$a_empr["fantasia_apelido"]." )";
$d_semana = date("w",strtotime($a_ativ["dt_prev_fim"]));
$DATA = substr($a_ativ["dt_prev_fim"],8,2).'/'.substr($a_ativ["dt_prev_fim"],5,2).'/'.substr($a_ativ["dt_prev_fim"],0,4).' '.$dias[$d_semana];

$HR_INICIO = $a_ativ["hr_inicio"];;
$HR_FIM = $a_ativ["hr_prev_fim"];;
$HR_ALMOCO = $a_ativ["tempo_intervalo"];;
$HR_TOTAL = $a_ativ["tempo_real"];;
$DESCRICAO = $a_ativ["obs"];


$ATIVIDADES_CLIENTE = "";
$ATIVIDADES_IPARTNER = "";
$q_tarefas = query("select * from is_atividade where id_atividade_pai = '".$a_ativ["numreg"]."'");
while ($a_tarefas = farray($q_tarefas)) {
  if($a_tarefas["id_usuario_resp"]=="cliente") {
     $ATIVIDADES_CLIENTE .= $a_tarefas["assunto"]."<br>";
  } else {
     $ATIVIDADES_IPARTNER .= $a_tarefas["assunto"]."<br>";
  }
}


$texto = '

<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns:m="http://schemas.microsoft.com/office/2004/12/omml"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="Microsoft Word 12">
<meta name=Originator content="Microsoft Word 12">
<link rel=File-List href="AC_Padrao_arquivos/filelist.xml">
<link rel=Edit-Time-Data href="AC_Padrao_arquivos/editdata.mso">
<!--[if !mso]>
<style>
v\:* {behavior:url(#default#VML);}
o\:* {behavior:url(#default#VML);}
w\:* {behavior:url(#default#VML);}
.shape {behavior:url(#default#VML);}
</style>
<![endif]-->
<title>OASIS</title>
<!--[if gte mso 9]><xml>
 <o:DocumentProperties>
  <o:Author>Marcos Fiore</o:Author>
  <o:Template>Akna2.dot</o:Template>
  <o:LastAuthor>EduardoOhe</o:LastAuthor>
  <o:Revision>2</o:Revision>
  <o:TotalTime>108</o:TotalTime>
  <o:LastPrinted>2007-11-07T22:26:00Z</o:LastPrinted>
  <o:Created>2010-07-27T00:12:00Z</o:Created>
  <o:LastSaved>2010-07-27T00:12:00Z</o:LastSaved>
  <o:Pages>1</o:Pages>
  <o:Words>127</o:Words>
  <o:Characters>686</o:Characters>
  <o:Company>Particular</o:Company>
  <o:Lines>5</o:Lines>
  <o:Paragraphs>1</o:Paragraphs>
  <o:CharactersWithSpaces>812</o:CharactersWithSpaces>
  <o:Version>12.00</o:Version>
 </o:DocumentProperties>
 <o:OfficeDocumentSettings>
  <o:RelyOnVML/>
  <o:AllowPNG/>
 </o:OfficeDocumentSettings>
</xml><![endif]-->
<link rel=dataStoreItem href="AC_Padrao_arquivos/item0001.xml"
target="AC_Padrao_arquivos/props0002.xml">
<link rel=themeData href="AC_Padrao_arquivos/themedata.thmx">
<link rel=colorSchemeMapping href="AC_Padrao_arquivos/colorschememapping.xml">
<!--[if gte mso 9]><xml>
 <w:WordDocument>
  <w:TrackMoves>false</w:TrackMoves>
  <w:TrackFormatting/>
  <w:HyphenationZone>21</w:HyphenationZone>
  <w:PunctuationKerning/>
  <w:DrawingGridHorizontalSpacing>6 pt</w:DrawingGridHorizontalSpacing>
  <w:DisplayHorizontalDrawingGridEvery>2</w:DisplayHorizontalDrawingGridEvery>
  <w:ValidateAgainstSchemas/>
  <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
  <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
  <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
  <w:DoNotPromoteQF/>
  <w:LidThemeOther>PT-BR</w:LidThemeOther>
  <w:LidThemeAsian>X-NONE</w:LidThemeAsian>
  <w:LidThemeComplexScript>X-NONE</w:LidThemeComplexScript>
  <w:Compatibility>
   <w:BreakWrappedTables/>
   <w:SnapToGridInCell/>
   <w:WrapTextWithPunct/>
   <w:UseAsianBreakRules/>
   <w:DontGrowAutofit/>
   <w:DontUseIndentAsNumberingTabStop/>
   <w:FELineBreak11/>
   <w:WW11IndentRules/>
   <w:DontAutofitConstrainedTables/>
   <w:AutofitLikeWW11/>
   <w:HangulWidthLikeWW11/>
   <w:UseNormalStyleForList/>
  </w:Compatibility>
  <m:mathPr>
   <m:mathFont m:val="Cambria Math"/>
   <m:brkBin m:val="before"/>
   <m:brkBinSub m:val="&#45;-"/>
   <m:smallFrac m:val="off"/>
   <m:dispDef/>
   <m:lMargin m:val="0"/>
   <m:rMargin m:val="0"/>
   <m:defJc m:val="centerGroup"/>
   <m:wrapIndent m:val="1440"/>
   <m:intLim m:val="subSup"/>
   <m:naryLim m:val="undOvr"/>
  </m:mathPr></w:WordDocument>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:LatentStyles DefLockedState="false" DefUnhideWhenUsed="true"
  DefSemiHidden="true" DefQFormat="false" DefPriority="99"
  LatentStyleCount="267">
  <w:LsdException Locked="false" Priority="0" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="Normal"/>
  <w:LsdException Locked="false" Priority="0" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="heading 1"/>
  <w:LsdException Locked="false" Priority="9" QFormat="true" Name="heading 2"/>
  <w:LsdException Locked="false" Priority="9" QFormat="true" Name="heading 3"/>
  <w:LsdException Locked="false" Priority="9" QFormat="true" Name="heading 4"/>
  <w:LsdException Locked="false" Priority="9" QFormat="true" Name="heading 5"/>
  <w:LsdException Locked="false" Priority="9" QFormat="true" Name="heading 6"/>
  <w:LsdException Locked="false" Priority="9" QFormat="true" Name="heading 7"/>
  <w:LsdException Locked="false" Priority="9" QFormat="true" Name="heading 8"/>
  <w:LsdException Locked="false" Priority="9" QFormat="true" Name="heading 9"/>
  <w:LsdException Locked="false" Priority="39" Name="toc 1"/>
  <w:LsdException Locked="false" Priority="39" Name="toc 2"/>
  <w:LsdException Locked="false" Priority="39" Name="toc 3"/>
  <w:LsdException Locked="false" Priority="39" Name="toc 4"/>
  <w:LsdException Locked="false" Priority="39" Name="toc 5"/>
  <w:LsdException Locked="false" Priority="39" Name="toc 6"/>
  <w:LsdException Locked="false" Priority="39" Name="toc 7"/>
  <w:LsdException Locked="false" Priority="39" Name="toc 8"/>
  <w:LsdException Locked="false" Priority="39" Name="toc 9"/>
  <w:LsdException Locked="false" Priority="35" QFormat="true" Name="caption"/>
  <w:LsdException Locked="false" Priority="10" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="Title"/>
  <w:LsdException Locked="false" Priority="1" Name="Default Paragraph Font"/>
  <w:LsdException Locked="false" Priority="0" Name="Body Text Indent"/>
  <w:LsdException Locked="false" Priority="11" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="Subtitle"/>
  <w:LsdException Locked="false" Priority="0" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="Strong"/>
  <w:LsdException Locked="false" Priority="20" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="Emphasis"/>
  <w:LsdException Locked="false" Priority="0" Name="Normal (Web)"/>
  <w:LsdException Locked="false" Priority="59" SemiHidden="false"
   UnhideWhenUsed="false" Name="Table Grid"/>
  <w:LsdException Locked="false" UnhideWhenUsed="false" Name="Placeholder Text"/>
  <w:LsdException Locked="false" Priority="1" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="No Spacing"/>
  <w:LsdException Locked="false" Priority="60" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Shading"/>
  <w:LsdException Locked="false" Priority="61" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light List"/>
  <w:LsdException Locked="false" Priority="62" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Grid"/>
  <w:LsdException Locked="false" Priority="63" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 1"/>
  <w:LsdException Locked="false" Priority="64" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 2"/>
  <w:LsdException Locked="false" Priority="65" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 1"/>
  <w:LsdException Locked="false" Priority="66" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 2"/>
  <w:LsdException Locked="false" Priority="67" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 1"/>
  <w:LsdException Locked="false" Priority="68" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 2"/>
  <w:LsdException Locked="false" Priority="69" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 3"/>
  <w:LsdException Locked="false" Priority="70" SemiHidden="false"
   UnhideWhenUsed="false" Name="Dark List"/>
  <w:LsdException Locked="false" Priority="71" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Shading"/>
  <w:LsdException Locked="false" Priority="72" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful List"/>
  <w:LsdException Locked="false" Priority="73" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Grid"/>
  <w:LsdException Locked="false" Priority="60" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Shading Accent 1"/>
  <w:LsdException Locked="false" Priority="61" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light List Accent 1"/>
  <w:LsdException Locked="false" Priority="62" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Grid Accent 1"/>
  <w:LsdException Locked="false" Priority="63" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 1 Accent 1"/>
  <w:LsdException Locked="false" Priority="64" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 2 Accent 1"/>
  <w:LsdException Locked="false" Priority="65" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 1 Accent 1"/>
  <w:LsdException Locked="false" UnhideWhenUsed="false" Name="Revision"/>
  <w:LsdException Locked="false" Priority="34" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="List Paragraph"/>
  <w:LsdException Locked="false" Priority="29" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="Quote"/>
  <w:LsdException Locked="false" Priority="30" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="Intense Quote"/>
  <w:LsdException Locked="false" Priority="66" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 2 Accent 1"/>
  <w:LsdException Locked="false" Priority="67" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 1 Accent 1"/>
  <w:LsdException Locked="false" Priority="68" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 2 Accent 1"/>
  <w:LsdException Locked="false" Priority="69" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 3 Accent 1"/>
  <w:LsdException Locked="false" Priority="70" SemiHidden="false"
   UnhideWhenUsed="false" Name="Dark List Accent 1"/>
  <w:LsdException Locked="false" Priority="71" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Shading Accent 1"/>
  <w:LsdException Locked="false" Priority="72" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful List Accent 1"/>
  <w:LsdException Locked="false" Priority="73" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Grid Accent 1"/>
  <w:LsdException Locked="false" Priority="60" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Shading Accent 2"/>
  <w:LsdException Locked="false" Priority="61" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light List Accent 2"/>
  <w:LsdException Locked="false" Priority="62" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Grid Accent 2"/>
  <w:LsdException Locked="false" Priority="63" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 1 Accent 2"/>
  <w:LsdException Locked="false" Priority="64" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 2 Accent 2"/>
  <w:LsdException Locked="false" Priority="65" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 1 Accent 2"/>
  <w:LsdException Locked="false" Priority="66" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 2 Accent 2"/>
  <w:LsdException Locked="false" Priority="67" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 1 Accent 2"/>
  <w:LsdException Locked="false" Priority="68" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 2 Accent 2"/>
  <w:LsdException Locked="false" Priority="69" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 3 Accent 2"/>
  <w:LsdException Locked="false" Priority="70" SemiHidden="false"
   UnhideWhenUsed="false" Name="Dark List Accent 2"/>
  <w:LsdException Locked="false" Priority="71" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Shading Accent 2"/>
  <w:LsdException Locked="false" Priority="72" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful List Accent 2"/>
  <w:LsdException Locked="false" Priority="73" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Grid Accent 2"/>
  <w:LsdException Locked="false" Priority="60" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Shading Accent 3"/>
  <w:LsdException Locked="false" Priority="61" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light List Accent 3"/>
  <w:LsdException Locked="false" Priority="62" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Grid Accent 3"/>
  <w:LsdException Locked="false" Priority="63" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 1 Accent 3"/>
  <w:LsdException Locked="false" Priority="64" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 2 Accent 3"/>
  <w:LsdException Locked="false" Priority="65" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 1 Accent 3"/>
  <w:LsdException Locked="false" Priority="66" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 2 Accent 3"/>
  <w:LsdException Locked="false" Priority="67" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 1 Accent 3"/>
  <w:LsdException Locked="false" Priority="68" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 2 Accent 3"/>
  <w:LsdException Locked="false" Priority="69" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 3 Accent 3"/>
  <w:LsdException Locked="false" Priority="70" SemiHidden="false"
   UnhideWhenUsed="false" Name="Dark List Accent 3"/>
  <w:LsdException Locked="false" Priority="71" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Shading Accent 3"/>
  <w:LsdException Locked="false" Priority="72" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful List Accent 3"/>
  <w:LsdException Locked="false" Priority="73" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Grid Accent 3"/>
  <w:LsdException Locked="false" Priority="60" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Shading Accent 4"/>
  <w:LsdException Locked="false" Priority="61" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light List Accent 4"/>
  <w:LsdException Locked="false" Priority="62" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Grid Accent 4"/>
  <w:LsdException Locked="false" Priority="63" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 1 Accent 4"/>
  <w:LsdException Locked="false" Priority="64" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 2 Accent 4"/>
  <w:LsdException Locked="false" Priority="65" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 1 Accent 4"/>
  <w:LsdException Locked="false" Priority="66" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 2 Accent 4"/>
  <w:LsdException Locked="false" Priority="67" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 1 Accent 4"/>
  <w:LsdException Locked="false" Priority="68" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 2 Accent 4"/>
  <w:LsdException Locked="false" Priority="69" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 3 Accent 4"/>
  <w:LsdException Locked="false" Priority="70" SemiHidden="false"
   UnhideWhenUsed="false" Name="Dark List Accent 4"/>
  <w:LsdException Locked="false" Priority="71" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Shading Accent 4"/>
  <w:LsdException Locked="false" Priority="72" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful List Accent 4"/>
  <w:LsdException Locked="false" Priority="73" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Grid Accent 4"/>
  <w:LsdException Locked="false" Priority="60" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Shading Accent 5"/>
  <w:LsdException Locked="false" Priority="61" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light List Accent 5"/>
  <w:LsdException Locked="false" Priority="62" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Grid Accent 5"/>
  <w:LsdException Locked="false" Priority="63" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 1 Accent 5"/>
  <w:LsdException Locked="false" Priority="64" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 2 Accent 5"/>
  <w:LsdException Locked="false" Priority="65" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 1 Accent 5"/>
  <w:LsdException Locked="false" Priority="66" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 2 Accent 5"/>
  <w:LsdException Locked="false" Priority="67" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 1 Accent 5"/>
  <w:LsdException Locked="false" Priority="68" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 2 Accent 5"/>
  <w:LsdException Locked="false" Priority="69" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 3 Accent 5"/>
  <w:LsdException Locked="false" Priority="70" SemiHidden="false"
   UnhideWhenUsed="false" Name="Dark List Accent 5"/>
  <w:LsdException Locked="false" Priority="71" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Shading Accent 5"/>
  <w:LsdException Locked="false" Priority="72" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful List Accent 5"/>
  <w:LsdException Locked="false" Priority="73" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Grid Accent 5"/>
  <w:LsdException Locked="false" Priority="60" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Shading Accent 6"/>
  <w:LsdException Locked="false" Priority="61" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light List Accent 6"/>
  <w:LsdException Locked="false" Priority="62" SemiHidden="false"
   UnhideWhenUsed="false" Name="Light Grid Accent 6"/>
  <w:LsdException Locked="false" Priority="63" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 1 Accent 6"/>
  <w:LsdException Locked="false" Priority="64" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Shading 2 Accent 6"/>
  <w:LsdException Locked="false" Priority="65" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 1 Accent 6"/>
  <w:LsdException Locked="false" Priority="66" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium List 2 Accent 6"/>
  <w:LsdException Locked="false" Priority="67" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 1 Accent 6"/>
  <w:LsdException Locked="false" Priority="68" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 2 Accent 6"/>
  <w:LsdException Locked="false" Priority="69" SemiHidden="false"
   UnhideWhenUsed="false" Name="Medium Grid 3 Accent 6"/>
  <w:LsdException Locked="false" Priority="70" SemiHidden="false"
   UnhideWhenUsed="false" Name="Dark List Accent 6"/>
  <w:LsdException Locked="false" Priority="71" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Shading Accent 6"/>
  <w:LsdException Locked="false" Priority="72" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful List Accent 6"/>
  <w:LsdException Locked="false" Priority="73" SemiHidden="false"
   UnhideWhenUsed="false" Name="Colorful Grid Accent 6"/>
  <w:LsdException Locked="false" Priority="19" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="Subtle Emphasis"/>
  <w:LsdException Locked="false" Priority="21" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="Intense Emphasis"/>
  <w:LsdException Locked="false" Priority="31" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="Subtle Reference"/>
  <w:LsdException Locked="false" Priority="32" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="Intense Reference"/>
  <w:LsdException Locked="false" Priority="33" SemiHidden="false"
   UnhideWhenUsed="false" QFormat="true" Name="Book Title"/>
  <w:LsdException Locked="false" Priority="37" Name="Bibliography"/>
  <w:LsdException Locked="false" Priority="39" QFormat="true" Name="TOC Heading"/>
 </w:LatentStyles>
</xml><![endif]-->
<style>
<!--
 /* Font Definitions */
 @font-face
	{font-family:Wingdings;
	panose-1:5 0 0 0 0 0 0 0 0 0;
	mso-font-charset:2;
	mso-generic-font-family:auto;
	mso-font-pitch:variable;
	mso-font-signature:0 268435456 0 0 -2147483648 0;}
@font-face
	{font-family:"Cambria Math";
	panose-1:2 4 5 3 5 4 6 3 2 4;
	mso-font-charset:0;
	mso-generic-font-family:roman;
	mso-font-pitch:variable;
	mso-font-signature:-1610611985 1107304683 0 0 159 0;}
@font-face
	{font-family:Cambria;
	panose-1:2 4 5 3 5 4 6 3 2 4;
	mso-font-charset:0;
	mso-generic-font-family:roman;
	mso-font-pitch:variable;
	mso-font-signature:-1610611985 1073741899 0 0 159 0;}
@font-face
	{font-family:Calibri;
	panose-1:2 15 5 2 2 2 4 3 2 4;
	mso-font-charset:0;
	mso-generic-font-family:swiss;
	mso-font-pitch:variable;
	mso-font-signature:-1610611985 1073750139 0 0 159 0;}
@font-face
	{font-family:Tahoma;
	panose-1:2 11 6 4 3 5 4 4 2 4;
	mso-font-charset:0;
	mso-generic-font-family:swiss;
	mso-font-pitch:variable;
	mso-font-signature:-520082689 -1073717157 41 0 66047 0;}
@font-face
	{font-family:Verdana;
	panose-1:2 11 6 4 3 5 4 4 2 4;
	mso-font-charset:0;
	mso-generic-font-family:swiss;
	mso-font-pitch:variable;
	mso-font-signature:-1593833729 1073750107 16 0 415 0;}
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-unhide:no;
	mso-style-qformat:yes;
	mso-style-parent:"";
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";}
h1
	{mso-style-unhide:no;
	mso-style-qformat:yes;
	mso-style-link:"Título 1 Char";
	mso-style-next:Normal;
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	page-break-after:avoid;
	mso-outline-level:1;
	font-size:11.0pt;
	mso-bidi-font-size:10.0pt;
	font-family:"Verdana","sans-serif";
	mso-font-kerning:0pt;
	font-weight:bold;
	mso-bidi-font-weight:normal;}
h2
	{mso-style-priority:9;
	mso-style-qformat:yes;
	mso-style-link:"Título 2 Char";
	mso-style-next:Normal;
	margin-top:12.0pt;
	margin-right:0cm;
	margin-bottom:3.0pt;
	margin-left:0cm;
	mso-pagination:widow-orphan;
	page-break-after:avoid;
	mso-outline-level:2;
	font-size:14.0pt;
	font-family:"Cambria","serif";
	font-weight:bold;
	font-style:italic;}
h3
	{mso-style-noshow:yes;
	mso-style-priority:9;
	mso-style-qformat:yes;
	mso-style-link:"Título 3 Char";
	mso-style-next:Normal;
	margin-top:12.0pt;
	margin-right:0cm;
	margin-bottom:3.0pt;
	margin-left:0cm;
	mso-pagination:widow-orphan;
	page-break-after:avoid;
	mso-outline-level:3;
	font-size:13.0pt;
	font-family:"Cambria","serif";
	font-weight:bold;}
p.MsoToc1, li.MsoToc1, div.MsoToc1
	{mso-style-update:auto;
	mso-style-priority:39;
	mso-style-next:Normal;
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	tab-stops:right dotted 453.05pt;
	font-size:14.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";
	font-weight:bold;
	mso-bidi-font-weight:normal;
	mso-no-proof:yes;}
p.MsoToc2, li.MsoToc2, div.MsoToc2
	{mso-style-update:auto;
	mso-style-priority:39;
	mso-style-next:Normal;
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:0cm;
	margin-left:12.0pt;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";}
p.MsoHeader, li.MsoHeader, div.MsoHeader
	{mso-style-noshow:yes;
	mso-style-unhide:no;
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	tab-stops:center 212.6pt right 425.2pt;
	font-size:12.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";}
p.MsoFooter, li.MsoFooter, div.MsoFooter
	{mso-style-priority:99;
	mso-style-unhide:no;
	mso-style-link:"Rodapé Char";
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	tab-stops:center 212.6pt right 425.2pt;
	font-size:12.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";}
span.MsoEndnoteReference
	{mso-style-noshow:yes;
	mso-style-priority:99;
	vertical-align:super;}
p.MsoEndnoteText, li.MsoEndnoteText, div.MsoEndnoteText
	{mso-style-noshow:yes;
	mso-style-priority:99;
	mso-style-link:"Texto de nota de fim Char";
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";}
p.MsoBodyTextIndent, li.MsoBodyTextIndent, div.MsoBodyTextIndent
	{mso-style-noshow:yes;
	mso-style-unhide:no;
	mso-style-link:"Recuo de corpo de texto Char";
	margin:0cm;
	margin-bottom:.0001pt;
	text-align:justify;
	text-indent:70.8pt;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";}
p.MsoBodyTextIndent2, li.MsoBodyTextIndent2, div.MsoBodyTextIndent2
	{mso-style-noshow:yes;
	mso-style-priority:99;
	mso-style-link:"Recuo de corpo de texto 2 Char";
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:6.0pt;
	margin-left:14.15pt;
	line-height:200%;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";}
a:link, span.MsoHyperlink
	{mso-style-priority:99;
	mso-style-unhide:no;
	color:blue;
	text-decoration:underline;
	text-underline:single;}
a:visited, span.MsoHyperlinkFollowed
	{mso-style-noshow:yes;
	mso-style-priority:99;
	color:purple;
	mso-themecolor:followedhyperlink;
	text-decoration:underline;
	text-underline:single;}
p
	{margin-top:3.2pt;
	margin-right:0cm;
	margin-bottom:3.2pt;
	margin-left:0cm;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";}
p.MsoAcetate, li.MsoAcetate, div.MsoAcetate
	{mso-style-noshow:yes;
	mso-style-priority:99;
	mso-style-link:"Texto de balão Char";
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:8.0pt;
	font-family:"Tahoma","sans-serif";
	mso-fareast-font-family:"Times New Roman";}
p.MsoNoSpacing, li.MsoNoSpacing, div.MsoNoSpacing
	{mso-style-priority:1;
	mso-style-unhide:no;
	mso-style-qformat:yes;
	mso-style-parent:"";
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";}
p.MsoListParagraph, li.MsoListParagraph, div.MsoListParagraph
	{mso-style-priority:34;
	mso-style-unhide:no;
	mso-style-qformat:yes;
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:10.0pt;
	margin-left:36.0pt;
	mso-add-space:auto;
	line-height:115%;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:"Calibri","sans-serif";
	mso-fareast-font-family:Calibri;
	mso-bidi-font-family:"Times New Roman";
	mso-fareast-language:EN-US;}
p.MsoListParagraphCxSpFirst, li.MsoListParagraphCxSpFirst, div.MsoListParagraphCxSpFirst
	{mso-style-priority:34;
	mso-style-unhide:no;
	mso-style-qformat:yes;
	mso-style-type:export-only;
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:0cm;
	margin-left:36.0pt;
	margin-bottom:.0001pt;
	mso-add-space:auto;
	line-height:115%;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:"Calibri","sans-serif";
	mso-fareast-font-family:Calibri;
	mso-bidi-font-family:"Times New Roman";
	mso-fareast-language:EN-US;}
p.MsoListParagraphCxSpMiddle, li.MsoListParagraphCxSpMiddle, div.MsoListParagraphCxSpMiddle
	{mso-style-priority:34;
	mso-style-unhide:no;
	mso-style-qformat:yes;
	mso-style-type:export-only;
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:0cm;
	margin-left:36.0pt;
	margin-bottom:.0001pt;
	mso-add-space:auto;
	line-height:115%;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:"Calibri","sans-serif";
	mso-fareast-font-family:Calibri;
	mso-bidi-font-family:"Times New Roman";
	mso-fareast-language:EN-US;}
p.MsoListParagraphCxSpLast, li.MsoListParagraphCxSpLast, div.MsoListParagraphCxSpLast
	{mso-style-priority:34;
	mso-style-unhide:no;
	mso-style-qformat:yes;
	mso-style-type:export-only;
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:10.0pt;
	margin-left:36.0pt;
	mso-add-space:auto;
	line-height:115%;
	mso-pagination:widow-orphan;
	font-size:11.0pt;
	font-family:"Calibri","sans-serif";
	mso-fareast-font-family:Calibri;
	mso-bidi-font-family:"Times New Roman";
	mso-fareast-language:EN-US;}
p.MsoTocHeading, li.MsoTocHeading, div.MsoTocHeading
	{mso-style-noshow:yes;
	mso-style-priority:39;
	mso-style-qformat:yes;
	mso-style-parent:"Título 1";
	mso-style-next:Normal;
	margin-top:24.0pt;
	margin-right:0cm;
	margin-bottom:0cm;
	margin-left:0cm;
	margin-bottom:.0001pt;
	line-height:115%;
	mso-pagination:widow-orphan lines-together;
	page-break-after:avoid;
	font-size:14.0pt;
	font-family:"Cambria","serif";
	mso-fareast-font-family:"Times New Roman";
	mso-bidi-font-family:"Times New Roman";
	color:#365F91;
	mso-fareast-language:EN-US;
	font-weight:bold;}
span.RodapChar
	{mso-style-name:"Rodapé Char";
	mso-style-priority:99;
	mso-style-unhide:no;
	mso-style-locked:yes;
	mso-style-link:Rodapé;
	mso-ansi-font-size:12.0pt;
	mso-bidi-font-size:12.0pt;}
p.Preformatted, li.Preformatted, div.Preformatted
	{mso-style-name:Preformatted;
	mso-style-unhide:no;
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	tab-stops:0cm 47.95pt 95.9pt 143.85pt 191.8pt 239.75pt 287.7pt 335.65pt 383.6pt 431.55pt 479.5pt;
	font-size:10.0pt;
	font-family:"Courier New";
	mso-fareast-font-family:"Times New Roman";
	mso-bidi-font-family:"Times New Roman";
	layout-grid-mode:line;}
span.Ttulo1Char
	{mso-style-name:"Título 1 Char";
	mso-style-unhide:no;
	mso-style-locked:yes;
	mso-style-link:"Título 1";
	mso-ansi-font-size:11.0pt;
	font-family:"Verdana","sans-serif";
	mso-ascii-font-family:Verdana;
	mso-hansi-font-family:Verdana;
	font-weight:bold;
	mso-bidi-font-weight:normal;}
p.corpodetexto, li.corpodetexto, div.corpodetexto
	{mso-style-name:corpodetexto;
	mso-style-unhide:no;
	margin-top:5.0pt;
	margin-right:0cm;
	margin-bottom:5.0pt;
	margin-left:0cm;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	mso-bidi-font-size:10.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";
	mso-fareast-language:EN-US;}
p.titulo-3, li.titulo-3, div.titulo-3
	{mso-style-name:titulo-3;
	mso-style-unhide:no;
	margin-top:5.0pt;
	margin-right:0cm;
	margin-bottom:5.0pt;
	margin-left:0cm;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	mso-bidi-font-size:10.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";
	mso-fareast-language:EN-US;}
p.bullet1, li.bullet1, div.bullet1
	{mso-style-name:bullet1;
	mso-style-unhide:no;
	margin-top:5.0pt;
	margin-right:0cm;
	margin-bottom:5.0pt;
	margin-left:0cm;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	mso-bidi-font-size:10.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";
	mso-fareast-language:EN-US;}
span.Ttulo3Char
	{mso-style-name:"Título 3 Char";
	mso-style-noshow:yes;
	mso-style-priority:9;
	mso-style-unhide:no;
	mso-style-locked:yes;
	mso-style-link:"Título 3";
	mso-ansi-font-size:13.0pt;
	mso-bidi-font-size:13.0pt;
	font-family:"Cambria","serif";
	mso-ascii-font-family:Cambria;
	mso-fareast-font-family:"Times New Roman";
	mso-hansi-font-family:Cambria;
	mso-bidi-font-family:"Times New Roman";
	font-weight:bold;}
p.BULLET10, li.BULLET10, div.BULLET10
	{mso-style-name:BULLET1;
	mso-style-unhide:no;
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:0cm;
	margin-left:34.85pt;
	margin-bottom:.0001pt;
	text-align:justify;
	text-indent:-17.85pt;
	mso-pagination:widow-orphan;
	mso-list:l2 level1 lfo2;
	mso-hyphenate:none;
	tab-stops:list 18.0pt;
	font-size:10.0pt;
	font-family:"Arial","sans-serif";
	mso-fareast-font-family:"Times New Roman";
	mso-bidi-font-family:"Times New Roman";
	mso-ansi-language:EN-US;
	mso-fareast-language:EN-US;}
p.BULLET2, li.BULLET2, div.BULLET2
	{mso-style-name:BULLET2;
	mso-style-unhide:no;
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:0cm;
	margin-left:36.0pt;
	margin-bottom:.0001pt;
	text-align:justify;
	text-indent:-18.0pt;
	mso-pagination:widow-orphan;
	mso-hyphenate:none;
	tab-stops:list 36.0pt;
	font-size:10.0pt;
	font-family:"Arial","sans-serif";
	mso-fareast-font-family:"Times New Roman";
	mso-bidi-font-family:"Times New Roman";
	mso-fareast-language:EN-US;}
span.RecuodecorpodetextoChar
	{mso-style-name:"Recuo de corpo de texto Char";
	mso-style-noshow:yes;
	mso-style-unhide:no;
	mso-style-locked:yes;
	mso-style-link:"Recuo de corpo de texto";
	mso-ansi-font-size:12.0pt;
	mso-bidi-font-size:12.0pt;}
p.PropostaPonto, li.PropostaPonto, div.PropostaPonto
	{mso-style-name:Proposta_Ponto;
	mso-style-unhide:no;
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:5.65pt;
	margin-left:59.55pt;
	text-indent:-5.65pt;
	mso-pagination:widow-orphan;
	mso-hyphenate:none;
	tab-stops:65.2pt;
	font-size:10.0pt;
	font-family:"Arial","sans-serif";
	mso-fareast-font-family:"Times New Roman";
	mso-bidi-font-family:"Times New Roman";}
span.Ttulo2Char
	{mso-style-name:"Título 2 Char";
	mso-style-priority:9;
	mso-style-unhide:no;
	mso-style-locked:yes;
	mso-style-link:"Título 2";
	mso-ansi-font-size:14.0pt;
	mso-bidi-font-size:14.0pt;
	font-family:"Cambria","serif";
	mso-ascii-font-family:Cambria;
	mso-fareast-font-family:"Times New Roman";
	mso-hansi-font-family:Cambria;
	mso-bidi-font-family:"Times New Roman";
	font-weight:bold;
	font-style:italic;}
span.Recuodecorpodetexto2Char
	{mso-style-name:"Recuo de corpo de texto 2 Char";
	mso-style-noshow:yes;
	mso-style-priority:99;
	mso-style-unhide:no;
	mso-style-locked:yes;
	mso-style-link:"Recuo de corpo de texto 2";
	mso-ansi-font-size:12.0pt;
	mso-bidi-font-size:12.0pt;}
p.PropostaItem, li.PropostaItem, div.PropostaItem
	{mso-style-name:Proposta_Item;
	mso-style-unhide:no;
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:6.0pt;
	margin-left:0cm;
	line-height:12.0pt;
	mso-pagination:widow-orphan;
	mso-hyphenate:none;
	tab-stops:list 18.0pt left 122.45pt 129.5pt;
	font-size:10.0pt;
	font-family:"Arial","sans-serif";
	mso-fareast-font-family:"Times New Roman";
	mso-bidi-font-family:"Times New Roman";
	text-decoration:underline;
	text-underline:single;}
p.PropostaSubItem, li.PropostaSubItem, div.PropostaSubItem
	{mso-style-name:Proposta_SubItem;
	mso-style-unhide:no;
	mso-style-parent:Proposta_Item;
	margin-top:0cm;
	margin-right:0cm;
	margin-bottom:6.0pt;
	margin-left:0cm;
	line-height:12.0pt;
	mso-pagination:widow-orphan;
	mso-hyphenate:none;
	tab-stops:list 18.0pt left 122.45pt 129.5pt;
	font-size:10.0pt;
	font-family:"Arial","sans-serif";
	mso-fareast-font-family:"Times New Roman";
	mso-bidi-font-family:"Times New Roman";}
span.row
	{mso-style-name:row;
	mso-style-unhide:no;}
span.TextodebaloChar
	{mso-style-name:"Texto de balão Char";
	mso-style-noshow:yes;
	mso-style-priority:99;
	mso-style-unhide:no;
	mso-style-locked:yes;
	mso-style-link:"Texto de balão";
	mso-ansi-font-size:8.0pt;
	mso-bidi-font-size:8.0pt;
	font-family:"Tahoma","sans-serif";
	mso-ascii-font-family:Tahoma;
	mso-hansi-font-family:Tahoma;
	mso-bidi-font-family:Tahoma;}
span.TextodenotadefimChar
	{mso-style-name:"Texto de nota de fim Char";
	mso-style-noshow:yes;
	mso-style-priority:99;
	mso-style-unhide:no;
	mso-style-locked:yes;
	mso-style-link:"Texto de nota de fim";}
 /* Page Definitions */
 @page
	{mso-footnote-separator:url("AC_Padrao_arquivos/header.htm") fs;
	mso-footnote-continuation-separator:url("AC_Padrao_arquivos/header.htm") fcs;
	mso-endnote-separator:url("AC_Padrao_arquivos/header.htm") es;
	mso-endnote-continuation-separator:url("AC_Padrao_arquivos/header.htm") ecs;}
@page WordSection1
	{size:595.3pt 841.9pt;
	margin:36.0pt 36.0pt 36.0pt 36.0pt;
	mso-header-margin:35.45pt;
	mso-footer-margin:35.45pt;
	mso-paper-source:0;}
div.WordSection1
	{page:WordSection1;}
 /* List Definitions */
 @list l0
	{mso-list-id:1;
	mso-list-template-ids:1;
	mso-list-name:WW8Num1;}
@list l0:level1
	{mso-level-number-format:bullet;
	mso-level-text:·;
	mso-level-tab-stop:11.35pt;
	mso-level-number-position:left;
	margin-left:11.35pt;
	text-indent:-11.35pt;
	mso-ascii-font-family:Symbol;
	mso-hansi-font-family:Symbol;}
@list l0:level2
	{mso-level-number-format:bullet;
	mso-level-text:·;
	mso-level-tab-stop:22.7pt;
	mso-level-number-position:left;
	margin-left:22.7pt;
	text-indent:-11.35pt;
	mso-ascii-font-family:Symbol;
	mso-hansi-font-family:Symbol;}
@list l0:level3
	{mso-level-number-format:bullet;
	mso-level-text:·;
	mso-level-tab-stop:34.0pt;
	mso-level-number-position:left;
	margin-left:34.0pt;
	text-indent:-11.35pt;
	mso-ascii-font-family:Symbol;
	mso-hansi-font-family:Symbol;}
@list l0:level4
	{mso-level-number-format:bullet;
	mso-level-text:·;
	mso-level-tab-stop:45.35pt;
	mso-level-number-position:left;
	margin-left:45.35pt;
	text-indent:-11.35pt;
	mso-ascii-font-family:Symbol;
	mso-hansi-font-family:Symbol;}
@list l0:level5
	{mso-level-number-format:bullet;
	mso-level-text:·;
	mso-level-tab-stop:2.0cm;
	mso-level-number-position:left;
	margin-left:2.0cm;
	text-indent:-11.35pt;
	mso-ascii-font-family:Symbol;
	mso-hansi-font-family:Symbol;}
@list l0:level6
	{mso-level-number-format:bullet;
	mso-level-text:·;
	mso-level-tab-stop:68.05pt;
	mso-level-number-position:left;
	margin-left:68.05pt;
	text-indent:-11.35pt;
	mso-ascii-font-family:Symbol;
	mso-hansi-font-family:Symbol;}
@list l0:level7
	{mso-level-number-format:bullet;
	mso-level-text:·;
	mso-level-tab-stop:79.35pt;
	mso-level-number-position:left;
	margin-left:79.35pt;
	text-indent:-11.35pt;
	mso-ascii-font-family:Symbol;
	mso-hansi-font-family:Symbol;}
@list l0:level8
	{mso-level-number-format:bullet;
	mso-level-text:·;
	mso-level-tab-stop:90.7pt;
	mso-level-number-position:left;
	margin-left:90.7pt;
	text-indent:-11.35pt;
	mso-ascii-font-family:Symbol;
	mso-hansi-font-family:Symbol;}
@list l0:level9
	{mso-level-number-format:bullet;
	mso-level-text:·;
	mso-level-tab-stop:102.05pt;
	mso-level-number-position:left;
	margin-left:102.05pt;
	text-indent:-11.35pt;
	mso-ascii-font-family:Symbol;
	mso-hansi-font-family:Symbol;}
@list l1
	{mso-list-id:2;
	mso-list-template-ids:2;
	mso-list-name:WW8Num2;}
@list l1:level1
	{mso-level-tab-stop:18.0pt;
	mso-level-number-position:left;
	margin-left:18.0pt;
	text-indent:-18.0pt;}
@list l1:level2
	{mso-level-text:"%1\.%2\.";
	mso-level-tab-stop:52.0pt;
	mso-level-number-position:left;
	margin-left:52.0pt;
	text-indent:-34.0pt;}
@list l1:level3
	{mso-level-text:"%1\.%2\.%3\.";
	mso-level-tab-stop:77.95pt;
	mso-level-number-position:left;
	margin-left:77.95pt;
	text-indent:-35.45pt;}
@list l1:level4
	{mso-level-text:"%1\.%2\.%3\.%4\.";
	mso-level-tab-stop:90.0pt;
	mso-level-number-position:left;
	margin-left:90.0pt;
	text-indent:-36.0pt;}
@list l1:level5
	{mso-level-text:"%1\.%2\.%3\.%4\.%5\.";
	mso-level-tab-stop:126.0pt;
	mso-level-number-position:left;
	margin-left:126.0pt;
	text-indent:-54.0pt;}
@list l1:level6
	{mso-level-text:"%1\.%2\.%3\.%4\.%5\.%6\.";
	mso-level-tab-stop:144.0pt;
	mso-level-number-position:left;
	margin-left:144.0pt;
	text-indent:-54.0pt;}
@list l1:level7
	{mso-level-text:"%1\.%2\.%3\.%4\.%5\.%6\.%7\.";
	mso-level-tab-stop:180.0pt;
	mso-level-number-position:left;
	margin-left:180.0pt;
	text-indent:-72.0pt;}
@list l1:level8
	{mso-level-text:"%1\.%2\.%3\.%4\.%5\.%6\.%7\.%8\.";
	mso-level-tab-stop:198.0pt;
	mso-level-number-position:left;
	margin-left:198.0pt;
	text-indent:-72.0pt;}
@list l1:level9
	{mso-level-text:"%1\.%2\.%3\.%4\.%5\.%6\.%7\.%8\.%9\.";
	mso-level-tab-stop:234.0pt;
	mso-level-number-position:left;
	margin-left:234.0pt;
	text-indent:-90.0pt;}
@list l2
	{mso-list-id:151218062;
	mso-list-type:simple;
	mso-list-template-ids:-480985206;
	mso-list-name:WW8Num432;}
@list l2:level1
	{mso-level-number-format:bullet;
	mso-level-style-link:BULLET1;
	mso-level-text:\F0B7;
	mso-level-tab-stop:18.0pt;
	mso-level-number-position:left;
	margin-left:18.0pt;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l3
	{mso-list-id:270363225;
	mso-list-type:hybrid;
	mso-list-template-ids:1868187662 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l3:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l4
	{mso-list-id:292907803;
	mso-list-type:hybrid;
	mso-list-template-ids:-1109883014 68550673 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l4:level1
	{mso-level-text:"%1\)";
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;}
@list l5
	{mso-list-id:336887223;
	mso-list-type:hybrid;
	mso-list-template-ids:1040719036 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l5:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l6
	{mso-list-id:366488531;
	mso-list-type:hybrid;
	mso-list-template-ids:341447132 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l6:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l7
	{mso-list-id:451750965;
	mso-list-type:hybrid;
	mso-list-template-ids:1830424488 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l7:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l8
	{mso-list-id:592710862;
	mso-list-type:hybrid;
	mso-list-template-ids:290886718 68550671 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l8:level1
	{mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;}
@list l9
	{mso-list-id:693002696;
	mso-list-type:hybrid;
	mso-list-template-ids:238610042 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l9:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l10
	{mso-list-id:737870687;
	mso-list-type:hybrid;
	mso-list-template-ids:680170532 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l10:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l11
	{mso-list-id:762146954;
	mso-list-type:hybrid;
	mso-list-template-ids:2146617014 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l11:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	margin-left:38.65pt;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l12
	{mso-list-id:785470539;
	mso-list-type:hybrid;
	mso-list-template-ids:2052122906 -1584114552 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l12:level1
	{mso-level-text:"%1\)";
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	margin-left:19.5pt;
	text-indent:-18.0pt;
	mso-ansi-font-size:10.0pt;
	mso-ascii-font-family:"Times New Roman";
	mso-fareast-font-family:"Times New Roman";
	mso-hansi-font-family:"Times New Roman";
	mso-bidi-font-family:"Times New Roman";}
@list l13
	{mso-list-id:808667253;
	mso-list-type:hybrid;
	mso-list-template-ids:-830053302 68550673 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l13:level1
	{mso-level-text:"%1\)";
	mso-level-tab-stop:18.0pt;
	mso-level-number-position:left;
	margin-left:18.0pt;
	text-indent:-18.0pt;}
@list l14
	{mso-list-id:819928702;
	mso-list-type:hybrid;
	mso-list-template-ids:169002462 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l14:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l15
	{mso-list-id:898981465;
	mso-list-type:hybrid;
	mso-list-template-ids:-16372624 -120917154 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l15:level1
	{mso-level-text:"%1\)";
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	margin-left:54.0pt;
	text-indent:-18.0pt;}
@list l16
	{mso-list-id:922301985;
	mso-list-type:hybrid;
	mso-list-template-ids:-570112176 62310500 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l16:level1
	{mso-level-text:"%1\)";
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	margin-left:39.3pt;
	text-indent:-18.0pt;
	mso-ansi-font-size:7.0pt;
	mso-bidi-font-size:7.0pt;
	color:red;}
@list l17
	{mso-list-id:1000472890;
	mso-list-type:hybrid;
	mso-list-template-ids:-2029615072 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l17:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l18
	{mso-list-id:1033926000;
	mso-list-type:hybrid;
	mso-list-template-ids:1637527682 68550673 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l18:level1
	{mso-level-text:"%1\)";
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;}
@list l19
	{mso-list-id:1095512886;
	mso-list-type:hybrid;
	mso-list-template-ids:-1517910714 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l19:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l20
	{mso-list-id:1100369614;
	mso-list-type:hybrid;
	mso-list-template-ids:253637644 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l20:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l21
	{mso-list-id:1140536981;
	mso-list-type:hybrid;
	mso-list-template-ids:-2135389516 68550679 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l21:level1
	{mso-level-number-format:alpha-lower;
	mso-level-text:"%1\)";
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;}
@list l22
	{mso-list-id:1143548646;
	mso-list-type:hybrid;
	mso-list-template-ids:233989318 -2023218166 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l22:level1
	{mso-level-number-format:roman-upper;
	mso-level-text:"%1\)";
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	margin-left:54.0pt;
	text-indent:-36.0pt;}
@list l23
	{mso-list-id:1158769972;
	mso-list-type:hybrid;
	mso-list-template-ids:1293180650 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l23:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l24
	{mso-list-id:1206331771;
	mso-list-type:hybrid;
	mso-list-template-ids:600080374 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l24:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	margin-left:18.0pt;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l25
	{mso-list-id:1224947580;
	mso-list-type:hybrid;
	mso-list-template-ids:-1851626410 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l25:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l26
	{mso-list-id:1250237546;
	mso-list-type:hybrid;
	mso-list-template-ids:1400116474 68550673 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l26:level1
	{mso-level-text:"%1\)";
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;}
@list l26:level2
	{mso-level-number-format:alpha-lower;
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;}
@list l27
	{mso-list-id:1338460027;
	mso-list-type:hybrid;
	mso-list-template-ids:-132230896 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l27:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l28
	{mso-list-id:1440225358;
	mso-list-type:hybrid;
	mso-list-template-ids:669543632 68550673 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l28:level1
	{mso-level-text:"%1\)";
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;}
@list l29
	{mso-list-id:1507092465;
	mso-list-type:hybrid;
	mso-list-template-ids:-861496390 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l29:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l30
	{mso-list-id:1522357739;
	mso-list-type:hybrid;
	mso-list-template-ids:-1465490282 68550673 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l30:level1
	{mso-level-text:"%1\)";
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;}
@list l30:level2
	{mso-level-number-format:alpha-lower;
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;}
@list l31
	{mso-list-id:1618174423;
	mso-list-type:hybrid;
	mso-list-template-ids:2061668106 68550673 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l31:level1
	{mso-level-text:"%1\)";
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;}
@list l32
	{mso-list-id:1932660881;
	mso-list-type:hybrid;
	mso-list-template-ids:1683547356 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l32:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l33
	{mso-list-id:1938630861;
	mso-list-type:hybrid;
	mso-list-template-ids:-261052814 68550671 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l33:level1
	{mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;}
@list l34
	{mso-list-id:1943761953;
	mso-list-type:hybrid;
	mso-list-template-ids:605706726 1160517710 -251341660 1428560082 2112095156 1851060512 -13058144 -276162262 -1065551470 66772170;}
@list l34:level1
	{mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;}
@list l35
	{mso-list-id:2014605282;
	mso-list-type:hybrid;
	mso-list-template-ids:-204703218 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l35:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l36
	{mso-list-id:2029092201;
	mso-list-type:hybrid;
	mso-list-template-ids:202919312 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l36:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l37
	{mso-list-id:2066173491;
	mso-list-type:hybrid;
	mso-list-template-ids:1773688190 68550657 68550659 68550661 68550657 68550659 68550661 68550657 68550659 68550661;}
@list l37:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:36.0pt;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
@list l38
	{mso-list-id:2106529744;
	mso-list-type:hybrid;
	mso-list-template-ids:-1979963128 68550657 68550681 68550683 68550671 68550681 68550683 68550671 68550681 68550683;}
@list l38:level1
	{mso-level-number-format:bullet;
	mso-level-text:\F0B7;
	mso-level-tab-stop:none;
	mso-level-number-position:left;
	text-indent:-18.0pt;
	font-family:Symbol;}
ol
	{margin-bottom:0cm;}
ul
	{margin-bottom:0cm;}
-->
</style>
<!--[if gte mso 10]>
<style>
 /* Style Definitions */
 table.MsoNormalTable
	{mso-style-name:"Tabela normal";
	mso-tstyle-rowband-size:0;
	mso-tstyle-colband-size:0;
	mso-style-noshow:yes;
	mso-style-priority:99;
	mso-style-qformat:yes;
	mso-style-parent:"";
	mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
	mso-para-margin:0cm;
	mso-para-margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:"Times New Roman","serif";}
table.MsoTableGrid
	{mso-style-name:"Tabela com grade";
	mso-tstyle-rowband-size:0;
	mso-tstyle-colband-size:0;
	mso-style-priority:59;
	mso-style-unhide:no;
	border:solid black 1.0pt;
	mso-border-alt:solid black .5pt;
	mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
	mso-border-insideh:.5pt solid black;
	mso-border-insidev:.5pt solid black;
	mso-para-margin:0cm;
	mso-para-margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:"Times New Roman","serif";}
</style>
<![endif]--><!--[if gte mso 9]><xml>
 <o:shapedefaults v:ext="edit" spidmax="36866"/>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <o:shapelayout v:ext="edit">
  <o:idmap v:ext="edit" data="1"/>
 </o:shapelayout></xml><![endif]-->
</head>

<body lang=PT-BR link=blue vlink=purple style="tab-interval:35.4pt">

<div class=WordSection1>

<p class=MsoNormal align=center style="text-align:center"><span
style="mso-no-proof:yes"><v:shapetype id="_x0000_t75" coordsize="21600,21600"
 o:spt="75" o:preferrelative="t" path="m@4@5l@4@11@9@11@9@5xe" filled="f"
 stroked="f">
 <v:stroke joinstyle="miter"/>
 <v:formulas>
  <v:f eqn="if lineDrawn pixelLineWidth 0"/>
  <v:f eqn="sum @0 1 0"/>
  <v:f eqn="sum 0 0 @1"/>
  <v:f eqn="prod @2 1 2"/>
  <v:f eqn="prod @3 21600 pixelWidth"/>
  <v:f eqn="prod @3 21600 pixelHeight"/>
  <v:f eqn="sum @0 0 1"/>
  <v:f eqn="prod @6 1 2"/>
  <v:f eqn="prod @7 21600 pixelWidth"/>
  <v:f eqn="sum @8 21600 0"/>
  <v:f eqn="prod @7 21600 pixelHeight"/>
  <v:f eqn="sum @10 21600 0"/>
 </v:formulas>
 <v:path o:extrusionok="f" gradientshapeok="t" o:connecttype="rect"/>
 <o:lock v:ext="edit" aspectratio="t"/>
</v:shapetype><v:shape id="Imagem_x0020_1" o:spid="_x0000_i1025" type="#_x0000_t75"
 alt="logo_i-partner" style="width:120.75pt;height:30.75pt;visibility:visible">
 <v:imagedata src="../../images/image001.png" o:title="logo_i-partner"/>
</v:shape></span></p>

<p class=MsoNormal align=center style="text-align:center"><b style="mso-bidi-font-weight:
normal">Relatório de A.C. (Assessoria ao Cliente)<o:p></o:p></b></p>

<p class=MsoNormal align=center style="text-align:center"><b style="mso-bidi-font-weight:
normal"><span style="font-size:8.0pt;font-family:"Calibri","sans-serif";
mso-ascii-theme-font:minor-latin;mso-hansi-theme-font:minor-latin"><o:p>&nbsp;</o:p></span></b></p>

<div align=center>

<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0 width=612
 style="width:459.0pt;border-collapse:collapse;border:none;mso-border-alt:solid black .5pt;
 mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt;mso-border-insideh:
 .5pt solid black;mso-border-insidev:.5pt solid black">
 <tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes">
  <td width=120 valign=top style="width:90.35pt;border:solid black 1.0pt;
  border-right:solid windowtext 1.0pt;mso-border-alt:solid black .5pt;
  mso-border-right-alt:solid windowtext .5pt;background:#8DB3E2;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal><b style="mso-bidi-font-weight:normal"><span
  style="font-size:8.0pt">Protocolo: @PROTOCOLO@ <o:p></o:p></span></b></p>
  </td>
  <td width=492 colspan=4 valign=top style="width:368.65pt;border:solid black 1.0pt;
  border-left:none;mso-border-left-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">@CLIENTE@</span></b><span
  style="font-size:8.0pt"><o:p></o:p></span></p>
  </td>
 </tr>
 <tr style="mso-yfti-irow:1">
  <td width=120 valign=top style="width:90.35pt;border:solid black 1.0pt;
  border-top:none;mso-border-top-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  background:#8DB3E2;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">Data<o:p></o:p></span></b></p>
  </td>
  <td width=113 valign=top style="width:84.95pt;border-top:none;border-left:
  none;border-bottom:solid black 1.0pt;border-right:solid black 1.0pt;
  mso-border-top-alt:solid black .5pt;mso-border-left-alt:solid black .5pt;
  mso-border-alt:solid black .5pt;background:#8DB3E2;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">Hr.Início<o:p></o:p></span></b></p>
  </td>
  <td width=123 valign=top style="width:92.2pt;border-top:none;border-left:
  none;border-bottom:solid black 1.0pt;border-right:solid black 1.0pt;
  mso-border-top-alt:solid black .5pt;mso-border-left-alt:solid black .5pt;
  mso-border-alt:solid black .5pt;background:#8DB3E2;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">Hr.Fim<o:p></o:p></span></b></p>
  </td>
  <td width=127 valign=top style="width:95.3pt;border-top:none;border-left:
  none;border-bottom:solid black 1.0pt;border-right:solid black 1.0pt;
  mso-border-top-alt:solid black .5pt;mso-border-left-alt:solid black .5pt;
  mso-border-alt:solid black .5pt;background:#8DB3E2;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">Hr.Intervalo (-)<o:p></o:p></span></b></p>
  </td>
  <td width=128 valign=top style="width:96.2pt;border-top:none;border-left:
  none;border-bottom:solid black 1.0pt;border-right:solid black 1.0pt;
  mso-border-top-alt:solid black .5pt;mso-border-left-alt:solid black .5pt;
  mso-border-alt:solid black .5pt;background:#8DB3E2;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">Hr.Total<o:p></o:p></span></b></p>
  </td>
 </tr>
 <tr style="mso-yfti-irow:2">
  <td width=120 valign=top style="width:90.35pt;border:solid black 1.0pt;
  border-top:none;mso-border-top-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">@DATA@<o:p></o:p></span></b></p>
  </td>
  <td width=113 valign=top style="width:84.95pt;border-top:none;border-left:
  none;border-bottom:solid black 1.0pt;border-right:solid black 1.0pt;
  mso-border-top-alt:solid black .5pt;mso-border-left-alt:solid black .5pt;
  mso-border-alt:solid black .5pt;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">@HR_INICIO@<o:p></o:p></span></b></p>
  </td>
  <td width=123 valign=top style="width:92.2pt;border-top:none;border-left:
  none;border-bottom:solid black 1.0pt;border-right:solid black 1.0pt;
  mso-border-top-alt:solid black .5pt;mso-border-left-alt:solid black .5pt;
  mso-border-alt:solid black .5pt;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">@HR_FIM@<o:p></o:p></span></b></p>
  </td>
  <td width=127 valign=top style="width:95.3pt;border-top:none;border-left:
  none;border-bottom:solid black 1.0pt;border-right:solid black 1.0pt;
  mso-border-top-alt:solid black .5pt;mso-border-left-alt:solid black .5pt;
  mso-border-alt:solid black .5pt;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">@HR_ALMOCO@<o:p></o:p></span></b></p>
  </td>
  <td width=128 valign=top style="width:96.2pt;border-top:none;border-left:
  none;border-bottom:solid black 1.0pt;border-right:solid black 1.0pt;
  mso-border-top-alt:solid black .5pt;mso-border-left-alt:solid black .5pt;
  mso-border-alt:solid black .5pt;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">@HR_TOTAL@<o:p></o:p></span></b></p>
  </td>
 </tr>
 <tr style="mso-yfti-irow:3">
  <td width=612 colspan=5 valign=top style="width:459.0pt;border:solid black 1.0pt;
  border-top:none;mso-border-top-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  background:#95B3D7;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">Participantes
  do Cliente<o:p></o:p></span></b></p>
  </td>
 </tr>
 <tr style="mso-yfti-irow:4">
  <td width=612 colspan=5 valign=top style="width:459.0pt;border:solid black 1.0pt;
  border-top:none;mso-border-top-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  background:white;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal><span style="font-size:8.0pt">@PARTICIPANTES_CLIENTE@<span
  style="color:red"><o:p></o:p></span></span></p>
  </td>
 </tr>
 <tr style="mso-yfti-irow:5">
  <td width=612 colspan=5 valign=top style="width:459.0pt;border:solid black 1.0pt;
  border-top:none;mso-border-top-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  background:#95B3D7;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">Participantes
  i-Partner<o:p></o:p></span></b></p>
  </td>
 </tr>
 <tr style="mso-yfti-irow:6">
  <td width=612 colspan=5 valign=top style="width:459.0pt;border:solid black 1.0pt;
  border-top:none;mso-border-top-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  background:white;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal><span style="font-size:8.0pt">@PARTICIPANTES_IPARTNER@<span
  style="color:red"><o:p></o:p></span></span></p>
  </td>
 </tr>
 <tr style="mso-yfti-irow:7">
  <td width=612 colspan=5 valign=top style="width:459.0pt;border:solid black 1.0pt;
  border-top:none;mso-border-top-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  background:#95B3D7;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">Atividades
  Executadas<o:p></o:p></span></b></p>
  </td>
 </tr>
 <tr style="mso-yfti-irow:8">
  <td width=612 colspan=5 valign=top style="width:459.0pt;border:solid black 1.0pt;
  border-top:none;mso-border-top-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  background:white;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal><span style="font-size:8.0pt">@DESCRICAO@<span
  style="color:red"><o:p></o:p></span></span></p>
  </td>
 </tr>
 <tr style="mso-yfti-irow:9">
  <td width=612 colspan=5 valign=top style="width:459.0pt;border:solid black 1.0pt;
  border-top:none;mso-border-top-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  background:#95B3D7;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">Atividades
  a Executar Cliente<o:p></o:p></span></b></p>
  </td>
 </tr>
 <tr style="mso-yfti-irow:10">
  <td width=612 colspan=5 valign=top style="width:459.0pt;border-top:none;
  border-left:solid black 1.0pt;border-bottom:solid black 1.0pt;border-right:
  solid windowtext 1.0pt;mso-border-top-alt:solid black .5pt;mso-border-alt:
  solid black .5pt;mso-border-right-alt:solid windowtext .5pt;background:white;
  padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal><span style="font-size:8.0pt">@ATIVIDADES_CLIENTE@<o:p></o:p></span></p>
  </td>
 </tr>
 <tr style="mso-yfti-irow:11">
  <td width=612 colspan=5 valign=top style="width:459.0pt;border:solid black 1.0pt;
  border-top:none;mso-border-top-alt:solid black .5pt;mso-border-alt:solid black .5pt;
  background:#95B3D7;padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal align=center style="text-align:center"><b
  style="mso-bidi-font-weight:normal"><span style="font-size:8.0pt">Atividades
  a Executar i-Partner<o:p></o:p></span></b></p>
  </td>
 </tr>
 <tr style="mso-yfti-irow:12;mso-yfti-lastrow:yes">
  <td width=612 colspan=5 valign=top style="width:459.0pt;border-top:none;
  border-left:solid black 1.0pt;border-bottom:solid black 1.0pt;border-right:
  solid windowtext 1.0pt;mso-border-top-alt:solid black .5pt;mso-border-alt:
  solid black .5pt;mso-border-right-alt:solid windowtext .5pt;background:white;
  padding:0cm 5.4pt 0cm 5.4pt">
  <p class=MsoNormal><span style="font-size:8.0pt">@ATIVIDADES_IPARTNER@<o:p></o:p></span></p>
  </td>
 </tr>
</table>

</div>

<p class=MsoNormal><span style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal><span style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal><span style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt">De Acordo :<o:p></o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal><span style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt">_______________________________________<o:p></o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt">@CLIENTE@<o:p></o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt">_______________________________________<o:p></o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt">i-Partner <o:p></o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span lang=EN-US
style="font-size:9.0pt;mso-bidi-font-size:12.0pt;mso-ansi-language:EN-US">i-Partner
Consulting &amp; Web Solutions<o:p></o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span lang=EN-US
style="font-size:9.0pt;mso-bidi-font-size:12.0pt;mso-ansi-language:EN-US">Unid.
</span><span style="font-size:9.0pt;mso-bidi-font-size:12.0pt">ABC : Av
Francisco Prestes Maia, 902 – Cj 12 – Centro – São Bernardo – SP – CEP :
09770-000 – Fone: 11 2677-0655<o:p></o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt">Unid SP : Rua Vergueiro, 2087
– Cj. 101 – Vila Mariana – São Paulo – SP<span style="mso-spacerun:yes"> 
</span>CEP 04101-000 – Fone: 11 5087-8905 <o:p></o:p></span></p>

<p class=MsoNormal align=center style="text-align:center"><span
style="font-size:9.0pt;mso-bidi-font-size:12.0pt"><o:p>&nbsp;</o:p></span></p>

</div>

</body>

</html>

';

$texto = str_replace('@PROTOCOLO@',$PROTOCOLO,$texto);
$texto = str_replace('@CLIENTE@',$CLIENTE,$texto);
$texto = str_replace('@DATA@',$DATA,$texto);
$texto = str_replace('@HR_INICIO@',$HR_INICIO,$texto);
$texto = str_replace('@HR_FIM@',$HR_FIM,$texto);
$texto = str_replace('@HR_ALMOCO@',$HR_ALMOCO,$texto);
$texto = str_replace('@HR_TOTAL@',$HR_TOTAL,$texto);
$texto = str_replace('@PARTICIPANTES_CLIENTE@',$PARTICIPANTES_CLIENTE,$texto);
$texto = str_replace('@PARTICIPANTES_IPARTNER@',$PARTICIPANTES_IPARTNER,$texto);
$texto = str_replace('@DESCRICAO@',$DESCRICAO,$texto);
$texto = str_replace('@ATIVIDADES_CLIENTE@',$ATIVIDADES_CLIENTE,$texto);
$texto = str_replace('@ATIVIDADES_IPARTNER@',$ATIVIDADES_IPARTNER,$texto);

echo $texto;


?>
