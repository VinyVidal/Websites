<?php include_once("../../lib/lib.php");
    session_start();
    //pegar a id do post que vai ser editado
    if(isset($_GET['id']))
    {
        if(filter_var($_GET['id'], FILTER_VALIDATE_INT))
        {
            $idPost = $_GET['id'];
        }
        else
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Falha ao recuperar informações da postagem!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/');
        }
    }
    else
    {
        $_SESSION['modalAlerta']['titulo'] = 'Erro';
        $_SESSION['modalAlerta']['mensagem'] = 'Falha ao recuperar informações da postagem!';
        $_SESSION['modalAlerta']['tipo'] = 'error';
        header('location: ../../forum/');
    }
    

    if(isset($_POST['btnEditar']))
    {
        $sql = $con->prepare("SELECT idTopico_Post FROM tbl_posts_forum WHERE id_Post = ?");
        $sql->bind_param("i", $idPost);
        $sql->execute();
        $getTopico = $sql->get_result();
        $dadosTopico = $getTopico->fetch_assoc();
        $idTopico = $dadosTopico['idTopico_Post'];
        $newCont = $_POST['newCont'];
        
        //verificar se o campo esta vazio
        if(empty($newCont))
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'O conteúdo não pode estar vazio. Tente novamente!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/topico.php?id='.$idTopico);
        }
        else
        {
            $sql = $con->prepare("UPDATE tbl_posts_forum SET conteudo_Post = ? WHERE id_Post = ?");
            $sql->bind_param("ss", $newCont, $idPost);
            if($sql->execute())
            {
                header('location: ../../forum/topico.php?id='.$idTopico);
            }
        }
        
    }
    
    if(isset($_POST['btnPreview']))
    {
        $_SESSION['conteudo'] = $_POST['newCont'];
        echo "<script>window.history.back();</script>";
    }