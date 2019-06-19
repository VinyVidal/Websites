<?php
include_once ('../lib/lib.php');
session_start();

if(!isset($_SESSION['user'])) // se nao estiver logado
{
    // redirecionar para a pg de login
    header('location: ../user/login.php');
    die();
}
else
{
    $idUser = $_SESSION['user']['idUser']; // teoricamente esse é o user enviando a msg

    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        if(isset($_POST['btnEnviarMsg']))
        {
        	// recuperando dados do form
        	$destinatario = filterField($_POST['msgDestinatario']);

        	$assunto = filterField($_POST['msgAssunto']);

        	$corpo = filterField($_POST['msgCorpo']);

        	// tratando os campos
        	if(empty($destinatario))
        	{
        		$_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Preencha o campo do destinatario!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/mensagensPrivadas.php?sendTo=');
                die('Aguarde um momento...');
        	}

        	if(empty($assunto))
        	{
        		$assunto = '(Sem assunto)';
        	}

        	if(empty($corpo))
        	{
        		$_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Corpo da mensagem está vazio!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/mensagensPrivadas.php?sendTo=');
                die('Aguarde um momento...');
        	}

        	//verificando se o destinatario existe
        	$sql = $con->prepare("SELECT id_User, nome_User FROM tbl_usuarios WHERE nome_User = ?");
        	$sql->bind_param("s", $destinatario);
        	if($sql->execute())
        	{
        		$getDest = $sql->get_result();
        		if($getDest->num_rows > 0)
        		{
        			$dadosDest = $getDest->fetch_assoc();
        			if($dadosDest['id_User'] === $idUser) // Se o remetente tentar enviar msg para ele mesmo
        			{
        				$_SESSION['modalAlerta']['titulo'] = 'Erro';
		                $_SESSION['modalAlerta']['mensagem'] = 'Você não pode enviar mensagens para si mesmo!';
		                $_SESSION['modalAlerta']['tipo'] = 'error';
		                header('location: ../user/mensagensPrivadas.php?sendTo=');
		                die('Aguarde um momento...');
        			}

        			// Tudo validado. Enviando mensagem fora desse if
        		}
        		else
        		{
        			$_SESSION['modalAlerta']['titulo'] = 'Erro';
	                $_SESSION['modalAlerta']['mensagem'] = 'Destinatario não encontrado. Verifique se o nome esta correto e tente novamente!';
	                $_SESSION['modalAlerta']['tipo'] = 'error';
	                header('location: ../user/mensagensPrivadas.php?sendTo=');
	                die('Aguarde um momento...');
        		}
        	}
        	else
        	{
        		$_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Erro ao tentar realizar operação.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/mensagensPrivadas.php?sendTo=');
                die('Aguarde um momento...');
        	}
        	// Tudo validado. Enviando mensagem...
        	$sql = $con->prepare("INSERT INTO tbl_mensagensPrivadas
        	(idRemetente_Mensagem, idDestinatario_Mensagem, assunto_Mensagem, conteudo_Mensagem, dataEnvio_Mensagem)
        	VALUES (?, ?, ?, ?, ?)");
        	$sql->bind_param("iisss", $idUser, $dadosDest['id_User'], $assunto, $corpo, date('Y-m-d H:i:s'));
        	$getDest->free_result();
            
        	if($sql->execute())
        	{
        		if($sql->affected_rows > 0)
        		{
        			$_SESSION['modalAlerta']['titulo'] = 'Mensagem Enviada';
	                $_SESSION['modalAlerta']['mensagem'] = 'Mensagem enviada com sucesso';
	                $_SESSION['modalAlerta']['tipo'] = 'success';
	                header('location: ../user/mensagensPrivadas.php');
	                die('Aguarde um momento...');
        		}
        		else
        		{
        			$_SESSION['modalAlerta']['titulo'] = 'Erro';
	                $_SESSION['modalAlerta']['mensagem'] = 'Ocorreu um erro ao tentar enviar a mensagem';
	                $_SESSION['modalAlerta']['tipo'] = 'error';
	                header('location: ../user/mensagensPrivadas.php?sendTo='.$destinatario);
	                die('Aguarde um momento...');
        		}
        	}
        	else
        	{
        		$_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Erro ao tentar realizar operação.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/mensagensPrivadas.php?sendTo='.$destinatario);
                die('Aguarde um momento...');
        	}
        }
    }
}