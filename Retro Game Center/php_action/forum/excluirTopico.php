<?php include_once("../../lib/lib.php");
    session_start();
    //pegar o id do topico que sera excluido
    if(isset($_GET['id']))
    {
        if(FILTER_VAR($_GET['id'], FILTER_VALIDATE_INT))
        {
            $idExc = $_GET['id'];
        }
    }
    else
    {
        $_SESSION['modalAlerta']['titulo'] = 'Erro';
        $_SESSION['modalAlerta']['mensagem'] = 'Erro ao recuperar informações de id!';
        $_SESSION['modalAlerta']['tipo'] = 'error';
        header('location: categoria.php');
    }
    
    //se o botao de excluir for clicado
    if(isset($_POST['btnExcluir']))
    {
        $idPost = $_POST['idPost'];
        
        $sql = $con->prepare("SELECT idTopico_Post FROM tbl_posts_forum WHERE id_Post = ?");
        $sql->bind_param("s", $idPost);
        $sql->execute();
        $id = $sql->get_result()->fetch_array();
        
        
        if(empty($idPost))
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Erro ao recuperar informações de id!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: categoria.php');
        }
        else
        {
            $sql = $con->prepare("DELETE FROM tbl_posts_forum WHERE id_Post = ?");
            $sql->bind_param("s", $idPost);
            $sql->execute();
            if($sql->affected_rows > 0)
            {
                $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                $_SESSION['modalAlerta']['mensagem'] = 'Postagem excluida com sucesso!';
                $_SESSION['modalAlerta']['tipo'] = 'success';
                header('location: ../../forum/topico.php?id='.$id['0']);
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Encontramos um problema e não conseguimos excluir a postagem. Tente novamente!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../../forum/topico.php?id='.$id['0']);
            }
        }
    }
?>