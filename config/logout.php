<?php 
    session_start();
    session_destroy();
    header("Location: ../prototype/index.php")
?>