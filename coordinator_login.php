<?php
session_start();
$conn = new mysqli("localhost", "root", "", "lab_reschedule_db");

$error = "";

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT coordinator_id, password FROM subject_coordinator WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($coordinator_id, $db_password);

    if ($stmt->fetch()) {
        if ($password === $db_password) { // Use password_verify() if using hashed passwords
            $_SESSION['coordinator_id'] = $coordinator_id;
            $redirect = $_GET['redirect'] ?? 'dashboard.php';
            header("Location: $redirect");
            exit();
        } else {
            $error = "❌ Incorrect password.";
        }
    } else {
        $error = "❌ No account found with that email.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Coordinator Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body {
            background-color: #0f172a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #f1f5f9;
        }

        .login-container {
            background-color: #1e293b;
            padding: 40px 30px;
            border-radius: 14px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 360px;
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
            margin: 12px 0;
            border-radius: 8px;
            border: 1px solid #475569;
            background-color: #334155;
            color: #f1f5f9;
            font-size: 1rem;
        }

        input:focus {
            border-color: #38bdf8;
            outline: none;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.3);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            border: none;
            color: white;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s ease-in-out;
        }

        button:hover {
            background: linear-gradient(135deg, rgb(99, 178, 214), rgb(39, 63, 75));
            color: #0f172a;
        }

        .error {
            margin-top: 15px;
            color: #f87171;
            font-weight: 500;
        }

        .home-link {
            margin-top: 20px;
        }

        .home-link a {
            text-decoration: none;
            color: #94a3b8;
            font-size: 0.95rem;
        }

        .home-link a:hover {
            color: #38bdf8;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔐 Coordinator Login</h2>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit">Login</button>
        </form>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="home-link">
            <a href="index.php">🏠 Back to Home</a>
        </div>
    </div>
</body>
</html>
