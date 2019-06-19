<?php include_once("../../lib/lib.php");
    session_start();
    //verificar se o botao de trancar foi clicado
    if(isset($_POST['btnTrancar']))
    {
        $idTopico = $_POST['idTopico'];
        $tranca = 1;
        
        //verificar o id
        if(empty($idTopico))
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Falha ao recuperar informações de ID!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../../forum/');
        }
        else
        {
            $sql = $con->prepare("UPDATE tbl_topicos_forum SET trancado_Topico = ? WHERE id_Topico = ?");
            $sql->bind_param("ss", $tranca, $idTopico);
            if($sql->execute())
            {
                echo "<script>window.history.back();</script>";
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Ops, encontramos um problema ao trancar o tópico. Tente novamente!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../../forum/');
            }
        }
    }
?>