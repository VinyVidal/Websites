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
  
  // validando o GET
  if(isset($_GET['id']))
  {
    if(filter_var($_GET['id'], FILTER_VALIDATE_INT))
    {
      $idCategoria = $_GET['id'];
    }
    else
    {
      $_SESSION['modalAlerta']['titulo'] = 'Erro';
      $_SESSION['modalAlerta']['mensagem'] = 'Categoria nao encontrada!';
      $_SESSION['modalAlerta']['tipo'] = 'error';
      header('location: /');
      die('Aguarde um momento...');
    }
  }
  
  $sql = $con->prepare("SELECT COUNT(*) FROM tbl_posts_forum AS p1, tbl_topicos_forum WHERE (SELECT idCategoria_Topico FROM tbl_topicos_forum WHERE id_Topico = p1.idTopico_Post) = ? AND id_Topico = p1.idTopico_Post AND (SELECT MAX(id_Post) FROM tbl_posts_forum AS p2 WHERE p2.idTopico_Post = tbl_topicos_forum.id_Topico) = p1.id_Post");
  $sql->bind_param("s", $idCategoria);
  try{
    $sql->execute();
    $getNumTopicos = $sql->get_result();
    $numTopicos = $getNumTopicos->fetch_array()['0'];
    }catch(Exception $e){
        $_SESSION['modalAlerta']['titulo'] = 'Erro';
        $_SESSION['modalAlerta']['mensagem'] = 'Não foi possível recuperar os dados. Tente novamente mais tarde!';
        $_SESSION['modalAlerta']['tipo'] = 'error';
        header('location: /');
    }
    
    //query que vai mostrar o nome da categoria
    $sql = $con->prepare("SELECT nome_Categoria FROM tbl_categorias_forum WHERE id_Categoria = ?");
    $sql->bind_param("s", $idCategoria);
    $sql->execute();
    
    if($sql){
        $get = $sql->get_result();
        $dado = $get->fetch_array();
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
    <script src="js/function.js"></script>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
      <link rel="stylesheet" href="../css/style.css">
      <link rel="stylesheet" href="../css/paginacao.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="icon" href="../img/iconSite.png">
    <link rel="text/javascript" href="js/function.js">
    <?php
    if(isset($_SESSION['modalAlerta']))
    {
        showModal($_SESSION['modalAlerta']['titulo'], $_SESSION['modalAlerta']['mensagem'], $_SESSION['modalAlerta']['tipo']);
    }

    unset($_SESSION['modalAlerta']); // parar de exibir o modal
    ?>
    <title><?php echo $dado['nome_Categoria'];?> - Retro Game Center Fórum</title>
  </head>
  <body class="bg-light">
    <!-- navbar Menu-->
    <nav class="navbar navbar-expand-md navbar-dark bg-black" >
        <a class="navbar-brand" href="/Retro Game Center/forum/"><img src="../img/LogoForum.png"></a>
        <div class="d-flex flex-row order-3 order-lg-3">
            <ul class="navbar-nav flex-row">
                <?php
                    if(!isset($_SESSION['user'])){ ?>
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
                <a class="nav-link" href="/Retro Game Center/"><img src="https://img.icons8.com/dusk/20/000000/visual-game-boy.png"> Home </a>
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
            <li class="breadcrumb-item active text-white" aria-current="page"><?php echo $dado['0'];?></li>
        </ol>
    </nav>

    <!-- NOME DA SESSAO -->
    <div class="card my-3 mx-3 border border-warning bg-black">
        <div class="card-body text-warning">
            <h6><?php echo $dado['0'];?></h6>
        </div>
    </div>
    <!-- nav para de topicos e ultimas atividades -->
    <?php
    $tabTop = "show active";
    $tabNewTop = "";
    
    if($_SESSION['preview'])
    {
        $tabTop = "";
        $tabNewTop = "show active";
    }
    ?>
    <ul class="nav nav-tabs mx-3" id="nav-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?php echo $tabTop; ?> text-warning" href="#topicos" role="tab" data-toggle="tab">Tópicos da Sessão</a>
        </li>
        
        <!-- se o user estiver logado podera ter acesso a pagina de criar novos topicos -->
        <?php if(isset($_SESSION['user'])){ ?>
            <li>
                <a class="nav-link  <?php echo $tabNewTop; ?> text-warning" href="#newTopic" role="tab" data-toggle="tab">Novo Tópico</a>
            </li>
        <?php } else{}?>
    </ul>
    <!-- fim nav, inicio do conteudos das navs(respctivamente) -->
    <div class="tab-content mt-2" id="nav-tabContent">
        <!-- conteudo CATEGORIAS -->
        <div class="tab-pane fade <?php echo $tabTop; ?>" id="topicos" role="tabpanel" aria-labelledby="categorias">  
        
        <?php
            if($numTopicos > 0){ 
                $topicosPorPg = 10; // quantidade de topicos por pagina

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
                
                $exceto = ($page * $topicosPorPg) - $topicosPorPg; // offset
                if($page == 1)
                {
                  $sql = $con->prepare("SELECT *, (SELECT nome_User FROM tbl_usuarios WHERE id_User = tbl_topicos_forum.idUser_Topico) AS nome_User FROM tbl_posts_forum AS p1, tbl_topicos_forum WHERE (SELECT idCategoria_Topico FROM tbl_topicos_forum WHERE id_Topico = p1.idTopico_Post) = ? AND id_Topico = p1.idTopico_Post AND (SELECT MAX(id_Post) FROM tbl_posts_forum AS p2 WHERE p2.idTopico_Post = tbl_topicos_forum.id_Topico) = p1.id_Post ORDER BY p1.data_Post DESC LIMIT ?");
                  $sql->bind_param("ii", $idCategoria, $topicosPorPg);
                }
                else
                {
                  $sql = $con->prepare("SELECT *, (SELECT nome_User FROM tbl_usuarios WHERE id_User = tbl_topicos_forum.idUser_Topico) AS nome_User FROM tbl_posts_forum AS p1, tbl_topicos_forum WHERE (SELECT idCategoria_Topico FROM tbl_topicos_forum WHERE id_Topico = p1.idTopico_Post) = ? AND id_Topico = p1.idTopico_Post AND (SELECT MAX(id_Post) FROM tbl_posts_forum AS p2 WHERE p2.idTopico_Post = tbl_topicos_forum.id_Topico) = p1.id_Post ORDER BY p1.data_Post DESC LIMIT ? OFFSET ?");
                  $sql->bind_param("iii", $idCategoria, $topicosPorPg, $exceto);
                }

                $sql->execute();
                $getTopicos = $sql->get_result(); // Todas as msgs ao usuario da sessao
                
                if($getTopicos->num_rows > 0)
                {
        ?>
            <!-- tabela com os titulos topicos e etc -->
            <div class="table-responsive-sm mx-2">
                
                <table class="table table-bordered mx-1">
                    <!-- Titulos -->
                    <thead class="thead bg-black text-warning">
                        <tr align="center">
                            <th scope="col"><img src="https://img.icons8.com/color/20/000000/category.png"> TÓPICO</th>
                            <th scope="col" style="widht: 10%;"><img src="https://img.icons8.com/color/20/000000/visible.png" class="mr-1"> VISÃO GERAL</th>
                            <th scope="col">ÚLTIMA POSTAGEM</th>
                        </tr>
                    </thead>
                    
                    <!-- conteudo respectivo aos titulos -->
                    <tbody>
                    <?php
                        while($linha = $getTopicos->fetch_assoc()){
                        $dataTopico = new DateTime($linha['dataCriacao_Topico']);
                        
                        $sql = $con->prepare("SELECT COUNT(resposta_Post) FROM tbl_posts_forum WHERE idTopico_Post = ? AND resposta_Post = 1");
                        $sql->bind_param("s", $linha['id_Topico']);
                        $sql->execute();
                        $get = $sql->get_result();
                        $resposta = $get->fetch_array();
                        
                    ?>
                        <tr>
                            <td>
                                <?php if($linha['trancado_Topico'] == 0){ ?>
                                <img src="https://img.icons8.com/ios-glyphs/18/000000/topic.png" class="mr-2"><?php }else{echo '<img title="Topico Fechado" src="https://img.icons8.com/material-rounded/18/000000/lock.png">';}?> <?php echo $linha['tipo_Topico'];?> - <a href="topico.php?id=<?php echo $linha['id_Topico'];?>" class="text-warning"><?php echo $linha['titulo_Topico']; ?> </a><br>criado por <a href="../user/profileView.php?userv=<?php echo $linha['idUser_Topico']; ?>" class="text-warning"><?php echo $linha['nome_User']; ?></a>, <?php echo $dataTopico->format('d/m/y H:i');?>
                            </td>

                            <td align="center">
                                <?php echo $resposta['0'];?> Respostas<br><?php echo $linha['views_Topico']; ?> Visitas<br>
                            </td>

                            <!-- dados da ultima postagem -->
                            <?php 
                                $sql = $con->prepare("SELECT *, MAX(data_Post) FROM tbl_posts_forum WHERE idTopico_Post = ?");
                                $sql->bind_param("s", $linha['id_Topico']);
                                $sql->execute();
                                $getLastPost = $sql->get_result();
                                
                                while($lastPost = $getLastPost->fetch_assoc()){
                                    //dados do usuario que fez a ultima postagem
                                    $sql = $con->prepare("SELECT nome_User, img_User FROM tbl_usuarios WHERE id_User = ?");
                                    $sql->bind_param("s", $lastPost['idUser_Post']);
                                    $sql->execute();
                                    $getDados = $sql->get_result();
                                    $dadosUser = $getDados->fetch_assoc();
                                    $dataLast = new DateTime($lastPost['data_Post']);
                                    
                                
                            ?>

                            <td align="center">
                                <img src="../<?php echo $dadosUser['img_User'];?>" alt="icon" class="rounded float-left mr-1" style=" widht: 8em; height:8em;"><br><img src="https://img.icons8.com/office/16/000000/gender-neutral-user.png"><a class="text-warning" href="../user/profileView.php?userv=<?php echo $linha['idUser_Post'];?>"> <?php echo $dadosUser['nome_User'];?></a><br>
                                <img class="mr-2" src="https://img.icons8.com/office/16/000000/calendar.png"><?php $date = $linha['data_Post']; echo date('d/m/Y H:i:s',  strtotime($date));?>
                            </td>
                            <!-- fim do while -->
                            <?php } ?>
                        </tr>
                    </tbody>
                    <?php } ?>
                </table>
            </div>
            
            <!-- Pagination NAV -->
            <div class="row pt-3">
              <div class="col">
                <nav aria-label="Paginacao-topicos">
                    <ul class="pagination justify-content-center">
                      <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''?>">
                          
                        <a class="page-link" href="?id=<?php echo $idCategoria; ?>&pg=<?php echo $page-1?>" >
                          <span aria-hidden="true">&laquo;</span><span class="sr-only">Anterior</span>
                        </a>
                      </li>
                  <?php
                      $pages = ceil(($numTopicos*1.0)/$topicosPorPg); // quantidade de pags
                      for($j = 0; $j < $pages; $j++) //para cada pagina
                      {
                          if(($j+1)==$page)
                          {
                              echo '<li class="page-item active"><a class="page-link" href="?id='.$idCategoria.'&pg='.($j+1).'">'.($j+1).'</a></li>';
                          }
                          else
                          {
                              echo '<li class="page-item"><a class="page-link" href="?id='.$idCategoria.'&pg='.($j+1).'">'.($j+1).'</a></li>';
                          }
                          
                      }
                  ?> 
                      <li class="page-item <?php echo $page == $pages ? 'disabled' : ''?>">
                        <a class="page-link" href="?id=<?php echo $idCategoria; ?>&pg=<?php echo $page+1?>" >
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
                <h6 class="text-center">Nenhum tópico encontrado</h6>
            </div>
        </div>
        <?php   }
        }else{ ?>
            <div class="card border border-warning my-2 mx-4">
                <div class="card-body text-warning bg-light">
                    <h6 class="text-center">A Categoria ainda não possui tópicos</h6>
                </div>
            </div>
        <?php }?>
        </div>
        <? $str = ""; $tituloTopico = ""?>
        <!-- conteudo "Novo Topico" -->
        <div class="tab-pane fade <?php echo $tabNewTop; ?>" id="newTopic" role="tabpanel" aria-labelledby="categorias">
            <!-- fazer a pre-visualização do post -->
            
            <?php if(isset($_SESSION['preview']))
            { 
                $tituloTopico = $_SESSION['titulo'];
                $str = $_SESSION['preview'];
                unset($_SESSION['preview']);
            ?>
                <div class="card mx-3 mb-3">
                    <h5 class="card-header text-warning font-weight-bolder bg-dark">Pré-Visualização</h5>
                    <div class="card-body border border-dark" id="preViewTopic">
                        <?php echo showAbsoluteFormat(bbCode($str)); ?>
                    </div>
                </div>
            <?php }else{}?>
            <div class="modal-body text-center mx-2">
                <form method="POST" action="../php_action/forum/criarTopico.php">
                  <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="tituloTopico" class="text-warning text-uppercase my-1">Titulo do novo tópico</label>
                          <input type="text" name="tituloTopico" id="tituloTopico" value="<?php echo $tituloTopico;?>" class="form-control">
                          <input type="hidden" name="idCategoria" id="idCategoria" value="<?php echo $idCategoria;?>">
                           <input type="hidden" name="resposta" id="resposta" value="0">
                          
                            <!-- select com os tipos de topicos -->
                            <label for="tipoTopico" class="text-warning text-uppercase my-1">Categoria do Tópico</label>
                            <select class="form-control mt-2" name="tipoTopico" id="tipoTopico">
                              <option value="discussao" selected>Discussão</option>
                              <option value="aviso">Aviso</option>
                              <option value="duvida">Dúvida</option>
                              <option value="tutorial">Tutorial</option>
                            </select>
                            <!-- fim select -->
                            <input type="hidden" value="<?php echo $idCategoria;?>" name="idTopico">
                        </div>
                      </div>
                    </div>
                
                <!-- Fim form-->
                <div class="modal-header border-warning mb-2">
                    <h5 class="modal-title text-warning text-uppercase" id="titulo">Conteúdo do post</h5>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="btn-group mb-2">
                            <!-- botoes de formatacao de texto -->
                            <!-- tipo de texto -->
                            <button class="border border-warning bg-light" type="button" title="Negrito: [b]texto[/b]" id="negrito" onclick="surroundBB('conteudoPost', '[b]', '[/b]')"><img src="https://img.icons8.com/metro/17/000000/bold.png"></button>
                            <button class="border border-warning bg-light" type="button" title="Itálico: [i]texto[/i]" id="italico" onclick="surroundBB('conteudoPost', '[i]', '[/i]')"><img src="https://img.icons8.com/metro/17/000000/italic.png"></button>
                            <button class="border border-warning bg-light" type="button" title="Sublinhado: [u]texto[/u]" id="sublinhado" onclick="surroundBB('conteudoPost', '[u]', '[/u]')"><img src="https://img.icons8.com/metro/21/000000/underline.png"></button>
                            <button class="border border-warning bg-light" type="button" title="Tachado: [s]texto[/s]" id="tachado" onclick="surroundBB('conteudoPost', '[s]', '[/s]')"><img src="https://img.icons8.com/metro/21/000000/strikethrough.png"></button> | 
                            <!-- alinhamento -->
                            <button class="border border-warning bg-light" type="button" title="Centralizar: [center]texto[/center]" id="aliCentro" onclick="surroundBB('conteudoPost', '[center]', '[/center]')"><img src="https://img.icons8.com/metro/21/000000/align-center.png"></button>
                            <button class="border border-warning bg-light" type="button" title="Alinhar à Direita: [right]texto[/right]" id="aliDireita" onclick="surroundBB('conteudoPost', '[right]', '[/right]')"><img src="https://img.icons8.com/ios/21/000000/align-right-filled.png"></button>
                            <button class="border border-warning bg-light" type="button" title="Justificar: [justify]texto[/justify]" id="aliJustificar" onclick="surroundBB('conteudoPost', '[justify]', '[/justify]')"><img src="https://img.icons8.com/metro/21/000000/align-justify.png"></button> | 
                            <!-- links -->
                            <button class="border border-warning bg-light" type="button" title="Url: [url=www.link.com]texto[/url]" id="url" onclick="surroundBB('conteudoPost', '[url=]', '[/url]')"><img src="https://img.icons8.com/metro/21/000000/link.png"></button> | 
                            <!-- listas -->
                            <button class="border border-warning bg-light" type="button" title="Lista Ordenada: [list=valor_inicial][*]texto[/list]" id="ulList" onclick="surroundBB('conteudoPost', '[list=1]', '[/list]')"><img src="https://img.icons8.com/metro/26/000000/numbered-list.png"></button>
                            <button class="border border-warning bg-light" type="button" title="Lista Desordenada: [list][*]texto[/list]" id="olList" onclick="surroundBB('conteudoPost', '[list]', '[/list]')"><img src="https://img.icons8.com/ios/21/000000/bulleted-list-filled.png"></button>
                            <button class="border border-warning bg-light" type="button" title="Item da Lista: [*]texto" id="iList" onclick="surroundBB('conteudoPost', '[*]', '')"><h6>[*]</h6></button> | 
                            <!-- imagem -->
                            <button class="border border-warning bg-light" type="button" title="Imagem: [img]link_da_img.com[/img]" id="img" onclick="surroundBB('conteudoPost', '[img]', '[/img]')"><img src="https://img.icons8.com/ios/21/000000/image-file.png"></button> | 
                            <!-- tam. Fonte -->
                            <button class="border border-warning bg-light" type="button" title="Tamanho da Fonte: [size=tamanho]texto[/size]" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="https://img.icons8.com/ios/21/000000/sentence-case.png">
                            </button> | 
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" onclick="surroundBB('conteudoPost', '[size=18]', '[/size]')" style="cursor:pointer;">Pequeno</a>
                                <a class="dropdown-item" onclick="surroundBB('conteudoPost', '[size=36]', '[/size]')" style="cursor:pointer;">Médio</a>
                                <a class="dropdown-item" onclick="surroundBB('conteudoPost', '[size=72]', '[/size]')" style="cursor:pointer;">Grande</a>
                            </div>
                            <!-- cor da fonte -->
                            <div class="btn-group dropleft">
                                <button class="border border-warning bg-light" type="button" title="Alterar cor da fonte: [color=cod.cor]texto[/color]" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img src="https://img.icons8.com/metro/21/000000/paint-palette.png"> </button>
                                <div class="dropdown-menu ml-5 border-0" style="background: transparent;">
                                    <div id="buttons">
                                        <button type="button" title="Preto" class="preto" onclick="surroundBB('conteudoPost', '[color=#000]', '[/color]')"></button>
                                        <button type="button" title="Cinza" class="cinza" onclick="surroundBB('conteudoPost', '[color=#808080]', '[/color]')"></button>
                                        <button type="button" title="Violeta" class="violeta" onclick="surroundBB('conteudoPost', '[color=#EE82EE]', '[/color]')"></button>
                                        <button type="button" title="Azul" class="azul" onclick="surroundBB('conteudoPost', '[color=#00F]', '[/color]')"></button>
                                        <button type="button" title="Turquesa" class="turquesa" onclick="surroundBB('conteudoPost', '[color=#008080]', '[/color]')"></button>
                                        <button type="button" title="Verde" class="verde" onclick="surroundBB('conteudoPost', '[color=#008000]', '[/color]')"></button>
                                        <button type="button" title="Verde Claro" class="verdeClaro" onclick="surroundBB('conteudoPost', '[color=#00FF00]', '[/color]')"></button>
                                        <button type="button" title="Marrom" class="marrom" onclick="surroundBB('conteudoPost', '[color=#654321]', '[/color]')"></button>
                                        <button type="button" title="Rosa" class="pink" onclick="surroundBB('conteudoPost', '[color=#FF00FF]', '[/color]')"></button>
                                        <button type="button" title="Amarelo" class="amarelo" onclick="surroundBB('conteudoPost', '[color=#FFFF00]', '[/color]')"></button>
                                        <button type="button" title="Laranja" class="laranja" onclick="surroundBB('conteudoPost', '[color=#FFA500]', '[/color]')"></button>
                                        <button type="button" title="Vermelho" class="vermelho" onclick="surroundBB('conteudoPost', '[color=#F00]', '[/color]')"></button>
                                    </div>
                                </div>
                            </div>
                            <div class="btn-group dropdown">
                                <button class="border border-warning bg-light" type="button" title="code: [code]nome_elemento[/code]"  id="dropCode" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="https://img.icons8.com/metro/21/000000/source-code.png"></button>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropCode">
                                    <button type="button" class="dropdown-item" onclick="surroundBB('conteudoPost', '[code]', '[/code]')">Tag HTML</button>
                                    <button type="button" class="dropdown-item" onclick="surroundBB('conteudoPost', '[block]', '[/block]')">Bloco de Código</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                    <div class="form-row">
                        <textarea class="form-control" name="conteudoPost" id="conteudoPost" rows="15"><?php echo $str;?></textarea>
                    </div>
                    <div class="modal-footer border-warning mt-2">
                        <button name="btnPreview" class="btn border border-warning bg-white float-right mt-2 mx-2"><img src="https://img.icons8.com/small/24/000000/preview-pane.png"> Preview</button>
                        <button name="btnPublicar" class="btn border border-warning bg-white float-right mt-2 mx-2"><img src="https://img.icons8.com/material-outlined/24/000000/plus.png"> Criar Tópico</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="responsive-panel">
        <!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <ins class="adsbygoogle"
             style="display:block; text-align:center;"
             data-ad-layout="in-article"
             data-ad-format="fluid"
             data-ad-client="ca-pub-7463114109083817"
             data-ad-slot="9163544234"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script> -->
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