<?php include_once("../lib/lib.php");
    session_start();

    //back-end alterar informações gerais
    if(isset($_SESSION['user'])){
        $idUser = $_SESSION['user']['idUser'];

        if(isset($_POST['btnEditar'])){
            $nomeCompleto_User = ucwords(filterField($_POST['nomeCompleto_User']));
            $dataNasc_User = filterField($_POST['dataNasc_User']);
            $email_User = strtolower(filterField($_POST['email_User']));
            $nome_User = filterField($_POST['nome_User']);
    
            $sql = $con->prepare("UPDATE tbl_usuarios SET nomeCompleto_User = ?, dataNasc_User = ?, email_User = ?, nome_User = ? WHERE id_User = ?");
            $sql->bind_param("sssss", $nomeCompleto_User, $dataNasc_User, $email_User, $nome_User, $idUser);
            $sql->execute();
    
                if($sql->affected_rows > 0){
                    $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                    $_SESSION['modalAlerta']['mensagem'] = 'Seus dados foram alterados com sucesso!';
                    $_SESSION['modalAlerta']['tipo'] = 'success';
                    header('location: ../user/profile.php');
                }else{
                    $_SESSION['modalAlerta']['titulo'] = 'Erro';
                    $_SESSION['modalAlerta']['mensagem'] = 'Falha ao alterar dados. Tente Novamente!';
                    $_SESSION['modalAlerta']['tipo'] = 'error';
                    header('location: ../user/profile.php');
                }
        }

        //back-end Alterar senha
        if(isset($_POST['btnAlterar'])){
            $currentPass = filterField($_POST['currentPass']);
            $newPass = filterField($_POST['newPass']);
            $confirmPass = filterField($_POST['confirmPass']);
            
            if(strlen($newPass) <= 7){
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Senha deve conter pelo menos 8 caracteres!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/profile.php');
            }else if(passwordVerify($newPass, $confirmPass) == false){
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Senhas não coincidem!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/profile.php');
            }else if(empty($newPass) || empty($confirmPass) || empty($currentPass)){
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Preencha todos os campos!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/profile.php');
            }else if(newPasswordVerify($con, $currentPass, $newPass, $sslKey) == true){
                $newPassCript = encryptData($newPass, $sslKey);
                $sql = $con->prepare("UPDATE tbl_usuarios SET senha_User = ? WHERE id_User = ?");
                $sql->bind_param("ss", $newPassCript, $idUser);
                $sql->execute();

                if($sql->affected_rows > 0){
                    $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                    $_SESSION['modalAlerta']['mensagem'] = 'Senha alterada com sucesso!';
                    $_SESSION['modalAlerta']['tipo'] = 'success';
                    header('location: ../user/profile.php');
                }else{
                    $_SESSION['modalAlerta']['titulo'] = 'Erro';
                    $_SESSION['modalAlerta']['mensagem'] = 'Erro ao alterar senha!';
                    $_SESSION['modalAlerta']['tipo'] = 'error';
                    header('location: ../user/profile.php');
                }
            }else{
                    $_SESSION['modalAlerta']['titulo'] = 'Erro2';
                    $_SESSION['modalAlerta']['mensagem'] = 'Erro ao alterar senha!';
                    $_SESSION['modalAlerta']['tipo'] = 'error';
                    header('location: ../user/profile.php');
                }
        }

        //back-end alterar imagem
        if(isset($_FILES['imgNova'])){
            $sql = $con->prepare("SELECT * FROM tbl_usuarios WHERE id_User = ?");
            $sql->bind_param("s", $idUser);
            $sql->execute();
            $get = $sql->get_result();
            $dados = $get->fetch_assoc();

            $dir = "../img/perfil/";
            $dirServer = "img/perfil/";
            $nome_User = $dados['nome_User'];
            
            // liberando o resultado para liberar memoria
            $get->free_result();
            
            $name = $_FILES['imgNova']['name'];
            $ext = explode(".", $name);
            $imgNova = $dir.$nome_User.'.'.$ext[1];
            $imgNovaServer = $dirServer.$nome_User.'.'.$ext[1];
            
            $img = utf8_decode($dir.$name); // caminho+nome
            $imgTipo = strtolower(pathinfo($img, PATHINFO_EXTENSION)); // extensão(tipo) do arquivo
            $imgsPermitidas = ['png', 'jpg', 'jpeg', 'bmp', 'ico'];
            
            if(!in_array($imgTipo, $imgsPermitidas)) // Se o arquivo nao for uma img valida
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Apenas imagens .png, .bmp, .jpg, .jpeg e .ico são permitidas!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/profile.php');
            }else if($_FILES['imgNova']['size'] > 1048576) // Se for maior que 1 MB
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Imagem muito grande!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/profile.php');
            }else if(move_uploaded_file($_FILES["imgNova"]["tmp_name"], $imgNova)){
            $sql = $con->prepare("UPDATE tbl_usuarios SET img_User = ? WHERE id_User = ?");
            $sql->bind_param("ss", $imgNovaServer, $idUser);
            $sql->execute();

                if($sql == true){
                    $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                    $_SESSION['modalAlerta']['mensagem'] = 'Imagem alterada com sucesso!';
                    $_SESSION['modalAlerta']['tipo'] = 'success';
                    header('location: ../user/profile.php');
                }              
            }else{
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Imagem não alterada!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/profile.php');
            }
        }
    
        //Editar links do social media
        if(isset($_POST['btnAdd'])){
            $link1 = filterUrl(filterField($_POST['link1']), 'INSTAGRAM'); // Instagram
            $link2 = filterUrl(filterField($_POST['link2']), 'FACEBOOK'); // Facebook
            $link3 = filterUrl(filterField($_POST['link3']), 'TWITTER'); // Twitter
            $link4 = filterUrl(filterField($_POST['link4']), 'GITHUB'); // GitHub

            $sql = $con->prepare("UPDATE tbl_usuarios SET link1_User = ?, link2_User = ?, link3_User = ?, link4_User = ? WHERE id_User =?");
            $sql->bind_param("sssss", $link1, $link2, $link3, $link4, $idUser);
            $sql->execute();

            $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
            $_SESSION['modalAlerta']['mensagem'] = 'Links atualizados com sucesso';
            $_SESSION['modalAlerta']['tipo'] = 'success';
            header('location: ../user/profile.php');
    
        }
        
        // Apresentação
        if(isset($_POST['btnAddApres'])){
            $apresentacao = filterField($_POST['apresentacao']);
            
            $sql = $con->prepare("UPDATE tbl_usuarios SET bio_User = ? WHERE id_User = ?");
            $sql->bind_param("ss", $apresentacao, $idUser);
            $sql->execute();
            
            if($sql->affected_rows > 0){
                $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                $_SESSION['modalAlerta']['mensagem'] = 'Apresentação atualizada com sucesso!';
                $_SESSION['modalAlerta']['tipo'] = 'success';
  
                header('location: ../user/profile.php');
            }
            else
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Erro ao tentar atualizar a apresentação!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/profile.php');
            }
        }
        else
        {
            header('location: ../user/profile.php');
        }
    }