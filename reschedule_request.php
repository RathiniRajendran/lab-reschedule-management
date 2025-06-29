<?php
$conn = new mysqli("localhost", "root", "", "lab_reschedule_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = intval($_POST['student_id']);
    $lab_id = intval($_POST['lab_id']);
    $reason = $conn->real_escape_string($_POST['reason']);
    $submitted_date = date("Y-m-d");
    $ar_approved = $_POST['ar_approved'] === "yes" ? 1 : 0;

    if (!$student_id || !$lab_id || !$reason) {
        $message = "❌ Please fill in all required fields.";
    } else {
        $sql = "INSERT INTO reschedule_request (student_id, lab_id, reason, submitted_date, ar_approved)
                VALUES ($student_id, $lab_id, '$reason', '$submitted_date', $ar_approved)";
        if ($conn->query($sql) === TRUE) {
            $message = "✅ Reschedule request submitted successfully.";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}

$labs = $conn->query("SELECT lab_id, lab_name FROM labs");
$students = $conn->query("SELECT student_id, name FROM student");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Reschedule Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #0f172a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #f1f5f9;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background-color: #1e293b;
            padding: 40px 30px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            color: #38bdf8;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin: 15px 0 6px;
            font-weight: 600;
            color: #cbd5e1;
        }

        select, input, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #475569;
            background-color: #334155;
            color: #f1f5f9;
            font-size: 1rem;
        }

        textarea {
            resize: none;
        }

        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 2px rgba(56,189,248,0.3);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            margin-top: 25px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        button:hover {
            background: linear-gradient(135deg,rgb(71, 129, 147),rgb(80, 135, 163));
            color: #0f172a;
        }

        .msg {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
            font-size: 0.95rem;
        }

        .msg.success {
            color: #34d399;
        }

        .msg.error {
            color: #f87171;
        }

        .home-link {
            text-align: center;
            margin-top: 20px;
        }

        .home-link a {
            color: #94a3b8;
            text-decoration: none;
            font-weight: 500;
        }

        .home-link a:hover {
            text-decoration: underline;
            color: #38bdf8;
        }

        @media (max-width: 550px) {
            .container {
                margin: 20px;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📄 Student Reschedule Request</h2>

    <?php if ($message): ?>
        <p class="msg <?= strpos($message, '❌') !== false ? 'error' : 'success' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <label for="student_id">Select Student</label>
        <select name="student_id" id="student_id" required>
            <option value="">Select Student</option>
            <?php while ($row = $students->fetch_assoc()): ?>
                <option value="<?= $row['student_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="lab_id">Select Lab</label>
        <select name="lab_id" id="lab_id" required>
            <option value="">Select Lab</option>
            <?php while ($row = $labs->fetch_assoc()): ?>
                <option value="<?= $row['lab_id'] ?>"><?= htmlspecialchars($row['lab_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="reason">Reason</label>
        <textarea name="reason" id="reason" rows="4" required></textarea>

        <label for="ar_approved">Approved by AR Office?</label>
        <select name="ar_approved" id="ar_approved" required>
            <option value=""> Select Option </option>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>

        <button type="submit">📨 Submit Request</button>
    </form>

    <div class="home-link">
        <a href="index.php">🏠 Go to Home Page</a>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
