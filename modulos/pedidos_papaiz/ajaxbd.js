// Faz o envio da página solicitada na função Geral()
var xmlHttp
function Geral()
{
xmlHttp=GetXmlHttpObject();
var url="ajax2.php";
xmlHttp.onreadystatechange=stateChanged;
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
document.getElementById("txtHint").innerHTML="<center><img src='ajax-loader(5).gif'>";
}

function stateChanged()
{
if (xmlHttp.readyState==4)
{
document.getElementById("txtHint").innerHTML="";
document.getElementById("resp").innerHTML=xmlHttp.responseText;
// até aki, normal, sem mistérios, mas, a função abaixo é o que vai fazer a "mágica"
// Na linha abaixo chamamos a função newTag(), que é a responsável por isso
newTag();
}
}

// A função MAGICA, heheheh

function newTag()
{    
    // Pegando a div que recebrá o JavaScript
    var conteudo = document.getElementById("teste");
    
    // Declarando a criação de uma nova tag <script>
    var newElement = document.createElement("script");
    
    // Pegando os valores das Tags <script> que estão na página carregada pelo AJAX
    var scripts = resp.getElementsByTagName("script");
        
    // Aki, vamos inserir o conteúdo da tag <script> que pegamos na linha acima    
        for(i = 0; i < scripts.length; i++)
       {
            newElement.text = scripts[i].innerHTML;
       }
      
       // Agora, inserimos a nova tag <script> dentro da div na página inicial
       conteudo.appendChild(newElement);
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}