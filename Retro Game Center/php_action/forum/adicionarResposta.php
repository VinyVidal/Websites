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
          $idTopico = $_GET['id'];
        }
        else
        {
          $_SESSION['modalAlerta']['titulo'] = 'Erro';
          $_SESSION['modalAlerta']['mensagem'] = 'Categoria nao encontrada!';
          $_SESSION['modalAlerta']['tipo'] = 'error';
          header('location: index.php');
          die('Aguarde um momento...');
        }
    }
    
    //adicionar resposta
    if(isset($_POST['btnResponder']))
    {
        $idTopico = $_POST['idTopico'];
        $conteudo = $_POST['conteudoResposta'];
        
        $sql = $con->prepare("INSERT INTO tbl_posts_forum(idUser_Post, idTopico_Post, conteudo_Post, data_Post) VALUES(?, ?, ?, ?)");
        $sql->bind_param("ssss", $idUser, $idTopico, $conteudo, date('Y-m-d H:i:s'));
        if($sql->execute())
        {
            header('location: ../../forum/topico.php?id='.$idTopico);
        }
        else
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Ops, algo deu errado e não foi possível adicionar uma resposta para essa postagem.';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/topico.php?id='.$idTopico);
        }
    }
    
    if(isset($_POST['btnPreview']))
    {
        $str = $_POST['conteudoResposta'];
        
        $_SESSION['preview'] = $str;
        echo "<script>window.history.back();</script>";
    }
    
?>