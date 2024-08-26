<?php
// Start the session only if it hasn't already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'C:/xampp/htdocs/AWT/includes/db_connection.php'; // Adjust path if needed

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in. Redirecting to login page.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $roll_no = $_POST['roll_no'];
    $remark = $_POST['remark'];
    $user_id = $_SESSION['user_id'];

    // Fetch student info
    $stmt = $conn->prepare("SELECT id FROM students WHERE roll_no = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $roll_no);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($student_id);
        $stmt->fetch();
        $stmt->close(); // Close statement after use

        // Insert remark
        $insert_stmt = $conn->prepare("INSERT INTO remarks (student_id, user_id, level, remark) VALUES (?, ?, 1, ?)");
        if (!$insert_stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $insert_stmt->bind_param("iis", $student_id, $user_id, $remark);
        
        if ($insert_stmt->execute()) {
            echo "<div class='alert alert-success text-center' role='alert'>Remark added successfully.</div>";
        } else {
            echo "Failed to add remark. Error: " . $insert_stmt->error;
        }
        $insert_stmt->close(); // Close statement after use
    } else {
        echo "Student not found.";
    }
}
?>

<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dealing Hand - Student Strike Off</title>
     Add your CSS here 
</head>
<body>
    <h2>Dealing Hand - Enter Student Details</h2>
    <form method="post" action="index.php">
        <label for="roll_no">Roll No:</label>
        <input type="text" id="roll_no" name="roll_no" required>
        <label for="remark">Remark:</label>
        <textarea id="remark" name="remark" required></textarea>
        <button type="submit">Submit</button>
    </form>
</body>
</html> -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
<h1 class="text-center">GNDEC</h1>
    <div class="container">
        <form action="index.php" method="post">
            <label for="roll_no">Roll Number</label>
            <input type="number" name="roll_no" class="form-control" id="roll_no" required>
            <label for="remark">Remarks</label>
            <textarea name="remark" id="remark" rows="3" cols="3" class="form-control" required></textarea>
            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary btn-block">Submit</button>
            </div>
        </form>
    </div>
    <div class="container mt-5 text-center">
        <!-- Button that links to the approval status page -->
        <a href="../approval_status.php" class="btn btn-primary btn-lg">
            View Student Approval Status
        </a>
    </div>
    <!-- <div class="container-fluid mt-5">
      <h1 class="text-center mb-5">List of your searched student </h1>
    <div class="d-flex flex-wrap justify-content-around mb-3">
      <div class="card mb-5" style="width: 18rem;">
        <img src="..." class="card-img-top" alt="...">
        <div class="card-body">
          <h5 class="card-title">Card title</h5>
          <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
          <a href="#" class="btn btn-primary">Go somewhere</a>
        </div>
      </div> -->
      
     
      
   
      <div class="container-fluid mt-5">
            <h1 class="text-center mb-5">List of Students</h1>
            <div class="d-flex flex-wrap justify-content-around mb-3">
                <?php
                // Fetch student data
                $query = "SELECT id, name, roll_no, status FROM students";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Generate HTML for each card
                        echo '<div class="card mb-5" style="width: 18rem;">';
                        echo '<img src="..." class="card-img-top" alt="Student Image">'; // Placeholder image
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
                        echo '<p class="card-text">Roll No: ' . htmlspecialchars($row['roll_no']) . '</p>';
                        echo '<p class="card-text">Status: ' . htmlspecialchars($row['status']) . '</p>';
                        echo '<a href="#" class="btn btn-primary">View Details</a>'; // Modify link as needed
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No students found.</p>';
                }
                ?>
            </div>
   
    <!-- </div> -->
  </div>

</body>
</html>