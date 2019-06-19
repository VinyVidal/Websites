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

    // Pegando quantidade de jogos aprovados
    $sql = "SELECT COUNT(*) AS 'qntJogos' FROM tbl_uploads WHERE status_Upload = 'APROVADO'";
    $jogos = $con->query($sql);
    $totalJogos = $jogos->fetch_assoc()['qntJogos'];
    $jogos->free_result();
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

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/paginacao.css">
    <link rel="icon" href="../img/iconSite.png">
    <?php
      if(isset($_SESSION['modalAlerta']))
      {
        showModal($_SESSION['modalAlerta']['titulo'], $_SESSION['modalAlerta']['mensagem'], $_SESSION['modalAlerta']['tipo']);
      }

    unset($_SESSION['modalAlerta']); // parar de exibir o modal
    ?>
    <title>Mural de Jogos - Retro Game Center</title>
  </head>
  <body class="bg-dark">
  <!-- navbar Menu-->
  <nav class="navbar navbar-expand-md navbar-dark bg-black" >
      <a class="navbar-brand" href="../"><img src="../img/Logo10.png"></a>
        <div class="d-flex flex-row order-3 order-lg-3">
            <ul class="navbar-nav flex-row">
                <?php
                if(!isset($_SESSION['user']))
                { ?>
                 <li class="nav-item mr-3"><a class="btn btn-outline-warning" role="button" href="../user/login.php">Entrar</a></li>
                <?php
                }
                else
                {
                ?>
                  <li class="nav-item dropdown mr-3"><a class="nav-link dropdown-toggle" id="navbarDropdownUser" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="nav-user" src="<?php echo '../'.$dados['img_User'];?>"><?php echo $dados['nome_User'];?></a>
                    <div class="dropdown-menu border-warning" aria-labelledby="navbarDropdownUser">
                      <a class="dropdown-item" href="../user/profile.php"><img src="https://img.icons8.com/material/16/000000/gender-neutral-user.png" class="pr-1"> Profile</a>
                      <a class="dropdown-item" href="../user/mensagensPrivadas.php"><img src="https://img.icons8.com/material/16/000000/sms.png"> Mensagens <?php echo showNewMessages($con, $dados['id_User']); ?></a>
                      <a class="dropdown-item" href="../php_action/logout.php"><img src="https://img.icons8.com/metro/16/000000/exit.png" class="pr-1"> Sair</a>
                    </div>
                  </li>
                <?php } //end if ?>
                <li class="nav-item">
                    <button class="navbar-toggler border-warning" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </li>
            </ul>
        </div>


      <div class="collapse navbar-collapse order-2" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto" >
          <li class="nav-item">
            <a class="nav-link" href="../"><img src="https://img.icons8.com/dusk/20/000000/visual-game-boy.png"> Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../forum/"><img src="https://img.icons8.com/ultraviolet/17/000000/comment-discussion.png"> Fórum</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Mural
            </a>
            <div class="dropdown-menu border-warning" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="enviarJogo.php"><img src="https://img.icons8.com/material-two-tone/24/000000/upload-to-cloud.png"> Envie seu Jogo</a>
              <a class="dropdown-item active"><img src="https://img.icons8.com/material-rounded/24/000000/controller.png"> Jogos <span class="sr-only">(current)</span></a>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../forum/categoria.php?id=2"><img src="https://img.icons8.com/office/17/000000/outline.png"> Tutoriais</a>
          </li>
          <li class="nav-item mr-3">
            <a class="nav-link" href="../about.php"><img src="https://img.icons8.com/cotton/20/000000/info.png"> Sobre</a>
          </li>
        </ul>
        
      </div>
    </nav>
    <!-- navbar Menu-->

    <!-- conteudo da pagina -->
    <div class="container bg-light mr-auto pt-3 pb-3">
        <div class="row bg-warning pt-1 mb-3">
            <div class="col text-center">
                <h3>Mural de Jogos</h3>
            </div>
        </div>
        
        <div class="row border-bottom">
            <div class="col">
                <p>Aqui se encontra os jogos enviados pelos usuários do site.</p>
                <p>Quer ter o seu jogo exposto no mural? <a href="enviarJogo.php">clique aqui</a> para enviar o seu jogo!</p>
            </div>
        </div>

        <form method="GET">
          <div class="input-group mt-3">
            <input type="search" name="pesquisar" class="form-control" placeholder="Pesquisar por titulo ou usuário" aria-label="Pesquisar por titulo ou usuário">
            <div class="input-group-append">
              <button class="btn btn-outline-warning" name="btnPesquisar" type="submit">Pesquisar</button>
            </div>
          </div>
        </form>
        
        <?php
            // Tratando filtros do botao Pesquisar
            $where = "";
          if(isset($_GET['btnPesquisar']))
          {
            if(!empty(filterField($_GET['pesquisar'])))
            {
              $proc = filterField($_GET['pesquisar']);
              $where = " AND nomeJogo_Upload LIKE '%$proc%' OR nome_User LIKE '%$proc%'";
            }
          }

          if(!empty($where))
          {
          ?>

          <div class="row">
            <div class="col">
              <p>Você pesquisou por: <?php echo $proc; ?></p>
            </div>
          </div>

          <?php
          }
            if($totalJogos > 0)
            {
              $itensPerPage = 5; // quantidade de 'cards' por pagina
              $i = 1;
              if(isset($_GET['pg']))
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
              // pegando jogos da pagina atual
              $exceto = ($page * $itensPerPage) - $itensPerPage; // offset
              if($page == 1)
              {
                $sql = 'SELECT tbl_uploads.*, id_User, nome_User FROM tbl_uploads, tbl_usuarios WHERE status_Upload = "APROVADO" AND id_User = idUser_Upload'.$where.' LIMIT '.$itensPerPage;
              }
              else
              {
                $sql = 'SELECT * FROM tbl_uploads WHERE status_Upload = "APROVADO"'.$where.' AND status_Upload = "APROVADO" LIMIT '.$itensPerPage.' OFFSET '.$exceto;
              }
              
              $jogos = $con->query($sql);
              if($jogos->num_rows > 0)
              {
                  while($linha = $jogos->fetch_assoc())
                  {
                      
        ?>
                    <div class="row pt-3 mb-3">
                        <div class="col">
                            <div class="card text-left mx-auto border-secondary" style="max-width: 60rem;">
                                <img class="img card-img-top border-bottom" src="<?php echo '../'.$linha['imgCapaJogo_Upload']; ?>" style="max-width: 60rem;max-height: 15rem;">
                                <div class="card-body">
                                    <h5 class="card-title"> <?php echo $linha['nomeJogo_Upload']; ?> </h5>
                                    <p class="card-text"> <?php echo sanitizeTableCellText($linha['descricaoJogo_Upload'], 300); ?> </p>
                                    <a href="jogar.php?jogo=<?php echo $linha['id_Upload'];?>" class="btn btn-warning text-dark"> JOGAR </a>
                                </div>
                                <div class="card-footer text-muted">
                                    Criado por: <?php echo '<a href="../user/profileView.php?userv='.$linha['idUser_Upload'].'">'.$linha['nome_User'].'</a>'; ?><br>
                                    
                                    <?php
                                    $dataJogo = new DateTime($linha['dataAprovacao_Upload']);
                                    
                                    echo $dataJogo->format('d/m/y H:i');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- <?php if($i === 2) { ?>
                        <div class="responsive-panel mx-auto" style="max-width: 60rem;">
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
                    <?php }?> -->

        <?php
                      $i++;
                    }// fim do while
                $jogos->free_result();
        ?>

                  <!-- Pagination NAV -->
                  <div class="row pt-3 mb-3">
                    <div class="col">
                      <nav aria-label="Paginacao-jogos">
                          <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''?>">
                                
                              <a class="page-link" href="?pg=<?php echo $page-1?>" >
                                <span aria-hidden="true">&laquo;</span><span class="sr-only">Anterior</span>
                              </a>
                            </li>
                        <?php
                            $pages = ceil(($totalJogos*1.0)/$itensPerPage); // quantidade de pags
                            for($j = 0; $j < $pages; $j++) //para cada pagina
                            {
                                if(($j+1)==$page)
                                {
                                    echo '<li class="page-item active"><a class="page-link" href="?pg='.($j+1).'">'.($j+1).'</a></li>';
                                }
                                else
                                {
                                    echo '<li class="page-item"><a class="page-link" href="?pg='.($j+1).'">'.($j+1).'</a></li>';
                                }
                                
                            }
                        ?> 
                            <li class="page-item <?php echo $page == $pages ? 'disabled' : ''?>">
                              <a class="page-link" href="?pg=<?php echo $page+1?>" >
                                <span aria-hidden="true">&raquo;</span><span class="sr-only">Proximo</span>
                              </a>
                            </li>
                          </ul>
                      </nav>
                    </div>
                  </div>

        <?php
                } // fim do if($jogos->num_rows > 0)
                else
                {
                    echo 'Nenhum jogo encontrado.';
                }
            } // Fim do if($totalJogos > 0)
            else
            {
                echo 'Nenhum jogo encontrado.';
            }
        ?>

        
    </div>
    <!-- conteudo da pagina -->
    
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