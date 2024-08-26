<?php
require(__DIR__ . '/../fpdf/fpdf.php');
include __DIR__ . '/../includes/db_connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_POST['roll_no'])) {
    die("Roll number is required.");
}

$roll_no = htmlspecialchars(trim($_POST['roll_no']), ENT_QUOTES, 'UTF-8');

$query = "SELECT students.name, students.roll_no, students.status, remarks.remark, remarks.level, users.username 
          FROM students 
          INNER JOIN remarks ON students.id = remarks.student_id 
          INNER JOIN users ON remarks.user_id = users.id 
          WHERE students.roll_no = ? 
          ORDER BY remarks.level ASC";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $roll_no);
    $stmt->execute();
    $stmt->bind_result($name, $roll_no, $status, $remark, $level, $username);
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Student Strike Off Report', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, "Name: $name", 0, 1);
        $pdf->Cell(0, 10, "Roll No: $roll_no", 0, 1);
        $pdf->Cell(0, 10, "Status: $status", 0, 1);

        $pdf->Ln(10);
        $pdf->Cell(0, 10, "Remarks:", 0, 1);

        while ($stmt->fetch()) {
            $pdf->Cell(0, 10, "Level $level - $username: $remark", 0, 1);
        }

        // Add timestamp
        $pdf->Ln(10);
        $pdf->Cell(0, 10, "Generated on: " . date('Y-m-d H:i:s'), 0, 1);

        $stmt->close();

        // Set headers and output PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Student_Strike_Off_Report.pdf"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($pdf->Output('S')));

        // Output the PDF
        $pdf->Output('D', 'Student_Strike_Off_Report.pdf');
        exit();

    } else {
        echo "No remarks found for the given student.";
    }
} else {
    echo "Failed to prepare the query.";
}

$conn->close();
?>

