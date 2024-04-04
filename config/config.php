<?php
    // Connect to database
    $servername = "132.145.18.222";
    $db_username = "yc89";
    $db_password = "t2!BgOChrfZ";
    $dbname = "yc89";

    $conn = mysqli_connect($servername, $db_username, $db_password, $dbname);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>