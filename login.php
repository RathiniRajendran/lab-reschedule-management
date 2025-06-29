<?php
session_start();
$conn = new mysqli("localhost", "root", "", "lab_reschedule_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role === 'student') {
        header("Location: reschedule_request.php");
    } elseif ($role === 'instructor') {
        header("Location: lab_schedule_create.php");
    } elseif ($role === 'coordinator') {
        header("Location: coordinator_approval.php");
    }
    exit();
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password_hash, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $role;

            // Redirect by role
            if ($role === 'student') {
                header("Location: reschedule_request.php");
            } elseif ($role === 'instructor') {
                header("Location: lab_schedule_create.php");
            } elseif ($role === 'coordinator') {
                header("Location: coordinator_approval.php");
            }
            exit();
        } else {
            $message = "❌ Invalid password.";
        }
    } else {
        $message = "❌ No user found with that email.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <style>
        body { background: #f4f4f4; font-family: Arial; padding: 40px; }
        .login-box {
            max-width: 400px; margin: auto; background: white;
            padding: 30px; border-radius: 8px;
        }
        input, button {
            width: 100%; padding: 10px; margin: 10px 0;
        }
        button {
            background: #2980b9; color: white; border: none; cursor: pointer;
        }
        .message { color: red; font-weight: bold; text-align: center; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login</h2>
    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required autofocus />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
