/* Parte do Procura User */
var campoPesquisaUser = document.getElementById('campoPesquisaUser'); // campo de pesquisar usuarios
campoPesquisaUser.addEventListener('keyup', function(){ // quando houver mudança no texto do campo pesquisar
	var str = campoPesquisaUser.value.toUpperCase().trim();
	var rows = document.getElementById('listaUsers').getElementsByTagName('a'); // pega todas as linhas (anchors) dentro da div 'listaUsers'
	for(var i = 0; i < rows.length; i++)
	{
		if(str.length > 1)
		{
			var userName = rows[i].getElementsByTagName('p')[0].textContent;
			if(userName.toUpperCase().indexOf(str) > -1)
			{
				rows[i].className = 'visible';
			}
			else
			{
				rows[i].className = ('hidden');
			}
		}
		else
		{
			rows[i].className = ('hidden');
		}
	}
});
/* Fim do Procura User */

/* Inbox */
// Função que marca/desmarca todas as checkBox com o nome passado, quando a source(checkBox mãe) for clicada
function toggleCheckBoxes(source, checkBoxName)
{
  var checkboxes = document.getElementsByName(checkBoxName);
  for(var i=0; i < checkboxes.length; i++) {
    checkboxes[i].checked = source.checked;
  }
    
}

// Se alguma checkbox estiver marcada, mostrar os botoes de opcao da mensagem
function checkBoxState(checkBoxName, targetId)
{
    document.getElementById('msgOptions').style.display = 'none';
    var checkboxes = document.getElementsByName(checkBoxName);
    for(var i=0; i < checkboxes.length; i++)
    {
        if(checkboxes[i].checked)
        {
            document.getElementById(targetId).style.display = '';
            break;
        }
    }
}
/* fim do inbox */
