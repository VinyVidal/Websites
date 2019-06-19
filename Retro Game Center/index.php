<?php include_once("lib/lib.php");
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
?>
<!doctype html>
<html lang="en">
  <head>
 <!--    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({
          google_ad_client: "ca-pub-7463114109083817",
          enable_page_level_ads: true
      });
     </script> -->
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="keywords" content="Javascript, Forum, Jogos, Retro, Game, Center, Criar jogos, tutoriais, Mural">
    <meta name ="robots" content="index">
    
    
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
  
  <?php
  if(isset($_SESSION['modalAlerta']))
  {
    showModal($_SESSION['modalAlerta']['titulo'], $_SESSION['modalAlerta']['mensagem'], $_SESSION['modalAlerta']['tipo']);
  }

    unset($_SESSION['modalAlerta']); // parar de exibir o modal
    ?>
  <link rel="icon" href="img/iconSite.png">
    <title>Retro Game Center - De gamers para gamers</title>
    
     
  </head>
  <body class="bg-dark">
  <!-- navbar Menu-->
  <nav class="navbar navbar-expand-md navbar-dark bg-black" >
    <a class="navbar-brand" href="/Retro Game Center/"><img src="img/Logo10.png"></a>
    <div class="d-flex flex-row order-3 order-lg-3">
          <ul class="navbar-nav flex-row">
              <?php
                if(isset($_SESSION['user']) == false)
                { ?>
              <li class="nav-item mr-3"><a class="btn btn-outline-warning" id="btnEntrar" role="button" 
href="user/login.php">Entrar</a></li>
              <?php
                }
                else
                {
                ?>
                  <li class="nav-item dropdown mr-3"><a class="nav-link dropdown-toggle" id="navbarDropdownUser" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="nav-user" src="<?php echo $dados['img_User'];?>"><?php echo $dados['nome_User'];?></a>
                    <div class="dropdown-menu border-warning" aria-labelledby="navbarDropdownUser">
                      <a class="dropdown-item" href="user/profile.php"><img src="https://img.icons8.com/material/16/000000/gender-neutral-user.png" class="pr-1"> Profile</a>
                      <a class="dropdown-item" href="user/mensagensPrivadas.php"><img src="https://img.icons8.com/material/16/000000/sms.png"> Mensagens <?php echo showNewMessages($con, $dados['id_User']); ?></a>
                      <a class="dropdown-item" href="php_action/logout.php"><img src="https://img.icons8.com/metro/16/000000/exit.png" class="pr-1"> Sair</a>
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
    <li class="nav-item active">
      <a class="nav-link" ><img src="https://img.icons8.com/dusk/20/000000/visual-game-boy.png"> Home <span 
class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="forum/"><img src="https://img.icons8.com/ultraviolet/17/000000/comment-
discussion.png"> Fórum</a>
      </li>
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" 
aria-haspopup="true" aria-expanded="false">
        Mural
      </a>
      <div class="dropdown-menu border-warning" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="mural/enviarJogo.php"><img src="https://img.icons8.com/material-two-
tone/24/000000/upload-to-cloud.png"> Envie seu Jogo</a>
        <a class="dropdown-item" href="mural/jogos.php"><img src="https://img.icons8.com/material-
rounded/24/000000/controller.png"> Jogos</a>
      </div>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="https://retrogamecenter.com.br/forum/categoria.php?id=2"><img src="https://img.icons8.com/office/17/000000/outline.png"> Tutoriais</a>
      </li>
      <li class="nav-item mr-3">
      <a class="nav-link" href="about.php"><img src="https://img.icons8.com/cotton/20/000000/info.png"> Sobre</a>
      </li>
    </ul>
    </div>
  </nav>
  <!-- navbar Menu-->
  
  <!-- conteudo da pagina -->

    <div class="container bg-light mr-auto pt-3 pb-3">
      <!-- Alert mensagens não lidas -->
        <?php
          if(isset($_SESSION['user']))
          {
            $sql = $con->prepare("SELECT * FROM tbl_mensagensPrivadas WHERE idDestinatario_Mensagem = ? AND destinatarioExcluiu_Mensagem = '0' AND (SELECT visualizou_Mensagem FROM tbl_usuarios WHERE id_User = ?) = '0'");
            $sql->bind_param("ss", $idUser, $idUser);
            $sql->execute();
            $getMsgNovas = $sql->get_result();
            //em caso de mal funcionamento, mudar o '$sql' pelo '$getMsgNovas' na linha a seguir
            $qntMsg = $getMsgNovas->num_rows;
            if($qntMsg > 0)
            { 
            ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                  if($qntMsg == 1)
                  {
                    echo '<strong>NOVAS MENSAGENS: </strong>Voce tem '.$qntMsg.' mensagem não lida!';
                  }
                  else
                  {
                    echo '<strong>NOVAS MENSAGENS: </strong>Voce tem '.$qntMsg.' mensagens não lidas!';
                  }
                ?>
                <a href="user/mensagensPrivadas.php" class="alert-link">Ver todas as mensagens</a>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <?php
            }
          }
        ?>
        <!-- fim do Alert mensagens não lidas -->

        <!-- Alert solicitações -->

        <div class="card mt-5 mx-3 mb-3 border-warning">
          <div class="card-body">
            <h5 class="card-title">O melhor site para a comunidade gamer do Brasil!</h5>
            <p class="card-text">Jogue os <a href="mural/jogos.php">jogos do mural</a> ou <a href="mural/enviarJogo.php">envie o seu próprio game</a>!</p>
            <p class="card-text">Visite o nosso <a href="forum">Fórum</a> para tirar dúvidas, participar de discussões.</p>
            <p class="card-text">Aprenda a criar seu próprio jogo com a tecnologia HTML5 com os <a href="forum/categoria.php?id=2">tutoriais</a>!</p>
          </div>
        </div>
  <?php
     $sql = $con->prepare("SELECT * FROM tbl_uploads WHERE status_Upload = 'APROVADO' ORDER BY dataEnvio_Upload DESC LIMIT 3");
     if(!$sql->execute())
     {
         die($sql->error);
     }
     $getJogosRecentes = $sql->get_result();
     $rows = $getJogosRecentes->num_rows;

     if($rows > 0)
     {
  ?>
  
    <h1 class="text-center">Jogos Novos</h1>

    
    <!-- Slider 'jogos recentes' -->
    <div class="row">
      <div class="col">
        <div id="carouselExampleIndicators" class="carousel slide mb-5 border border-dark" data-ride="carousel">
          <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>

            <?php
                if($rows > 1)
                {
            ?>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <?php
                } 
                if($rows > 2)
                {
            ?>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
          <?php } ?>
          </ol>
          <div class="carousel-inner">
            <?php
                $i = 0;
                while($jogo = $getJogosRecentes->fetch_array())
                  {
            ?>
            <div class="carousel-item <?php echo $i === 0 ? 'active' : '' ?>">
              <a href="mural/jogar.php?jogo=<?php echo $jogo['id_Upload']; ?>">
                <img class="d-block w-100" src="<?php echo $jogo['imgCapaJogo_Upload']; ?>">
                <div id="carouselbody" class="carousel-caption d-md-block text-warning panel-transparent">
                  <h2><?php echo $jogo['nomeJogo_Upload'];?></h2>
                  <p><?php echo sanitizeTableCellText($jogo['descricaoJogo_Upload'], 120); ?></p>
                </div>
              </div>
            <?php 
                    if(($i+1) === $rows)
                    {
                        break;
                    }
                    $i++;
                  }// fim do while?>
              </a>
          </div>
          <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
      </div>
    </div>
    <!-- Slider 'jogos recentes' -->
    <?php
        } // fim do if($rows > 0)
        else
        {
          echo '<h4>Nenhum jogo disponivel</h4>';
        }

        //Pegando jogos mais jogados(visualizados)
        $sql = $con->prepare("SELECT * FROM tbl_uploads WHERE status_Upload = 'APROVADO' ORDER BY visualizacoesJogo_Upload DESC 

LIMIT 3");
        $sql->execute();
        $getJogosMaisJogados = $sql->get_result();
        $rows = $getJogosMaisJogados->num_rows;

        if($rows > 0)
        {
            
    ?>

    <div class="row">
      <div class="col text-center">
        <h2>Jogos em Destaque</h2>
      </div>
    </div>

    <div class="card-deck ml-auto mt-3 mb-3">
        <?php
          $j = 0;
          $jogosPorLinha = 1;
          while($jogo = $getJogosMaisJogados->fetch_assoc())
            {
        ?>
                <div class="card text-left border-secondary" style="max-width: 30rem;">
                    <img class="img card-img-top border-bottom" src="<?php echo $jogo['imgCapaJogo_Upload']; ?>" 
style="max-width: 30rem;max-height: 9rem;">
                    <div class="card-body">
                        <h5 class="card-title"> <?php echo $jogo['nomeJogo_Upload']; ?> </h5>
                        <p class="card-text"> <?php echo sanitizeTableCellText($jogo['descricaoJogo_Upload'], 120); ?> </p>
                        <a href="mural/jogar.php?jogo=<?php echo $jogo['id_Upload'];?>" class="btn btn-warning text-dark"> JOGAR </a>
                    </div>
                </div>
        <?php
                $j++;
            } // fim do while
        ?>
        </div>
    <?php
      } //fim do if($rows > 0)
    ?>


    <div class="row">
        <div class="col text-center mt-3">
            <h2>Últimas atividades do Fórum</h2>
        </div>
    </div>
    
    <?php
        $sql = $con->prepare("SELECT * FROM tbl_posts_forum, tbl_usuarios, tbl_topicos_forum WHERE id_User = idUser_Post AND id_Topico = idTopico_Post AND CHAR_LENGTH(titulo_Topico) > 0 ORDER BY data_Post DESC LIMIT 3");
        $sql->execute();
        $getPosts = $sql->get_result();
        $total = $getPosts->num_rows;
        
    ?>
    <div class="row">
        <div class="col">
                <?php if($total > 0)
                      {
                while($post = $getPosts->fetch_array()){
                    //busca do titulo e tipo de topico da postagem e os dados da categoria
                    $sql = $con->prepare("SELECT id_Topico, titulo_Topico, tipo_Topico, idCategoria_Topico, id_Categoria, nome_Categoria FROM tbl_topicos_forum, tbl_categorias_forum WHERE id_Topico = ? AND id_Categoria = idCategoria_Topico");
                    $sql->bind_param("s", $post['idTopico_Post']);
                    $sql->execute();
                    $get = $sql->get_result();
                    $dados = $get->fetch_assoc();
                    // Texto da atividade em questão
                    $atividade = $post['resposta_Post'] == 0 ? 'Iniciou um novo tópico' : 'Respondeu ao tópico';
                ?>
                    <div class="card mx-3 mb-3 border border-warning">
                        <div class="card-body">
                            <h5 class="card-title"><img src="https://img.icons8.com/material/16/000000/user.png"> <a href="user/profileView.php?userv=<?php echo $post['id_User'];?>"><?php echo $post['nome_User'];?></a></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo $atividade.' - <a href=forum/topico.php?id='.$dados['id_Topico'].'>'.$dados['titulo_Topico'].'</a> - em <a href="forum/categoria.php?id='.$dados['id_Categoria'].'">'.$dados['nome_Categoria'].'</a>'; ?></h6>
                            <!-- p class="card-text border-top mt-md-2 pt-md-2"><?php echo sanitizeTableCellText($post['conteudo_Post'], 100) ?></p> -->
                        </div>
                        <div class="card-footer text-right">
                            <a href="forum/topico.php?id=<?php echo $post['idTopico_Post'];?>&#post<?php echo $post['id_Post'];?>" class="card-link">Ver Postagem</a>
                        </div>
                    </div>
                <!-- fim while com o conteudo dos posts recentes do forum -->
                <?php }
                }else{echo "<div class='alert alert-warning text-center mx-4'>Nenhum registro de atividades.</div>";}?>
        </div>
    </div>
    
  </div> 
  <!-- conteudo da pagina -->
  
  <?php //if(isset($get)) $get->free_result(); ?>
  
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
    <div class="footer-copyright text-center py-3">© 2018 Copyright BMV
    </div>
    <!-- Copyright -->

  </footer>
  <!-- navbar Rodape-->
  
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>