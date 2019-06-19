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
            <div class="card-header bg-black border border-warning">
                <h5 class="text-weight-bolder text-warning">Você está prestes a excluir este tópico:</h5>
            </div>
            <div class="card-body">
                <p class="card-text"><?php echo showAbsoluteFormat(bbCode($conteudo['conteudo_Post']));?></p>
            </div>
            <div class="card-footer border border-warning bg-black">
                <div class="card-text text-warning">Não será possível desfazer a ação caso você exclua o tópico.
                    <form action="../php_action/forum/excluirTopico.php?id=<?php echo $idPost;?>" method="POST">
                        <input type="hidden" name="idPost" value="<?php echo $idPost;?>">
                        <button name="btnExcluir" class="btn border border-warning bg-black float-right text-warning">Excluir</button>
                    </form>
                </div>
                    
            </div>
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