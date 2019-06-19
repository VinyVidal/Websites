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

    //PEGAR A ID DO TOPICO
    if(isset($_GET['id'])){
        if(filter_var($_GET['id'], FILTER_VALIDATE_INT)){
            $idTopico = $_GET['id'];
        }
        else
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Tópico não encontrado!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: topicos.php');
            die('Aguarde um momento...');
        }
    
        
    }
    else
    {
        $_SESSION['modalAlerta']['titulo'] = 'Erro';
        $_SESSION['modalAlerta']['mensagem'] = 'Erro ao recuperar informações de id!';
        $_SESSION['modalAlerta']['tipo'] = 'error';
        header('location: topicos.php');
        die('Aguarde um momento...');
    }
    
      //query que vai mostrar o nome da categoria
    $sql = $con->prepare("SELECT nome_Categoria, titulo_Topico, trancado_Topico FROM vw_dadosTopicos WHERE id_Topico = ?");
    $sql->bind_param("s", $idTopico);
    $sql->execute();
    
    if($sql == true){
        $get = $sql->get_result();
        $dado = $get->fetch_array();
    }

    //dados da tbl_postagens
    $sql = $con->prepare("SELECT * FROM vw_dadosPostagens WHERE idTopico_Post = ?");
    $sql->bind_param("s", $idTopico);
    try{
        $sql->execute();
        $getPostagem = $sql->get_result();
    }catch(Exception $e){
        $_SESSION['modalAlerta']['titulo'] = 'Erro';
        $_SESSION['modalAlerta']['mensagem'] = 'Não foi possível recuperar os dados. Tente novamente mais tarde!';
        $_SESSION['modalAlerta']['tipo'] = 'error';
        header('location: topicos.php');
    }

?>
<!doctype html>
<html lang="en">
  <head>
      <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({
          google_ad_client: "ca-pub-7463114109083817",
          enable_page_level_ads: true
      });
     </script>
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
        <a class="navbar-brand" href="/"><img src="../img/LogoForum.png"></a>
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
 
    <?php 
    /**
     * condição verifica se o user esta logado, assim verificando se aparecera a nav abaixo ou nao
     */
        if(!isset($_SESSION['user'])){
    ?>
    <!-- card que aparece caso user nao estiver logado e entrar no forum-->
        <div class="card mt-3 mx-5 border border-warning alert alert-warning text-body font-weight-normal">
            <div class="card-body">
                <div class="ml-3 mr-1">
                    Essa é sua primeira vez aqui? Você precisa <a href="../user/login.php">logar</a> para poder postar. Ainda não possui um cadastro? Você pode criar uma conta clicando no link <a href="../user/cadastro.php">registrar</a>. Para ler as mensagens, selecione o título que você quer visitar na seção abaixo.
                </div>
            </div>
        </div>
    <!-- fim card-->

    <?php /* fim da verificacao*/ } else{} ?>
    
    <div class="responsive-panel">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <ins class="adsbygoogle"
             style="display:block; text-align:center;"
             data-ad-layout="in-article"
             data-ad-format="fluid"
             data-ad-client="ca-pub-7463114109083817"
             data-ad-slot="9163544234"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    
    <!-- hierarquia de navegação -->
    <nav aria-label="breadcrumb" class="mx-3 my-3">
        <ol class="breadcrumb mx-3 border border-warning bg-black">
            <li class="breadcrumb-item"><a href="/" class="text-warning">Home</a></li>
            <li class="breadcrumb-item"><a href="topicos.php?id=<?php echo $idTopico;?>" class="text-warning"><?php echo $dado['nome_Categoria'];?></a></li>
            <li class="breadcrumb-item active text-white" aria-current="page"><?php echo $dado['1']; ?></li>
        </ol>
    </nav>

    <!-- Mensagem que aparece caso o tópico da postagem estiver trancado
        0 = destrancado
        1 = trancado
     -->
    <?php
        if($dado['trancado_Topico'] == 1){
    ?>
        <!-- campo de aviso que informa que o topico esta fechado -->
        <div class="mx-3 my-3 py-1 border border-warning alert alert-warning" align="center">
            <h6>Não será possível enviar respostas pois o tópico está fechado!</h6>
        </div>
    <?php }else{} ?>

    <!-- nav para de topicos e ultimas atividades -->
    <ul class="nav nav-tabs mx-3 mb-2" id="nav-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active text-warning" href="#topicos" role="tab" data-toggle="tab">Postagens</a>
        </li>
    </ul>
    <!-- fim nav, inicio do conteudos das navs(respctivamente) -->

    <!-- while para mostrar os dados da tbl_posts -->
    <?php
        while($linha = $getPostagem->fetch_assoc()){
            //dados do user
            $sql = $con->prepare("SELECT nome_User, img_User FROM tbl_usuarios WHERE id_User = ?");
            $sql->bind_param("s", $linha['idUser_Post']);
            try{
                $sql->execute();
                $dadosPostador = $sql->get_result()->fetch_assoc();
            }catch(Exception $e){
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Não foi possível recuperar os dados. Tente novamente mais tarde!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: topicos.php');
            }
            $dataPost = new DateTime($linha['data_Post']);
    ?>

    
    <!-- ************************** POSTAGEM PRINCIPAL ************************** -->
    <?php if($linha['resposta_Post'] == 0){ ?>
        <div class="tab-content mt-2" id="nav-tabContent">
            <div class="container-fluid ml-0">
                <div class="row h-100 mb-3">
                    <div class="col-md-3">
                        <div class="card h-100 align-items-center border border-warning">
                            <img src="../<?php echo $dadosPostador['img_User'];?>" class="card-img-top my-2" style="max-width: 10rem;">
                            <div class="card-body">
                              <h5 class="card-title text-center"><?php echo $dadosPostador['nome_User'];?></h5>
                            </div>
                            <div class="card-footer">
      <small class="text-muted">Last updated 3 mins ago</small>
    </div>
                        </div>
                    </div>
            
                    <div class="col">
                        <div class="card h-100 border border-warning">
                            <div class="card-header">
                                <h4 class="text-weight-bolder"><?php echo $linha['titulo_Topico'];?> </h4>
                            </div>
                            <div class="card-body border border-warning">
                              <p class="card-text"><?php echo bbCode($linha['conteudo_Post']);?></p>
                            </div>
                            <div class="card-footer">
                              <small class="text-muted"><h6 class="text-right my-2 px-2"><?php echo $dataPost->format('d/m/y H:i');?> <?php if($linha['resposta_Post'] == 0){ ?>  |  <img name="likePost" src="https://img.icons8.com/small/22/000000/facebook-like.png" style="cursor:pointer;"> <?php echo " 70";?> <?php }else{}?></h6></small>
                            </div>
                        </div>
                    </div>
                    <?php if(isset($_SESSION['user']) && $linha['trancado_Topico'] == 0){ ?>
                        <form method="POST">
                            <button name="responderPost" class="bg-light border border-none"><img title="Responder" name="responderPost" class="rounded float-right" src="https://img.icons8.com/android/20/000000/reply-arrow.png" style="cursor: pointer;"></button>
                        </form>
                </div>
                
                <!-- se o botao de responder for clicado aparecera a text area para o user inserir a resposta (textarea) -->
                <?php if(isset($_POST['responderPost'])){ ?>
                <form method="POST" action="../php_action/forum/acoes.php">
                    <input type="hidden" name="idTopico" id="idTopico" value="<?php echo $idTopico;?>">
                    <div class="row">
                        <div class="col">
                            <div class="modal-header border-warning mb-2">
                                <h5 class="modal-title text-warning text-uppercase" id="titulo">Escreva uma resposta para o post</h5>
                            </div>
                            <div class="button-group mb-2 px-1">
                                <!-- botoes de formatacao de texto -->
                                <!-- tipo de texto -->
                                <button type="button" title="Negrito" id="negrito" onclick="surroundBB('conteudoResposta', '[b]', '[/b]')"><img src="https://img.icons8.com/metro/10/000000/bold.png"></button>
                                <button type="button" title="Itálico" id="italico" onclick="surroundBB('conteudoResposta', '[i]', '[/i]')"><img src="https://img.icons8.com/metro/10/000000/italic.png"></button>
                                <button type="button" title="Sublinhado" id="sublinhado" onclick="surroundBB('conteudoResposta', '[u]', '[/u]')"><img src="https://img.icons8.com/metro/15/000000/underline.png"></button>
                                <button type="button" title="Tachado" id="tachado" onclick="surroundBB('conteudoResposta', '[s]', '[/s]')"><img src="https://img.icons8.com/metro/15/000000/strikethrough.png"></button> | 
                                <!-- alinhamento -->
                                <button type="button" title="Alinhar à Esquerda" id="aliEsquerda" onclick="conteudoResposta('conteudoResposta', '[left]', '[/left]')"><img src="https://img.icons8.com/metro/15/000000/align-left.png"></button>
                                <button type="button" title="Centralizar" id="aliCentro" onclick="surroundBB('conteudoResposta', '[center]', '[/center]')"><img src="https://img.icons8.com/metro/15/000000/align-center.png"></button>
                                <button type="button" title="Alinhar à Direita" id="aliDireita" onclick="surroundBB('conteudoResposta', '[right]', '[/right]')"><img src="https://img.icons8.com/metro/15/000000/align-right.png"></button>
                                <button type="button" title="Justificar" id="aliJustificar" onclick="surroundBB('conteudoResposta', '[justify]', '[/justify]')"><img src="https://img.icons8.com/metro/15/000000/align-justify.png"></button> | 
                                <!-- links -->
                                <button type="button" title="Url" id="url" onclick="surroundBB('conteudoResposta', '[url=]', '[/url]')"><img src="https://img.icons8.com/metro/15/000000/link.png"></button> | 
                                <!-- listas -->
                                <button type="button" title="Lista Ordenada" id="ulList"><img src="https://img.icons8.com/metro/15/000000/numbered-list.png"></button>
                                <button type="button" title="Lista Desordenada" id="olList"><img src="https://img.icons8.com/ios/15/000000/bulleted-list-filled.png"></button> | 
                                <!-- imagem -->
                                <button type="button" title="Imagem" id="img" onclick="surroundBB('conteudoResposta', '[img]', '[/img]')"><img src="https://img.icons8.com/ios/20/000000/image-file.png"></button>
                                <!-- tam. Fonte -->
                                <button type="button" title="Tamanho da fonte" id="tamFonte" onclick="surroundBB('conteudoResposta', '[size=]', '[/size]')">fonte</button>
                            </div>
                            <div class="form-row">
                                <textarea class="form-control" name="conteudoResposta" id="conteudoResposta" widht="200px"><?php echo $str;?></textarea>
                            </div>
                            <div class="modal-footer border-warning mt-2">
                                <button type="submit" class="btn btn-success" name="btnResponder">Responder</button>
                            </div>
                        </form>
                    <!--fim da verificacao do botao de responder o post principal -->
                    <?php }else{}?>
            <!-- fim da verificacao de login -->
            <?php }else{}?>
                        </div>
                    </div>
            </div>
            
        </div>
    
        
    
    <?php }else{?>
    <!-- ************************** POSTAGEM RESPOSTA ************************** -->
        <div class="tab-content mt-2" id="nav-tabContent">
            <div class="container-fluid ml-0">
                <div class="row h-100 mb-3">
                    <div class="col-md-3">
                        <div class="card h-100 align-items-center border border-dark">
                            <img src="../<?php echo $dadosPostador['img_User'];?>" class="card-img-top my-2" style="max-width: 10rem;">
                            <div class="card-body">
                              <h5 class="card-title text-center"><?php echo $dadosPostador['nome_User'];?></h5>
                            
                            </div>
                        </div>
                    </div>
                  
                    <div class="col">
                        <div class="card h-100 border border-dark">
                            <div class="card-header">
                                <h5 class="text-weight-bolder">Resposta à - <?php echo $linha['titulo_Topico'];?></h5>
                            </div>
                            <div class="card-body border border-dark">
                              <p class="card-text"><?php echo bbCode($linha['conteudo_Post']);?></p>
                            </div>
                            <div class="card-footer">
                              <small class="text-muted"><h6 class="text-right"><?php echo $dataPost->format('d/m/y H:i');?>  |  <img name="likePost" src="https://img.icons8.com/small/22/000000/facebook-like.png" style="cursor:pointer;"></h6> </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php }?>    
    
    <?php } ?>
    
    <!-- navbar Rodape-->
    <footer class="page-footer font-small pt-4 text-light bg-black">

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