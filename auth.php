<?php
session_start();
require 'db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

$message = "";

// Handle Signup
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $message = "❌ Email already registered. Please login.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            $message = "✅ Signup successful! Please log in.";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        // 🔑 Check if the user is admin
        if ($user['email'] === "admin@example.com") {
            header("Location: admin_panel.php");
        } else {
            header("Location: home.php");
        }
        exit();

    } else {
        $message = "❌ Invalid email or password.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Table Reservation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; padding: 0;
            background: url('https://i.pinimg.com/736x/32/24/24/322424f8095fa9a1e8764f9056b9b3fe.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #e0e1dd;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(13, 27, 42, 0.85);
            z-index: 0;
        }
        .auth-container {
            position: relative;
            z-index: 1;
            width: 400px;
            margin: 60px auto;
            padding: 40px;
            background: rgba(27, 38, 59, 0.85);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
        }
        h1.title {
            text-align: center; 
            font-size: 28px;
            color: #00b4d8;
            margin-bottom: 30px;
        }
        .tabs { display: flex; margin-bottom: 20px; cursor: pointer; }
        .tab { flex: 1; padding: 12px; text-align: center; background: rgba(13,27,42,0.7); border-radius: 10px 10px 0 0; font-weight: bold; color: #e0e1dd; }
        .tab.active { background: #00b4d8; color: #0d1b2a; }
        form { display: none; }
        form.active { display: block; }
        input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #ccc; }
        button { width: 100%; padding: 12px; background: #00b4d8; color: #0d1b2a; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background: #0077b6; color: #e0e1dd; }
        p.message { color: #ff4d6d; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1 class="title">Restaurant Table Reservation</h1>

        <div class="tabs">
            <div class="tab active" onclick="showForm('login')">Login</div>
            <div class="tab" onclick="showForm('signup')">Signup</div>
        </div>

        <?php if($message) echo "<p class='message'>$message</p>"; ?>

        <!-- Login Form -->
        <form method="POST" id="login-form" class="active">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <!-- Signup Form -->
        <form method="POST" id="signup-form">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="signup">Signup</button>
        </form>
    </div>

<script>
function showForm(form) {
    document.getElementById('login-form').classList.remove('active');
    document.getElementById('signup-form').classList.remove('active');
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));

    if(form === 'login') {
        document.getElementById('login-form').classList.add('active');
        document.querySelector('.tab:nth-child(1)').classList.add('active');
    } else {
        document.getElementById('signup-form').classList.add('active');
        document.querySelector('.tab:nth-child(2)').classList.add('active');
    }
}
</script>
</body>
</html>
