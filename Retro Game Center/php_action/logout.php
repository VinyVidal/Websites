<?php
	session_start();
	
	if(isset($_SESSION['user'])){
		session_destroy();
		session_unset();

		header('location: ../');
		die('s');
	}
?>