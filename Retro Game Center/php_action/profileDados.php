<?php include_once("../lib/lib.php");
session_start();

    if(isset($_SESSION['idUser'])){
        $idUser = $_SESSION['idUser'];

        $sql = $con->prepare("SELECT * FROM tbl_usuarios WHERE id_User = ?");
        $sql->bind_param("s", $idUser);
        $sql->execute();

        $get = $sql->get_result();
        $dados = $get->fetch_assoc();
    }

?>