<?php
if (!isset($_GET['booking_id'])) {
    die("Invalid access.");
}

$booking_id = (int)$_GET['booking_id'];

// You can now fetch booking details from database, display amount, etc.
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment - RediGoo</title>
</head>
<body>
    <h1>Payment Page</h1>
    <p>Proceed with payment for booking ID: <?php echo htmlspecialchars($booking_id); ?></p>
    <!-- Payment gateway integration goes here -->
</body>
</html>
