<?php
session_start();

// Check if the right person is logged in
if (!isset($_SESSION['UserID']) || ($_SESSION['Role'] !== 'Admin' && $_SESSION['Role'] !== 'Phlebotomist')) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "Loveyourself@1";
$dbname = "bloodbank_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT BloodUnitID, BloodType, ExpirationDate, StorageLocation
          FROM bloodunit
          WHERE ExpirationDate <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
          ORDER BY ExpirationDate ASC";

$result = $conn->query($query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expiration Forecast</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">
    <h1>Expiration Report</h1>
    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="donor_report.php">Donor</a></li>
            <li><a href="donation_report.php">Donation</a></li>
            <li><a href="inventory_report.php">Inventory</a></li>
            <li><a href="distribution_report.php">Distribution</a></li>
            <li><a href="testing_report.php">Testing</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
<main>
    <section>
        <h2>Blood Units Expiring Soon</h2>
        <table>
            <thead>
            <tr>
                <th>Blood Unit ID</th>
                <th>Blood Type</th>
                <th>Expiration Date</th>
                <th>Storage Location</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['BloodUnitID']; ?></td>
                        <td><?php echo $row['BloodType']; ?></td>
                        <td><?php echo $row['ExpirationDate']; ?></td>
                        <td><?php echo $row['StorageLocation']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No blood units expiring within the next month.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>
<footer>
    <p>&copy; 2024 Blood Bank Management System</p>
</footer>
</body>
</html>
