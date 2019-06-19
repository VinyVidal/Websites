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
        $conteudo = $_POST['conteudoPost'];
        $_SESSION['preview'] = $conteudo;
        header('location: ../../forum/categoria.php?');
    }
    
    //****Cria nova categoria******
    if(isset($_POST['addCategoria']))
    {
        //pegando dados dos campos
        $nomeCategoria = filterField($_POST['nameCategoria']);
        $descCategoria = filterField($_POST['descCategoria']);

        //verificando se os campos foram preenchidos
        if(empty($nomeCategoria) || empty($descCategoria))
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Todos os campos devem ser preenchidos. Tente novamente!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/');
        }
        else
        {
            //insercao dos dados no banco
            $sql = $con->prepare("INSERT INTO tbl_categorias_forum(idUser_Categoria, nome_Categoria, descricao_Categoria, dataCriacao_Categoria) VALUES(?,?,?,?)");
            $sql->bind_param("ssss", $idUser, $nomeCategoria, $descCategoria, date('Y-m-d H:i:s'));
            $sql->execute();

            //verificando se a query foi
            if($sql->affected_rows > 0)
            {
                $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                $_SESSION['modalAlerta']['mensagem'] = 'Categoria adicionada com sucesso, clique em fechar para visualizar as categorias do fórum!';
                $_SESSION['modalAlerta']['tipo'] = 'success';
                header('location: ../../forum/');
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Ops, Algo deu errado e não foi possível adicionar uma nova categoria. Tente novamente!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../../forum/categoria.php?id='.$idCategoria);
            }
        }
    }
?>