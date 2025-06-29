<?php
session_start();

// Check if instructor is logged in
if (!isset($_SESSION['instructor_id'])) {
    header("Location: instructor_login.php?redirect=" . urlencode(basename($_SERVER['PHP_SELF'])));
    exit();
}

$conn = new mysqli("localhost", "root", "", "lab_reschedule_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Get labs and students for dropdown/filter
$labs = $conn->query("SELECT lab_id, lab_name FROM labs ORDER BY lab_name");
$students = $conn->query("SELECT student_id, name FROM student ORDER BY name");

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lab_id = intval($_POST['lab_id']);
    $date = $conn->real_escape_string($_POST['attendance_date']);
    $present_students = isset($_POST['present_students']) ? $_POST['present_students'] : [];

    // Remove old attendance records for this lab and date (overwrite)
    $conn->query("DELETE FROM attendance WHERE lab_id = $lab_id AND attendance_date = '$date'");

    // Prepare insert statement once
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, lab_id, attendance_date, status) VALUES (?, ?, ?, ?)");

    // Insert attendance for all students
    while ($student = $students->fetch_assoc()) {
        $student_id = $student['student_id'];
        $status = in_array($student_id, $present_students) ? 'Present' : 'Absent';

        $stmt->bind_param("iiss", $student_id, $lab_id, $date, $status);
        $stmt->execute();
    }
    $stmt->close();

    $message = "✅ Attendance recorded successfully for $date.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Record Lab Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background-color: #e9f5ff;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 600px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            color: #00509e;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        select, input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .msg {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        .home-link {
            text-align: center;
            margin-top: 25px;
        }
        .home-link a {
            text-decoration: none;
            color: #00509e;
            font-weight: bold;
        }
        .home-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>📝 Record Lab Attendance</h2>

<?php if ($message): ?>
    <p class="msg"><?= htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="POST">
    <label for="lab_id">Select Lab:</label>
    <select name="lab_id" id="lab_id" required>
        <option value=""> Select Lab</option>
        <?php
        $labs->data_seek(0);
        while($lab = $labs->fetch_assoc()):
        ?>
            <option value="<?= $lab['lab_id'] ?>"><?= htmlspecialchars($lab['lab_name']) ?></option>
        <?php endwhile; ?>
    </select>

    <label for="attendance_date">Attendance Date:</label>
    <input type="date" name="attendance_date" id="attendance_date" required>

    <label>Mark Present Students:</label>
    <table>
        <tr>
            <th>Present</th>
            <th>Student Name</th>
        </tr>
        <?php
        $students->data_seek(0);
        while ($student = $students->fetch_assoc()):
        ?>
        <tr>
            <td><input type="checkbox" name="present_students[]" value="<?= $student['student_id'] ?>"></td>
            <td><?= htmlspecialchars($student['name']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <button type="submit">Submit Attendance</button>
</form>

<div class="home-link">
    <a href="index.php">🏠 Go to Home Page</a>
</div>

</body>
</html>

<?php $conn->close(); ?>
