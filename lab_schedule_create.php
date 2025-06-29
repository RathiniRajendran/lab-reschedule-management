<?php
session_start();
if (!isset($_SESSION['coordinator_id'])) {
    header("Location: coordinator_login.php?redirect=" . basename($_SERVER['PHP_SELF']));
    exit();
}

$conn = new mysqli("localhost", "root", "", "lab_reschedule_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lab_id = intval($_POST['lab_id']);
    $coordinator_id = intval($_POST['coordinator_id']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $conn->real_escape_string($_POST['location']);

    if (!$lab_id || !$coordinator_id || !$date || !$time || !$location) {
        $message = "❌ Please fill in all fields.";
    } else {
        $sql = "INSERT INTO lab_schedule (lab_id, coordinator_id, date, time, location)
                VALUES ($lab_id, $coordinator_id, '$date', '$time', '$location')";
        if ($conn->query($sql) === TRUE) {
            $message = "✅ Lab schedule created successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}

$lab_result = $conn->query("SELECT lab_id, lab_name FROM labs ORDER BY lab_name");
$coord_result = $conn->query("SELECT coordinator_id, name FROM subject_coordinator ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Lab Schedule</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #0f172a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 550px;
            margin: 60px auto;
            background-color: #1e293b;
            padding: 40px 30px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.6);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #38bdf8;
        }

        label {
            display: block;
            margin-top: 16px;
            margin-bottom: 6px;
            font-weight: 600;
            color: #cbd5e1;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #475569;
            border-radius: 8px;
            background-color: #334155;
            color: #f1f5f9;
            font-size: 1rem;
        }

        input:focus,
        select:focus {
            border-color: #38bdf8;
            outline: none;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.4);
        }

        button {
            margin-top: 30px;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            border: none;
            color: white;
            font-size: 1.1rem;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease-in-out;
        }

        button:hover {
            background: linear-gradient(135deg,rgb(48, 84, 99),rgb(39, 54, 61));
            color: #0f172a;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
        }

        .message.error {
            color:rgb(98, 125, 162);
        }

        .message.success {
            color: #34d399;
        }

        .links {
            text-align: center;
            margin-top: 25px;
        }

        .links a {
            color: #94a3b8;
            text-decoration: none;
            font-weight: 500;
            margin: 0 10px;
        }

        .links a:hover {
            text-decoration: underline;
            color: #38bdf8;
        }

        @media (max-width: 600px) {
            .container {
                margin: 30px 20px;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📅 Create New Lab Schedule</h2>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, '❌') !== false ? 'error' : 'success' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label for="lab_id">Select Lab</label>
        <select name="lab_id" id="lab_id" required>
            <option value=""> Select Lab </option>
            <?php while ($row = $lab_result->fetch_assoc()): ?>
                <option value="<?= $row['lab_id'] ?>"><?= htmlspecialchars($row['lab_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="coordinator_id">Select Coordinator</label>
        <select name="coordinator_id" id="coordinator_id" required>
            <option value=""> Select Coordinator </option>
            <?php while ($row = $coord_result->fetch_assoc()): ?>
                <option value="<?= $row['coordinator_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="date">Date</label>
        <input type="date" name="date" id="date" required>

        <label for="time">Time</label>
        <input type="time" name="time" id="time" required>

        <label for="location">Location</label>
        <input type="text" name="location" id="location" required>

        <button type="submit">➕ Create Schedule</button>
    </form>

    <div class="links">
        <a href="view_lab_schedule.php">← Back to Schedule List</a> |
        <a href="index.php">🏠 Home</a>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
