<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="../style/index.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <?php

            include("../config/config.php");
            if(isset($_POST['submit'])){
                // submitted form values
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $password = mysqli_real_escape_string($conn, $_POST['password']);
                
                // execute query
                $sql = $conn ->prepare("SELECT * FROM players WHERE name=?");
                $sql -> bind_param("s",$username);
                $sql -> execute();
                $result = $sql->get_result();
                if ($result->num_rows <=0){ 
                    echo "<div> <p> Account does not exist </p>
                    </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button> Go Back </button>";
                    die();
                }
                $row = $result->fetch_assoc();

                // matching password
                $password_match = password_verify($password, $row['password']);
                if($password_match){
                    $_SESSION['valid'] = $row['name'];
                    $_SESSION['username'] = $row['name'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['elo'] = $row['elo_rating'];

                } else {
                    echo "<div> 
                    <p> Wrong username or password </p>
                    </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button> Go Back </button>";
                }
                if (isset($_SESSION['valid'])){
                    echo "<script>location.href = 'matchingLobby.php';</script>";
                }
            } else {

        ?>
        <h1>Sign in</h1>

        <form id="login" action="" method="post">
            <!-- <label for="username">Username:</label> -->
            <input type="text" id="username" name="username" placeholder="Username" required>
            <br>
            <!-- <label for="password">Password:</label> -->
            <input type="password" id="password" name="password" placeholder="Password" required>
            <br>
            <button type="submit" name="submit">Log In</button>
        </form>
        <button id="createAccount" onclick="goToCreateAccount()">or Create Account</button>
    </div>
    <?php } ?>
    <script>
        function goToCreateAccount() {
            window.location.href = "createAccount.php";
        }
    </script>
</body>
</html>
