// Faz o envio da p�gina solicitada na fun��o Geral()
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
// at� aki, normal, sem mist�rios, mas, a fun��o abaixo � o que vai fazer a "m�gica"
// Na linha abaixo chamamos a fun��o newTag(), que � a respons�vel por isso
newTag();
}
}

// A fun��o MAGICA, heheheh

function newTag()
{    
    // Pegando a div que recebr� o JavaScript
    var conteudo = document.getElementById("teste");
    
    // Declarando a cria��o de uma nova tag <script>
    var newElement = document.createElement("script");
    
    // Pegando os valores das Tags <script> que est�o na p�gina carregada pelo AJAX
    var scripts = resp.getElementsByTagName("script");
        
    // Aki, vamos inserir o conte�do da tag <script> que pegamos na linha acima    
        for(i = 0; i < scripts.length; i++)
       {
            newElement.text = scripts[i].innerHTML;
       }
      
       // Agora, inserimos a nova tag <script> dentro da div na p�gina inicial
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