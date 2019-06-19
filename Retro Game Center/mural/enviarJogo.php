<?php include_once("../lib/lib.php"); 
session_start();

if(!isset($_SESSION['user'])) // se nao estiver logado
{
	// redirecionar para a pg de login
	header('location: ../user/login.php');
}
else
{
	$idUser = $_SESSION['user']['idUser'];

    $sql = $con->prepare("SELECT * FROM tbl_usuarios WHERE id_User = ?");
    $sql->bind_param("s", $idUser);
    $sql->execute();
    $get = $sql->get_result();
    $dados = $get->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({
          google_ad_client: "ca-pub-7463114109083817",
          enable_page_level_ads: true
      });
     </script> -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href="../css/style.css">

<!-- coloquei o include do jquery para o modal funcionar -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

<?php
	if(isset($_SESSION['modalAlerta']))
	{
		showModal($_SESSION['modalAlerta']['titulo'], $_SESSION['modalAlerta']['mensagem'], $_SESSION['modalAlerta']['tipo']);
	}

unset($_SESSION['modalAlerta']); // parar de exibir o modal
?>
<link rel="icon" href="../img/iconSite.png">
<title>Enviar jogo - Retro Game Center</title>
</head>

<body class="bg-dark">


<!-- navbar Menu-->
<nav class="navbar navbar-expand-md navbar-dark bg-black">
  <a class="navbar-brand" href="../"><img src="../img/Logo10.png"></a>
  		<div class="d-flex flex-row order-3 order-lg-3">
	        <ul class="navbar-nav flex-row">
	            <li class="nav-item dropdown mr-3"><a class="nav-link dropdown-toggle" id="navbarDropdownUser" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="nav-user" src="<?php echo '../'.$dados['img_User'];?>"><?php echo $dados['nome_User'];?></a>
                    <div class="dropdown-menu border-warning" aria-labelledby="navbarDropdownUser">
                      <a class="dropdown-item" href="../user/profile.php"><img src="https://img.icons8.com/material/16/000000/gender-neutral-user.png" class="pr-1"> Profile</a>
                      <a class="dropdown-item" href="../user/mensagensPrivadas.php"><img src="https://img.icons8.com/material/16/000000/sms.png"> Mensagens <?php echo showNewMessages($con, $dados['id_User']); ?></a>
                      <a class="dropdown-item" href="../php_action/logout.php"><img src="https://img.icons8.com/metro/16/000000/exit.png" class="pr-1"> Sair</a>
                    </div>
                  </li>
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
		  <a class="dropdown-item active"><img src="https://img.icons8.com/material-two-tone/24/000000/upload-to-cloud.png"> Envie seu Jogo <span class="sr-only">(current)</span></a>
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


<!-- Formulario para enviar jogo-->
<div class="container bg-light mr-auto pt-3 pb-3">
	<div class="row">
		<div class="col">
			<h3>Enviar o seu jogo</h3>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<p> Envie o seu jogo preenchendo os dados abaixo. Antes de enviar, recomendamos que você leia as <a href="../forum/topico.php?id=4">regras de envio de jogos</a> contidas no forum do site.
		</div>
	</div>
	<form enctype="multipart/form-data" name="formEnviarJogo" method="POST" action="../php_action/uploadJogo.php" >
		<div class="form-group row">
			<label for="txtNomeJogo" class="col-sm-4 col-md-3 col-lg-2 col-form-label">Nome do jogo:</label>
			<div class="col-sm-10 col-md-8">
				<input type="text" name="txtNomeJogo" id="txtNomeJogo" class="form-control"  maxlength="50" required>
			</div>
		</div>
		
		<div class="form-group">
			<label for="txtDescricao" class="form-label">Escreva uma breve descrição para o jogo: </label>
			<div class="col-sm-10">
			<textarea name="txtDescricao" id="txtDescricao" rows="5" class="form-control" maxlength="255" required></textarea>
			</div>
		</div>
		
		<div class="form-group row">
			<label for="capaJogo" class="col-sm-4 col-form-label">Escolha uma imagem de capa para o jogo: </label>
			<div class="col-lg-5">
				<input type="hidden" name="MAX_FILE_SIZE" value="1048576" /> 
				<input type="file" name="capaJogo" id="capaJogo" class="form-control-file" accept="image/*" required>
			</div>
		</div>
		
		<div class="form-group row">
			<label for="arquivosJogo" class="col-sm-4 col-form-label">Envie o .ZIP com os arquivos do jogo: </label>
			<div class="col-lg-5">
				<input type="hidden" name="MAX_FILE_SIZE" value="52428800" /> 
				<input type="file" name="arquivoJogo" id="arquivoJogo" class="form-control-file" accept="application/zip" required>
			</div>
		</div>
		
		<div class="row">
			<div class="col">
				<p><b>OBS:</b> Ao clicar em Enviar você concorda que o arquivo do jogo (código-fonte) pode ser baixado, visualizado e compartilhado por outros usuários.</p>
			</div>
		</div>
		
		<div class="form-group row">
			<div class="col-lg-5">
				<button type="submit" class="btn btn-warning" name="enviarJogo">Enviar</button>
			</div>
		</div>
		
		<div class="row">
			<div class="col">
				<p>Após o envio, seu jogo entrará no estado de solicitação, aguarde até um admin aprovar a sua solicitação e dentro de pouco tempo o seu jogo estará na pagina do mural.</p>
			</div>
		</div>
		
	</form>
	
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
<!-- Formulario para enviar jogo-->


<!-- navbar Rodape-->
<footer class="page-footer font-small text-light bg-black">

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


<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>