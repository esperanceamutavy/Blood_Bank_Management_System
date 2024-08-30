<?php
session_start();

// Check if user is logged in as Admin or Phlebotomist
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

// Handle filters
$testStatusFilter = $_GET['test_status'] ?? '';
$testTypeFilter = $_GET['test_type'] ?? '';

// Query for testing and quality control report
$query = "SELECT bt.TestID, bt.BloodUnitID, bt.TestDate, bt.TestStatus, 
                 bt.HIVAIDS, bt.HepatitisB, bt.HepatitisC, bt.Syphilis, 
                 b.BloodType, b.ExpirationDate, b.StorageLocation
          FROM bloodtesting bt
          JOIN bloodunit b ON bt.BloodUnitID = b.BloodUnitID
          WHERE 1=1";

if ($testStatusFilter) {
    $query .= " AND bt.TestStatus = '$testStatusFilter'";
}
if ($testTypeFilter) {
    switch ($testTypeFilter) {
        case 'HIVAIDS':
            $query .= " AND bt.HIVAIDS = 'Y'";
            break;
        case 'HepatitisB':
            $query .= " AND bt.HepatitisB = 'Y'";
            break;
        case 'HepatitisC':
            $query .= " AND bt.HepatitisC = 'Y'";
            break;
        case 'Syphilis':
            $query .= " AND bt.Syphilis = 'Y'";
            break;
    }
}

$query .= " ORDER BY bt.TestDate";

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
    <title>Testing and Quality Control Report</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">
    <h2>Testing Report</h2>
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
        <h2>Testing Report</h2>
        <form method="GET" action="">
            <label for="test_status">Test Status:</label>
            <select id="test_status" name="test_status">
                <option value="">All</option>
                <option value="P" <?php echo $testStatusFilter === 'P' ? 'selected' : ''; ?>>Passed</option>
                <option value="F" <?php echo $testStatusFilter === 'F' ? 'selected' : ''; ?>>Failed</option>
            </select>
            <label for="test_type">Test Type:</label>
            <select id="test_type" name="test_type">
                <option value="">All</option>
                <option value="HIVAIDS" <?php echo $testTypeFilter === 'HIVAIDS' ? 'selected' : ''; ?>>HIV/AIDS</option>
                <option value="HepatitisB" <?php echo $testTypeFilter === 'HepatitisB' ? 'selected' : ''; ?>>Hepatitis B</option>
                <option value="HepatitisC" <?php echo $testTypeFilter === 'HepatitisC' ? 'selected' : ''; ?>>Hepatitis C</option>
                <option value="Syphilis" <?php echo $testTypeFilter === 'Syphilis' ? 'selected' : ''; ?>>Syphilis</option>
            </select>
            <button type="submit">Filter</button>
        </form>
        <table>
            <thead>
            <tr>
                <th>Test ID</th>
                <th>Blood Unit ID</th>
                <th>Test Date</th>
                <th>Test Status</th>
                <th>HIV/AIDS</th>
                <th>Hepatitis B</th>
                <th>Hepatitis C</th>
                <th>Syphilis</th>
                <th>Blood Type</th>
                <th>Expiration Date</th>
                <th>Storage Location</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo ($row['TestID']); ?></td>
                    <td><?php echo ($row['BloodUnitID']); ?></td>
                    <td><?php echo ($row['TestDate']); ?></td>
                    <td><?php echo ($row['TestStatus']); ?></td>
                    <td><?php echo ($row['HIVAIDS']); ?></td>
                    <td><?php echo ($row['HepatitisB']); ?></td>
                    <td><?php echo ($row['HepatitisC']); ?></td>
                    <td><?php echo ($row['Syphilis']); ?></td>
                    <td><?php echo ($row['BloodType']); ?></td>
                    <td><?php echo ($row['ExpirationDate']); ?></td>
                    <td><?php echo ($row['StorageLocation']); ?></td>
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
