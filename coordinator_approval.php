<?php
session_start();

if (!isset($_SESSION['coordinator_id'])) {
    header("Location: coordinator_login.php?redirect=coordinator_approval.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "lab_reschedule_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle approval
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_id'])) {
    $request_id = intval($_POST['request_id']);

    $update_sql = "UPDATE reschedule_request 
                   SET coordinator_approved = 1, forwarded_to_lab_instructor = 1 
                   WHERE request_id = $request_id AND ar_approved = 1";

    if ($conn->query($update_sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            $message = "✅ Request approved and forwarded to lab instructor.";
        } else {
            $message = "❌ Request is either not AR-approved or already handled.";
        }
    } else {
        $message = "❌ Error updating request: " . $conn->error;
    }
}

// Fetch only AR-approved requests
$result = $conn->query("
    SELECT r.request_id, s.name AS student_name, l.lab_name, r.reason, 
           r.coordinator_approved, r.forwarded_to_lab_instructor
    FROM reschedule_request r
    JOIN student s ON r.student_id = s.student_id
    JOIN labs l ON r.lab_id = l.lab_id
    WHERE r.ar_approved = 1
    ORDER BY r.submitted_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coordinator Approval</title>
    <style>
        body {
            background-color: #0d1117;
            color: #e6edf3;
            font-family: 'Segoe UI', sans-serif;
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #58a6ff;
            margin-bottom: 30px;
        }

        .message {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .message.success { color: #2ecc71; }
        .message.error { color: #e74c3c; }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #161b22;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(100, 100, 100, 0.2);
        }

        th, td {
            padding: 14px 12px;
            text-align: center;
            border-bottom: 1px solid #30363d;
        }

        th {
            background-color: #21262d;
            color: #58a6ff;
            text-transform: uppercase;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #1a1f26;
        }

        tr:hover {
            background-color: #2c313a;
        }

        button {
            background-color: #238636;
            color: #ffffff;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #2ea043;
        }

        button[disabled] {
            background-color: #666;
            cursor: not-allowed;
        }

        form {
            margin: 0;
        }

        .home-link {
            text-align: center;
            margin-top: 30px;
        }

        .home-link a {
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            color: #58a6ff;
        }

        .home-link a:hover {
            text-decoration: underline;
            color: #91cfff;
        }
    </style>
</head>
<body>

    <h2>📋 Coordinator Approval – AR-Approved Requests</h2>

    <?php if (!empty($message)): ?>
        <p class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Request ID</th>
                <th>Student Name</th>
                <th>Lab Name</th>
                <th>Reason</th>
                <th>Approved</th>
                <th>Forwarded</th>
                <th>Action</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['request_id'] ?></td>
                <td><?= htmlspecialchars($row['student_name']) ?></td>
                <td><?= htmlspecialchars($row['lab_name']) ?></td>
                <td><?= htmlspecialchars($row['reason']) ?></td>
                <td><?= $row['coordinator_approved'] ? "✅" : "❌" ?></td>
                <td><?= $row['forwarded_to_lab_instructor'] ? "✅" : "❌" ?></td>
                <td>
                    <?php if (!$row['coordinator_approved']): ?>
                        <form method="post">
                            <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>" />
                            <button type="submit">Approve & Forward</button>
                        </form>
                    <?php else: ?>
                        <button disabled>Approved</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p class="message error">No AR-approved requests to approve at this time.</p>
    <?php endif; ?>

    <div class="home-link">
        <a href="index.php">🏠 Go to Home Page</a>
    </div>

</body>
</html>

<?php $conn->close(); ?>
