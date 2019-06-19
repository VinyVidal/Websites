<?php include_once("../lib/lib.php");
    session_start();
    if(isset($_SESSION['user'])){
        $idUser = $_SESSION['user']['idUser'];
        
        $sql = $con->prepare("SELECT * FROM tbl_usuarios WHERE id_User = ?");
        $sql->bind_param("s", $idUser);
        $sql->execute();
        $get = $sql->get_result();
        $dados = $get->fetch_assoc();
    }

    $sql = $con->prepare("SELECT * FROM tbl_categorias_forum");
    $sql->execute();
    $getCategorias = $sql->get_result();
    $total = $getCategorias->num_rows;
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/function.js"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/index.css">
	
	<link rel="icon" href="../img/iconSite.png">
    <title>Fórum - Retro Game Center</title>
  </head>
  
  <?php
        if(isset($_SESSION['modalAlerta']))
        {
          showModal($_SESSION['modalAlerta']['titulo'], $_SESSION['modalAlerta']['mensagem'], $_SESSION['modalAlerta']['tipo']);
        }
        unset($_SESSION['modalAlerta']); // parar de exibir o modal
    ?>
  
  <body class="bg-light">

    <!-- navbar Menu-->
    <nav class="navbar navbar-expand-md navbar-dark bg-black" >
        <a class="navbar-brand" href="/Retro Game Center/forum/"><img src="../img/LogoForum.png"></a>
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
                <a class="nav-link" href="categoria.php?id=2"><img src="https://img.icons8.com/office/17/000000/outline.png"> Tutoriais</a>
            </li>
            <li class="nav-item mr-3">
                <a class="nav-link" href="../about.php"><img src="https://img.icons8.com/cotton/20/000000/info.png"> Sobre</a>
            </li>
            </ul>
        </div>
    </nav>
    <!-- navbar Menu-->
    
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
            <form action="../php_action/forum/gerenciarTopicos.php" method="POST">
                <input name="idTopicoDestrancar" id="idTopicoDestrancar" type="hidden">
                <input name="nomeTopicoDestrancar" id="nomeTopicoDestrancar" type="hidden">
                <div class="button-group">
                    <button name="btnDestrancar" class="btn border border-warning text-warning font-weight-bold mt-2 float-right">Destrancar</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fim modal -->

    <!-- Modal Adicionar Categoria -->
    <div class="modal fade" id="addCategoria" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
      <div class="modal-dialog border border-warning" role="document">
        <div class="modal-content text-warning bg-light">
          <div class="modal-header border-warning">
            <h5 class="modal-title" id="titulo">Adicionar nova categoria</h5>
            <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <!-- Form -->
            <form method="POST" action="../php_action/forum/novaCategoria.php">
              <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label for="nameCategoria">Nome da nova categoria</label>
                      <input type="text" name="nameCategoria" id="nameCategoria" class="form-control">
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label for="descCategoria">Breve descrição da categoria</label>
                      <textarea name="descCategoria" id="descCategoria" class="form-control" row="1"></textarea>
                    </div>
                  </div>
                </div>
              <div class="modal-footer border-warning">
                  <button type="submit" class="btn btn-success" name="addCategoria">Adicionar</button>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
              </div>
            </form>
            <!-- Fim form-->
          </div>
        </div>
      </div>
    </div>
    <!-- Fim modal -->
    
    <!-- modal de alterar titulo e tipo do topico -->
    <div class="modal fade" id="editarTopico" tabindex="-1" role="dialog" aria-labelledby="editarTopico" aria-hidden="true">
      <div class="modal-dialog border border-warning" role="document">
        <div class="modal-content text-warning bg-light">
          <div class="modal-header border-warning">
            <h5 class="modal-title" id="titulo">Editar Titulo e Tipo do Tópico</h5>
            <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <!-- Form -->
            <form method="POST" action="../php_action/forum/gerenciarTopicos.php">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="altNomeCategoria">Novo Nome do Tópico</label>
                            <input type="text" name="altNomeTopico" id="altNomeTopico" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="altDescCategoria">Novo Tipo do Tópico</label>
                            <select class="form-control" id="altTipoTopico" name="altTipoTopico">
                                <option value="discussao" selected>Discussão</option>
                                <option value="aviso">Aviso</option>
                                <option value="duvida">Dúvida</option>
                                <option value="tutorial">Tutorial</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <input type="hidden" id="idEditarTopico" name="idEditarTopico">
                

              <div class="modal-footer border-warning">
                  <button type="submit" class="btn btn-success" name="btnEditarTopico">Alterar</button>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
              </div>
            </form>
            <!-- Fim form-->
          </div>
        </div>
      </div>
    </div>
    <!-- fim modal -->
    
    <!-- modal de alterar titulo e descricao da categoria -->
    <div class="modal fade" id="tituloDescGen" tabindex="-1" role="dialog" aria-labelledby="tituloDescGen" aria-hidden="true">
      <div class="modal-dialog border border-warning" role="document">
        <div class="modal-content text-warning bg-light">
          <div class="modal-header border-warning">
            <h5 class="modal-title" id="titulo">Editar Titulo e Descrição</h5>
            <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <!-- Form -->
            <form method="POST" action="../php_action/forum/gerenciarCategorias.php">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="altNomeCategoria">Novo Nome da Categoria</label>
                            <input type="text" name="altNomeCategoria" id="altNomeCategoria" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="altDescCategoria">Nova Descrição da Categoria</label>
                            <textarea type="text" name="altDescCategoria" id="altDescCategoria" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                    <input type="hidden" id="idCategoria" name="idCategoria">
                

              <div class="modal-footer border-warning">
                  <button type="submit" class="btn btn-success" name="alterarCategoria">Alterar</button>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
              </div>
            </form>
            <!-- Fim form-->
          </div>
        </div>
      </div>
    </div>
    <!-- fim modal -->
    
    <!-- Modal excluir Categoria -->
    <div class="modal fade" id="excluirCategoria" tabindex="-1" role="dialog" aria-labelledby="excluir" aria-hidden="true">
      <div class="modal-dialog border border-warning" role="document">
        <div class="modal-content text-warning bg-light">
          <div class="modal-header border-warning">
            <h5 class="modal-title" id="titulo">Excluir Categoria</h5>
            <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <div class="card">
                <div class="card-body">
                    <h4>Atenção, você está prestes a realizar uma ação irreversivel.</h4>
                    <p>Após excluir a categoria as seguintes mudanças serão aplicadas:</p>
                    <ul class="list-group">
                        <li class="list-group-item">Exclusão permanente da categoria<br>
                            Exclusão permanente dos tópicos da categoria<br>
                            Exclusão permanente das postagens da categoria
                        </li>
                    </ul>
                </div>
            </div>
            <form action="../php_action/forum/gerenciarCategorias.php" method="POST">
                <input type="hidden" name="excIdCategoria" id="excIdCategoria">
                <div class="button-group">
                    <button name="btnExcluir" class="btn border border-warning text-warning font-weight-bold mt-2 float-right">Excluir</button>
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
                    <p>Após excluir a categoria as seguintes mudanças serão aplicadas:</p>
                    <ul class="list-group">
                        <li class="list-group-item">Exclusão permanente do tópico<br>
                            Exclusão permanente das postagens do tópico
                        </li>
                    </ul>
                </div>
            </div>
            <form action="../php_action/forum/gerenciarTopicos.php" method="POST">
                <input type="hidden" name="excIdTopico" id="excIdTopico">
                <input type="hidden" name="excNomeTopico" id="excNomeTopico">
                <div class="button-group">
                    <button name="btnExcluir" class="btn border border-warning text-warning font-weight-bold mt-2 float-right">Excluir</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fim modal -->
    
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
            <form action="../php_action/forum/gerenciarTopicos.php" method="POST">
                <input type="hidden" name="idTopicoTrancar" id="idTopicoTrancar">
                <input type="hidden" name="nomeTopicoTrancar" id="nomeTopicoTrancar">
                <div class="button-group">
                    <button name="btnTrancar" class="btn border border-warning text-warning font-weight-bold mt-2 float-right">Trancar</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Fim modal -->
    
    <!-- modal de mudar topico de categoria -->
    <div class="modal fade" id="novaCategoriaTopico" tabindex="-1" role="dialog" aria-labelledby="novaCategoriaTopico" aria-hidden="true">
      <div class="modal-dialog border border-warning" role="document">
        <div class="modal-content text-warning bg-light">
          <div class="modal-header border-warning">
            <h5 class="modal-title" id="titulo">Mudar Tópico de Categoria</h5>
            <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <?php
                $sql = $con->prepare("SELECT id_Categoria, nome_Categoria FROM tbl_categorias_forum");
                $sql->execute();
                $getCat = $sql->get_result();
                $total = $getCat->num_rows;
            ?>
            <!-- Form -->
            <form method="POST" action="../php_action/forum/gerenciarTopicos.php">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="altDescCategoria">Categorias Existentes</label>
                            <?php if($total > 0){ ?>
                                <select class="form-control" id="categoriasDisponiveis" name="categoriasDisponiveis">
                                    <?php while($categoria = $getCat->fetch_assoc()){ ?>
                                        <option value="<?php echo $categoria['id_Categoria']; ?>"><?php echo $categoria['nome_Categoria']; ?></option>
                                        
                                    <?php } ?>
                                </select>
                            <?php }else{ ?>
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Nenhuma categoria a ser exibida</h6>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="idEditarCategoriaTopico" name="idEditarCategoriaTopico">
                <input type="hidden" id="nomeTopicoMudarCategoria" name="nomeTopicoMudarCategoria">    

              <div class="modal-footer border-warning">
                  <button type="submit" class="btn btn-success" name="btnMudarCategoria">Mudar</button>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
              </div>
            </form>
            <!-- Fim form-->
          </div>
        </div>
      </div>
    </div>
    <!-- fim modal -->
 
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

    <?php 
    /**
     * condição verifica se o user esta logado, assim verificando se aparecera a nav abaixo ou nao
     */
        if(isset($_SESSION['user'])){
            $sql = $con->prepare("SELECT COUNT(*) FROM tbl_posts_forum WHERE idUser_Post = ?");
            $sql->bind_param("i", $idUser);
            $sql->execute();
            $get = $sql->get_result();
            $qtdePosts = $get->fetch_array()['0'];
            if($qtdePosts == 0)
            {
    ?>
    <!-- card que aparece caso user nao tiver nenhum post no forum -->
        <div class="card mt-3 mx-5 border border-warning alert alert-warning text-body font-weight-normal">
            <div class="card-body">
                <div class="ml-3 mr-1">
                    Bem-vindo ao Fórum do Retro Game Center! Por que você não começa se apresentando para o pessoal? Basta clicar <a href="categoria.php?id=3">neste link</a> e selecionar a aba "Novo Tópico"!
                </div>
            </div>
        </div>
    <!-- fim card-->
    <?php   }
        }?>

    <!-- NOME DA SESSAO -->
    <div class="card my-3 mx-3 border border-warning bg-black">
        <div class="card-body text-warning">
            Retro Game Center
        </div>
    </div>
    
    <!-- nav para de categorias e ultimas atividades -->
    <ul class="nav nav-tabs mx-3" id="nav-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active text-warning" href="#categorias" role="tab" data-toggle="tab">Categorias</a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-warning" href="#lastActivity" role="tab" data-toggle="tab">Últimas Atividades</a>
        </li>
        
        <!-- verificar se o usuario possui credenciais suficiente para poder gerenciar o forum -->
        <?php if(isset($_SESSION['user'])) {
            if($dados['nivel_User'] > 2){ ?>
            <li class="nav-item">
                <a class="nav-link text-warning" href="#gerenciar" role="tab" data-toggle="tab"> | <img src="https://img.icons8.com/small/22/000000/maintenance.png"> Gerenciar</a>
            </li>
        <?php }
            }?>
    </ul>
    <!-- fim nav, inicio do conteudos das navs(respctivamente) -->
    <div class="tab-content mt-2" id="nav-tabContent">
        <!-- conteudo CATEGORIAS -->
        <div class="tab-pane fade show active" id="categorias" role="tabpanel" aria-labelledby="categorias">
            
            <!-- botao de criar nova categoria -->
            <ul class="nav nav-tabs mx-3 my-2">
                <!-- verificacao de nivel de usuario -->
                <?php if(isset($_SESSION['user'])) {
                    if($dados['nivel_User'] > 2){ ?>
                    <li class="nav-item">
                        <!-- botao que para criar novas categorias, somente mod++ tem acesso a ele -->
                            <button type="submit" name="addCategoria" class="btn btn-outline-warning mr-1 my-1" data-toggle="modal" data-target="#addCategoria"><img src="https://img.icons8.com/ios-glyphs/30/000000/plus.png"> Nova Categoria</button> 
                    </li>
                <?php }else{}
                    }?>
                <!-- fim verificacao de nivel de usuario -->            
            </ul>
            
            <!-- tabela com os titulos topicos e etc -->
            <div class="table-responsive-sm mx-2">
                <table class="table table-striped table-bordered mx-1">
                    <!-- Titulos -->
                    <thead class="thead bg-black text-warning">
                        <tr align="center">
                            <th scope="col"><img src="https://img.icons8.com/color/20/000000/category.png"> CATEGORIA</th>
                            <th scope="col" style="widht: 10%;"><img src="https://img.icons8.com/color/20/000000/topic.png"> TÓPICOS</th>
                            <th scope="col"><img src="https://img.icons8.com/ultraviolet/20/000000/comments.png"> POSTAGENS</th>
                            <th scope="col">ÚLTIMA POSTAGEM</th>
                        </tr>
                    </thead>
                
                    <!-- conteudo respectivo aos titulos -->
                    <tbody>
                        <?php if ($total > 0){
                            while($linha=$getCategorias->fetch_assoc()){
                                //query busca o ultimo post da categoria
                                $sql = $con->prepare("SELECT * FROM tbl_posts_forum, tbl_topicos_forum WHERE (SELECT idCategoria_Topico FROM tbl_topicos_forum WHERE id_Topico = tbl_posts_forum.idTopico_Post) = ? AND id_Topico = idTopico_Post ORDER BY tbl_posts_forum.data_Post DESC LIMIT 1");
                                $sql->bind_param("s", $linha['id_Categoria']);
                                $sql->execute();
                                $getLastPost = $sql->get_result();
                                $lastPost = $getLastPost->fetch_assoc();
                                $totalTopico = $getLastPost->num_rows;
                                
                                //dados de quem fez a ultima postagem
                                $sql = $con->prepare("SELECT * FROM tbl_usuarios WHERE id_User = ?");
                                $sql->bind_param("s", $lastPost['idUser_Post']);
                                $sql->execute();
                                $user = $sql->get_result()->fetch_assoc();
                        ?>
                        <tr>
                            <td><a href="categoria.php?id=<?php echo $linha['id_Categoria'];?>"><img src="https://img.icons8.com/material-outlined/16/000000/comment-discussion.png"> <?php echo $linha['nome_Categoria'];?></a><br><a class="text-warning text-sm-left"><?php echo $linha['descricao_Categoria'];?></a>
                            </td>
                            
                            <td align="center"><?php echo topicosFromCategory($con, $linha['id_Categoria']);?></td>
                            <td align="center"><?php echo postsFromCategory($con, $linha['id_Categoria']);?></td>
                            <td align="center">
                                <?php if($totalTopico > 0){ ?>
                                    <img class="rounded float-left mr-1" src="../<?php echo $user['img_User'];?>" style="widht: 10px; height: 85px;">
                                    <img src="https://img.icons8.com/office/16/000000/gender-neutral-user.png"><a class="text-warning" href="../user/profileView.php?userv=<?php echo $lastPost['idUser_Post'];?>"> <?php echo $user['nome_User'];?></a><br>
                                    <a href="topico.php?id=<?php echo $lastPost['idTopico_Post'];?>"><?php echo $lastPost['titulo_Topico'];?></a><br>
                                    <img class="mr-2" src="https://img.icons8.com/office/16/000000/calendar.png"><?php $date = $lastPost['data_Post']; echo date('d/m/Y H:i',  strtotime($date));?>
                                <?php }else{echo "A categoria ainda não possui tópicos";} ?>
                            </td>
                        </tr>
                        <?php } //fim while
                        }else{echo "<tr class='bg-light border border-light'><td colspan='4'><div class='alert alert-warning text-center'>Nenhum categoria encontrada.</div></td></tr>";} //fim if?> 
                    </tbody>
                </table>
            </div>
        </div>

        <!-- conteudo da "ultimas atividades" -->
        <div class="tab-pane fade" id="lastActivity" role="tabpanel" aria-labelledby="lastActivity">
            <?php
        $sql = $con->prepare("SELECT * FROM tbl_posts_forum, tbl_usuarios, tbl_topicos_forum WHERE id_User = idUser_Post AND id_Topico = idTopico_Post AND CHAR_LENGTH(titulo_Topico) > 0 ORDER BY data_Post DESC LIMIT 15");
        $sql->execute();
        $getPosts = $sql->get_result();
        $totalPost = $getPosts->num_rows;
    ?>
    <div class="row">
        <div class="col">
            <?php if ($totalPost > 0){
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
                            <h5 class="card-title"><img src="https://img.icons8.com/material/16/000000/user.png"> <a class="text-warning" href="../user/profileView.php?userv=<?php echo $post['id_User'];?>"><?php echo $post['nome_User'];?></a></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo $atividade.' - <a class="text-warning" href=topico.php?id='.$dados['id_Topico'].'>'.$dados['titulo_Topico'].'</a> - em <a class="text-warning" href="categoria.php?id='.$dados['id_Categoria'].'">'.$dados['nome_Categoria'].'</a>'; ?></h6>
                            <!-- p class="card-text border-top mt-md-2 pt-md-2"><?php echo sanitizeTableCellText($post['conteudo_Post'], 100) ?></p> -->
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col">
                                    <img class="mr-2" src="https://img.icons8.com/office/16/000000/calendar.png"><?php $date = $post['data_Post']; echo date('d/m/Y H:i',  strtotime($date));?>
                                </div>
                                <div class="col text-right">
                                    <a href="topico.php?id=<?php echo $post['idTopico_Post'];?>&#post<?php echo $post['id_Post'];?>" class="card-link text-warning">Ver Postagem</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <!-- fim if -->
                <?php }?>
            <!-- fim while -->    
            <?php }else{echo "<div class='alert alert-warning text-center mx-4'>Nenhum registro de atividades.</div>";} ?>
        </div>
    </div>
        </div>
        
        
        
        <!-- conteudo respectivo a aba gerenciar -->
        <div class="tab-pane" id="gerenciar" role="tabpanel" aria-labelledby="gerenciar">
            <ul class="nav nav-tabs mx-3 my-2">
                <li class="nav-item">
                    <a class="shadow-none btn border border-warning text-warning nav-item" href="#gerenciarCategorias" role="tab" data-toggle="tab">Categorias</a>                    
                </li>
                
                <li class="nav-item">
                    <a class="shadow-none btn border border-warning text-warning nav-item mx-2" href="#gerenciarTopicos" role="tab" data-toggle="tab">Tópicos</a>                    
                </li>
            </ul>
            
            <div class="tab-content">
                <!-- query com os dados necessarios para o gerenciamento -->
                <?php
                    $sql = $con->prepare("SELECT id_Categoria, idUser_Categoria, nome_Categoria, descricao_Categoria FROM tbl_categorias_forum");
                    $sql->execute();
                    $getDadosCat = $sql->get_result();
                    $total = $getDadosCat->num_rows;
                ?>
                <!-- fim query -->
                
                <!-- conteudo do gerenciamento de topicos -->
                <div role="tabpanel" class="tab-pane active mt-3 mx-2" id="gerenciarCategorias">
                    <div class="table-responsive-sm mx-2">
                        <!-- verificar se existe algum registro>
                        <?php if($total > 0){ ?>
                            <!-- tabela de gerenciamente de categorias -->
                            <table class="table table-striped table-bordered mx-1">
                                <!-- titulos -->
                                <thead class="thead bg-black text-warning">
                                    <th>ID</th>
                                    <th>Usuário</th>
                                    <th>Titulo</th>
                                    <th>Descrição</th>
                                    <th>Ações</th>
                                </thead>
                                
                                <!-- conteudo da table -->
                                <?php while($linha = $getDadosCat->fetch_assoc()){
                                $sql = $con->prepare("SELECT nome_User FROM tbl_usuarios WHERE id_User = ?");
                                $sql->bind_param("s", $linha['idUser_Categoria']);
                                $sql->execute();
                                $getDadosUser = $sql->get_result();
                                $dadosUser = $getDadosUser->fetch_assoc();
                                ?>
                                    <tbody>
                                        <td><?php echo $linha['id_Categoria'];?></td>
                                        <td><a class="text-warning" href="../user/profileView.php?userv=<?php echo $linha['idUser_Categoria'];?>"><?php echo $dadosUser['nome_User'];?></a></td>
                                        <td><?php echo $linha['nome_Categoria'];?></td>
                                        <td><?php echo $linha['descricao_Categoria'];?></td>
                                        <!-- dropdown com as acoes disponiveis -->
                                        <td><div class="btn-group dropleft">
                                              <button type="button" class="shadow-none btn bg-black border border-warning text-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Gerenciar
                                              </button>
                                              <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#tituloDescGen" name="tituloDescGen" data-toggle="modal" data-target="#tituloDescGen" onclick="(function(){setModalInput('idCategoria', <?php echo $linha['id_Categoria'];?>); setModalInput('altNomeCategoria', '<?php echo $linha['nome_Categoria'];?>'); setModalInput('altDescCategoria', '<?php echo $linha['descricao_Categoria'];?>');})()">Editar</a>
                                                <a class="dropdown-item" href="#excluirCategoria" data-toggle="modal" data-target="#excluirCategoria" onclick="(function(){setModalInput('excIdCategoria', '<?php echo $linha['id_Categoria'];?>');})()">Excluir</a>
                                              </div>
                                            </div>
                                        </td>
                                    </tbody>
                                <?php } //fim do while?>
                            </table>
                        <!-- fim da verifiacao de existencia de registros -->
                        <?php }else{echo "<div class='alert alert-warning'>Nenhum registro encontrado.</div>";} ?>
                    </div>
                </div>
                
                <!-- fim do conteudo de gerenciar categorias | inicio do conteudo de gerenciar topico-->
                <!-- query com os dados necessarios para o gerenciamento dos topicos -->
                <?php
                    $sql = $con->prepare("SELECT id_Topico, idCategoria_Topico, idUser_Topico, titulo_Topico, tipo_Topico, trancado_Topico FROM tbl_topicos_forum");
                    $sql->execute();
                    $getDadosTopico = $sql->get_result();
                    $total = $getDadosTopico->num_rows;
                ?>
                <!-- fim da query -->
                <div id="gerenciarTopicos" class="tab-pane mt-3 mx-2" role="tabpanel">
                    <div class="table-responsive-sm mx-2">
                        <!-- verificar se existe algum registro -->
                        <?php if($total > 0){ ?>
                            <!-- tabela de gerenciamento de topicos -->
                            <table class="table table-striped table-bordered mx-1">
                                <!-- titulos -->
                                <thead class="thead bg-black text-warning">
                                    <th>ID</th>
                                    <th>Usuário</th>
                                    <th>Titulo Tópico</th>
                                    <th>Tipo Tópico</th>
                                    <th>Trancado</th>
                                    <th>Ações</th>
                                </thead>
                                
                                <?php while($linha = $getDadosTopico->fetch_assoc()){ 
                                    if($linha['trancado_Topico'] == 1)
                                    {
                                        $trancado = "Sim";
                                    }else{
                                        $trancado = "Não";
                                    }
                                    
                                    //dados do user
                                    $sql = $con->prepare("SELECT nome_User FROM tbl_usuarios WHERE id_User = ?");
                                    $sql->bind_param("s", $linha['idUser_Topico']);
                                    $sql->execute();
                                    $getDadosUser = $sql->get_result();
                                    $dadosUser = $getDadosUser->fetch_assoc();
                                ?>
                                    <!-- conteudo da table -->
                                    <tbody>
                                        <td><?php echo $linha['id_Topico'];?></td>
                                        <td><a class="text-warning" href="../user/profileView.php?userv=<?php echo $linha['idUser_Topico'];?>"><?php echo $dadosUser['nome_User'];?></a></td>
                                        <td><?php echo $linha['titulo_Topico'];?></td>
                                        <td><?php echo $linha['tipo_Topico'];?></td>
                                        <td><?php echo $trancado;?></td>
                                        <!-- dropdown com as acoes disponiveis -->
                                        <td><div class="btn-group dropleft">
                                              <button type="button" class="shadow-none btn bg-black border border-warning text-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Gerenciar
                                              </button>
                                              <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#editarTopico" data-toggle="modal" data-target="#editarTopico" onclick="(function(){setModalInput('idEditarTopico', '<?php echo $linha['id_Topico'];?>'); setModalInput('altNomeTopico', '<?php echo $linha['titulo_Topico']; ?>');}())">Editar</a>
                                                <?php if($linha['trancado_Topico'] == 0){ ?>
                                                    <a class="dropdown-item" href="#trancaTopico" data-toggle="modal" data-target="#trancarTopico" onclick="(function(){setModalInput('idTopicoTrancar', '<?php echo $linha['id_Topico'];?>'); setModalInput('nomeTopicoTrancar', '<?php echo $linha['titulo_Topico'];?>');})()">Trancar</a>
                                                <?php }else{?>
                                                    <a class="dropdown-item" href="#destrancaTopico" data-toggle="modal" data-target="#destrancarTopico" onclick="(function(){setModalInput('idTopicoDestrancar', '<?php echo $linha['id_Topico'];?>'); setModalInput('nomeTopicoDestrancar', '<?php echo $linha['titulo_Topico'];?>');})()">Destrancar</a>
                                                <?php } ?>
                                                <a class="dropdown-item" href="#novaCategoriaTopico" data-toggle="modal" data-target="#novaCategoriaTopico" onclick="(function(){setModalInput('idEditarCategoriaTopico', '<?php echo $linha['id_Topico'];?>'); setModalInput('nomeTopicoMudarCategoria', '<?php echo $linha['titulo_Topico'];?>');})()">Mudar de Categoria</a>
                                                <a class="dropdown-item" href="categoria.php?id=<?php echo $linha['idCategoria_Topico'];?>">Ver</a>
                                                <a class="dropdown-item" href="#excluirTopico" data-toggle="modal" data-target="#excluirTopico" onclick="(function(){setModalInput('excIdTopico', '<?php echo $linha['id_Topico'];?>'); setModalInput('excNomeTopico', '<?php echo $linha['titulo_Topico'];?>');})()">Excluir</a>
                                              </div>
                                            </div>
                                        </td>
                                    </tbody>
                                <?php } //fim do while?>
                            </table>
                        <!-- fim da verificacao de existencia de registro -->
                        <?php }else{echo "<div class='alert alert-warning'>Nenhum registro encontrado.</div>";} ?>
                    </div>
                </div>
            </div>
            
        </div>
    </div>


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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  </body>
</html>