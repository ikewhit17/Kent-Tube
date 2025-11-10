<?php
  include("database.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>registration</title>
</head>
<body>
    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        <h2>Welcome to Kent-Tube</h2>
        username:<br>
        <input type="text" name="username" required><br>
        password:<br>
        <input type="password" name="password" required><br><br>
        <input type="submit" name="submit" value="register">
    </form>
    <br>
  <form action="login.php" method="get">
    <button type="submit">Already have an account? Login</button>
  </form>
</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    if(empty($username) || empty($password)){
        echo "Please fill in all fields.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (user, password) VALUES ('$username', '$hash')";
        if (mysqli_query($conn, $sql)) {
            header("Location: login.php");
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>