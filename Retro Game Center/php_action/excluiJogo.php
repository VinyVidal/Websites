<?php
// Exclui/Reprova um jogo aprovado que já esta no mural

include_once("../lib/lib.php");
session_start();

if(!isset($_SESSION['user'])) // se nao estiver logado
{
    // redirecionar para a pg de login
    header('location: ../user/login.php');
}

$currentUserId = $_SESSION['user']['idUser']; // Esse é o usuario reprovando o jogo

$sql = $con->prepare("SELECT * FROM tbl_usuarios WHERE id_User = ?");
$sql->bind_param("s", $currentUserId);
$sql->execute();
$get = $sql->get_result();
$dadosCurrentUser = $get->fetch_assoc();

if($dadosCurrentUser['nivel_User'] < 3) // usuario nao tem nivel de acesso necessario
{
  $get->free_result();
  header('location: ../user/');
}



if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if(isset($_POST['btnReprovar']))
    {
        $idJogo = $_POST['idJogo'];
        $motivo = filterField($_POST['motivoReprovacao']);
        if(empty($motivo))
        {
          $motivo = 'Nenhum motivo específico foi informado.';
        }

        $sql = $con->prepare("SELECT * FROM vw_uploadsAprovados WHERE id_Upload = ?");
        $sql->bind_param("i", $idJogo);
        $sql->execute();
        $getJogo = $sql->get_result();
        $dadosJogo = $getJogo->fetch_assoc();

        $idJogoUser = $dadosJogo['idUser_Upload'];
        $nomeJogo = $dadosJogo['nomeJogo_Upload'];

        //Movendo arquivos da pasta aprovados para a pasta pendentes
        if($idJogo < 10)
        {
            $sourceDir = 'uploads/aprovados/upload0'.$idJogo.'/';
            $targetDir = 'uploads/reprovados/upload0'.$idJogo.'/';
        }
        else
        {
            $sourceDir = 'uploads/aprovados/upload'.$idJogo.'/';
            $targetDir = 'uploads/reprovados/upload'.$idJogo.'/';
        }
        if(moveDir('../'.$sourceDir, '../'.$targetDir))
        {
            $sql = $con->prepare('CALL sp_reprovaSolicitacao(?, ?, ?)');
            $sql->bind_param('iis', $idJogo, $idJogoUser, date('Y-m-d H:i:s'));
            $sql->execute();

            // atualizar o caminho no banco
            $sql = $con->prepare("SELECT imgCapaJogo_Upload, arquivosJogo_Upload, nomeJogo_Upload FROM tbl_uploads WHERE id_Upload = ?");
            $sql->bind_param("i", $idJogo);
            $sql->execute();
            $getArquivosJogo = $sql->get_result();
            $arquivosJogo = $getArquivosJogo->fetch_assoc();

            $imgPath = explode($sourceDir, $arquivosJogo['imgCapaJogo_Upload'])[1];
            $filesPath = explode($sourceDir, $arquivosJogo['arquivosJogo_Upload'])[1];

            $newImgPath = $targetDir.$imgPath;
            $newFilesPath = $targetDir.$filesPath;
            
            $nomeRep = $arquivosJogo['nomeJogo_Upload'].' (REPROVADO)';
            
            $sql = $con->prepare("UPDATE tbl_uploads SET imgCapaJogo_Upload = ?, arquivosJogo_Upload = ?, nomeJogo_Upload = ? WHERE id_Upload = ?");
            $sql->bind_param("sssi", $newImgPath, $newFilesPath, $nomeRep, $idJogo);
            $sql->execute();

            // enviando msg ao usuario informando a exclusão do jogo
            if($currentUserId != $idJogoUser) // Só vai enviar msg caso o admin nao esteja aprovando o proprio jogo
            {
              $assunto = 'Jogo excluído (MENSAGEM AUTOMÁTICA)';
              $corpo = 'O seu jogo [ '.$nomeJogo.' ] foi excluido do mural pelo seguinte motivo:<br>
              <i>'.$motivo.'</i><br>';

              sendMessage($con, $currentUserId, $idJogoUser, $assunto, $corpo);
            }

            $getJogo->free_result();
            $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
            $_SESSION['modalAlerta']['mensagem'] = 'O jogo foi excluido com sucesso.';
            $_SESSION['modalAlerta']['tipo'] = 'success';
            header('location: ../mural/jogos.php');
        }
        else
        {
            $getJogo->free_result();
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Erro ao tentar excluir o jogo!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../mural/jogar.php?jogo='.$idJogo);
        }
    }
}
