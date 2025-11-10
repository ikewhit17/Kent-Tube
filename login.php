<?php
  include("database.php");
  session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
</head>
<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <h2>Login to Kent-Tube</h2>
    username:<br>
    <input type="text" name="username" required><br>
    password:<br>
    <input type="password" name="password" required><br><br>
    <input type="submit" name="login" value="Login">
  </form>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST["password"];

    if(empty($username) || empty($password)) {
        echo "Please fill in all fields.";
    } else {
        // Check if user exists
        $sql = "SELECT * FROM users WHERE user = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row["password"])) {
                $_SESSION["username"] = $username;
                echo "Login successful. Welcome, " . htmlspecialchars($username) . "!";
                // redirect example:
                // header("Location: dashboard.php");
                // exit;
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "User not found.";
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>