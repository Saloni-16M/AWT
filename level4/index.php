<?php
include '../includes/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $remark = $_POST['remark'];
    $user_id = $_SESSION['user_id'];
    $current_date = date("Y-m-d");

    // Insert Dean's remark
    $stmt = $conn->prepare("INSERT INTO remarks (student_id, user_id, level, remark) VALUES (?, ?, 4, ?)");
    $stmt->bind_param("iis", $student_id, $user_id, $remark);
    $stmt->execute();

    // Update student status
    $update_stmt = $conn->prepare("UPDATE students SET status = 'struck off', struck_off_date = ?, approved_by = ? WHERE id = ?");
    $update_stmt->bind_param("sii", $current_date, $user_id, $student_id);
    $update_stmt->execute();

    echo "Student has been struck off successfully.";

    // Redirect to generate PDF
    header("Location: ../pdf/generate_pdf.php?id=" . $student_id);
    exit();
}

// Fetch students waiting for Dean's approval
$result = $conn->query("SELECT s.id, s.roll_no, s.name FROM students s JOIN remarks r ON s.id = r.student_id WHERE r.level = 3 AND s.status = 'active'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dean Academics - Student Strike Off</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Dean Academics - Review and Approve Struck Off</h2>
    <form method="post" action="index.php">
        <label for="student_id">Select Student:</label>
        <select id="student_id" name="student_id" required>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= $row['roll_no'] ?> - <?= $row['name'] ?></option>
            <?php endwhile; ?>
        </select>
        <label for="remark">Final Remark:</label>
        <textarea id="remark" name="remark" required></textarea>
        <button type="submit">Approve and Generate PDF</button>
    </form>
</body>
</html>
