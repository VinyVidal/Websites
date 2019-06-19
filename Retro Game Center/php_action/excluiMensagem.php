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
    $idUser = $_SESSION['user']['idUser']; // teoricamente esse é o user exluindo a msg

    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
    	// EXCLUIR MENSAGEM QUE ESTA SENDO VISUALIZADA
        if(isset($_POST['btnExcluirMsgAtual']))
        {
        	$idMensagem = $_POST['idMensagem'];

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
        }
    }
}