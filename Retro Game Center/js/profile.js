window.addEventListener('load', function()
{
    $(document).ready(function() {
        
        
        /* Mostrar imagem no avatar */
        var readURL = function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('.avatar').attr('src', e.target.result);
                }
        
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        $(".file-upload").on('change', function(){
            readURL(this);
        });

        // Paginação nas tabelas
        $('#tblUsuarios').DataTable({
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "Nenhum resultado encontrado.",
                "info": "Exibindo página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro.",
                "infoFiltered": "",
                "paginate": {
                    "first":      "Primeira",
                    "last":       "Última",
                    "next":       "Próxima",
                    "previous":   "Anterior"
                },
                "search":         "Pesquisar:",
                "thousands":      ".",
                "decimal":        ","
            }
        } );
        $('#tblPendentes').DataTable({
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "Nenhum resultado encontrado.",
                "info": "Exibindo página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro.",
                "infoFiltered": "",
                "paginate": {
                    "first":      "Primeira",
                    "last":       "Última",
                    "next":       "Próxima",
                    "previous":   "Anterior"
                },
                "search":         "Pesquisar:",
                "thousands":      ".",
                "decimal":        ","
            }
        } );
        $('#tblAprovados').DataTable({
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "Nenhum resultado encontrado.",
                "info": "Exibindo página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro.",
                "infoFiltered": "",
                "paginate": {
                    "first":      "Primeira",
                    "last":       "Última",
                    "next":       "Próxima",
                    "previous":   "Anterior"
                },
                "search":         "Pesquisar:",
                "thousands":      ".",
                "decimal":        ","
            }
        } );
        $('#tblReprovados').DataTable({
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "Nenhum resultado encontrado.",
                "info": "Exibindo página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro.",
                "infoFiltered": "",
                "paginate": {
                    "first":      "Primeira",
                    "last":       "Última",
                    "next":       "Próxima",
                    "previous":   "Anterior"
                },
                "search":         "Pesquisar:",
                "thousands":      ".",
                "decimal":        ","
            }
        } );
        $('.dataTables_length').addClass('bs-select');

        for(var i = 0; i < 4; i++)
        {
            checkLinkInput('link'+(i+1), i, 'eraseLink');
        }

        initializeLinkPopovers();
    });
});

/* Valida os links do Social Media e impede o form de ser enviado caso haja erros*/
function validateSocialLinks()
{
    var link1 = document.getElementById('link1').value;
    var link2 = document.getElementById('link2').value;
    var link3 = document.getElementById('link3').value;
    var link4 = document.getElementById('link4').value;

    if(link1.match(/http(?:s)?:\/\/(?:www.)?instagram\.com\/([a-zA-Z0-9_]+)/) === null && link1.length > 0)
    {
        $('#link1').popover('show');
        return false;
    }
    else if(link2.match(/http(?:s)?:\/\/(?:www.)?facebook\.com\/([a-zA-Z0-9_]+)/) === null && link2.length > 0)
    {
        $('#link2').popover('show');
        return false;
    }
    else if(link3.match(/http(?:s)?:\/\/(?:www.)?twitter\.com\/([a-zA-Z0-9_]+)/) === null && link3.length > 0)
    {
        $('#link3').popover('show');
        return false;
    }
    else if(link4.match(/http(?:s)?:\/\/(?:www.)?github\.com\/([a-zA-Z0-9_]+)/) === null && link4.length > 0)
    {
        $('#link4').popover('show');
        return false;
    }
    else
    {
        return true;
    }
}

// Ao clicar no botao de apagar o link
function eraseLinkButtonClick(linkInputId)
{
    $('#'+linkInputId).val('');
    
}

// Verifica se o campo do link está preenchido ou não, para exibir o botao de apagar, ou não
function checkLinkInput(linkInputId, linkPos, eraseLinkButtonName)
//
{
    if($('#'+linkInputId).val().length > 0)
    {
        document.getElementsByName(eraseLinkButtonName)[linkPos].className = '';
    }
    else
    {
        document.getElementsByName(eraseLinkButtonName)[linkPos].className = 'hidden';
    }
}

// inicializa os popovers dos social media links
function initializeLinkPopovers()
{
    var style = '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>';
    var medias = ['instagram', 'facebook', 'twitter', 'github'];

    for(var i = 0; i < 4; i++)
    {
        $('#link'+(i+1)).popover({
        container: 'body',
        content: 'Insira um link do ' + medias[i] +' válido!',
        trigger: 'manual',
        template: style
    });
    }
    

    document.addEventListener('click', function(){
        for(var i = 0; i < 4; i++)
        {
            $('#link'+(i+1)).popover('hide');
        }
    });
}