<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_level = $_SESSION['level'];

switch ($user_level) {
    case 1:
        include 'level1/index.php';
        break;
    case 2:
        include 'level2/index.php';
        break;
    case 3:
        include 'level3/index.php';
        break;
    case 4:
        include 'level4/index.php';
        break;
    default:
        echo "Unauthorized access.";
}
?>
