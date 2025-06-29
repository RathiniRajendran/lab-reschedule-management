<?php
session_start();
$conn = new mysqli("localhost", "root", "", "lab_reschedule_db");

$error = "";

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT student_id, password FROM student WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($student_id, $db_password);

    if ($stmt->fetch() && $password === $db_password) {
        $_SESSION['student_id'] = $student_id;
        $redirect = $_GET['redirect'] ?? 'dashboard.php';
        header("Location: $redirect");
        exit();
    } else {
        $error = "❌ Invalid email or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Student Login - Lab Reschedule System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0f172a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #e2e8f0;
        }

        .login-box {
            background-color: #1e293b;
            padding: 40px 30px;
            border-radius: 14px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 380px;
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;
            color: #38bdf8;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #334155;
            background-color: #334155;
            color: #f1f5f9;
            font-size: 1rem;
        }

        input:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.4);
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #2563eb, #38bdf8);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
            transition: background 0.3s ease-in-out;
        }

        button:hover {
            background: linear-gradient(to right, #facc15, #f97316);
            color: #0f172a;
        }

        .error {
            margin-top: 12px;
            color: #f87171;
            font-weight: 500;
        }

        .home-link {
            margin-top: 20px;
        }

        .home-link a {
            text-decoration: none;
            color: #94a3b8;
        }

        .home-link a:hover {
            color: #38bdf8;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔐 Student Login</h2>
        <form method="post">
            <input type="email" name="email" placeholder="Enter your email" required />
            <input type="password" name="password" placeholder="Enter your password" required />
            <button type="submit">Login</button>
        </form>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="home-link">
            <a href="index.php">🏠 Return to Home</a>
        </div>
    </div>
</body>
</html>
