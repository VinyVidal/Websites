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
            header('location: /');
            die('Aguarde um momento...');
        }
    
        // Setando visualização do Topico!!
        if(!isset($_SESSION["visualizouID$idTopico"]))
        {
            $_SESSION["visualizouID$idTopico"] = true;
            $sql = $con->prepare("UPDATE tbl_topicos_forum SET views_Topico = views_Topico + 1 WHERE id_Topico = ?");
            $sql->bind_param('i', $idTopico);
            $sql->execute();
        }
    }
    else
    {
        $_SESSION['modalAlerta']['titulo'] = 'Erro';
        $_SESSION['modalAlerta']['mensagem'] = 'Erro ao recuperar informações de id!';
        $_SESSION['modalAlerta']['tipo'] = 'error';
        header('location: categoria.php');
        die('Aguarde um momento...');
    }
    
      //query que vai mostrar o nome da categoria
    $sql = $con->prepare("SELECT nome_Categoria,id_Categoria, titulo_Topico, trancado_Topico FROM tbl_topicos_forum, tbl_categorias_forum WHERE id_Topico = ? AND id_Categoria = idCategoria_Topico");
    $sql->bind_param("s", $idTopico);
    $sql->execute();
    
    if($sql == true){
        $get = $sql->get_result();
        $dado = $get->fetch_array();
    }

    //dados da tbl_postagens
    $sql = $con->prepare("SELECT COUNT(*) FROM tbl_posts_forum WHERE idTopico_Post = ?");
    $sql->bind_param("s", $idTopico);
    try{
        $sql->execute();
        $getCountTituloTopico = $sql->get_result();
        $numPosts = $getCountTituloTopico->fetch_array()['0'];
    }catch(Exception $e){
        $_SESSION['modalAlerta']['titulo'] = 'Erro';
        $_SESSION['modalAlerta']['mensagem'] = 'Não foi possível recuperar os dados. Tente novamente mais tarde!';
        $_SESSION['modalAlerta']['tipo'] = 'error';
        header('location: categoria.php');
    }

?>
<!doctype html>
<html lang="en">
  <head>
      <!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({
          google_ad_client: "ca-pub-7463114109083817",
          enable_page_level_ads: true
      });
     </script> -->
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  <link rel="stylesheet" href="../css/style.css">
	  <link rel="stylesheet" href="../css/paginacao.css">
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
    <title><?php echo $dado['titulo_Topico'];?> - Retro Game Center Fórum</title>
  </head>
  <body class="bg-light">
      
    <!-- navbar Menu-->
    <nav class="navbar navbar-expand-md navbar-dark bg-black" >
        <a class="navbar-brand" href="/Retro Game Center/forum/"><img src="../img/LogoForum.png"></a>
        <div class="d-flex flex-row order-3 order-lg-3">
            <ul class="navbar-nav flex-row">
                <?php if(!isset($_SESSION['user'])){ ?>
                    <li class="nav-item mr-3"><a class="btn btn-outline-warning" role="button" href="../user/login.php">Entrar</a></li>
                <?php }else{ ?>
                    <li class="nav-item dropdown mr-3"><a class="nav-link dropdown-toggle" id="navbarDropdownUser" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="nav-user" src="../<?php echo $dados['img_User'];?>"><?php echo $dados['nome_User'];?></a>
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
                <a class="nav-link" href="../"><img src="https://img.icons8.com/dusk/20/000000/visual-game-boy.png"> Home </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../forum/"><img src="https://img.icons8.com/ultraviolet/17/000000/comment-discussion.png"> Fórum</a>
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
                <a class="nav-link" href="categoria.php?id=2"><img src="https://img.icons8.com/office/17/000000/outline.png"> Tutoriais</a>
            </li>
            <li class="nav-item mr-3">
                <a class="nav-link" href="../about.php"><img src="https://img.icons8.com/cotton/20/000000/info.png"> Sobre</a>
            </li>
            </ul>
        </div>
    </nav>
    <!-- navbar Menu-->
    
    <!-- Modal trancar topico -->
    <div class="modal fade" id="trancarTopico" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
      <div class="modal-dialog border border-warning" role="document">
        <div class="modal-content text-warning bg-light">
          <div class="modal-header border-warning">
            <h5 class="modal-title" id="titulo">Trancar Tópico</h5>
            <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <div class="card">
                <div class="card-body">
                    <h4>Atenção, você está prestes a trancar o tópico!</h4>
                    <p>Após trancar o tópico as seguintes mudanças serão feitas:</p>
                    <ul>
                        <li>Usuários ficarão impossibilitados de comentar no tópico</li>
                    </ul>
                </div>
            </div>
            <form action="../php_action/forum/trancarTopico.php" method="POST">
                <input name="idTopico" type="hidden" value="<?php echo $idTopico;?>">
                <div class="button-group">
                    <button name="btnTrancar" class="btn border border-warning text-warning font-weight-bold mt-2 float-right">Trancar</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fim modal -->
 
    <!-- Modal destrancar topico -->
    <div class="modal fade" id="destrancarTopico" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
      <div class="modal-dialog border border-warning" role="document">
        <div class="modal-content text-warning bg-light">
          <div class="modal-header border-warning">
            <h5 class="modal-title" id="titulo">Destrancar Tópico</h5>
            <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <div class="card">
                <div class="card-body">
                    <h4>Atenção, o tópico se encontra trancado</h4>
                    <p>Após destrancar o tópico as seguintes mudanças serão feitas:</p>
                    <ul>
                        <li>Usuários poderão comentar no tópico</li>
                    </ul>
                </div>
            </div>
            <form action="../php_action/forum/destrancarTopico.php" method="POST">
                <input name="idTopico" type="hidden" value="<?php echo $idTopico;?>">
                <div class="button-group">
                    <button name="btnDestrancar" class="btn border border-warning text-warning font-weight-bold mt-2 float-right">Destrancar</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fim modal -->
 
    <!-- Modal excluir topico -->
    <div class="modal fade" id="excluirTopico" tabindex="-1" role="dialog" aria-labelledby="excluir" aria-hidden="true">
      <div class="modal-dialog border border-warning" role="document">
        <div class="modal-content text-warning bg-light">
          <div class="modal-header border-warning">
            <h5 class="modal-title" id="titulo">Excluir Tópico</h5>
            <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <div class="card">
                <div class="card-body">
                    <h4>Atenção, você está prestes a realizar uma ação irreversivel.</h4>
                    <p>Após excluir o tópico a seguinte mudança será aplicada:</p>
                    <ul>
                        <li>Exclusão permanente do tópico</li>
                    </ul>
                </div>
            </div>
            <form action="../php_action/forum/excluirTopico.php" method="POST">
                <input name="idTopico" type="text" value="<?php echo $idTopico;?>">
                <div class="button-group">
                    <button name="btnExcluir" class="btn border border-warning text-warning font-weight-bold mt-2 float-right">Excluir</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fim modal -->
 
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
    
    <!-- hierarquia de navegação -->
    <nav aria-label="breadcrumb" class="mx-3 my-3">
        <ol class="breadcrumb mx-3 border border-warning bg-black">
            <li class="breadcrumb-item"><a href="../forum/" class="text-warning">Home</a></li>
            <li class="breadcrumb-item"><a href="categoria.php?id=<?php echo $dado['id_Categoria'];?>" class="text-warning"><?php echo $dado['nome_Categoria'];?></a></li>
            <li class="breadcrumb-item active text-white" aria-current="page"><?php echo $dado['titulo_Topico']; ?></li>
        </ol>
    </nav>
    
    <!-- <div class="responsive-panel mx-auto">
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
    </div> -->

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
        if($numPosts > 0)
        {
            $postsPorPg = 10; // quantidade de topicos por pagina

            if(isset($_GET['pg'])) // pg
            {
               if(filter_var($_GET['pg'], FILTER_VALIDATE_INT))
               {
                   $page = $_GET['pg']; // pag atual
               }
               else
               {
                   $page = 0; // Se nao for int, a pag eh invalida (0) para dar erro mais em baixo
               }
            }
            else
            {
               $page = 1; // pag atual
            }
            
            $exceto = ($page * $postsPorPg) - $postsPorPg; // offset
            if($page == 1)
            {
              $sql = $con->prepare("SELECT *,
                (SELECT titulo_Topico FROM tbl_topicos_forum WHERE id_Topico = tbl_posts_forum.idTopico_Post) AS titulo_Topico,
                (SELECT tipo_Topico FROM tbl_topicos_forum WHERE id_Topico = tbl_posts_forum.idTopico_Post) AS tipo_Topico,
                ( SELECT trancado_Topico FROM tbl_topicos_forum WHERE id_Topico = tbl_posts_forum.idTopico_Post) AS trancado_Topico
                FROM tbl_posts_forum WHERE idTopico_Post = ? LIMIT ?");
              $sql->bind_param("ii", $idTopico, $postsPorPg);
            }
            else
            {
              $sql = $con->prepare("SELECT *,
                (SELECT titulo_Topico FROM tbl_topicos_forum WHERE id_Topico = tbl_posts_forum.idTopico_Post) AS titulo_Topico,
                (SELECT tipo_Topico FROM tbl_topicos_forum WHERE id_Topico = tbl_posts_forum.idTopico_Post) AS tipo_Topico,
                ( SELECT trancado_Topico FROM tbl_topicos_forum WHERE id_Topico = tbl_posts_forum.idTopico_Post) AS trancado_Topico
                FROM tbl_posts_forum WHERE idTopico_Post = ? LIMIT ? OFFSET ?");
              $sql->bind_param("iii", $idTopico, $postsPorPg, $exceto);
            }

            $sql->execute();
            $getPostagem = $sql->get_result(); // Todas as msgs ao usuario da sessao
            
            if($getPostagem->num_rows > 0)
            {
                while($linha = $getPostagem->fetch_assoc()){
                    //dados do user
                    $sql = $con->prepare("SELECT nome_User, img_User, nivel_User FROM tbl_usuarios WHERE id_User = ?");
                    $sql->bind_param("s", $linha['idUser_Post']);
                    try{
                        $sql->execute();
                        $dadosPostador = $sql->get_result()->fetch_assoc();
                    }catch(Exception $e){
                        $_SESSION['modalAlerta']['titulo'] = 'Erro';
                        $_SESSION['modalAlerta']['mensagem'] = 'Não foi possível recuperar os dados. Tente novamente mais tarde!';
                        $_SESSION['modalAlerta']['tipo'] = 'error';
                        header('location: categoria.php');
                    }
                    
                    $dataPost = new DateTime($linha['data_Post']);
    ?>

    
    <!-- ************************** POSTAGEM PRINCIPAL ************************** -->
    <?php if($linha['resposta_Post'] == 0){ ?>
        <div class="tab-content mt-2" id="nav-tabContent">
            <a name="post<?php echo $linha['id_Post'];?>"></a>
            <div class="container-fluid ml-0">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card align-items-center border border-warning">
                            <img src="../<?php echo $dadosPostador['img_User'];?>" class="card-img-top my-2" style="max-width: 10rem; max-height: 10rem;">
                            <div class="card-body">
                              <a class="card-title text-center text-warning" href="../user/profileView.php?userv=<?php echo $linha['idUser_Post'];?>"><?php if($dadosPostador['nivel_User'] == 2){echo "[MOD] ";}else if($dadosPostador['nivel_User'] == 3){echo "[ADMIN] ";}else if($dadosPostador['nivel_User'] == 4){echo "[MASTER] ";} echo $dadosPostador['nome_User'];?></a><br>
                              <?php
                                    $sql = $con->prepare("SELECT COUNT(idUser_Post) FROM tbl_posts_forum WHERE idUser_Post = ?");
                                    $sql->bind_param("s", $linha['idUser_Post']);
                                    $sql->execute();
                                    $get = $sql->get_result();
                                    $posts = $get->fetch_array();
                                    
                                    if($posts['0'] <= 50){
                                ?>
                                        <h6 class="card-title text-center text-muted">Novato</h6>
                                    <?php }else if($posts['0'] <= 100){ ?>
                                        <h6 class="card-title text-center text-muted">Aprendiz</h6>
                                    <?php }else if($posts['0'] <= 160){ ?>
                                        <h6 class="card-title text-center text-muted">Veterano</h6>
                                    <?php }else{ ?>
                                        <h6 class="card-title text-center text-muted">Mestre</h6>
                                    <?php } ?>
                              
                            </div>
                        </div>
                    </div>
            
                    <div class="col-md-9">
                        <div class="card border border-warning">
                            <div class="card-header">
                                <h4 class="text-warning"><?php echo $linha['titulo_Topico'];?>
                                    <!-- gerenciar a publicação caso o user for logado for admin++ -->
                                    <?php if(idVerify($con, $idUser) == true || $dados['nivel_User'] >= 2){ ?>
                                        <button class="float-right border-0 bg-light" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="https://img.icons8.com/material/24/000000/menu-2.png"></button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <!-- verifica se o topico esta trancado -->
                                            <?php if($dado['trancado_Topico'] == 1){ ?>
                                                <a name="destrancarTopico" class="dropdown-item" href="#" data-toggle="modal" data-target="#destrancarTopico">Destrancar Tópico</a>
                                            <?php }else{ ?>
                                                <a name="trancarTopico" class="dropdown-item" href="#" data-toggle="modal" data-target="#trancarTopico">Trancar Tópico</a>
                                            <?php }?>
                                            <!-- fim da verificacao do topico trancado -->
                                            <a class="dropdown-item" href="/forum/editarTopico.php?id=<?php echo $linha['id_Post'];?>">Editar</a>
                                            <!-- verifica se o post é principal ou post resposta -->
                                            <?php if($linha['resposta_Post'] == 1){ ?>
                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#excluirTopico">Excluir Postagem</a>
                                            <?php } ?>
                                            <!-- fim da verificacao do post principal ou resposta -->
                                        </div>
                                    <?php }?>
                                      
                                        <?php if(isset($_SESSION['user']) && $linha['trancado_Topico'] == 0){ ?>
                                                <a class="bg-light border border-light float-right" href="topico.php?id=<?php echo $idTopico;?>&reply=&#tituloResposta"><img title="Responder"  class="rounded float-right" src="https://img.icons8.com/android/20/000000/reply-arrow.png" style="cursor: pointer;"></a>
                                        <!-- fim da verificacao de login -->
                                        <?php }?>
                                </h4>
                                
                            </div>
                            <div class="card-body border border-warning">
                                
                              <p class="card-text"><?php echo showAbsoluteFormat(bbCode($linha['conteudo_Post']));?></p>
                            </div>
                            <div class="card-footer">
                              <small class="text-muted"><h6 class="text-right my-2 px-2"><?php echo $dataPost->format('d/m/y H:i');?> <?php if($linha['resposta_Post'] == 0){ ?><?php }?></h6></small>
                            </div>
                        </div>
                    </div>
                    
                </div>
            
                    
                        </div>
                    </div>
            </div>
            
        </div>
    
        
    
    <?php }else{?>
    <!-- ************************** POSTAGEM RESPOSTA ************************** -->
        <div class="tab-content mt-2" id="nav-tabContent">
            <a name="post<?php echo $linha['id_Post'];?>"></a>
            <div class="container-fluid ml-0">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card align-items-center border border-dark">
                            <img src="../<?php echo $dadosPostador['img_User'];?>" class="card-img-top my-2" style="max-width: 10rem;">
                            <div class="card-body">
                                <a class="card-title text-center text-warning" href="../user/profileView.php?userv=<?php echo $linha['idUser_Post'];?>"><?php if($dadosPostador['nivel_User'] == 2){echo "[MOD] ";}else if($dadosPostador['nivel_User'] == 3){echo "[ADMIN] ";}else if($dadosPostador['nivel_User'] == 4){echo "[MASTER] ";} echo $dadosPostador['nome_User'];?></a><br>
                                <?php
                                    $sql = $con->prepare("SELECT COUNT(idUser_Post) FROM tbl_posts_forum WHERE idUser_Post = ?");
                                    $sql->bind_param("s", $linha['idUser_Post']);
                                    $sql->execute();
                                    $get = $sql->get_result();
                                    $posts = $get->fetch_array();
                                    
                                    if($posts['0'] <= 50){
                                ?>
                                        <h6 class="card-title text-center text-muted">Novato</h6>
                                    <?php }else if($posts['0'] <= 100){ ?>
                                        <h6 class="card-title text-center text-muted">Aprendiz</h6>
                                    <?php }else if($posts['0'] <= 160){ ?>
                                        <h6 class="card-title text-center text-muted">Veterano</h6>
                                    <?php }else{ ?>
                                        <h6 class="card-title text-center text-muted">Mestre</h6>
                                    <?php } ?>
                            </div>
                        </div>
                    </div>
                  
                    <div class="col">
                        <div class="card border border-dark">
                            <div class="card-header">
                                <h5 class="text-weight-bolder text-muted">Resposta à - <?php echo $linha['titulo_Topico'];?>
                                <?php if(idVerify($con, $idUser) == true || $dados['nivel_User'] >= 2){ ?>
                                    <button class="float-right border-0 bg-light" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="https://img.icons8.com/material/24/000000/menu-2.png"></button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="editarTopico.php?id=<?php echo $linha['id_Post'];?>">Editar</a>
                                            <a class="dropdown-item" href="excluirTopico.php?id=<?php echo $linha['id_Post'];?>" >Excluir Postagem</a>
                                    </div>
                                <?php }?>
                                </h5>
                            </div>
                            <div class="card-body border border-dark">
                              <p class="card-text"><?php echo showAbsoluteFormat(bbCode($linha['conteudo_Post']));?></p>
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
        <!-- fim do while -->
        <?php } ?>
        
        <!-- Pagination NAV -->
        <div class="row pt-3">
          <div class="col">
            <nav aria-label="Paginacao-postagens">
                <ul class="pagination justify-content-center">
                  <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''?>">
                      
                    <a class="page-link" href="?id=<?php echo $idTopico; ?>&pg=<?php echo $page-1?>" >
                      <span aria-hidden="true">&laquo;</span><span class="sr-only">Anterior</span>
                    </a>
                  </li>
              <?php
                  $pages = ceil(($numPosts*1.0)/$postsPorPg); // quantidade de pags
                  for($j = 0; $j < $pages; $j++) //para cada pagina
                  {
                      if(($j+1)==$page)
                      {
                          echo '<li class="page-item active"><a class="page-link" href="?id='.$idTopico.'&pg='.($j+1).'">'.($j+1).'</a></li>';
                      }
                      else
                      {
                          echo '<li class="page-item"><a class="page-link" href="?id='.$idTopico.'&pg='.($j+1).'">'.($j+1).'</a></li>';
                      }
                      
                  }
              ?> 
                  <li class="page-item <?php echo $page == $pages ? 'disabled' : ''?>">
                    <a class="page-link" href="?id=<?php echo $idTopico; ?>&pg=<?php echo $page+1?>" >
                      <span aria-hidden="true">&raquo;</span><span class="sr-only">Proximo</span>
                    </a>
                  </li>
                </ul>
            </nav>
          </div>
        </div>
        
    <?php // else do if(getTopicos->num_rows > 0) 
        }else{
    ?>
    <div class="card border border-warning my-2 mx-4">
        <div class="card-body text-warning bg-light">
            <h6 class="text-center">Nenhuma postagem nesta página!</h6>
        </div>
    </div>
        
        <?php
            } // fim do if(getPostagem->num_rows > 0)
          } // fim do if(numPosts > 0)
          else{ ?>
            <div class="card border border-warning my-2 mx-4">
                <div class="card-body text-warning bg-light">
                    <h6 class="text-center">Nenhuma Postagem presente no Tópico!</h6>
                </div>
            </div>
          <?php }?>
    
    <!-- preview da resposta -->
    <?php if(isset($_SESSION['preview']))
    { 
        $str = $_SESSION['preview'];
        unset($_SESSION['preview']);
    ?>
        <div class="card mx-3 mb-3">
            <h5 class="card-header text-warning font-weight-bolder bg-dark">Pré-Visualização</h5>
            <div class="card-body border border-dark" id="preViewTopic">
                <?php echo showAbsoluteFormat(bbCode($str)); ?>
            </div>
        </div>
    <?php }?>
    
    <!-- se o botao de responder for clicado aparecera a text area para o user inserir a resposta (textarea) -->
                <?php if(isset($_GET['reply'])){ ?>
                <div class="tab-content mt-2" id="nav-tabContent">
                    <div class="container-fluid mx-auto text-center">
                        <div class="row mb-3">
                            <div class="col">
                                <form method="POST" action="../php_action/forum/adicionarResposta.php">
                                    <input type="hidden" name="idTopico" id="idTopico" value="<?php echo $idTopico;?>">
                                    <div class="row">
                                        <div class="col">
                                            <div class="modal-header border-warning mb-2">
                                                <a name="tituloResposta">
                                                <h5 class="modal-title text-warning text-uppercase">Escreva uma resposta para o post</h5></a>
                                            </div>
                                            <div class="btn-group mb-2 px-1 text-center">
                                                <!-- botoes de formatacao de texto -->
                                                <!-- tipo de texto -->
                                                <button class="border border-warning bg-light" type="button" title="Negrito: [b]texto[/b]" id="negrito" onclick="surroundBB('conteudoResposta', '[b]', '[/b]')"><img src="https://img.icons8.com/metro/17/000000/bold.png"></button>
                                                <button class="border border-warning bg-light" type="button" title="Itálico: [i]texto[/i]" id="italico" onclick="surroundBB('conteudoResposta', '[i]', '[/i]')"><img src="https://img.icons8.com/metro/17/000000/italic.png"></button>
                                                <button class="border border-warning bg-light" type="button" title="Sublinhado: [u]texto[/u]" id="sublinhado" onclick="surroundBB('conteudoResposta', '[u]', '[/u]')"><img src="https://img.icons8.com/metro/21/000000/underline.png"></button>
                                                <button class="border border-warning bg-light" type="button" title="Tachado: [s]texto[/s]" id="tachado" onclick="surroundBB('conteudoResposta', '[s]', '[/s]')"><img src="https://img.icons8.com/metro/21/000000/strikethrough.png"></button> | 
                                                <!-- alinhamento -->
                                                <button class="border border-warning bg-light" type="button" title="Centralizar: [center]texto[/center]" id="aliCentro" onclick="surroundBB('conteudoResposta', '[center]', '[/center]')"><img src="https://img.icons8.com/metro/21/000000/align-center.png"></button>
                                                <button class="border border-warning bg-light" type="button" title="Alinhar à Direita: [right]texto[/right]" id="aliDireita" onclick="surroundBB('conteudoResposta', '[right]', '[/right]')"><img src="https://img.icons8.com/ios/21/000000/align-right-filled.png"></button>
                                                <button class="border border-warning bg-light" type="button" title="Justificar: [justify]texto[/justify]" id="aliJustificar" onclick="surroundBB('conteudoResposta', '[justify]', '[/justify]')"><img src="https://img.icons8.com/metro/21/000000/align-justify.png"></button> | 
                                                <!-- links -->
                                                <button class="border border-warning bg-light" type="button" title="Url: [url=www.link.com]texto[/url]" id="url" onclick="surroundBB('conteudoResposta', '[url=]', '[/url]')"><img src="https://img.icons8.com/metro/21/000000/link.png"></button> | 
                                                <!-- listas -->
                                                <button class="border border-warning bg-light" type="button" title="Lista Ordenada: [list=valor_inicial][*]texto[/list]" id="ulList" onclick="surroundBB('conteudoResposta', '[list=1]', '[/list]')"><img src="https://img.icons8.com/metro/26/000000/numbered-list.png"></button>
                                                <button class="border border-warning bg-light" type="button" title="Lista Desordenada: [list][*]texto[/list]" id="olList" onclick="surroundBB('conteudoResposta', '[list]', '[/list]')"><img src="https://img.icons8.com/ios/21/000000/bulleted-list-filled.png"></button>
                                                <button class="border border-warning bg-light" type="button" title="Item da Lista: [*]texto" id="iList" onclick="surroundBB('conteudoResposta', '[*]', '')"><h6>[*]</h6></button> |
                                                <!-- imagem -->
                                                <button class="border border-warning bg-light" type="button" title="Imagem: [img]link_da_img.com[/img]" id="img" onclick="surroundBB('conteudoResposta', '[img]', '[/img]')"><img src="https://img.icons8.com/ios/21/000000/image-file.png"></button> | 
                                                <!-- tam. Fonte -->
                                                <button class="border border-warning bg-light" type="button" title="Tamanho da Fonte: [size=tamanho]texto[/size]" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <img src="https://img.icons8.com/ios/21/000000/sentence-case.png">
                                                </button> | 
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" onclick="surroundBB('conteudoResposta', '[size=18]', '[/size]')" style="cursor:pointer;">Pequeno</a>
                                                    <a class="dropdown-item" onclick="surroundBB('conteudoResposta', '[size=36]', '[/size]')" style="cursor:pointer;">Médio</a>
                                                    <a class="dropdown-item" onclick="surroundBB('conteudoResposta', '[size=72]', '[/size]')" style="cursor:pointer;">Grande</a>
                                                </div>
                                                <!-- cor da fonte -->
                                                <div class="btn-group dropleft">
                                                    <button class="border border-warning bg-light" type="button" title="Alterar cor da fonte: [color=cod.cor]texto[/color]" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img src="https://img.icons8.com/metro/21/000000/paint-palette.png"> </button>
                                                    <div class="dropdown-menu ml-5 border-0" style="background: transparent;">
                                                        <div id="buttons">
                                                            <button type="button" title="Preto" class="preto" onclick="surroundBB('conteudoResposta', '[color=#000]', '[/color]')"></button>
                                                            <button type="button" title="Cinza" class="cinza" onclick="surroundBB('conteudoResposta', '[color=#808080]', '[/color]')"></button>
                                                            <button type="button" title="Violeta" class="violeta" onclick="surroundBB('conteudoResposta', '[color=#EE82EE]', '[/color]')"></button>
                                                            <button type="button" title="Azul" class="azul" onclick="surroundBB('conteudoResposta', '[color=#00F]', '[/color]')"></button>
                                                            <button type="button" title="Turquesa" class="turquesa" onclick="surroundBB('conteudoResposta', '[color=#008080]', '[/color]')"></button>
                                                            <button type="button" title="Verde" class="verde" onclick="surroundBB('conteudoResposta', '[color=#008000]', '[/color]')"></button>
                                                            <button type="button" title="Verde Claro" class="verdeClaro" onclick="surroundBB('conteudoResposta', '[color=#00FF00]', '[/color]')"></button>
                                                            <button type="button" title="Marrom" class="marrom" onclick="surroundBB('conteudoResposta', '[color=#654321]', '[/color]')"></button>
                                                            <button type="button" title="Rosa" class="pink" onclick="surroundBB('conteudoResposta', '[color=#FF00FF]', '[/color]')"></button>
                                                            <button type="button" title="Amarelo" class="amarelo" onclick="surroundBB('conteudoResposta', '[color=#FFFF00]', '[/color]')"></button>
                                                            <button type="button" title="Laranja" class="laranja" onclick="surroundBB('conteudoResposta', '[color=#FFA500]', '[/color]')"></button>
                                                            <button type="button" title="Vermelho" class="vermelho" onclick="surroundBB('conteudoResposta', '[color=#F00]', '[/color]')"></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="btn-group dropdown">
                                                    <button class="border border-warning bg-light" type="button" title="code: [code]nome_elemento[/code]"  id="dropCode" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="https://img.icons8.com/metro/21/000000/source-code.png"></button>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropCode">
                                                        <button type="button" class="dropdown-item" onclick="surroundBB('conteudoResposta', '[code]', '[/code]')">Tag HTML</button>
                                                        <button type="button" class="dropdown-item" onclick="surroundBB('conteudoResposta', '[block]', '[/block]')">Bloco de Código</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <textarea class="form-control" name="conteudoResposta" id="conteudoResposta" rows="15"><?php echo $str;?></textarea>
                                            </div>
                                            <div class="modal-footer border-warning mt-2">
                                                <button type="submit" class="btn border border-warning bg-light text-warning" id="btnPreview" name="btnPreview"><img src="https://img.icons8.com/small/24/000000/preview-pane.png"> Preview</button>
                                                <button type="submit" class="btn border border-warning bg-light text-warning" id="btnResponder" name="btnResponder">Responder</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                    <!--fim da verificacao do botao de responder o post principal -->
                            </div>
                        </div>
                    </div>
                </div>
                        <?php }?>
    
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