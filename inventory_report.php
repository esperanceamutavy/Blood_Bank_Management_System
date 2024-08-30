<?php
session_start();

if (!isset($_SESSION['UserID']) || ($_SESSION['Role'] !== 'Admin' && $_SESSION['Role'] !== 'Phlebotomist')) {
    header("Location: login.php");
    exit;
}
//Database connection
$servername = "localhost";
$username = "user";
$password = "    ";//4 spaces
$dbname = "bloodbank_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$filters = [
    'start_date' => $_GET['start_date'] ?? null,
    'end_date' => $_GET['end_date'] ?? null,
    'search' => $_GET['search'] ?? null
];

$query = "
SELECT bloodunit.BloodUnitID, bloodunit.BloodType, bloodunit.ExpirationDate, location.Name AS location_name
FROM bloodunit
JOIN location ON bloodunit.StorageLocation LIKE CONCAT(location.Name, '%')
WHERE 1 = 1
";

if ($filters['start_date'] && $filters['end_date']) {
    $query .= " AND bloodunit.ExpirationDate BETWEEN '{$filters['start_date']}' AND '{$filters['end_date']}'";
}

if ($filters['search']) {
    $query .= " AND (bloodunit.BloodType LIKE '%{$filters['search']}%' OR location.Name LIKE '%{$filters['search']}%')";
}

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory Report</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <header>
        <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">
        <h2>Inventory Report</h2>
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
            <h2>Inventory Report</h2>
            <form method="GET">
                <label for="start_date">Start Date:</label>
                <input type="text" id="start_date" name="start_date" value="<?php echo $filters['start_date']; ?>" placeholder="YYYY-MM-DD">
                <label for="end_date">End Date:</label>
                <input type="text" id="end_date" name="end_date" value="<?php echo $filters['end_date']; ?>" placeholder="YYYY-MM-DD">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search" placeholder="Search by blood type or location" value="<?php echo $filters['search']; ?>">
                <button type="submit">Filter</button>
            </form>
            <table>
                <thead>
                <tr>
                    <th>Unit ID</th>
                    <th>Blood Type</th>
                    <th>Expiry Date</th>
                    <th>Location</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['BloodUnitID'] ?? 'N/A'; ?></td>
                        <td><?php echo $row['BloodType'] ?? 'N/A'; ?></td>
                        <td><?php echo $row['ExpirationDate'] ?? 'N/A'; ?></td>
                        <td><?php echo $row['location_name'] ?? 'N/A'; ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Blood Bank Management System</p>
    </footer>
    </body>
    </html>
<?php $conn->close(); ?>