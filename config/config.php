<?php
    // Connect to database
    $servername = "fdb1033.awardspace.net";
    $db_username = "4466136_matchmaster";
    $db_password = "%nk_M-ai5Yo_*gw;";
    $dbname = "4466136_matchmaster";

    $conn = mysqli_connect($servername, $db_username, $db_password, $dbname);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>