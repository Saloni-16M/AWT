<?php
session_start(); // Ensure this is at the top
include 'C:/xampp/htdocs/stuck_off/includes/db_connection.php'; // Adjust path if needed

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, password, level FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $stored_password, $level);
        $stmt->fetch();
        
        // Compare plain passwords (if not hashed)
        if ($password === $stored_password) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['level'] = $level;
            header("Location: /stuck_off/level1/index.php"); // Redirect to level1 after login
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
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
