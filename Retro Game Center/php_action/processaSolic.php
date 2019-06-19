<?php
// processaSolic.php
// Aprova ou Reprova a solicitacao

include_once("../lib/lib.php");
session_start();

if(!isset($_SESSION['user'])) // se nao estiver logado
{
    // redirecionar para a pg de login
    header('location: ../user/login.php');
}

$sessionUserId = $_SESSION['user']['idUser']; // Esse é o usuario aprovando/reprovando a solic

$sql = $con->prepare("SELECT * FROM tbl_usuarios WHERE id_User = ?");
$sql->bind_param("s", $sessionUserId);
$sql->execute();
$get = $sql->get_result();
$sessionUser = $get->fetch_assoc();

if($sessionUser['nivel_User'] < 3) // usuario nao tem nivel de acesso necessario
{
  $get->free_result();
  header('location: ../user/');
}

$get->free_result();

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    /* SE A Ação FOI REPROVAR O PEDIDO */
    if(isset($_POST['btnReprovar']))
    {
        $idUser = $_POST['idUser'];
        $idUpload = $_POST['idUpload'];
        $sql = $con->prepare('CALL sp_reprovaSolicitacao(?, ?, ?)');
        $sql->bind_param('iis', $idUpload, $idUser, date('Y-m-d H:i:s'));
        if($sql->execute())
        {
            /* AQUI DEVE NOTIFICAR O USUARIO SOBRE A REPROVACAO */
            /* DEPOIS IMPLEMENTAR O MOTIVO DA REPROVACAO */

            /* Movendo os arquivos para a pasta 'reprovados' */
            // pegando os caminhos antigos (pasta pendentes)
            $sql = $con->prepare('SELECT imgCapaJogo_Upload, arquivosJogo_Upload, nomeJogo_Upload FROM tbl_uploads WHERE id_Upload = ?');
            $sql->bind_param('i', $idUpload);
            $sql->execute();
            $get = $sql->get_result();
            $caminhos = $get->fetch_assoc();

            $nomeJogo = $caminhos['nomeJogo_Upload']. ' (REPROVADO)'; // nome do jogo reprovado
            // novo diretorio
            $novoPathImg = str_replace('/pendentes/', '/reprovados/', $caminhos['imgCapaJogo_Upload']);
            $novoPathImg = str_replace('/aprovados/', '/reprovados/', $novoPathImg);
            $novoPathArquivo = str_replace('/pendentes/', '/reprovados/', $caminhos['arquivosJogo_Upload']);
            $novoPathArquivo = str_replace('/aprovados/', '/reprovados/', $novoPathArquivo);

            // verificando a existencia do novo diretorio;
            if($idUpload < 10)
            {
                $antDir = 'uploads/pendentes/upload0'.$idUpload.'/' ; // Diretorio anterior (é na pasta pendentes)
                $novoDir = mb_substr($novoPathImg, 0, 19).'upload0'.$idUpload.'/';
            }
            else
            {
                $antDir = 'uploads/pendentes/upload'.$idUpload.'/' ;
                $novoDir = mb_substr($novoPathImg, 0, 19).'upload'.$idUpload.'/';
            }
            // Se não existir, cria-lo
            if(!is_file('../'.$novoDir))
            {
                mkdir('../'.$novoDir);
            }
            if((file_exists('../'.$caminhos['imgCapaJogo_Upload']) && !file_exists('../'.$novoPathImg))
            && (file_exists('../'.$caminhos['arquivosJogo_Upload']) && !file_exists('../'.$novoPathArquivo)))
            {
                if(rename('../'.$caminhos['imgCapaJogo_Upload'], '../'.$novoPathImg)
                && rename('../'.$caminhos['arquivosJogo_Upload'], '../'.$novoPathArquivo))
                {
                // liberando dados da consulta
                $get->free_result();

                // apagar diretorio anterior
                rmdir('../'.$antDir);
                

                /* Se moveu, dar update no caminho*/
                $sql = $con->prepare('UPDATE tbl_uploads SET imgCapaJogo_Upload = ?, arquivosJogo_Upload = ?, nomeJogo_Upload = ? WHERE id_Upload = ?');
                $sql->bind_param('sssi', $novoPathImg, $novoPathArquivo, $nomeJogo, $idUpload);
                $sql->execute();

                // Enviado Mensagem ao usuario informando a reprovacao da solic
                // Pegando o motivo da reprovacao
                $motivo = filterField($_POST['motivoReprovacao']);
                if(empty($motivo))
                {
                  $motivo = 'Nenhum motivo específico foi informado.';
                }

                // Pegando o nome do jogo
                $sql = $con->prepare("SELECT nomeJogo_Upload FROM tbl_uploads WHERE id_Upload = ?");
                $sql->bind_param('s', $idUpload);
                $sql->execute();
                $getJogo = $sql->get_result();
                $dadosNomeJogo = $getJogo->fetch_assoc();
                $nomeJogo = $dadosNomeJogo['nomeJogo_Upload'];

                if($sessionUserId != $idUser) // Só vai enviar msg caso o admin nao esteja aprovando o proprio jogo
                {
                  $assunto = 'Jogo enviado reprovado (MENSAGEM AUTOMÁTICA)';
                  $corpo = 'O seu jogo [ '.$nomeJogo.' ] foi reprovado pelo seguinte motivo:<br>
                  <i>'.$motivo.'</i><br>
                  Leia as regras de envio de jogos no forum para saber como enviar um jogo no formato correto!';

                  sendMessage($con, $sessionUserId, $idUser, $assunto, $corpo);
                }

                $getJogo->free_result();

                $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                $_SESSION['modalAlerta']['mensagem'] = 'Solicitação reprovada com sucesso!';
                $_SESSION['modalAlerta']['tipo'] = 'success';
                header('location: ../user/profile.php');
                }
                else
                {
                    $get->free_result();
                    $_SESSION['modalAlerta']['titulo'] = 'Erro';
                    $_SESSION['modalAlerta']['mensagem'] = 'Erro ao mover o arquivo. Contate o responsável do site.';
                    $_SESSION['modalAlerta']['tipo'] = 'error';
                    header('location: ../user/profile.php');
                    die('Aguarde um momento');
                }

                
            }
            else
            {
                $get->free_result();
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Erro ao mover o arquivo. Contate o responsável do site.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/profile.php');
                die('Aguarde um momento');
            }
            
        }
        else
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = "Erro no banco de dados, contate o responsável do site.";
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../user/profile.php');
            die('Aguarde um momento');
        }
    }
    // CASO a ação foi APROVAR o pedido
    else if(isset($_POST['btnAprovar']))
    {
        // Primeiro, atualizar os dados com as informações modificadas

        // Recuperando dados enviados
        $idUser = $_POST['idUser'];
        $idUpload = $_POST['idUpload'];
        $nomeJogo = filterField($_POST['nomeJogo']);
        $descricaoJogo = filterField($_POST['descricaoJogo']);

        //Recuperando os dados do BD
      $sql = $con->prepare("SELECT nomeJogo_Upload, imgCapaJogo_Upload, arquivosJogo_Upload FROM tbl_uploads WHERE id_Upload = ?");
      $sql->bind_param('i', $idUpload);
      $sql->execute();
      $get = $sql->get_result();
      $dados = $get->fetch_assoc();

      $nomeJogoSalvo = $dados['nomeJogo_Upload'];
      $imgCapaJogoSalvo = $dados['imgCapaJogo_Upload'];
      $arquivoJogoSalvo = $dados['arquivosJogo_Upload'];

      $get->free_result();


        // Verificando se os campos nome do jogo e descricao estao preenchidos
        if(empty($nomeJogo) || empty($descricaoJogo))
        {
          $_SESSION['modalAlerta']['titulo'] = 'Aviso';
            $_SESSION['modalAlerta']['mensagem'] = 'O nome do jogo e a descrição NÃO podem estar vazios!';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../user/solicitacao.php?id='.$idUpload);
            die('Aguarde um momento');
        }
        else if($nomeJogo !== $nomeJogoSalvo) // Se o nome do jogo foi editado
        {
          if(!gameNameVerify($con, $nomeJogo)) // verificar se o novo nome já esta sendo usado
          {
            $_SESSION['modalAlerta']['titulo'] = 'Nome do jogo duplicado';
              $_SESSION['modalAlerta']['mensagem'] = 'O nome do jogo inserido não pode ser usado, use outro nome!';
              $_SESSION['modalAlerta']['tipo'] = 'error';
              header('location: ../user/solicitacao.php?id='.$idUpload);
              die('Aguarde um momento');
          } 
        }

        // os Dados serao atualizados dps de salvar os arquivos editados

        // Validando os arquivos, caso tenham sido modificados
        // Verificando se a imagem de capa foi alterada
        if($_FILES['capaJogo']['size'] > 0)
        {
          // Se a imagem foi editada, atualiza-la
          if($idUpload < 10)
            {
                $path = 'uploads/pendentes/upload0'.$idUpload.'/' ; // caminho que está a imagem
            }
            else
            {
                $path = 'uploads/pendentes/upload'.$idUpload.'/' ;
            }
          // Validando a nova imagem
          $imgNome = basename($_FILES['capaJogo']['name']); // Nome do arquivo
            $img = $path.$imgNome; // caminho+nome a ser salvo no banco
            $imgTipo = strtolower(pathinfo($img, PATHINFO_EXTENSION)); // extensão(tipo) do arquivo
            $imgNome = md5(uniqid(rand())); // Novo nome da imagem (nome 'encriptado') NOME SEM EXTENSAO
            $img = $path.$imgNome.'.'.$imgTipo; // Novo nome do imagem (com valores aleatorios)

            $imgsPermitidas = ['png', 'jpg', 'jpeg', 'bmp'];

            if(!in_array($imgTipo, $imgsPermitidas)) // Se o arquivo nao for uma img valida
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Apenas imagens .png, .bmp, .jpg e .jpeg são permitidas!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/solicitacao.php?id='.$idUpload);
                die('Aguarde um momento');
            }
            else if($_FILES['capaJogo']['size'] > 1048576) // Se for maior que 1 MB
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Imagem muito grande!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/solicitacao.php?id='.$idUpload);    
                die('Aguarde um momento'); 
            }
            else
            {
              // Imagem validada. Apagando a imagem antiga e salvando a nova
              if(is_file('../'.$imgCapaJogoSalvo))
              {
                unlink('../'.$imgCapaJogoSalvo);
              }

              if(!file_exists('../'.$img))
              {
                move_uploaded_file($_FILES['capaJogo']['tmp_name'], '../'.$img); // nova imagem salva com sucesso, atualizando o nome...
                $sql = $con->prepare("UPDATE tbl_uploads SET imgCapaJogo_Upload = ? WHERE id_Upload = ?");
            $sql->bind_param('si', $img, $idUpload);
            $sql->execute();
              }
              else
              {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                  $_SESSION['modalAlerta']['mensagem'] = 'Conflito com o nome da imagem de capa do jogo. Renomeie a imagem e tente novamente.';
                  $_SESSION['modalAlerta']['tipo'] = 'error';
                  header('location: ../user/solicitacao.php?id='.$idUpload);
                  die('Aguarde um momento');
              }
            }
        }

        // Verificando se arquivo zip foi alterado
        if($_FILES['arquivosJogo']['size'] > 0)
        {
          // Se o arquivo foi, atualiza-lo
          if($idUpload < 10)
            {
                $path = 'uploads/pendentes/upload0'.$idUpload.'/' ; // caminho que está o arquivo (e a imagem)
            }
            else
            {
                $path = 'uploads/pendentes/upload'.$idUpload.'/' ;
            }
          // Validando o novo arquivo
          $arquivoNome = basename($_FILES['arquivosJogo']['name']); // Nome do arquivo
            $arquivo = $path.$arquivoNome; // caminho+nome a ser salvo no banco
            $arquivoTipo = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION)); // extensão(tipo) do arquivo
            $arquivoNome = md5(uniqid(rand())); // Novo nome do arquivo (nome 'encriptado') NOME SEM EXTENSAO
            $arquivo = $path.$arquivoNome.'.'.$arquivoTipo; // Novo nome do arquivo (com valores aleatorios)

            $arquivosPermitidos = ['zip'];

            if(!in_array($arquivoTipo, $arquivosPermitidos)) // Se o arquivo nao for uma img valida
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Apenas arquivos .ZIP são permitidos!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/solicitacao.php?id='.$idUpload);
                die('Aguarde um momento');
            }
            else if($_FILES['arquivosJogo']['size'] > 52428800) // Se for maior que 50 MB
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'arquivo muito grande!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/solicitacao.php?id='.$idUpload);    
                die('Aguarde um momento'); 
            }
            else
            {
              // arquivo validado. Apagando o arquivo antigo e salvando o novo
              if(is_file('../'.$arquivoJogoSalvo))
              {
                unlink('../'.$arquivoJogoSalvo);
              }

              if(!file_exists('../'.$arquivo))
              {
                move_uploaded_file($_FILES['arquivosJogo']['tmp_name'], '../'.$arquivo); // novo arquivo salvo com sucesso, atualizando o nome...
                $sql = $con->prepare("UPDATE tbl_uploads SET arquivosJogo_Upload = ? WHERE id_Upload = ?");
            $sql->bind_param('si', $arquivo, $idUpload);
            $sql->execute();
              }
              else
              {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                  $_SESSION['modalAlerta']['mensagem'] = 'Conflito com o nome do arquivo do jogo. Renomeie o arquivo zipado e tente novamente.';
                  $_SESSION['modalAlerta']['tipo'] = 'error';
                  header('location: ../user/solicitacao.php?id='.$idUpload);
                  die('Aguarde um momento');
              }
            }
        }

        
        // Atualizando nome do jogo e descricao...
        $sql = $con->prepare("UPDATE tbl_uploads SET nomeJogo_Upload = ?, descricaoJogo_Upload = ? WHERE id_Upload = ?");
        $sql->bind_param('ssi', $nomeJogo, $descricaoJogo, $idUpload);
        if($sql->execute())
        {
            // Nada é feito
        }
        else
        {
            $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = 'Erro no banco de dados, verifique a conexão.';
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../user/solicitacao.php?id='.$idUpload);
            die('Aguarde um momento');
        }

        $sql = $con->prepare('CALL sp_aprovaSolicitacao(?, ?, ?)');
        $sql->bind_param('iis', $idUpload, $idUser, date('Y-m-d H:i:s'));
        if($sql->execute())
        {
          /* Movendo os arquivos para a pasta aprovados.. */
          // pegando os caminhos antigos (pasta pendentes)
            $sql = $con->prepare('SELECT imgCapaJogo_Upload, arquivosJogo_Upload FROM tbl_uploads WHERE id_Upload = ?');
            $sql->bind_param('i', $idUpload);
            $sql->execute();
            $get = $sql->get_result();
            $caminhos = $get->fetch_assoc();

            // novo diretorio
            $novoPathImg = str_replace('/pendentes/', '/aprovados/', $caminhos['imgCapaJogo_Upload']);
            $novoPathArquivo = str_replace('/pendentes/', '/aprovados/', $caminhos['arquivosJogo_Upload']);

            // verificando a existencia do novo diretorio;
            if($idUpload < 10)
            {
                $antDir = 'uploads/pendentes/upload0'.$idUpload.'/' ; // Diretorio anterior (é na pasta pendentes)
                $novoDir = mb_substr($novoPathImg, 0, 18).'upload0'.$idUpload.'/';
            }
            else
            {
                $antDir = 'uploads/pendentes/upload'.$idUpload.'/' ;
                $novoDir = mb_substr($novoPathImg, 0, 18).'upload'.$idUpload.'/';
            }
            // Se não existir, cria-lo
            if(!is_file('../'.$novoDir))
            {
                mkdir('../'.$novoDir);
            }
            if((file_exists('../'.$caminhos['imgCapaJogo_Upload']) && !file_exists('../'.$novoPathImg))
            && (file_exists('../'.$caminhos['arquivos_Upload']) && !file_exists('../'.$novoPathArquivo)))
            {
                if(rename('../'.$caminhos['imgCapaJogo_Upload'], '../'.$novoPathImg)
                && rename('../'.$caminhos['arquivosJogo_Upload'], '../'.$novoPathArquivo))
                {
                  // liberando dados da consulta
                  $get->free_result();

                  // apagar diretorio anterior
                  rmdir('../'.$antDir);
                  

                  /* Se moveu, dar update no caminho*/
                  $sql = $con->prepare('UPDATE tbl_uploads SET imgCapaJogo_Upload = ?, arquivosJogo_Upload = ? WHERE id_Upload = ?');
                  $sql->bind_param('ssi', $novoPathImg, $novoPathArquivo, $idUpload);
                  $sql->execute();
                  
                  /* Descompactando o ZIP */
                  $zip = new ZipArchive; // Classe do php que manipula arquivos ZIP
                  if($zip->open('../'.$novoPathArquivo))
                  {
                    $extraiu = $zip->extractTo('../'.$novoDir.'jogo'.$idUpload);

                    $zip->close();
                        /* JOGO APROVADO !! !! ! !*/
                    /* Enviando mensagem ao usuario informando a aprovação */

                    if($sessionUserId != $idUser) // Só vai enviar msg caso o admin nao esteja aprovando o proprio jogo
                    {

                      $assunto = 'Jogo enviado aprovado (MENSAGEM AUTOMÁTICA)';
                      $corpo = 'O seu jogo [ '.$nomeJogo.' ] foi aprovado e já está jogável no mural do site!<br>
                      <a href="../mural/jogar.php?jogo='.$idUpload.'">Clique aqui</a> para ver o seu jogo.<br>
                      <a href="../mural/jogos.php">Clique aqui</a> para ver o mural de jogos.';
                      
                      sendMessage($con, $sessionUserId, $idUser, $assunto, $corpo);
                    }



                    $_SESSION['modalAlerta']['titulo'] = 'Sucesso';
                    $_SESSION['modalAlerta']['mensagem'] = "Jogo aprovado com sucesso! Veja o jogo no Mural para ter certeza que tudo ocorreu bem.";
                    $_SESSION['modalAlerta']['tipo'] = 'success';
                    header('location: ../user/profile.php');
                  }
                  else
                  {
                    $_SESSION['modalAlerta']['titulo'] = 'Erro';
                      $_SESSION['modalAlerta']['mensagem'] = 'Erro ao descompactar o arquivo. Contate o responsável do site.';
                      $_SESSION['modalAlerta']['tipo'] = 'error';
                      header('location: ../user/profile.php');
                      die('Aguarde um momento');
                  }
                  
                  
                }
                else
                {
                    $get->free_result();
                    $_SESSION['modalAlerta']['titulo'] = 'Erro';
                    $_SESSION['modalAlerta']['mensagem'] = 'Erro ao mover o arquivo. Contate o responsável do site.';
                    $_SESSION['modalAlerta']['tipo'] = 'error';
                    header('location: ../user/profile.php');
                    die('Aguarde um momento');
                }    
            }
            else
            {
                $get->free_result();
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Erro ao mover o arquivo. Contate o responsável do site.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../user/profile.php');
                die('Aguarde um momento');
            }
        }
        else
        {
          $_SESSION['modalAlerta']['titulo'] = 'Erro';
            $_SESSION['modalAlerta']['mensagem'] = "Erro no banco de dados, contate o responsável do site.";
            $_SESSION['modalAlerta']['tipo'] = 'error';
            header('location: ../user/profile.php');
            die('Aguarde um momento');
        }

    } // fim btnaprovar
    else
    {
        $_SESSION['modalAlerta']['titulo'] = 'Erro';
        $_SESSION['modalAlerta']['mensagem'] = 'Erro ao processar a solicitação';
        $_SESSION['modalAlerta']['tipo'] = 'error';
        header('location: ../user/solicitacao.php?id='.$idUpload);
        die('Aguarde um momento');
    }
}