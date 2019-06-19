<?php

    try {
        //$con = new PDO($host, $user, $pass,[PDO::ATTR_EMULATE_PREPARES => true]);
        $con = new mysqli($host = 'localhost', $username = 'root', $passwd = '', $dbname = 'rgc', $port = 3306);

    } catch (Exception $e) {
        if(mysqli_connect_errno())
        {
            exit('Falha na conexão com o banco de dados'. mysqli_connect_error());
        }
    }

    $con->set_charset("utf8")
    
    
?>