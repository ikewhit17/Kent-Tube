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
  <style>
  body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background-color: #f5f5f5;
    font-family: Arial, sans-serif;
  }
  form {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    text-align: center;
  }
  input, button {
    margin: 5px 0;
  }
</style>
</head>
<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <h2>Login to Kent-Tube</h2>
    username:<br>
    <input type="text" name="username" required><br>
    password:<br>
    <input type="password" name="password" required><br><br>
    <input type="submit" name="login" value="Login">
  </form><br>
  <form action="index.php" method="get">
    <button type="submit">Don’t have an account? Register</button>
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
        $sql = "SELECT * FROM users WHERE user = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row["password"])) {
                $_SESSION["username"] = $username;
                header("Location: home.php");
                exit;
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