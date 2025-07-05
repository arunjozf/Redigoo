<?php
session_start();
require 'db.php'; // database connection

// Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === "login") {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_email'] = $email;
            header("Location: select_route.php");
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Email not found!";
    }
    $stmt->close();
}

// Handle Signup
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === "signup") {
    $name = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_email'] = $email;
            header("Location: select_route.php");
            exit;
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $check->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RediGoo - Login & Signup</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', sans-serif;
      background-color: #0f0f1a;
      color: white;
    }
    .container {
      width: 350px;
      margin: 100px auto;
      background: #1a1a2e;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 25px rgba(0,0,0,0.4);
    }
    .container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #d4ff00;
    }
    .container input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: none;
      border-radius: 6px;
      background: #333;
      color: white;
    }
    .container button {
      width: 100%;
      padding: 12px;
      background: #d4ff00;
      color: #000;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s;
    }
    .container button:hover {
      background: #bce000;
    }
    .toggle-link {
      text-align: center;
      color: #d4ff00;
      cursor: pointer;
      margin-top: 10px;
    }
    .hidden {
      display: none;
    }
    .logo {
      font-size: 32px;
      text-align: center;
      margin-bottom: 20px;
      color: #d4ff00;
    }
    .back-home {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #d4ff00;
      text-decoration: none;
    }
    .back-home:hover {
      text-decoration: underline;
    }
    .error-message {
      color: red;
      text-align: center;
      margin-bottom: 10px;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">Redi<span style="color:white;">Goo</span></div>

    <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

    <!-- Login Form -->
    <div id="loginForm">
      <h2>Login</h2>
      <form method="POST">
        <input type="hidden" name="action" value="login">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>
      <div class="toggle-link" onclick="toggleForm()">Don't have an account? Sign Up</div>
    </div>

    <!-- Signup Form -->
    <div id="signupForm" class="hidden">
      <h2>Create Account</h2>
      <form method="POST">
        <input type="hidden" name="action" value="signup">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign Up</button>
      </form>
      <div class="toggle-link" onclick="toggleForm()">Already have an account? Login</div>
    </div>

    <a href="Home Page.html" class="back-home">Back to Home</a>
  </div>

  <script>
    function toggleForm() {
      document.getElementById('loginForm').classList.toggle('hidden');
      document.getElementById('signupForm').classList.toggle('hidden');
    }
  </script>
</body>
</html>
