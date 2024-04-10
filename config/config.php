<?php
    // Connect to database
    $servername = "fdb1033.awardspace.net";
    $db_username = "4466440_matchmaster";
    $db_password = "F28CD_Group4";
    $dbname = "4466440_matchmaster";

    $conn = mysqli_connect($servername, $db_username, $db_password, $dbname);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>