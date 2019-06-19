<?php include_once("../../lib/lib.php");
    session_start();
    
    //editar titulo e tipo do tópico
    if(isset($_POST['btnEditarTopico']))
    {
        $idTopico = $_POST['idEditarTopico'];
        $titulo = filterField($_POST['altNomeTopico']);
        $tipo = filterField($_POST['altTipoTopico']);
        
        if(empty($titulo) || empty($tipo) || empty($idTopico))
        {
            echo "err";
        }
        else
        {
            $sql = $con->prepare("UPDATE tbl_topicos_forum SET titulo_Topico = ?, tipo_Topico = ? WHERE id_Topico = ?");
            $sql->bind_param("sss", $titulo, $tipo, $idTopico);
            $sql->execute();
            
            if($sql->affected_rows > 0)
            {
                $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                $_SESSION['modalAlerta']['mensagem'] = 'Os dados do tópico foi alterado com sucesso.';
                $_SESSION['modalAlerta']['tipo'] = 'success';
                header('location: ../../forum/');
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Ops, encontramos um problema e não conseguimos concluir a edição dos dados, tente novamente.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../../forum/');
            }
        }
    }
    
    //trancar topico
    if(isset($_POST['btnTrancar']))
    {
        $idTopico = filterField($_POST['idTopicoTrancar']);
        $titulo = $_POST['nomeTopicoTrancar'];
        
        if(empty($idTopico))
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Não foi possível recuperar informções de ID, tente novamente';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/');
        }
        else
        {
            $sql = $con->prepare("UPDATE tbl_topicos_forum SET trancado_Topico = 1 WHERE id_Topico = ?");
            $sql->bind_param("s", $idTopico);
            $sql->execute();
            
            //verificar se o topico foi trancado
            if($sql->affected_rows > 0)
            {
                $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                $_SESSION['modalAlerta']['mensagem'] = 'O tópico '.$titulo.' foi trancado com sucesso.';
                $_SESSION['modalAlerta']['tipo'] = 'success';
                header('location: ../../forum/');
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Ops, encontramos um problema e não conseguimos trancar o tópico '.$titulo.', tente novamente.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../../forum/');
            }
        }
    }
    
    //excluir topico
    if(isset($_POST['btnExcluir']))
    {
        $idTopico = filterField($_POST['excIdTopico']);
        $titulo = $_POST['excNomeTopico'];
        
        if(empty($idTopico))
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Não foi possível recuperar informções de ID, tente novamente';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/');
        }
        else
        {
            $sql = $con->prepare("DELETE FROM tbl_topicos_forum WHERE id_Topico = ?");
            $sql->bind_param("s", $idTopico);
            $sql->execute();
            
            if($sql->affected_rows > 0)
            {
                $sql->prepare("DELETE FROM tbl_posts_forum WHERE idTopico_Post = ?");
                $sql->bind_param("s", $idTopico);
                $sql->execute();
                
                if($sql->affected_rows > 0)
                {
                    $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                    $_SESSION['modalAlerta']['mensagem'] = 'Você acabou de excluir o tópico '.$titulo.'.';
                    $_SESSION['modalAlerta']['tipo'] = 'success';
                    header('location: ../../forum/');
                }
                else
                {
                    $_SESSION['modalAlerta']['titulo'] = 'Erro';
                    $_SESSION['modalAlerta']['mensagem'] = 'Ops, encontramos um problema e não conseguimos excluir o tópico '.$titulo.', tente novamente.';
                    $_SESSION['modalAlerta']['tipo'] = 'error';
                    header('location: ../../forum/'); 
                }
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Ops, encontramos um problema e não conseguimos excluir o tópico '.$titulo.', tente novamente.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../../forum/');
            }
        }
    }
    
    if(isset($_POST['btnDestrancar']))
    {
        $idTopico = $_POST['idTopicoDestrancar'];
        $titulo = $_POST['nomeTopicoDestrancar'];
        
        if(empty($idTopico))
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Não foi possível recuperar informções de ID, tente novamente';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/');
        }
        else
        {
            $sql = $con->prepare("UPDATE tbl_topicos_forum SET trancado_Topico = 0 WHERE id_Topico = ?");
            $sql->bind_param("s", $idTopico);
            $sql->execute();
            
            if($sql->affected_rows > 0)
            {
                $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                $_SESSION['modalAlerta']['mensagem'] = 'O tópico '.$titulo.' foi destrancado com sucesso.';
                $_SESSION['modalAlerta']['tipo'] = 'success';
                header('location: ../../forum/');
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Ops, encontramos um problema e não conseguimos destrancar o tópico '.$titulo.', tente novamente.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../../forum/');
            }
        }
    }
    
    //mudar o topico de categoria
    if(isset($_POST['btnMudarCategoria']))
    {
        $idCategoria = $_POST['categoriasDisponiveis'];
        $idTopico = $_POST['idEditarCategoriaTopico'];
        $titulo = $_POST['nomeTopicoMudarCategoria'];
        
        //buscar qual o id da categoria do post
        $sql = $con->prepare("SELECT idCategoria_Topico FROM tbl_topicos_forum WHERE id_Topico = ?");
        $sql->bind_param("s", $idTopico);
        $sql->execute();
        $getCat = $sql->get_result();
        $idCat = $getCat->fetch_assoc();
        //buscar o nome da categoria pelo id pegado acima
        $sql = $con->prepare("SELECT nome_Categoria FROM tbl_categorias_forum WHERE id_Categoria = ?");
        $sql->bind_param("s", $idCat['idCategoria_Topico']);
        $sql->execute();
        $getName = $sql->get_result();
        $nameCat = $getName->fetch_assoc();
        
        //buscar nome da nova categoria do topico
        $sql = $con->prepare("SELECT nome_Categoria FROM tbl_categorias_forum WHERE id_Categoria = ?");
        $sql->bind_param("s", $idCategoria);
        $sql->execute();
        $getNewCat = $sql->get_result();
        $newCat = $getNewCat->fetch_assoc();
        
        if(empty($idCategoria) || empty($idTopico))
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Não foi possível recuperar informções de ID, tente novamente';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/');
        }
        else
        {
            $sql = $con->prepare("UPDATE tbl_topicos_forum SET idCategoria_Topico = ? WHERE id_Topico = ?");
            $sql->bind_param("ss", $idCategoria, $idTopico);
            $sql->execute();
            
            if($sql->affected_rows > 0)
            {
               $_SESSION['modalAlerta']['titulo'] = 'Tópico Movido de Categoria';
                $_SESSION['modalAlerta']['mensagem'] = 'Você acabou de mover o tópico '.$titulo.' de '.$nameCat['nome_Categoria'].' para '.$newCat['nome_Categoria'];
                $_SESSION['modalAlerta']['tipo'] = 'success';
                header('location: ../../forum/'); 
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Não foi possível mover o tópico '.$titulo.' de categoria, tente novamente';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../../forum/');
            }
        }
    }
?>