<?php
include_once ('../lib/lib.php');
session_start();

if(!isset($_SESSION['user'])) // se nao estiver logado
{
    // redirecionar para a pg de login
    header('location: ../user/login.php');
}
else
{
	$idUser = $_SESSION['user']['idUser'];

    $sql = $con->prepare("SELECT * FROM tbl_usuarios WHERE id_User = ?");
    $sql->bind_param("s", $idUser);
    $sql->execute();

    $get = $sql->get_result();
    $dados = $get->fetch_assoc();

    if($dados['nivel_User'] < 2)
    {
    	header('location: ../');
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        if(isset($_POST['btnAlterar']))
        {
        	$userAlvo = $_POST['idUser'];
        	$valuesSelect = array('1', '2', '3');
        	if(!in_array($_POST['alterarPrivilegioSelect'], $valuesSelect))
        	{
        		$_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Selecione um privilégio válido!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/profileView.php?userv='.$userAlvo);
                die('Aguarde um momento...');
        	}

        	$novoNivel = $_POST['alterarPrivilegioSelect'];

        	$sql = $con->prepare("UPDATE tbl_usuarios SET nivel_User = ? WHERE id_User = ?");
        	$sql->bind_param("ii", $novoNivel, $userAlvo);
        	if($sql->execute())
        	{
        		if($sql->affected_rows > 0 )
        		{
        			$_SESSION['modalAlerta']['titulo'] = 'Sucesso';
		            $_SESSION['modalAlerta']['mensagem'] = 'Privilégio alterado com sucesso!';
		            $_SESSION['modalAlerta']['tipo'] = 'success';
		            header('location: ../user/profileView.php?userv='.$userAlvo);

        		}
        		else
        		{
        			$_SESSION['modalAlerta']['titulo'] = 'Erro';
	                $_SESSION['modalAlerta']['mensagem'] = 'Erro ao tentar atualizar o privilégio do usuário. Tente novamente!';
	                $_SESSION['modalAlerta']['tipo'] = 'error';
	                header('location: ../user/profileView.php?userv='.$userAlvo);
	                die('Aguarde um momento...');
        		}
        	}
        	else
        	{
        		$_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Erro ao tentar atualizar o privilégio do usuário. Tente novamente!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/profileView.php?userv='.$userAlvo);
                die('Aguarde um momento...');
        	}
        }
    }
}
