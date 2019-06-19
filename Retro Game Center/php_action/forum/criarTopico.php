<?php include_once("../../lib/lib.php");
    session_start();
    //pegar id do user(lvl 3 ou 4) que criou o tópico
    if(isset($_SESSION['user']))
    {
        $idUser = $_SESSION['user']['idUser'];
    }
    else
    {
        $_SESSION['modalAlerta']['titulo'] = 'Erro';
        $_SESSION['modalAlerta']['mensagem'] = 'Erro ao recuperar dados. Tente novamente!';
        $_SESSION['modalAlerta']['tipo'] = 'error';
        header('location: ../../forum/');
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
          header('location: ../../forum/');
          die('Aguarde um momento...');
        }
    }
    
    if(isset($_POST['btnPreview']))
    {
        $_SESSION['titulo'] = $_POST['tituloTopico'];
        $_SESSION['preview'] = $_POST['conteudoPost'];
        header('location: ../../forum/categoria.php?id='.$_POST['idCategoria']); 
        
    }
    
    //back-end criar novo tópico
    if(isset($_POST['btnPublicar']))
    {
        //pegar os conteudos
        $conteudoPost = $_POST['conteudoPost'];
        $tituloTopico = filterField($_POST['tituloTopico']);
        $tipoTopico = $_POST['tipoTopico'];
        $idCategoria = $_POST['idCategoria'];
        $resposta = filterField($_POST['resposta']);
        
        
        //verificar se algum campo está vazio
        if(empty($conteudoPost) || empty($tituloTopico) || empty($tipoTopico) || empty($idCategoria) || $resposta != 0)
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Ops, algo deu errado e não foi possível adicionar uma nova categoria. Verifique se todos os campor foram preenchidos e tente novamente!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/');
        }
        else
        {
            $query = $con->prepare("INSERT INTO tbl_topicos_forum(idCategoria_Topico, idUser_Topico, titulo_Topico, tipo_Topico, dataCriacao_Topico) VALUES(?, ?, ?, ?, ?)");
            $query->bind_param("sssss",$idCategoria, $idUser, $tituloTopico, $tipoTopico, date('Y-m-d H:i:s'));
            //se o novo tópico for criado com sucesso em seguida tenta fazer a inserção na tabela do post
            if($query->execute())
            {
               $sql = $con->prepare("SELECT id_Topico FROM tbl_topicos_forum WHERE titulo_Topico = ?");
               $sql->bind_param("s", $tituloTopico);
               $sql->execute();
               $getDadosTopico = $sql->get_result();
               $dadosTopico = $getDadosTopico->fetch_assoc();
            
               $sql = $con->prepare("INSERT INTO tbl_posts_forum(idUser_Post, idTopico_Post, resposta_Post, conteudo_Post, data_Post) VALUES(?, ?, ?, ?, ?)");
               $sql->bind_param("sssss", $idUser, $dadosTopico['id_Topico'], $resposta, $conteudoPost, date('Y-m-d H:i:s'));
               if($sql->execute())
               {
                    $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                    $_SESSION['modalAlerta']['mensagem'] = 'Você acabou de criar um novo tópico.';
                    $_SESSION['modalAlerta']['tipo'] = 'success';
                    header('location: ../../forum/');
               }
               else
               {
                    $_SESSION['modalAlerta']['titulo'] = 'Erro';
                    $_SESSION['modalAlerta']['mensagem'] = 'Ops, algo deu errado e não foi possível adicionar uma nova categoria.';
                    $_SESSION['modalAlerta']['tipo'] = 'error';
                    header('location: ../../forum/categoria.php?id='.$idCategoria); 
               }
            }
        }
    }
    
?>