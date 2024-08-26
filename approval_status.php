<?php
include 'C:/xampp/htdocs/AWT/includes/db_connection.php'; // Include your database connection

// Fetch data from students and remarks tables
$sql = "
    SELECT 
        s.name,
        s.status,
        MAX(CASE WHEN r.level = 1 THEN r.remark ELSE '-' END) AS dealinghand,
        MAX(CASE WHEN r.level = 2 THEN r.remark ELSE '-' END) AS sectionincharge,
        MAX(CASE WHEN r.level = 3 THEN r.remark ELSE '-' END) AS deputyregistrar,
        MAX(CASE WHEN r.level = 4 THEN r.remark ELSE '-' END) AS deanacademics
    FROM students s
    LEFT JOIN remarks r ON s.id = r.student_id
    GROUP BY s.id, s.name, s.status
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Approval Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-5">Remarks</h1>
        <table class="table table-dark table-striped">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Student Name</th>
                    <th scope="col">Status</th>
                    <th scope="col">Dealing Hand</th>
                    <th scope="col">Section Incharge</th>
                    <th scope="col">Deputy Registrar</th>
                    <th scope="col">Dean Academics</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['dealinghand']) ?></td>
                            <td><?= htmlspecialchars($row['sectionincharge']) ?></td>
                            <td><?= htmlspecialchars($row['deputyregistrar']) ?></td>
                            <td><?= htmlspecialchars($row['deanacademics']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close(); // Close the database connection
