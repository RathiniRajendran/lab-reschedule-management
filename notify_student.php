<?php
session_start();
if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php?redirect=" . basename($_SERVER['PHP_SELF']));
    exit();
}

$conn = new mysqli("localhost", "root", "", "lab_reschedule_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = intval($_POST['request_id']);
    $new_date = $_POST['rescheduled_date'];
    $new_time = $_POST['rescheduled_time'];
    $new_location = $conn->real_escape_string($_POST['rescheduled_location']);

    // Update the reschedule_request
    $update_sql = "UPDATE reschedule_request 
                   SET rescheduled_date='$new_date', rescheduled_time='$new_time', rescheduled_location='$new_location'
                   WHERE request_id=$request_id";

    if ($conn->query($update_sql) === TRUE) {
        // Fetch info for email
        $info_sql = "
            SELECT 
                s.name AS student_name, s.email AS student_email,
                sc.name AS coordinator_name, sc.email AS coordinator_email,
                l.lab_name
            FROM reschedule_request r
            JOIN student s ON r.student_id = s.student_id
            JOIN labs l ON r.lab_id = l.lab_id
            JOIN lab_schedule ls ON r.lab_id = ls.lab_id
            JOIN subject_coordinator sc ON ls.coordinator_id = sc.coordinator_id
            WHERE r.request_id = $request_id
            LIMIT 1
        ";

        $result = $conn->query($info_sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $student_name = $row['student_name'];
            $student_email = $row['student_email'];
            $coordinator_name = $row['coordinator_name'];
            $coordinator_email = $row['coordinator_email'];
            $lab_name = $row['lab_name'];

            $subject = "Lab Reschedule Notification - $lab_name";
            $email_message = "Dear $student_name and $coordinator_name,\n\n" .
                             "Your lab ($lab_name) has been rescheduled.\n\n" .
                             "📅 New Date: $new_date\n" .
                             "⏰ New Time: $new_time\n" .
                             "📍 Venue: $new_location\n\n" .
                             "Please take note of the new schedule.\n\nRegards,\nLab Rescheduling System";

            $headers = "From: no-reply@university.edu";

            // Send emails
           

            $message = "✅ Schedule updated and notifications sent to student and coordinator!";
        } else {
            $message = "⚠️ Schedule updated, but contact information not found.";
        }
    } else {
        $message = "❌ Error updating schedule: " . $conn->error;
    }
}

// Load reschedule requests for the dropdown
$requests = $conn->query("
    SELECT r.request_id, l.lab_name
    FROM reschedule_request r
    JOIN labs l ON r.lab_id = l.lab_id
    WHERE r.coordinator_approved = 1 AND r.forwarded_to_lab_instructor = 1
");

if (!$requests) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notify Student and Coordinator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background-color: #f0f8ff;
        }
        form {
            background: #fff;
            padding: 25px;
            width: 420px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            color: #00509e;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            margin-top: 20px;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .msg {
            text-align: center;
            font-weight: bold;
            margin: 15px;
            color: green;
        }
        .home-link {
            text-align: center;
            margin-top: 20px;
        }
        .home-link a {
            text-decoration: none;
            font-weight: bold;
            color: #00509e;
        }
    </style>
</head>
<body>

<h2>Notify Student and Coordinator of Reschedule</h2>

<?php if ($message): ?>
    <p class="msg"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="request_id">Select Lab:</label>
    <select name="request_id" id="request_id" required>
        <option value="">Select Lab </option>
        <?php if ($requests->num_rows > 0): ?>
            <?php while ($row = $requests->fetch_assoc()): ?>
                <option value="<?= $row['request_id'] ?>">
                    <?= htmlspecialchars($row['lab_name']) ?>
                </option>
            <?php endwhile; ?>
        <?php else: ?>
            <option value="" disabled>No approved & forwarded requests found</option>
        <?php endif; ?>
    </select>

    <label for="rescheduled_date">New Date:</label>
    <input type="date" name="rescheduled_date" id="rescheduled_date" required>

    <label for="rescheduled_time">New Time:</label>
    <input type="time" name="rescheduled_time" id="rescheduled_time" required>

    <label for="rescheduled_location">Venue:</label>
    <input type="text" name="rescheduled_location" id="rescheduled_location" required>

    <button type="submit">📩 Reschedule & Notify</button>
</form>

<div class="home-link">
    <a href="index.php">🏠 Go to Home Page</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
