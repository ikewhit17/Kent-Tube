<?php
  include("database.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>registration</title>
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
  .login-link {
  margin-top: 15px;
}

.login-link button {
  background: none;
  border: 1px solid #999;
  border-radius: 6px;
  padding: 6px 12px;
  cursor: pointer;
  transition: background 0.2s;
}

.login-link button:hover {
  background: #eee;
}
</style>
</head>
<body>
    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        <h2>Welcome to Kent-Tube</h2>
        username:<br>
        <input type="text" name="username" required><br>
        password:<br>
        <input type="password" name="password" required><br><br>
        <input type="submit" name="submit" value="register">
        <div class="login-link">
    <button type="button" onclick="window.location.href='login.php'">
      Already have an account? Login
    </button>
  </div>
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