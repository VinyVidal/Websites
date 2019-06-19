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
        if(isset($_POST['enviarJogo']))
        {
            $idUser = $_SESSION['user']['idUser']; // ID do usuario (Será obtido atraves de uma SESSION referente ao usuario logado)
            /* Pegando e filtrando os dados do Form */
            $nomeJogo = filterField($_POST['txtNomeJogo']); // filterField está em 'lib/functions.php'
            $descricaoJogo = filterField($_POST['txtDescricao']);

            

            /* CAMINHO A PARTIR DA RAIZ DO SERVER */
            $path = 'uploads/pendentes/temp/'; /* caminho a ser salvo. primeiro na pasta geral pendentes, depois será movido para uma pasta com o ID DO UPLOAD, caso a query nao der erros */

            /* Verificando a existencia do diretorio, caso não exista, cria-lo */
            if(is_file('../'.$path) == false)
            {
                mkdir('../'.$path);
            }

            if($_FILES['capaJogo']['size'] > 0)
            {
                /* upload da imagem de capa*/
                $imgNome = basename($_FILES['capaJogo']['name']); // Nome do arquivo
                $img = $path.$imgNome; // caminho+nome a ser salvo no banco
                $imgTipo = strtolower(pathinfo($img, PATHINFO_EXTENSION)); // extensão(tipo) do arquivo
                $imgNome = md5(uniqid(rand())); // Novo nome da imagem (nome 'encriptado') NOME SEM EXTENSAO
                $img = $path.$imgNome.'.'.$imgTipo; // Novo nome do imagem (com valores aleatorios)
            }
            
            if($_FILES['arquivoJogo']['size'] > 0)
            {
                /* upload do arquivo do jogo */
                $arquivoNome = basename($_FILES['arquivoJogo']['name']);
                $arquivo = $path.$arquivoNome;
                $arquivoTipo = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
                $arquivoNome = md5(uniqid(rand())); // novo nome do arquivo
                $arquivo = $path.$arquivoNome.'.'.$arquivoTipo;
            }

            $imgsPermitidas = ['png', 'jpg', 'jpeg', 'bmp'];
            $arquivosPermitidos = ['zip'];

            /* VALIDANDO OS CAMPOS */
            if(empty($nomeJogo) // se algum campo obrigatorio estiver vazio (nesse caso todos são obrigatorios)
                || empty($descricaoJogo)
                || $_FILES['capaJogo']['size'] == 0
                || $_FILES['arquivoJogo']['size'] == 0
                )
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Todos os campos devem ser preenchidos!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../mural/enviarJogo.php');
                die('Aguarde um momento...');
            }

            // Se já existir um jogo com o mesmo nome (seja ele pendente ou aprovado)
            if(!gameNameVerify($con, $nomeJogo))
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'O jogo de outro usuário já utiliza esse nome. Tente novamente usando outro nome.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../mural/enviarJogo.php');
                die('Aguarde um momento...');
            }

            /* VALIDANDO FILES */

            if(!in_array($imgTipo, $imgsPermitidas)) // Se o arquivo nao for uma img valida
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Apenas imagens .png, .bmp, .jpg e .jpeg são permitidas!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../mural/enviarJogo.php');
                die('Aguarde um momento...');
            }
            
            /* verificando tamanho do arquivo */
            if($_FILES['capaJogo']['size'] > 1048576) // Se for maior que 1 MB
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Imagem muito grande!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../mural/enviarJogo.php');
                die('Aguarde um momento...');
                
            }
            /* Agora a img já esta validada */


            if(!in_array($arquivoTipo, $arquivosPermitidos))
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Os arquivos do jogo devem estar compactados com a extensão .zip!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../mural/enviarJogo.php');
                die('Aguarde um momento...');
            }

            if($_FILES['arquivoJogo']['size'] > 52428800) // se o arquivo for maior que 50 MB
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Arquivo ZIP muito grande!';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../mural/enviarJogo.php');
                die('Aguarde um momento...');
            }
            /* Agora o arquivo já esta validado */

            // Erro caso já existe os arquivos no diretorio
            if(is_file('../'.$img))
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Conflito com o nome da imagem de capa. Renomeie a imagem e tente novamente.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../mural/enviarJogo.php');
                die('Aguarde um momento...');
            }
            if(is_file('../'.$arquivo))
            {
                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                $_SESSION['modalAlerta']['mensagem'] = 'Conflito com o nome do arquivo do jogo. Renomeie o arquivo zipado e tente novamente.';
                $_SESSION['modalAlerta']['tipo'] = 'error';
                header('location: ../mural/enviarJogo.php');
                die('Aguarde um momento...');
            }
            /* Salvando os arquivos na pasta uploads (temporario)*/
            if(move_uploaded_file($_FILES['capaJogo']['tmp_name'], '../'.$img) && move_uploaded_file($_FILES['arquivoJogo']['tmp_name'], '../'.$arquivo))
                // Se os dois arquivos foram salvos com sucesso no caminho temporario
            {
                // Tentar inserir os dados no banco
                $sql = $con->prepare('CALL sp_enviaJogo(?, ?, ?, ?, ?, ?)');
                $sql->bind_param('isssss', $idUser, $nomeJogo, $descricaoJogo, $img, $arquivo, date('Y-m-d H:i:s'));
                if($sql->execute()) // Se a query foi executada com sucesso
                {
                    if($sql->affected_rows > 0)
                    {
                        // jogo foi enviado (teoricamente)

                        // Movendo os arquivos para o diretorio definitivo
                        $sql = $con->prepare('SELECT id_Upload, imgCapaJogo_Upload, arquivosJogo_Upload FROM tbl_uploads WHERE nomeJogo_Upload = ?'); // AQUI QUE ESTA O PROBLEMA
                        $sql->bind_param('s', $nomeJogo);
                        $sql->execute();
                        $get = $sql->get_result();
                        $upload = $get->fetch_assoc(); // pega as info do upload que acabou de ser enviado

                        // caminho definivo (pasta upload+idupload/)
                        if($upload['id_Upload'] < 10)
                        {
                            $defPath = 'uploads/pendentes/upload0'.$upload['id_Upload'].'/';
                        }
                        else
                        {
                            $defPath = 'uploads/pendentes/upload'.$upload['id_Upload'].'/';
                        }

                        if(!is_file('../'.$defPath)) // se não existir a pasta, cria-la
                        {
                            mkdir('../'.$defPath);
                        }
                        $defImg = $defPath.$imgNome.'.'.$imgTipo;
                        $defArquivo = $defPath.$arquivoNome.'.'.$arquivoTipo;
                        // Tentando mover os arquivos da pasta temp para a pasta definitiva
                        if(rename('../'.$img, '../'.$defImg) && rename('../'.$arquivo, '../'.$defArquivo))
                        {

                            // atualiza o caminho no banco
                            $sql = $con->prepare('UPDATE tbl_uploads SET imgCapaJogo_Upload = ?, arquivosJogo_Upload = ? WHERE id_Upload = ?');
                            $sql->bind_param('ssi', $defImg, $defArquivo, $upload['id_Upload']);

                            // liberando consulta anterior
                            $get->free_result();

                            if($sql->execute())
                            {
                                $_SESSION['modalAlerta']['titulo'] = 'Jogo enviado';
                                $_SESSION['modalAlerta']['mensagem'] = 'Seu jogo foi enviado com sucesso! Aguarde a avaliação de um administrador do site.';
                                $_SESSION['modalAlerta']['tipo'] = 'success';
                                header('location: ../mural/enviarJogo.php'); // Aqui deveria redirecionar para o profile do usuario, na aba de jogo pendente
                                die('Aguarde um momento...');
                            }
                            else
                            {
                                $_SESSION['modalAlerta']['titulo'] = 'Erro';
                                $_SESSION['modalAlerta']['mensagem'] = mysqli_error($con);
                                $_SESSION['modalAlerta']['tipo'] = 'error';
                                header('location: ../mural/enviarJogo.php');
                                die('Aguarde um momento...');
                            }

                            
                        }
                        else
                        {
                            // liberando consulta anterior
                            $get->free_result();
                            $_SESSION['modalAlerta']['titulo'] = 'Jogo enviado com erros';
                            $_SESSION['modalAlerta']['mensagem'] = 'O seu jogo não foi enviado corretamente, por favor contate um administrador.';
                            $_SESSION['modalAlerta']['tipo'] = 'danger';
                            header('location: ../mural/enviarJogo.php'); // Aqui deveria redirecionar para o profile do usuario, na aba de jogo pendente
                            die('Aguarde um momento...');
                        }

                        /* Apos o jogo ser enviado, incrementar o campo jogosPendentes_User da Tbl_Usuarios*/
                        $_SESSION['modalAlerta']['titulo'] = 'Jogo enviado';
                        $_SESSION['modalAlerta']['mensagem'] = 'Seu jogo foi enviado com sucesso! Aguarde a avaliação de um administrador do site.';
                        $_SESSION['modalAlerta']['tipo'] = 'success';
                        header('location: ../mural/enviarJogo.php'); // Aqui deveria redirecionar para o profile do usuario, na aba de jogo pendente
                        die('Aguarde um momento...');
                    }
                    else
                    {
                        // Senão, apagar o arquivo que foi movido
                        // apagando a imagem
                        if(is_file('../'.$img))
                        {
                            unlink('../'.$img);
                        }

                        // apagando o .zip
                        if(is_file('../'.$arquivo))
                        {
                            unlink('../'.$arquivo);
                        }

                        $_SESSION['modalAlerta']['titulo'] = 'Erro';
                        $_SESSION['modalAlerta']['mensagem'] = 'Erro no banco de dados, o jogo não foi enviado.';
                        $_SESSION['modalAlerta']['tipo'] = 'error';
                        header('location: ../mural/enviarJogo.php'); // Melhor redirecionar para o profile, na aba de solicitações pendentes
                        die('Aguarde um momento...');
                    }
                }
                else
                {
                    // Senão, apagar o arquivo que foi movido
                    // apagando a imagem
                    if(is_file('../'.$img))
                    {
                        unlink('../'.$img);
                    }

                    // apagando o .zip
                    if(is_file('../'.$arquivo))
                    {
                        unlink('../'.$arquivo);
                    }

                    $_SESSION['modalAlerta']['titulo'] = 'Erro';
                    $_SESSION['modalAlerta']['mensagem'] = mysqli_error($con);
                    $_SESSION['modalAlerta']['tipo'] = 'error';
                    header('location: ../mural/enviarJogo.php');
                    die('Aguarde um momento...');
                }
            }

        }
    }