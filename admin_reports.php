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

    <title>Admin Reports</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">
    <h2>Admin Reports</h2>
    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin_reports.php">Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
<main>
    <section>
        <h2>Select The Report You Want To View</h2>
        <ul>
            <li><a href="donor_report.php">Donor</a></li>
            <li><a href="donation_report.php">Donation</a></li>
            <li><a href="inventory_report.php">Inventory</a></li>
            <li><a href="distribution_report.php">Distribution</a></li>
            <li><a href="testing_report.php">Testing</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </section>
</main>
<footer>
    <p>&copy; 2024 Blood Bank Management System</p>
</footer>
</body>
</html>