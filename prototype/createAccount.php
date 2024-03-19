<?php
// 1. Get form data
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// 2. Connect to database
$servername = "132.145.18.222";
$db_username = "yc89";
$db_password = "t2!BgOChrfZ";
$dbname = "yc89";

$conn = mysqli_connect($servername, $db_username, $db_password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 3. Hash the password (for security)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 4. Check if the users table exists, if not, create it
$check_table_query = "SHOW TABLES LIKE 'users'";
$result = mysqli_query($conn, $check_table_query);

if (mysqli_num_rows($result) == 0) {
    // Table does not exist, create it
    $create_table_query = "CREATE TABLE users (
        username VARCHAR(255) PRIMARY KEY,
        email VARCHAR(255),
        password VARCHAR(255)
    )";
    if (mysqli_query($conn, $create_table_query)) {
        echo "Table created successfully!";
    } else {
        echo "Error creating table: " . mysqli_error($conn);
    }
}

$sql = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$sql->bind_param("sss", $username, $email, $hashed_password);


// 5. Execute the query
$sql->execute();

// 6. Check for success or errors
if ($sql->affected_rows > 0) {
    // echo "Account created successfully!";
    echo '<script type="text/javascript">
        alert("Welcome at c-sharpcorner.com."); }
    </script>';
} else {
    echo "Error creating account: " . $sql->error;
}

// 7. Close connection
$sql->close();
$conn->close();
?>