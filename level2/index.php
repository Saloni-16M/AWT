<?php
include '../includes/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $remark = $_POST['remark'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO remarks (student_id, user_id, level, remark) VALUES (?, ?, 2, ?)");
    $stmt->bind_param("iis", $student_id, $user_id, $remark);
    $stmt->execute();

    echo "Remark added successfully.";
}

// Fetch students waiting for Level 2 verification
$result = $conn->query("SELECT s.id, s.roll_no, s.name FROM students s JOIN remarks r ON s.id = r.student_id WHERE r.level = 1 AND s.status = 'active'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Section Incharge - Student Strike Off</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Section Incharge - Review Student Details</h2>
    <form method="post" action="index.php">
        <label for="student_id">Select Student:</label>
        <select id="student_id" name="student_id" required>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= $row['roll_no'] ?> - <?= $row['name'] ?></option>
            <?php endwhile; ?>
        </select>
        <label for="remark">Remark:</label>
        <textarea id="remark" name="remark" required></textarea>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
