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
                $rep_password = $_POST['reppassword'];
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // check if username or email is already registered
                $sql = $conn->prepare("SELECT * FROM players WHERE name=? OR email=?");
                $sql->bind_param("ss", $username, $email);
                $sql->execute();
                $result = $sql->get_result();
                if ($result->num_rows>0){ 
                    echo "<div>
                        <p> Username or email is already registered! </p>
                    </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button> Go Back </button>";
                } else {
                    // generate random player values
                    $combat_score = rand(1200, 1800);
                    $elo_rating = rand(10000, 20000) / 10;
                    $kills = rand(0, 10);
                    $deaths = rand(0, 10);

                    // execute query
                    $sql = $conn->prepare("INSERT INTO players (name, email, password, combat_score, elo_rating, kills, deaths) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $sql->bind_param("sssdddd", $username, $email, $hashed_password, $combat_score, $elo_rating, $kills, $deaths);
                    $sql->execute();

                    // check for success or error
                    if ($sql->affected_rows > 0) {
                        echo "<div>
                            <p> Account created successfully! </p>
                        </div> <br>";
                        echo "<a href='index.php'><button> Login now </button>";
                    } else {
                        echo "Error creating account: " . $sql->error . "<br>";
                        echo "<a href='javascript:self.history.back()'><button> Go Back </button>";
                    }
                }
            } else {
        ?>

        <h1>Sign Up</h1>

        <form id="createAcc" action ="" onsubmit="return validateForm()" method="post">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <br>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <br>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <br>
            <input type="password" id="reppassword" name="reppassword" placeholder="Repeat Password" required>
            <div id="rep_password_alert" style="color: red; display: none;">Passwords do not match.</div>
            <br>
            <button type="submit" name="submit">Create Account</button>
        </form>

        <button id="loginPage" onclick="goToLoginPage()">Go to sign in</button>
    </div>
    <?php } ?>
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var repPassword = document.getElementById("reppassword").value;
            var repPasswordAlert = document.getElementById("rep_password_alert");
            
            if (password !== repPassword) {
                repPasswordAlert.style.display = "block";
                return false;
            } else {
                repPasswordAlert.style.display = "none";
                return true;
            }
        }

        function goToLoginPage() {
            window.location.href = "index.php";
        }
    </script>
</body>
</html>