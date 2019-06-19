<?php include_once("../lib/lib.php");
  session_start();
  // Pagina de cada jogo

  if(isset($_SESSION['user']))
  {
    $idUser = $_SESSION['user']['idUser'];

    $sql = $con->prepare("SELECT * FROM tbl_usuarios WHERE id_User = ?");
    $sql->bind_param("s", $idUser);
    $sql->execute();
    $get = $sql->get_result();
    $dados = $get->fetch_assoc();
  }

  // recuperando dados do jogo pela url
  if(isset($_GET['jogo']))
  {
    // validando o GET
    if(filter_var($_GET['jogo'], FILTER_VALIDATE_INT))
    {
      $idJogo = $_GET['jogo'];
    }
    else
    {
      $_SESSION['modalAlerta']['titulo'] = 'Erro';
      $_SESSION['modalAlerta']['mensagem'] = 'Jogo não encontrado.';
      $_SESSION['modalAlerta']['tipo'] = 'error';
      header('location: jogos.php');
      die('Aguarde um momento...');
    }

    $sql = $con->prepare("SELECT * FROM tbl_uploads, tbl_usuarios WHERE id_Upload = ? AND status_Upload = 'APROVADO' AND idUser_Upload = id_User");
    $sql->bind_param("i", $idJogo);
    if($sql->execute())
    {
      $getJogo = $sql->get_result();
      $rows = $getJogo->num_rows;

      if($rows > 0)
      {
        $dadosJogo = $getJogo->fetch_assoc(); //pegando a primeira (e unica) linha do SELECT
        // setando a visualizacao do jogo
        if(!isset($_SESSION["visualizouID$idJogo"]))
        {
            $_SESSION["visualizouID$idJogo"] = true;
            $sql = $con->prepare("UPDATE tbl_uploads SET visualizacoesJogo_Upload = visualizacoesJogo_Upload + 1 WHERE id_Upload = ?");
            $sql->bind_param('i', $idJogo);
            $sql->execute();
        }
      }
      else
      {
        $_SESSION['modalAlerta']['titulo'] = 'Erro';
        $_SESSION['modalAlerta']['mensagem'] = 'Jogo não encontrado.';
        $_SESSION['modalAlerta']['tipo'] = 'error';
        header('location: jogos.php');
        die('Aguarde um momento...');
      }
    }
    else
      {
        $_SESSION['modalAlerta']['titulo'] = 'Erro';
        $_SESSION['modalAlerta']['mensagem'] = 'Erro ao carregar o jogo, tente novamente mais tarde!';
        $_SESSION['modalAlerta']['tipo'] = 'error';
        header('location: jogos.php');
        die('Aguarde um momento...');
      }

  }
  else
  {
    $_SESSION['modalAlerta']['titulo'] = 'Erro';
    $_SESSION['modalAlerta']['mensagem'] = 'Jogo não encontrado.';
    $_SESSION['modalAlerta']['tipo'] = 'error';
    header('location: jogos.php');
    die('Aguarde um momento...');
  }
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/jogar.css">
    <link rel="icon" href="../img/iconSite.png">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    
    <?php
      if(isset($_SESSION['modalAlerta']))
      {
        showModal($_SESSION['modalAlerta']['titulo'], $_SESSION['modalAlerta']['mensagem'], $_SESSION['modalAlerta']['tipo']);
      }

    unset($_SESSION['modalAlerta']); // parar de exibir o modal
    ?>
    <title><?php echo $dadosJogo['nomeJogo_Upload']?> - Retro Game Center</title>
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
        <a class="dropdown-item" href="enviarJogo.php"> <img src="https://img.icons8.com/material-two-tone/24/000000/upload-to-cloud.png">Envie seu Jogo</a>
        <a class="dropdown-item" href="jogos.php"><img src="https://img.icons8.com/material-rounded/24/000000/controller.png"> Jogos</a>
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

<?php

  if(isset($_SESSION['user'])) 
  {
      if($dados['nivel_User'] >=3 )
      {
?>
  <!-- Modal Confirmar Excluir(Reprovar) jogo -->
  <div class="modal fade" id="modalReprovarJogo" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
      <div class="modal-dialog border border-dark" role="document">
        <div class="modal-content text-dark bg-light">
          <div class="modal-header border-dark">
            <h5 class="modal-title" id="titulo">Confirmar Reprovação</h5>
            <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <!-- Form -->
            <form method="POST" action="../php_action/excluiJogo.php">
            <p>
              <label for="motivoReprovacao">Descreva o motivo para estar excluindo este jogo:</label>
            </p>
            <p>
                <textarea id="motivoReprovacao" name="motivoReprovacao" class="form-control" rows="5" required></textarea>
            </p>
          </div>
          <div class="modal-footer border-dark">
              <input type="hidden" name="idJogo" value="<?php echo $idJogo; ?>">
              <!-- NECESSARIO ADICIONAR SEGURANÇA NESSES HIDDEN INPUTS -->
              <button type="submit" class="btn btn-success" name="btnReprovar">Reprovar</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </form>
            <!-- Fim form-->
          </div>
        </div>
      </div>
    </div>
<?php
      }
  }
?>

<?php

  if(isset($_SESSION['user'])) 
  {
      if($dados['nivel_User'] >=3 )
      {
?>
  <!-- Modal Editar Descrição do Jogo -->
  <div class="modal fade" id="modalAlterarDescJogo" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
      <div class="modal-dialog border border-dark" role="document">
        <div class="modal-content text-dark bg-light">
          <div class="modal-header border-dark">
            <h5 class="modal-title" id="titulo">Alterar Descrição do Jogo</h5>
            <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <!-- Form -->
            <form method="POST" action="../php_action/alteraDescJogo.php">
            <p>
                <textarea id="altDescricao" name="altDescricao" class="form-control" rows="8" required><?php echo $dadosJogo['descricaoJogo_Upload'];?></textarea>
            </p>
          </div>
          <div class="modal-footer border-dark">
              <input type="hidden" name="idJogo" value="<?php echo $idJogo; ?>">
              <!-- NECESSARIO ADICIONAR SEGURANÇA NESSES HIDDEN INPUTS -->
              <button type="submit" class="btn btn-success" name="btnAltDesc">Salvar Alterações</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </form>
            <!-- Fim form-->
          </div>
        </div>
      </div>
    </div>
<?php
      }
  }
?>


  <!-- conteudo da pagina -->
  <div class="container bg-light mr-auto pt-3 pb-3">
    <div class="row bg-warning pt-1 mb-3">
      <div class="col text-center">
        <h3><?php echo $dadosJogo['nomeJogo_Upload'];?></h3>
      </div>
    </div>
    
    <div class="row border-bottom mb-1">
      <div class="col">
        <p><?php echo nl2br($dadosJogo['descricaoJogo_Upload']);?></p>
                <!-- Botao Editar Desc
        Somente o criador do jogo e os admins podem alterar -->
        <?php
          if(isset($_SESSION['user']))
          {
              
              if($dados['nivel_User'] >=3 || $dados['id_User'] == $dadosJogo['idUser_Upload'])
              {
        ?>
        <button type="button" class="btn btn-warning mb-3" data-toggle="modal" data-target="#modalAlterarDescJogo">Alterar Descrição</button>
        <?php
              }
          }
        ?>
      </div>
    </div>
    
    <?php
        $query = $con->prepare("SELECT visualizacoesJogo_Upload FROM tbl_uploads WHERE id_Upload = ?");
        $query->bind_param('i', $idJogo);
        $query->execute();
        $getVis = $query->get_result();
        $visualizacoes = $getVis->fetch_assoc();

    ?>
    
    <!-- criador, data e visualizacoes -->
    <div class="row border-bottom mb-5">
        <div class="col">
            <span class="text-muted" style="font-size: 14px;"> Criado por: </span><a class="mr-5" href="../user/profileView.php?userv=<?php echo $dadosJogo['idUser_Upload'];?>"><?php echo $dadosJogo['nome_User'];?></a> 
            <?php $dataJogo = new DateTime($dadosJogo['dataAprovacao_Upload']); ?>
            <span class="text-muted mr-5" style="font-size: 14px;"> <?php echo $dataJogo->format('d/m/y H:i');?> </span>
            <img src="https://img.icons8.com/ios-glyphs/26/000000/visible.png" title="Visualizações"> <?php echo $visualizacoes['visualizacoesJogo_Upload']; ?>
        </div>
    </div>

    <div class="row mb-3 mr-2">
      <div class="col text-right">
          <div class="btn-group">
              <a role="button" class="btn btn-warning mr-3 pt-2 text-body" href="../<?php echo $dadosJogo['arquivosJogo_Upload'];?>"><b>&lt;Código Fonte/&gt;</b></a>
        <?php
          if(isset($_SESSION['user']))
          {
              if($dados['nivel_User'] >=3 )
              {
        ?>
            
            <button type="button" class="btn btn-danger mr-3" data-toggle="modal" data-target="#modalReprovarJogo">Excluir</button>
            <?php
                  }
              }
            ?>
            
            <button id="btnFullScreen" type="button" class="btn btn-warning" title="Tela Cheia"><img src="https://img.icons8.com/ios/32/000000/full-screen-filled.png"></button>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <?php
          //Setando a url do jogo.html
          if($idJogo < 10)
          {
            $urlJogo = '../uploads/aprovados/upload0'.$dadosJogo["id_Upload"].'/jogo'.$dadosJogo["id_Upload"].'/'.$dadosJogo["nomeJogo_Upload"].'/jogo.html';
          }
          else
          {
            $urlJogo = '../uploads/aprovados/upload'.$dadosJogo["id_Upload"].'/jogo'.$dadosJogo["id_Upload"].'/'.$dadosJogo["nomeJogo_Upload"].'/jogo.html';
          }
          
        ?>
        <!-- AQUI ONDE VAI FICAR O CANVAS DO JOGO -->
        <div id="gameScreenDiv" class="embed-responsive embed-responsive-4by3">
             <iframe id="gameScreen" class="embed-responsive-item" src="<?php echo $urlJogo?>" allowfullscreen>O seu navegador não suporta recursos do HTML5, atualize ou troque de navegador.</iframe>
        </div>
        
      </div>
    </div>
    
    <!-- <div class="responsive-panel">
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

    <?php 
    if(isset($_SESSION['user']))
    {
      $get->free_result();
    }
    $getJogo->free_result();
    ?>

  </footer>
  <!-- navbar Rodape-->
  
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    
    <script type="text/javascript" src="../js/jogar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>