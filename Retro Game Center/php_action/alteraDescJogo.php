<?php
/* Recebe os dados do Form da pag enviarJogo.php e manda pro banco */
include_once ('../lib/lib.php');
session_start();
if(!isset($_SESSION['user'])) // se nao estiver logado
{
    // redirecionar para a pg de login
    header('location: ../user/login.php');
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if(isset($_POST['btnAltDesc']))
    {
        $idUser = $_SESSION['user']['idUser'];
        $idJogo = $_POST['idJogo'];
        $descricaoJogo = filterField($_POST['altDescricao']);
        
        if(empty($descricaoJogo))
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'A descrição não pode ficar vazia!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../mural/jogar.php?jogo='.$idJogo);
            die('Aguarde um momento...');
        }
        
        // Validado, agora atualizando a descricao...
        
        $sql = $con->prepare("UPDATE tbl_uploads SET descricaoJogo_Upload = ? WHERE id_Upload = ?");
        $sql->bind_param("ss", $descricaoJogo, $idJogo);
        if($sql->execute())
        {
            $_SESSION['modalAlerta']['titulo'] = 'Descrição Alterada';
            $_SESSION['modalAlerta']['mensagem'] = 'Descrição alterada com sucesso!';
            $_SESSION['modalAlerta']['tipo'] = 'success';
            header('location: ../mural/jogar.php?jogo='.$idJogo);
            die('Aguarde um momento...');
        }
        else
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Ocorreu um erro ao tentar alterar a descrição do jogo.';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../mural/jogar.php?jogo='.$idJogo);
            die('Aguarde um momento...');
        }
    }
}