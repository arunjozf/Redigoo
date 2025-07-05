<?php
session_start();
require 'db.php'; // database connection

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$step = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['route_id']) && !isset($_POST['bus_id'])) {
        // Step 1 -> Step 2: Store selected route
        $_SESSION['selected_route'] = $_POST['route_id'];
        $step = 2;
    } elseif (isset($_POST['bus_id'])) {
        // Step 2 -> Step 3: Store selected bus
        $_SESSION['selected_bus'] = $_POST['bus_id'];
        $step = 3;
    } elseif (isset($_POST['book_ticket'])) {
        // Step 3: Process booking and redirect to payment
        $bus_id = $_SESSION['selected_bus'];
        $members = (int)$_POST['members'];
        $sleeper = htmlspecialchars($_POST['sleeper']);
        $username = $_SESSION['user_email'];

        $stmt = $conn->prepare("INSERT INTO bookings (user, bus_id, passengers, sleeper) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siis", $username, $bus_id, $members, $sleeper);

        if ($stmt->execute()) {
            $booking_id = $stmt->insert_id;

            // Clear selected route & bus session
            unset($_SESSION['selected_route'], $_SESSION['selected_bus']);

            // Redirect to payment page
            header("Location: payment.php?booking_id=" . urlencode($booking_id));
            exit;
        } else {
            $error = "Booking failed: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RediGoo - Select Route & Book Ticket</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: url('your-homepage-background.jpg') no-repeat center center fixed;
      background-size: cover;
      color: #fff;
    }
    .container {
      max-width: 700px;
      margin: 80px auto;
      padding: 30px;
      background: rgba(0, 0, 0, 0.7);
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
    }
    h1, h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .message {text-align:center; color:lime;}
    .error {text-align:center; color:#ff4444;}
    form {text-align:center; margin-top:20px;}
    select, input, button {
      width: 80%;
      padding:12px;
      margin:10px auto;
      border-radius:5px;
      border:none;
      font-size:16px;
    }
    button {
      background:#d4ff00;
      color:#000;
      cursor:pointer;
      font-weight:bold;
    }
    button:hover {background:#bce000;}
    table {
      width:100%;
      border-collapse:collapse;
      margin-top:20px;
    }
    th, td {
      padding:15px;
      border:1px solid #ddd;
      text-align:center;
      background-color:rgba(255,255,255,0.1);
    }
    a {
      display:block;
      text-align:center;
      margin-top:20px;
      color:#d4ff00;
      text-decoration:none;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?>!</h1>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <?php if ($step === 1): ?>
      <h2>Select Your Route</h2>
      <form method="POST">
        <select name="route_id" required>
          <option value="">Choose a route</option>
          <option value="1">Kerala to Bangalore</option>
          <option value="2">Bangalore to Kerala</option>
          <option value="3">Kerala to Chennai</option>
          <option value="4">Chennai to Kerala</option>
          <option value="5">Kerala to Hyderabad</option>
          <option value="6">Hyderabad to Kerala</option>
          <option value="7">Kerala to Mangalore</option>
          <option value="8">Mangalore to Kerala</option>
        </select>
        <button type="submit">Next: Select Bus</button>
      </form>

    <?php elseif ($step === 2): ?>
      <h2>Selected Bus</h2>
      <?php
      $route_id = $_SESSION['selected_route'];
      $bus_map = [
        1 => "Sera",
        2 => "Sera",
        3 => "Adrin",
        4 => "Adrin",
        5 => "A1",
        6 => "A1",
        7 => "Greenline",
        8 => "Greenline"
      ];
      $selected_bus_name = isset($bus_map[$route_id]) ? $bus_map[$route_id] : "Unknown";

      if ($selected_bus_name !== "Unknown"): ?>
        <table>
          <tr><th>Bus Name</th><th>Select</th></tr>
          <tr>
            <td><?php echo htmlspecialchars($selected_bus_name); ?></td>
            <td>
              <form method="POST">
                <input type="hidden" name="bus_id" value="<?php echo $route_id; ?>">
                <button type="submit">Select</button>
              </form>
            </td>
          </tr>
        </table>
      <?php else: ?>
        <p>No assigned bus found for this route. <a href="select_route.php">Choose another route</a></p>
      <?php endif; ?>

    <?php elseif ($step === 3): ?>
      <h2>Enter Passenger Details</h2>
      <form method="POST">
        <input type="number" name="members" min="1" value="1" placeholder="Number of Members" required><br>
        <select name="sleeper" required>
          <option value="">Sleeper or Non-Sleeper?</option>
          <option value="Sleeper">Sleeper</option>
          <option value="Non-Sleeper">Non-Sleeper</option>
        </select><br>
        <button type="submit" name="book_ticket">Book Ticket</button>
      </form>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
  </div>
</body>
</html>
