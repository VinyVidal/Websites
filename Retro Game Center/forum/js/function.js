function surroundBB(fieldId, bbStart, bbEnd)
{
	var campo = document.getElementById(fieldId);
	campo.focus();
	var start = campo.selectionStart;
	var end	= campo.selectionEnd;
	var selecao = campo.value.substring(start, end);
	var stringEnvolvida = bbStart + selecao + bbEnd;
	campo.value =  campo.value.substring(0,start) + stringEnvolvida + campo.value.substring(end,campo.value.length);
}

// Passa um valor para o valor do input (de um modal, geralmente)
// PARAMETROS: inputID = id do input que vai receber o valor
//             val = valor que será passado para o input
function setModalInput(inputId, val)
{
    var input = document.getElementById(inputId);
    input.value = val;
}