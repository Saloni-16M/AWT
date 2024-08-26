<?php
session_start();
include 'C:/xampp/htdocs/AWT/includes/db_connection.php'; // Adjust path if needed

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT id, password, level FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // Bind result variables
        $stmt->bind_result($user_id, $stored_password, $level);
        $stmt->fetch();
        
        // Direct comparison of plain text passwords
        if ($password === $stored_password) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['level'] = $level;
            header("Location: /AWT/level$level/index.php"); // Redirect to level1 after login
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
    
    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Add your CSS here -->
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
        <form method="post" action="login.php">
            <!-- <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button> -->
            <div class="form-group container">
          <label for="username">Username</label>
          <input type="text" class="form-control" id="username" name="username">
        </div>
        <div class="form-group container">
          <label for="password">Password:</label>
          <input type="password" class="form-control" id="password" name="password">
          <div class="row justify-content-center container">
          <button type="submit" class="btn btn-primary my-3">Submit</button></div>
        </div>
        </form>
    </div>
</body>
</html>
