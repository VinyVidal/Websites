<?php include_once("../lib/lib.php");
session_start();
if(isset($_SESSION['user'])) // se estiver logado
{
	// redirecionar para o index, pois usuario logado nao pode acesar pg de recuperar senha
	header('location: ../');
}
else
{
    if(isset($_POST['enviaSenha']))
    {
        $email = $_POST['email_Recovery'];
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        //verificar se o email informado existe no bd
        $sql = $con->prepare("SELECT email_User, nome_User FROM tbl_usuarios WHERE email_User = ?");
        $sql->bind_param("s", $email);
        $sql->execute();
        $get = $sql->get_result();
        $emailsServer = $get->fetch_assoc();

        if($sql->affected_rows > 0)
        {
            //gerando uma nova senha para o usuario
            $newPass = substr(md5(time()), 0, 8);
            $newPassCript = encryptData($newPass, $sslKey);
            $sql = $con->prepare("UPDATE tbl_usuarios SET senha_User = ? WHERE email_User = ?");
            $sql->bind_param("ss", $newPassCript, $email);
            $sql->execute();

            if($sql->affected_rows > 0)
            {
                //enviando o email
                $subject = "Suporte Retro Game Center";
                $headers = "Content-Type: text/html; charset=UTF-8\n";
                $headers .= "From: RetroGameCenter.com.br <master@retrogamecenter.com.br>\n";
                $headers .= "X-Sender: <master@retrogamecenter.com.br>\n";
                $headers .= "Return-Path: <master@retrogamecenter.com.br>\n";
                $message = "<html><head>";
                $message .= "<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css'>
                            <div class='card mb-3'>
                              <img class='card-img-top text-center' src='../img/Logo10.png' style='width: 18rem;'>
                              <div class='card-body'>
                                <h5 class='card-title'>Retro Game Center</h5>
                                <p class='card-text'>Olá ".$emailsServer['nome_User'].", acabamos de receber sua solicitação de recuperação de senha!</p>
                                <p class='card-text'><small class='text-muted'>Aqui está sua nova senha de acesso: ".$newPass."</small></p>
                                <p class='card-text'>Esta enfrentando algum outro tipo de problema? Acesse a categoria de Suporte em nosso <a class='text-warning' href='https://retrogamecenter.com.br/forum/categoria.php?id=4'>fórum</a> e reporte seu problema.</p>
                                <p class='text-muted'>att, Suporte Retro Game Center</p>
                              </div>
                            </div>
                            ";

                if(mail($email, $subject, $message, $headers))
                {
                    $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                    $_SESSION['modalAlerta']['mensagem'] = 'Enviamos sua nova senha para o seu endereço de e-mail!<br>Cheque seu lixo eletrônico ou caixa de spam';
                    $_SESSION['modalAlerta']['tipo'] = 'success';
                    header('location: ../user/login.php');
                }
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Ops, ocorreu um erro ao enviar sua nova senha. Tente novamente!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/passRecovery.php');
            }
        }
        else
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Ops, o email informado não existe em nossa base de dados!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../user/passRecovery.php');
        }
    }
}
    
?>