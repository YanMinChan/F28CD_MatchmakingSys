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
        <h1>Sign in</h1>

        <form id="login">
            <!-- <label for="username">Username:</label> -->
            <input type="text" id="username" name="username" placeholder="Username" required>
            <br>
            <!-- <label for="password">Password:</label> -->
            <input type="password" id="password" name="password" placeholder="Password" required>
            <br>
            <button type="button" onclick="login()">Log In</button>
        </form>
        <button id="createAccount" onclick="goToCreateAccount()">or Create Account</button>
    </div>

    <script>
        function goToCreateAccount() {
            window.location.href = "createAccount.php";
        }
        function login() {
            window.location.href = "matchingLobby.html";
        }
    </script>
</body>
</html>
