<?php
include '../includes/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $remark = $_POST['remark'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO remarks (student_id, user_id, level, remark) VALUES (?, ?, 3, ?)");
    $stmt->bind_param("iis", $student_id, $user_id, $remark);
    $stmt->execute();
    $stmt->close();

    echo "<div class='alert alert-success text-center' role='alert'>Remark added successfully.</div>";
}

// Fetch students waiting for Level 3 verification
$result = $conn->query("SELECT s.id, s.roll_no, s.name FROM students s JOIN remarks r ON s.id = r.student_id WHERE r.level = 2 AND s.status = 'active'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deputy Registrar - Student Strike Off</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Deputy Registrar - Review Student Details</h2>
        <form method="post" action="index.php" class="p-4 border rounded bg-light">
            <div class="mb-3">
                <label for="student_id" class="form-label">Select Student:</label>
                <select id="student_id" name="student_id" class="form-select" required>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['roll_no'] ?> - <?= $row['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="remark" class="form-label">Remark:</label>
                <textarea id="remark" name="remark" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
    </div>
    <div class="container mt-5 text-center">
        <!-- Button that links to the approval status page -->
        <a href="../approval_status.php" class="btn btn-primary btn-lg">
            View Student Approval Status
        </a>
    </div>
</body>
</html>
