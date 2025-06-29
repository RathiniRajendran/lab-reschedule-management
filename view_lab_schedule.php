<?php
$conn = new mysqli("localhost", "root", "", "lab_reschedule_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT ls.schedule_id, l.lab_name, sc.name AS coordinator_name, ls.date, ls.time, ls.location
        FROM lab_schedule ls
        JOIN labs l ON ls.lab_id = l.lab_id
        JOIN subject_coordinator sc ON ls.coordinator_id = sc.coordinator_id
        ORDER BY ls.date, ls.time";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Schedule Viewer</title>
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px 40px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2d3436;
            text-align: center;
            margin-bottom: 40px;
            font-size: 28px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 12px 24px;
            background-color: #0984e3;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #00cec9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 16px 12px;
            text-align: center;
            border-bottom: 1px solid #ecf0f1;
        }

        th {
            background-color: #2d3436;
            color: #ffffff;
            font-size: 14px;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #dff9fb;
        }

        .action-links a {
            color: #0984e3;
            text-decoration: none;
            font-weight: 600;
            margin: 0 6px;
        }

        .action-links a:hover {
            color: #d63031;
            text-decoration: underline;
        }

        .message {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #d63031;
            margin-top: 30px;
        }

        @media screen and (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                position: sticky;
                top: 0;
                background-color: #2d3436;
            }

            tr {
                margin-bottom: 20px;
                background: #fff;
                box-shadow: 0 2px 6px rgba(0,0,0,0.05);
                border-radius: 8px;
                padding: 10px;
            }

            td {
                text-align: left;
                padding: 10px;
                border: none;
                display: flex;
                justify-content: space-between;
            }

            td::before {
                content: attr(data-label);
                font-weight: bold;
                color: #2d3436;
                width: 45%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h2>📅 Lab Schedule Overview</h2>
        <a href="lab_schedule_create.php" class="btn">+ Create New Schedule</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
            <tr>
                <th>Schedule ID</th>
                <th>Lab Name</th>
                <th>Coordinator</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td data-label="Schedule ID"><?php echo $row['schedule_id']; ?></td>
                <td data-label="Lab Name"><?php echo htmlspecialchars($row['lab_name']); ?></td>
                <td data-label="Coordinator"><?php echo htmlspecialchars($row['coordinator_name']); ?></td>
                <td data-label="Date"><?php echo $row['date']; ?></td>
                <td data-label="Time"><?php echo $row['time']; ?></td>
                <td data-label="Location"><?php echo htmlspecialchars($row['location']); ?></td>
                <td data-label="Actions" class="action-links">
                    <a href="edit_schedule.php?id=<?php echo $row['schedule_id']; ?>">Edit</a> |
                    <a href="delete_schedule.php?id=<?php echo $row['schedule_id']; ?>" 
                       onclick="return confirm('Are you sure you want to delete this schedule?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="message">No lab schedules created yet.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php $conn->close(); ?>
