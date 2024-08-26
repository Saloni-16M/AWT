<?php
include '../includes/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit_update'])) {
        // Handle the update and remark submission
        $student_id = $_POST['student_id'];
        $remark = $_POST['remark'];
        $user_id = $_SESSION['user_id'];
        $current_date = date("Y-m-d");

        // Insert Dean's remark
        $stmt = $conn->prepare("INSERT INTO remarks (student_id, user_id, level, remark) VALUES (?, ?, 4, ?)");
        $stmt->bind_param("iis", $student_id, $user_id, $remark);
        $stmt->execute();
        $stmt->close();

        // Update student status without approved_by
        $update_stmt = $conn->prepare("UPDATE students SET status = 'struck off', struck_off_date = ? WHERE id = ?");
        $update_stmt->bind_param("si", $current_date, $student_id);
        $update_stmt->execute();
        $update_stmt->close();

        echo "<div class='alert alert-success text-center' role='alert'>Student status updated successfully</div>";
    } elseif (isset($_POST['generate_pdf'])) {
        // Redirect to generate PDF
        $roll_no = isset($_POST['roll_no']) ? trim($_POST['roll_no']) : '';
        if (empty($roll_no)) {
            die("Roll number is required.");
        }
        header("Location: ../pdf/generate_pdf.php?roll_no=" . urlencode($roll_no));
        exit();
    }
}

// Fetch students waiting for Dean's approval
$result = $conn->query("SELECT s.id, s.roll_no, s.name FROM students s JOIN remarks r ON s.id = r.student_id WHERE r.level = 3 AND s.status = 'active'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dean Academics - Student Strike Off</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h2 class="text-center">Dean Academics - Review and Approve Struck Off</h2>
    <div class="container mt-5">
        <form method="post" action="index.php">
            <label for="student_id">Select Student:</label>
            <select id="student_id" name="student_id" class="form-control" required>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['roll_no'] ?> - <?= $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
            <label for="remark">Final Remark:</label>
            <textarea id="remark" name="remark" class="form-control" rows="3" required></textarea>
            <div class="mt-3">
                <button type="submit" name="submit_update" class="btn btn-primary">Update Status</button>
            </div>
        </form>
        <div class="container mt-5">
            <form action="index.php" method="post">
                <label for="pdf_roll_no">Roll Number for PDF:</label>
                <input type="text" id="pdf_roll_no" name="roll_no" class="form-control" required>
                <div class="mt-3">
                    <button type="submit" name="generate_pdf" class="btn btn-success">Generate PDF</button>
                </div>
            </form>
        </div>
        <div class="container mt-5 text-center">
        <!-- Button that links to the approval status page -->
        <a href="../approval_status.php" class="btn btn-primary btn-lg">
            View Student Approval Status
        </a>
    </div>
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
        </div>
    </div>
</body>
</html>
