<?php
/* Gerenciar mensagens da inbox,
Excluir ou marcar como lida */
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
    $idUser = $_SESSION['user']['idUser']; // teoricamente esse é o user gerenciando a msg

    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
    	// MARCAR AS MENSAGENS SELECIONADAS COMO 'LIDAS'
        if(isset($_POST['btnInboxMarcarLida']))
        {
        	if(!empty($_POST['msgCheckBox'])) {
        		$checkboxs = $_POST['msgCheckBox'];

        		// para cada mensagem marcada
        		for($i = 0; $i < count($checkboxs); $i++)
        		{
        			$idMensagem = $checkboxs[$i]; // id da mensagem

        			$sql = $con->prepare("UPDATE tbl_mensagensPrivadas SET visualizou_Mensagem = 1 WHERE id_Mensagem = ?");
        			$sql->bind_param("i", $idMensagem);
        			if($sql->execute())
        			{
        				header('location: ../user/mensagensPrivadas.php');
        			}
        			else
        			{
        			    $_SESSION['modalAlerta']['titulo'] = 'Erro';
		                $_SESSION['modalAlerta']['mensagem'] = 'Erro ao tentar realizar operação.';
		                $_SESSION['modalAlerta']['tipo'] = 'error';
		                header('location: ../user/mensagensPrivadas.php');
		                die('Aguarde um momento...');
        			}

        			header('location: ../user/mensagensPrivadas.php');
        		}
        	}
        	else
        	{
        		header('location: ../user/mensagensPrivadas.php');
        	}
        }

        // EXCLUIR AS MENSAGENS SELECIONADAS
        if(isset($_POST['btnInboxExcluirMsg']))
        {
        	if(!empty($_POST['msgCheckBox'])) {
        		$checkboxs = $_POST['msgCheckBox'];

        		// para cada mensagem marcada
        		for($i = 0; $i < count($checkboxs); $i++)
        		{
        			$idMensagem = $checkboxs[$i]; // id da mensagem

        			$sql = $con->prepare("UPDATE tbl_mensagensPrivadas SET destinatarioExcluiu_Mensagem = 1 WHERE id_Mensagem = ?");
        			$sql->bind_param("i", $idMensagem);
        			if($sql->execute())
        			{
        				header('location: ../user/mensagensPrivadas.php');
        			}
        			else
        			{
        			    $_SESSION['modalAlerta']['titulo'] = 'Erro';
		                $_SESSION['modalAlerta']['mensagem'] = 'Erro ao tentar realizar operação.';
		                $_SESSION['modalAlerta']['tipo'] = 'error';
		                header('location: ../user/mensagensPrivadas.php');
		                die('Aguarde um momento...');
        			}

        			header('location: ../user/mensagensPrivadas.php');
        		}
        	}
        	else
        	{
        		header('location: ../user/mensagensPrivadas.php');
        	}
        }
    }
}