<?php include_once("../lib/lib.php");
session_start();
if(isset($_SESSION['user'])) // se estiver logado
{
    // redirecionar para o index, pois usuario logado nao pode se logar.
    header('location: ../');
}else{
    if(isset($_POST['btnLogin'])){
        $nomeUser = filterField($_POST['nomeUser']);
        $senhaUser = filterField($_POST['senhaUser']);

        //se os campos estiverem vazios aparece modal com erro
        if(empty($nomeUser) || empty($senhaUser)){
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Preencha todos os campos!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../user/login.php');
        }else{
            $sql = $con->prepare("SELECT * FROM tbl_usuarios WHERE nome_User = ?");
            $sql->bind_param("s", $nomeUser);
            $sql->execute();
            //jogando dados para um array
            $get = $sql->get_result();
            $total = $get->num_rows;
            $dados = $get->fetch_assoc();

            //se os campos forem iguais aos do bd fará o login
            if($dados['nome_User'] == $nomeUser && decryptData($dados['senha_User'],$sslKey) == $senhaUser){
                $_SESSION['user']['logado'] = true;
                $_SESSION['user']['idUser'] = $dados['id_User'];
                $_SESSION['user']['nomeCompleto'] = $dados['nomeCompleto_User'];
                $_SESSION['user']['nomeUser'] = $dados['nome_User'];
                $_SESSION['user']['nivel'] = $dados['nivel_User'];

                $get->free_result();
                header('location: ../');
            }else{//senao aparece modal com erro 
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Dados incorretos!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/login.php');
            }
            $get->free_result();
        }
    }
}
    
?>