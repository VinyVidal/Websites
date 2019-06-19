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
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
  
  <link rel="icon" href="img/iconSite.png">
    <title>Sobre - Retro Game Center</title>
    
     
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
                <button class="navbar-toggler border-warning" data-toggle="collapse" data-

target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-
label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
          </li>
          </ul>
    </div>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav ml-auto" >
    <li class="nav-item">
      <a class="nav-link" href="/Retro Game Center/"><img src="https://img.icons8.com/dusk/20/000000/visual-game-boy.png"> Home</a>
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
      <a class="nav-link" href="forum/categoria.php?id=2"><img src="https://img.icons8.com/office/17/000000/outline.png"> Tutoriais</a>
      </li>
      <li class="nav-item mr-3 active">
      <a class="nav-link"><img src="https://img.icons8.com/cotton/20/000000/info.png"> Sobre <span class="sr-only">(current)</span></a>
      </li>
    </ul>
    </div>
  </nav>
  <!-- navbar Menu-->
  <!-- conteudo da pagina -->
  <div class="container bg-light mr-auto pt-3 pb-3">
        <div class="card mt-2 mb-3 mx-1 border border-warning">
          <h5 class="card-header border border-warning bg-black text-warning text-uppercase">Sobre o Retro Game Center</h5>
          <div class="card-body py-3">
            <p class="card-text text-justify">Retro Game Center é um site voltado para amantes de jogos, em especial, aqueles que têm interesse em começar a desenvolver jogos.</p>
            <p class="card-text text-justify">O Mural é o lugar onde os jogos enviados pela comunidade ficam expostos para qualquer um jogar. Qualquer usuário cadastrado pode enviar o seu jogo e, após avaliação do conteúdo, o jogo é inserido no mural.<br>
            O Fórum é onde todas as discussões relacionadas à jogos e ao desenvolvimento de jogos são feitas. Além disso, é no Fórum onde há os tutoriais ensinando o passo-a-passo de como desenvolver jogos em HTML5.</p>
          </div>
        </div>
      
        <div class="card my-3 mx-1 border border-warning">
          <h5 class="card-header border border-warning bg-black text-warning text-uppercase">Missão e Valores</h5>
          <div class="card-body py-3">
            <p class="card-text text-justify">Sendo um site focado em jogos e no desenvolvimento destes, temos como missão construir e alimentar a criação de jogos no Brasil: nosso país é o maior consumidor de jogos eletrônicos da America Latina, e mesmo assim temos pouco espaço no cenário de desenvolvimento - apesar de que nos ultimos anos nossa presença tenha aumentado.</p>
            <p class="card-text text-justify">Disponibilizando tutoriais e um lugar para criadores exibirem os seus projetos, acreditamos termos o potencial de erguer uma grande comunidade de desenvolvedores de games.</p>
            <p class="card-text text-justify">Inicialmente estamos dando ênfase à tecnologia HTML5, por permitir a criação de jogos multi-plataforma para a Web, mas no futuro, planejamos expandir os horizontes e fornecer suporte à outras tecnologias.</p>
          </div>
        </div>

        <div class="card my-3 mx-1 border border-warning">
          <h5 class="card-header border border-warning bg-black text-warning text-uppercase">Fale Conosco</h5>
          <div class="card-body py-3">
            <p class="card-text">Queremos saber a sua opinião e ouvir críticas e sugestões sobre o nosso site! Fale conosco através deste e-mail: <a href="#">RetroG.Center@gmail.com</a></p>
          </div>
        </div>

  </div>
  <!-- conteudo da pagina -->
  
  <?php if(isset($get)) $get->free_result(); ?>
  
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