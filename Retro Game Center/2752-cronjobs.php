<?php
include('lib/lib.php');
/* Reprovar as solicitações pendentes após 7 dias */
$solicsReprovadas = 0;
$sql = $con->prepare("SELECT id_Upload, idUser_Upload, nomeJogo_Upload, dataEnvio_Upload FROM vw_uploadsPendentes");
$sql->execute();
$get = $sql->get_result();
if($get->num_rows > 0)
{
	while($l = $get->fetch_assoc())
	{
		$idUpload = $l['id_Upload'];
		

		$dataEnvio = new DateTime($l['dataEnvio_Upload']);
		$dataEnvio->modify('midnight');

		$dataAtual = new DateTime();
		$dataAtual->modify('midnight');

		$diff = $dataEnvio->diff($dataAtual);

		
		if($diff->d >= 7) // se passou 7 dias ou mais
		{
		
			// reprovar a SOLIC
			if($idUpload < 10)
			{
				$sourceDir = 'uploads/pendentes/upload0'.$idUpload.'/';
				$targetDir = 'uploads/reprovados/upload0'.$idUpload.'/';
			}
			else
			{
				$sourceDir = 'uploads/pendentes/upload'.$idUpload.'/';
				$targetDir = 'uploads/reprovados/upload'.$idUpload.'/';
			}
			
			
			if(is_dir($sourceDir))
			{
			    if(moveDir($sourceDir, $targetDir))
			    {
			    	// atualizar o caminho no banco
			    	$sql = $con->prepare("SELECT imgCapaJogo_Upload, arquivosJogo_Upload FROM tbl_uploads WHERE id_Upload = ?");
			    	$sql->bind_param("i", $idUpload);
			    	$sql->execute();
			    	$getArquivosJogo = $sql->get_result();
			    	$arquivosJogo = $getArquivosJogo->fetch_assoc();

			    	$imgPath = explode($sourceDir, $arquivosJogo['imgCapaJogo_Upload'])[1];
			    	$filesPath = explode($sourceDir, $arquivosJogo['arquivosJogo_Upload'])[1];

			    	$newImgPath = $targetDir.$imgPath;
			    	$newFilesPath = $targetDir.$filesPath;

			    	$sql = $con->prepare("UPDATE tbl_uploads SET imgCapaJogo_Upload = ?, arquivosJogo_Upload = ? WHERE id_Upload = ?");
			    	$sql->bind_param("ssi", $newImgPath, $newFilesPath, $idUpload);
			    	if($sql->execute())
			    	{
			    	    //Enviando msg ao usuario
            			$assunto = 'Jogo enviado reprovado (MENSAGEM AUTOMÁTICA)';
            	        $corpo = 'O seu jogo [ '.$l['nomeJogo_Upload'].' ] foi reprovado por ter passado 7 dias desde o envio. Tente enviar o jogo novamente e aguarde.';
            
            	        // id 3 = conta MASTER
            	        sendMessage($con, 3, $l['idUser_Upload'], $assunto, $corpo);
            
            			$sql = $con->prepare('CALL sp_reprovaSolicitacao(?, ?, ?)');
            			$sql->bind_param("iis", $idUpload, $l['idUser_Upload'], date('Y-m-d H:i:s'));
            			$sql->execute();
            
            			$solicsReprovadas++;
			    	}

			    	$getArquivosJogo->free_result();
			    }
			}


		}
	}
}

$get->free_result();
echo date('Y-m-d H:i:s').': '.$solicsReprovadas.' solicitacoes pendentes reprovadas.<br>';

/* Apagar as solicitações reprovadas após 3 dias */
$solicsApagadas = 0;
$sql = $con->prepare("SELECT id_Upload, dataReprovacao_Upload FROM vw_uploadsReprovados");
$sql->execute();
$get = $sql->get_result();
if($get->num_rows > 0)
{
	while($l = $get->fetch_assoc())
	{
		$idUpload = $l['id_Upload'];

		$dataReprov = new DateTime($l['dataReprovacao_Upload']);
		$dataReprov->modify('midnight');

		$dataAtual = new DateTime();
		$dataAtual->modify('midnight');

		$diff = $dataReprov->diff($dataAtual);

		
		if($diff->d >= 3) // se passou 3 dias ou mais
		{
		
			// Apagar os arquivos da solic
			if($idUpload < 10)
			{
				$dir = 'uploads/reprovados/upload0'.$idUpload;
			}
			else
			{
				$dir = 'uploads/reprovados/upload'.$idUpload;
			}
			
			if(is_dir($dir))
			{
			    echo 'delTree: '.delTree($dir).'<br><br>'; // AINDA NAO ESTA EXLUINDO OS ARQUIVOS DA PASTA UPLOADS
			}

			$sql = $con->prepare("DELETE FROM tbl_uploads WHERE id_Upload = ?");
			$sql->bind_param("i", $idUpload);
			$sql->execute();

			$solicsApagadas++;
		}
	}
}

$get->free_result();
echo date('Y-m-d H:i:s').': '.$solicsApagadas.' solicitacoes reprovadas excluidas.<br>';