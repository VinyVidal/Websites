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
                header('location: ../../forum/topicos.php?id='.$idCategoria);
            }
        }
    }
      
    //Adicionar novo tópico
    if(isset($_POST['addTopico']))
    {
        $tituloTopico = filterField($_POST['tituloTopico']);
        $idCategoria = $_POST['idCategoria'];
        $tipoTopico = $_POST['tipoTopico'];
        
        //verificando se os campos estão preenchidos
        if(empty($idCategoria) || empty($tituloTopico))
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Ops, é necessário preencher todos os campos para adicionar um novo tópico. Tente novamente!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/?err');
        }
        else
        {
            //fazendo a insercao dos dados no bd
            $sql = $con->prepare("INSERT INTO tbl_topicos_forum(idCategoria_Topico, idUser_topico, titulo_Topico, tipo_Topico, dataCriacao_Topico) VALUES(?, ?, ?, ?, ?)");
            $sql->bind_param("sssss", $idCategoria, $idUser, $tituloTopico, $tipoTopico, date('Y-m-d H:i:s'));
            $sql->execute();
            
            //verificando se foi feita a query
            if($sql->affected_rows > 0)
            {
                $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                $_SESSION['modalAlerta']['mensagem'] = 'Você adicionou um novo tópico, clique em fechar para visualizar os tópicos da sessão!';
                $_SESSION['modalAlerta']['tipo'] = 'success';
                header('location: ../../forum/?err');
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Ops, algo deu errado e não foi possível adicionar um novo tópico. Tente novamente!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../../forum/?succ');
            }
        }
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
                    header('location: ../../forum/topicos.php?id='.$idCategoria); 
               }
            }
            

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
            header('location: ../../forum/topicos.php?id='.$idCategoria);
        }
        else
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Ops, algo deu errado e não foi possível adicionar uma resposta para essa postagem.';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/topicos.php?id='.$idCategoria);
        }
    }
    
?>