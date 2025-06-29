<?php
$conn = new mysqli("localhost", "root", "", "lab_reschedule_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM lab_schedule WHERE schedule_id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: view_lab_schedule.php");
        exit();
    } else {
        echo "Error deleting schedule: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
