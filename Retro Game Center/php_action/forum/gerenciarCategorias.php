<?php include_once("../../lib/lib.php");
    session_start();
    if(isset($_POST['alterarCategoria']))
    {
        $nomeCategoria = $_POST['altNomeCategoria'];
        $descCategoria = $_POST['altDescCategoria'];
        $idCategoria = $_POST['idCategoria'];
        
        //verifica se os campos estao vazios
        if(empty($nomeCategoria) || empty($descCategoria) || empty($idCategoria))
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Preencha todos os campos e tente novamente.';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/');
        }
        else
        {
            $sql = $con->prepare("UPDATE tbl_categorias_forum SET nome_Categoria = ?, descricao_Categoria = ? WHERE id_Categoria = ?");
            $sql->bind_param("sss", $nomeCategoria, $descCategoria, $idCategoria);
            if($sql->execute())
            {
                //verificando se a query retornou algum resultado
                $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                $_SESSION['modalAlerta']['mensagem'] = 'Dados da categoria foram alterados com sucesso.';
                $_SESSION['modalAlerta']['tipo'] = 'success';
                header('location: ../../forum/');
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Ops, encontramos um problema ao editar os dados da categoria! Tente Novamente.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../../forum/');
            }
        }
    }
    
    if(isset($_POST['btnExcluir']))
    {
        $idCategoria = $_POST['excIdCategoria'];
        
        if(empty($idCategoria))
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Ops, encontramos um problema ao recuperar informações de identificação! Tente Novamente.';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/');
        }
        else
        {
            $sql = $con->prepare("DELETE FROM tbl_categorias_forum WHERE id_Categoria = ?");
            $sql->bind_param("s", $idCategoria);
            if($sql->execute())
            {
                $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                $_SESSION['modalAlerta']['mensagem'] = 'Categoria excluida com sucesso.';
                $_SESSION['modalAlerta']['tipo'] = 'success';
                header('location: ../../forum/');
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Ops, encontramos um problema ao excluir a categoria! Tente Novamente.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../../forum/');
            }
        }
    }
?>