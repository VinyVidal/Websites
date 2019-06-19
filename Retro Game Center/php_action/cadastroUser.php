<!-- Back-end cadastro-->
<?php include_once("../lib/lib.php");
session_start();
if(isset($_SESSION['user'])) // se estiver logado
{
	// redirecionar para o index, pois usuario logado nao pode se cadastrar.
	header('location: ../');
}

	if(isset($_POST['cadUser'])){
		$nomeCompleto_User = ucwords(filterField(($_POST['nomeCompleto_User'])));
        $dataNasc_User = filterField(($_POST['dataNasc_User']));
        $email_User = strtolower(filterField(($_POST['email_User'])));
        $nome_User = filterField(($_POST['nome_User']));
        $senha_User = filterField(($_POST['senha_User']));
        $confirmarPassword = filterField(($_POST['confirmarPassword']));

		//verificar se os campos foram preenchidos
		if(empty($nomeCompleto_User)
				||empty($dataNasc_User)
				||empty($email_User)
				||empty($nome_User)
				||empty($senha_User)
				||empty($confirmarPassword)){
			$_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Todos os campos devem ser preenchidos!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
			header('location: ../user/cadastro.php');
		//verificar se a senha atende o requisito de caracteres minimos
		}else if(strlen($senha_User) <= 7){
			$_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Senha deve conter pelo menos 8 caracteres!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
			header('location: ../user/cadastro.php');
		//verificar se as senhas coincidem
		}else if(passwordVerify($senha_User, $confirmarPassword) == false){
			$_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Senhas não coincidem!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../user/cadastro.php');
		}else if(!filterName($nome_User)){
			$_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Nome de usuario inválido. Não use simbolos, espaços em branco ou acentuação!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../user/cadastro.php');
		}else if(nomeUserVerify($con, $nome_User) == false){
			$_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Nome de usuario já está sendo utilizado. Tente novamente!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
			header('location: ../user/cadastro.php');
		}else if(emailUserVerify($con, $email_User) == false){
			$_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Este email já está sendo utilizado. Tente novamente!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
			header('location: ../user/cadastro.php');
		}else{
		    $senha_User = encryptData($senha_User, $sslKey);
		    
			$sql = $con->prepare("INSERT INTO tbl_usuarios(nomeCompleto_User, dataNasc_User, email_User, nome_User, senha_User, dataCadastro) VALUES(?, ?, ?, ?, ?, ?)");
			$sql->bind_param("ssssss", $nomeCompleto_User, $dataNasc_User, $email_User, $nome_User, $senha_User, date('Y-m-d H:i:s'));
			$sql->execute();

			//verificar se foi realizado o cadastro
			if($sql->affected_rows > 0){
				$_SESSION['modalAlerta']['titulo'] = 'Sucesso';
           		$_SESSION['modalAlerta']['mensagem'] = 'Cadastro realizado com sucesso! Faça login para continuar.';
            	$_SESSION['modalAlerta']['tipo'] = 'success';
				header('location: ../user/login.php');
			}else{
				$_SESSION['modalAlerta']['titulo'] = 'Erro';
	            $_SESSION['modalAlerta']['mensagem'] = 'Falha ao realizar cadastro. Tente novamente!';
	            $_SESSION['modalAlerta']['tipo'] = 'error';
				header('location: ../user/cadastro.php');
			}
		}
	}

?>
<!-- Back-end cadastro-->