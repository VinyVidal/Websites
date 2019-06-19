<?php include_once("../lib/lib.php");
    session_start();
    
    if(isset($_SESSION['user']))
    {
        $idUser = $_SESSION['user']['idUser'];
    
        $sql = $con->prepare("SELECT * FROM tbl_usuarios WHERE id_User = ?");
        $sql->bind_param("s", $idUser);
        $sql->execute();
        $get = $sql->get_result();
        $dados = $get->fetch_assoc();
    }
    else
    {
        header('location: ../');   
    }
    //recuperar informacao de id
    if(isset($_GET['id']))
    {
        if(filter_var($_GET['id'], FILTER_VALIDATE_INT))
        {
            $idPost = $_GET['id'];
        }
    }
    else
    {
        $_SESSION['modalAlerta']['titulo'] = 'Erro';
        $_SESSION['modalAlerta']['mensagem'] = 'Falha ao recuperar informações de ID!';
        $_SESSION['modalAlerta']['tipo'] = 'error';
        header('location: ../../forum/');
    }
    
    //query que recupera o conteudo do topico que ira ser editado
    $sql = $con->prepare("SELECT conteudo_Post, idTopico_Post FROM tbl_posts_forum WHERE id_Post = ?");
    $sql->bind_param("s", $idPost);
    if($sql->execute())
    {
        $getCont = $sql->get_result();
        $conteudo = $getCont->fetch_assoc();
    }
?>
<!DOCTYPE html>
<html>
<head>
	<!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/index.css">
	<link rel="icon" href="../img/icon.png">
	<?php
	if(isset($_SESSION['modalAlerta']))
	{
		showModal($_SESSION['modalAlerta']['titulo'], $_SESSION['modalAlerta']['mensagem'], $_SESSION['modalAlerta']['tipo']);
	}

    unset($_SESSION['modalAlerta']); // parar de exibir o modal
    ?>
    <script type="text/javascript" src="js/function.js"></script>
    <title>Fórum - Retro Game Center</title>
</head>
<body class="bg-light">
    <!-- navbar Menu-->
    <nav class="navbar navbar-expand-md navbar-dark bg-black" >
        <a class="navbar-brand" href="index.php"><img src="../img/LogoForum.png"></a>
        <div class="d-flex flex-row order-3 order-lg-3">
            <ul class="navbar-nav flex-row">
                <?php if(!isset($_SESSION['user'])){ ?>
                    <li class="nav-item mr-3"><a class="btn btn-outline-warning" role="button" href="../user/login.php">Entrar</a></li>
                <?php }else{ ?>
                    <li class="nav-item dropdown mr-3"><a class="nav-link dropdown-toggle" id="navbarDropdownUser" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="nav-user" src="../../<?php echo $dados['img_User'];?>"><?php echo $dados['nome_User'];?></a>
                        <div class="dropdown-menu border-warning" aria-labelledby="navbarDropdownUser">
                        <a class="dropdown-item" href="../user/profile.php"><img src="https://img.icons8.com/material/16/000000/gender-neutral-user.png" class="pr-1"> Profile</a>
                        <a class="dropdown-item" href="../user/mensagensPrivadas.php"><img src="https://img.icons8.com/material/16/000000/sms.png"> Mensagens <?php echo showNewMessages($con, $dados['id_User']); ?></a>
                        <a class="dropdown-item" href="../php_action/logout.php"><img src="https://img.icons8.com/metro/16/000000/exit.png" class="pr-1">Sair</a>
                        </div>
                    </li>
                    <?php } ?>
                <li class="nav-item">
                    <button class="navbar-toggler border-warning" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
            </li>
            </ul>
        </div>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto" >
            <li class="nav-item">
                <a class="nav-link" href="../../"><img src="https://img.icons8.com/dusk/20/000000/visual-game-boy.png"> Home </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link"><img src="https://img.icons8.com/ultraviolet/17/000000/comment-discussion.png"> Fórum <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Mural
                </a>
                <div class="dropdown-menu border-warning" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="../mural/enviarJogo.php"><img src="https://img.icons8.com/material-two-tone/24/000000/upload-to-cloud.png"> Envie seu Jogo</a>
                <a class="dropdown-item" href="../mural/jogos.php"><img src="https://img.icons8.com/material-rounded/24/000000/controller.png"> Jogos</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><img src="https://img.icons8.com/office/17/000000/outline.png"> Tutoriais</a>
            </li>
            <li class="nav-item mr-3">
                <a class="nav-link" href="../about.php"><img src="https://img.icons8.com/cotton/20/000000/info.png"> Sobre</a>
            </li>
            </ul>
        </div>
    </nav>
    <!-- navbar Menu-->

    <div class="container-fluid my-3">
        <a href="../../forum/topico.php?id=<?php echo $conteudo['idTopico_Post'];?>" class="btn border border-warning text-warning font-weight-bold bg-black my-2"> < </a>
        <div class="card border border-warning">
            <div class="card-header border border-warning text-warning bg-black">
                Editar Conteúdo do Tópico
            </div>
            <div class="card-body">
                <?php if(isset($_SESSION['conteudo']))
                { 
                    $str = $_SESSION['conteudo'];
                    unset($_SESSION['conteudo']);
                ?>
                    <div class="card mb-3">
                        <h5 class="card-header text-warning font-weight-bolder bg-dark">Pré-Visualização</h5>
                        <div class="card-body border border-dark" id="preViewTopic">
                            <?php echo showAbsoluteFormat(bbCode($str)); ?>
                        </div>
                    </div>
                <?php }?>
                
                <hr class="border-warning mb-2">
                <div class="button-group mb-2 px-1">
                    <!-- botoes de formatacao de texto -->
                    <!-- tipo de texto -->
                    <button type="button" title="Negrito: [b]texto[/b]" id="negrito" onclick="surroundBB('newCont', '[b]', '[/b]')"><img src="https://img.icons8.com/metro/17/000000/bold.png"></button>
                    <button type="button" title="Itálico: [i]texto[/i]" id="italico" onclick="surroundBB('newCont', '[i]', '[/i]')"><img src="https://img.icons8.com/metro/17/000000/italic.png"></button>
                    <button type="button" title="Sublinhado: [u]texto[/u]" id="sublinhado" onclick="surroundBB('newCont', '[u]', '[/u]')"><img src="https://img.icons8.com/metro/21/000000/underline.png"></button>
                    <button type="button" title="Tachado: [s]texto[/s]" id="tachado" onclick="surroundBB('newCont', '[s]', '[/s]')"><img src="https://img.icons8.com/metro/21/000000/strikethrough.png"></button> | 
                    <!-- alinhamento -->
                    <button type="button" title="Centralizar: [center]texto[/center]" id="aliCentro" onclick="surroundBB('newCont', '[center]', '[/center]')"><img src="https://img.icons8.com/metro/21/000000/align-center.png"></button>
                    <button type="button" title="Alinhar à Direita: [right]texto[/right]" id="aliDireita" onclick="surroundBB('newCont', '[right]', '[/right]')"><img src="https://img.icons8.com/ios/21/000000/align-right-filled.png"></button>
                    <button type="button" title="Justificar: [justify]texto[/justify]" id="aliJustificar" onclick="surroundBB('newCont', '[justify]', '[/justify]')"><img src="https://img.icons8.com/metro/21/000000/align-justify.png"></button> | 
                    <!-- links -->
                    <button type="button" title="Url: [url=www.link.com]texto[/url]" id="url" onclick="surroundBB('newCont', '[url=]', '[/url]')"><img src="https://img.icons8.com/metro/21/000000/link.png"></button> | 
                    <!-- listas -->
                    <button type="button" title="Lista Ordenada: [list=valor_inicial][*]texto[/list]" id="ulList" onclick="surroundBB('newCont', '[list=1]', '[/list]')"><img src="https://img.icons8.com/metro/26/000000/numbered-list.png"></button>
                    <button type="button" title="Lista Desordenada: [list][*]texto[/list]" id="olList" onclick="surroundBB('newCont', '[list]', '[/list]')"><img src="https://img.icons8.com/ios/21/000000/bulleted-list-filled.png"></button> | 
                    <!-- imagem -->
                    <button type="button" title="Imagem: [img]link_da_img.com[/img]" id="img" onclick="surroundBB('newCont', '[img]', '[/img]')"><img src="https://img.icons8.com/ios/21/000000/image-file.png"></button> | 
                    <!-- tam. Fonte -->
                    <button type="button" title="Tamanho da Fonte: [size=tamanho]texto[/size]" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="https://img.icons8.com/ios/21/000000/sentence-case.png">
                    </button> | 
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" onclick="surroundBB('newCont', '[size=18]', '[/size]')" style="cursor:pointer;">Pequeno</a>
                        <a class="dropdown-item" onclick="surroundBB('newCont', '[size=36]', '[/size]')" style="cursor:pointer;">Médio</a>
                        <a class="dropdown-item" onclick="surroundBB('newCont', '[size=72]', '[/size]')" style="cursor:pointer;">Grande</a>
                    </div>
                    <!-- cor da fonte -->
                    <div class="btn-group dropleft">
                        <button type="button" title="Alterar cor da fonte: [color=cod.cor]texto[/color]" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img src="https://img.icons8.com/metro/21/000000/paint-palette.png"> </button>
                        <div class="dropdown-menu ml-5 border-0" style="background: transparent;">
                            <div id="buttons">
                                <button type="button" title="Preto" class="preto" onclick="surroundBB('newCont', '[color=#000]', '[/color]')"></button>
                                <button type="button" title="Cinza" class="cinza" onclick="surroundBB('newCont', '[color=#808080]', '[/color]')"></button>
                                <button type="button" title="Violeta" class="violeta" onclick="surroundBB('newCont', '[color=#EE82EE]', '[/color]')"></button>
                                <button type="button" title="Azul" class="azul" onclick="surroundBB('newCont', '[color=#00F]', '[/color]')"></button>
                                <button type="button" title="Turquesa" class="turquesa" onclick="surroundBB('newCont', '[color=#008080]', '[/color]')"></button>
                                <button type="button" title="Verde" class="verde" onclick="surroundBB('newCont', '[color=#008000]', '[/color]')"></button>
                                <button type="button" title="Verde Claro" class="verdeClaro" onclick="newCont('conteudoPost', '[color=#00FF00]', '[/color]')"></button>
                                <button type="button" title="Marrom" class="marrom" onclick="surroundBB('newCont', '[color=#654321]', '[/color]')"></button>
                                <button type="button" title="Rosa" class="pink" onclick="surroundBB('newCont', '[color=#FF00FF]', '[/color]')"></button>
                                <button type="button" title="Amarelo" class="amarelo" onclick="surroundBB('newCont', '[color=#FFFF00]', '[/color]')"></button>
                                <button type="button" title="Laranja" class="laranja" onclick="surroundBB('newCont', '[color=#FFA500]', '[/color]')"></button>
                                <button type="button" title="Vermelho" class="vermelho" onclick="surroundBB('newCont', '[color=#F00]', '[/color]')"></button>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group dropdown">
                        <button type="button" title="code: [code]nome_elemento[/code]"  id="dropCode" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="https://img.icons8.com/metro/21/000000/source-code.png"></button>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropCode">
                            <button type="button" class="dropdown-item" onclick="surroundBB('newCont', '[code]', '[/code]')">Tag HTML</button>
                            <button type="button" class="dropdown-item" onclick="surroundBB('newCont', '[block]', '[/block]')">Bloco de Código</button>
                        </div>
                    </div>
                    <button type="button" onclick="surroundBB('newCont', '[tab]', '[/tab]')"><img src="https://img.icons8.com/material-outlined/21/000000/add-white-space.png"></button>
                </div>
            </div>
            <form action="../php_action/forum/editarTopico.php?id=<?php echo $idPost; ?>" method="POST">
                <textarea name="newCont" id="newCont" class="form-control" rows="8"><?php echo $conteudo['conteudo_Post'];?></textarea>
                <button class="btn border border-warning bg-white float-right mt-2 mx-2" name="btnEditar"><img class="mr-2" src="https://img.icons8.com/material/24/000000/edit.png">Editar</button>
                <button name="btnPreview" class="btn border border-warning bg-white float-right mt-2 mx-2"><img src="https://img.icons8.com/small/24/000000/preview-pane.png"> Preview</button>
            </form>
        </div>
    </div>

    <!-- navbar Rodape-->
    <footer class="page-footer font-small pt-4 text-light bg-black mb-0">

        <!-- Footer Links -->
        <div class="container-fluid text-center text-md-left">

            <!-- Grid row -->
            <div class="row">

                <!-- Grid column -->
                <div class="col-md-3 ml-md-5 mr-auto mt-md-0 mt-3">

                    <!-- Content -->
                    <h5 class="text-uppercase">Retro Game Center</h5>
                    <p>A sua diversão começa aqui.</p>

                </div>
                <!-- Grid column -->

                <hr class="clearfix w-100 d-md-none pb-3">

                <!-- Grid column -->
                <div class="col-md-auto ml-auto mr-md-5 mb-md-0 mb-3">

                    <!-- Links -->
                    <h5 class="text-uppercase">Contato</h5>

                    <ul class="list-unstyled">
                        <li>
                            <a href="#!">RetroG.Center@gmail.com</a>
                        </li>

                        <li>
                            <a href="#!">45551211</a>
                        </li>

                        <li>
                            <a href="#!">Avenida da Saudade, 795</a>
                        </li>
                    </ul>

                </div>
                <!-- Grid column -->

            </div>
            <!-- Grid row -->

        </div>
        <!-- Footer Links -->

        <!-- Copyright -->
        <div class="footer-copyright text-center py-3">© 2018 Copyright BMV</div>
        <!-- Copyright -->

    </footer>
    <!-- navbar Rodape-->

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>