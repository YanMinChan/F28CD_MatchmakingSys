<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link href="../style/createAccount.css" rel="stylesheet" type="text/css"> 
</head>
<body>
    <!-- the whole create account container -->
    <div class="container">

        <?php
            // include database configuration
            include("../config/config.php");

            // obtain info from POST
            if (isset($_POST['submit'])){
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // execute query
                $sql = $conn->prepare("INSERT INTO players (name, email, password) VALUES (?, ?, ?)");
                $sql->bind_param("sss", $username, $email, $hashed_password);
                $sql->execute();

                // check for success or error
                if ($sql->affected_rows > 0) {
                    echo "<div>
                        <p> Account created successfully! </p>
                    </div> <br>";
                    echo "<a href='index.php'><button> Login now </button>";
                } else {
                    echo "Error creating account: " . $sql->error;
                }
            } else {
        ?>

        <h1>Sign Up</h1>

        <form id="createAcc" action = "" method="post">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <br>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <br>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <br>
            <input type="password" id="reppassword" name="reppassword" placeholder="Repeat Password" required>
            <br>
            <button type="submit" name="submit">Create Account</button>
        </form>

        <button id="loginPage" onclick="goToLoginPage()">Go to sign in</button>
    </div>
    <?php } ?>
    <script>
        function goToLoginPage() {
            window.location.href = "index.html";
        }
    </script>
</body>
</html>