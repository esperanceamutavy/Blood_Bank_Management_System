<?php
session_start();

// Redirect to login if not admin or phlebotomist
if (!isset($_SESSION['UserID']) || !in_array($_SESSION['Role'], ['Admin', 'Phlebotomist'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">
    <h2>Admin Dashboard</h2>
    <nav>
        <ul>

            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="donor.php">Donors</a></li>
            <li><a href="donation.php">Donations</a></li>
            <li><a href="distribution.php">Distributions</a></li>
            <li><a href="staff.php">Staff</a></li>
            <li><a href="hospital.php">Hospitals</a></li>
            <li><a href="bloodunit.php">Blood Units</a></li>
            <li><a href="requests.php">Blood Requests</a></li>
            <li><a href="bloodtesting.php">Blood Testing</a> </li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
<main>
    <section>
        <h2>Welcome!</h2>
        <p>From here you can manage the bloodbank management system.</p>
    </section>
    <button onclick="window.location.href='admin_reports.php'">View Reports</button>
</main>
<footer>
    <p>&copy; 2024 Blood Bank Management System</p>
</footer>
<script src="validation.js"></script>
</body>
</html>