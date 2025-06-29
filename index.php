<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lab Reschedule Management - Dashboard</title>
    <style>
        /* Reset & base */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0f172a; /* dark navy */
            color: #f1f5f9; /* light slate */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #1e293b; /* dark slate background */
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
            text-align: center;
            width: 380px;
        }

        h1 {
            margin-bottom: 30px;
            color: #38bdf8; /* sky blue */
            font-size: 1.9rem;
            font-weight: bold;
        }

        .nav-button {
            display: block;
            width: 100%;
            padding: 14px 0;
            margin: 12px 0;
            font-size: 1.1rem;
            font-weight: 500;
            text-decoration: none;
            color: #f1f5f9;
            background: linear-gradient(135deg, #2563eb, #38bdf8); /* blue gradient */
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease-in-out;
        }

        .nav-button:hover {
            background: linear-gradient(135deg,rgb(162, 238, 238),rgb(74, 152, 175)); /* yellow-orange hover */
            color: #0f172a;
        }

        .footer {
            margin-top: 25px;
            font-size: 0.9rem;
            color: #94a3b8; /* muted slate */
        }

        @media (max-width: 420px) {
            .container {
                width: 90%;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lab Reschedule System</h1>

        <!-- Each link redirects to login page with redirect parameter -->
        <a href="coordinator_login.php?redirect=lab_schedule_create.php" class="nav-button">Create Lab Schedule</a>
        <a href="student_login.php?redirect=reschedule_request.php" class="nav-button">Student Reschedule Request</a>
        <a href="coordinator_login.php?redirect=coordinator_approval.php" class="nav-button">Coordinator Approval</a>
        <a href="instructor_login.php?redirect=notify_student.php" class="nav-button">Notify Student & Coordinator</a>
        <a href="instructor_login.php?redirect=create_attendance.php" class="nav-button">Create Attendance</a>

    </div>
</body>
</html>
