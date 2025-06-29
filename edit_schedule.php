<?php
$conn = new mysqli("localhost", "root", "", "lab_reschedule_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$schedule_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = "";

// Fetch current schedule
$sql = "SELECT * FROM lab_schedule WHERE schedule_id = $schedule_id";
$result = $conn->query($sql);
if ($result->num_rows === 0) {
    echo "<p style='color:red;'>Invalid Schedule ID.</p>";
    echo "<a href='view_lab_schedule.php'>Back to Schedule List</a>";
    exit();
}
$row = $result->fetch_assoc();

// Fetch dropdown data
$labs = $conn->query("SELECT lab_id, lab_name FROM labs");
$coordinators = $conn->query("SELECT coordinator_id, name FROM subject_coordinator");

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lab_id = intval($_POST['lab_id']);
    $coordinator_id = intval($_POST['coordinator_id']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $conn->real_escape_string($_POST['location']);

    $update_sql = "UPDATE lab_schedule 
                   SET lab_id = $lab_id, coordinator_id = $coordinator_id, date = '$date', time = '$time', location = '$location' 
                   WHERE schedule_id = $schedule_id";

    if ($conn->query($update_sql) === TRUE) {
        header("Location: view_lab_schedule.php");
        exit();
    } else {
        $message = "Update failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Lab Schedule</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0f172a;
            margin: 0;
            padding: 20px;
            color: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #1e293b;
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.6);
        }

        h2 {
            text-align: center;
            color: #38bdf8;
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            margin-top: 12px;
            display: block;
            color: #e2e8f0;
        }

        select,
        input[type="date"],
        input[type="time"],
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #475569;
            background-color: #334155;
            color: #f1f5f9;
            font-size: 1rem;
        }

        select:focus,
        input:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.4);
        }

        .btn {
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            color: white;
            padding: 12px;
            margin-top: 24px;
            width: 100%;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: linear-gradient(135deg, #facc15, #f97316);
            color: #0f172a;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .error {
            background-color: #f87171;
            padding: 10px;
            margin-top: 15px;
            color: white;
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Lab Schedule</h2>

    <?php if ($message): ?>
        <div class="error"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="lab_id">Lab</label>
        <select name="lab_id" required>
            <option value="">-- Select Lab --</option>
            <?php while ($lab = $labs->fetch_assoc()): ?>
                <option value="<?php echo $lab['lab_id']; ?>" <?php if ($lab['lab_id'] == $row['lab_id']) echo "selected"; ?>>
                    <?php echo htmlspecialchars($lab['lab_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="coordinator_id">Coordinator</label>
        <select name="coordinator_id" required>
            <option value="">-- Select Coordinator --</option>
            <?php while ($coord = $coordinators->fetch_assoc()): ?>
                <option value="<?php echo $coord['coordinator_id']; ?>" <?php if ($coord['coordinator_id'] == $row['coordinator_id']) echo "selected"; ?>>
                    <?php echo htmlspecialchars($coord['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="date">Date</label>
        <input type="date" name="date" value="<?php echo $row['date']; ?>" required>

        <label for="time">Time</label>
        <input type="time" name="time" value="<?php echo $row['time']; ?>" required>

        <label for="location">Location</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($row['location']); ?>" required>

        <button type="submit" class="btn">Update Schedule</button>
    </form>

    <div class="back-link">
        <a href="view_lab_schedule.php">← Back to Schedule List</a>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
