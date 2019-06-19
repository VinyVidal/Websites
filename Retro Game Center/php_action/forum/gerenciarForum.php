<?php include_once("../../lib/lib.php");

    if(isset($_POST['alterarCategoria']))
    {
        $nomeCategoria = $_POST['nomeCategoria'];
        $descCategoria = $_POST['descCategoria'];
        $idCategoria = $_POST['idCategoria'];
        
        echo $nomeCategoria.$descCategoria.$idCategoria;
    }