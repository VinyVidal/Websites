<?php include_once("../../lib/lib.php");
    session_start();
    
    if(isset($_POST['btnDestrancar']))
    {
        $destranca = 0;
        $idTopico = $_POST['idTopico'];
        
        $sql = $con->prepare("UPDATE tbl_topicos_forum SET trancado_Topico = ? WHERE id_Topico = ?");
        $sql->bind_param("ss", $destranca, $idTopico);
        if($sql->execute())
        {
            echo "<script>window.history.back();</script>";
        }
        else
        {
            
        }
    }
?>