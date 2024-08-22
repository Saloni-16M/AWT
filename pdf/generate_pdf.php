<?php
require('fpdf/fpdf.php'); // Adjust the path as needed
include 'C:/xampp/htdocs/stuck_off/includes/db_connection.php'; // Adjust path if needed

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in. Redirecting to login page.";
    header("Location: ../login.php");
    exit();
}

// Fetch student details and remarks
$query = "SELECT students.name, students.roll_no, students.status, remarks.remark, remarks.level, users.username 
          FROM students 
          INNER JOIN remarks ON students.id = remarks.student_id 
          INNER JOIN users ON remarks.user_id = users.id 
          WHERE students.roll_no = ? 
          ORDER BY remarks.level ASC";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $_POST['roll_no']);
    $stmt->execute();
    $stmt->bind_result($name, $roll_no, $status, $remark, $level, $username);
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Create a new PDF document
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Add title
        $pdf->Cell(0, 10, 'Student Strike Off Report', 0, 1, 'C');

        // Add student details
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, "Name: $name", 0, 1);
        $pdf->Cell(0, 10, "Roll No: $roll_no", 0, 1);
        $pdf->Cell(0, 10, "Status: $status", 0, 1);

        // Add remarks
        $pdf->Ln(10);
        $pdf->Cell(0, 10, "Remarks:", 0, 1);

        while ($stmt->fetch()) {
            $pdf->Cell(0, 10, "Level $level - $username: $remark", 0, 1);
        }

        $stmt->close();

        // Output the PDF
        $pdf->Output('D', 'Student_Strike_Off_Report.pdf'); // This will download the PDF

    } else {
        echo "No remarks found for the given student.";
    }
} else {
    echo "Failed to prepare the query.";
}

$conn->close();
?>
