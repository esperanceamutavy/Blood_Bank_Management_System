<?php
session_start();

//Database connection
$servername = "localhost";
$username = "user";
$password = "    ";//4 spaces
$dbname = "bloodbank_db";


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle filters
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$hospitalFilter = $_GET['hospital'] ?? '';
$bloodTypeFilter = $_GET['blood_type'] ?? '';

// Query for distribution report
$query = "SELECT d.DistributionID, d.DistributionDate, d.BloodUnitID, d.DeliveryStatus, 
                 b.BloodType, h.HospitalName, r.RequestDate, r.Quantity AS RequestedQuantity
          FROM distribution d
          JOIN bloodrequests r ON d.RequestID = r.RequestID
          JOIN hospital h ON r.HospitalID = h.HospitalID
          JOIN bloodunit b ON d.BloodUnitID = b.BloodUnitID
          WHERE 1=1";

if ($startDate && $endDate) {
    $query .= " AND d.DistributionDate BETWEEN '$startDate' AND '$endDate'";
}
if ($hospitalFilter) {
    $query .= " AND h.HospitalName LIKE '%$hospitalFilter%'";
}
if ($bloodTypeFilter) {
    $query .= " AND b.BloodType LIKE '%$bloodTypeFilter%'";
}

$query .= " ORDER BY d.DistributionDate";

$result = $conn->query($query);
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Distribution Report</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <header>
        <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">
        <h2>Distribution Report</h2>
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
            <h2>Distribution Report</h2>
            <form method="GET" action="">
                <label for="start_date">Start Date:</label>
                <input type="text" id="start_date" name="start_date" value="<?php echo ($startDate ?? ''); ?>" placeholder="YYYY-MM-DD">
                <label for="end_date">End Date:</label>
                <input type="text" id="end_date" name="end_date" value="<?php echo ($endDate ?? ''); ?>"placeholder="YYYY-MM-DD">
                <label for="hospital">Hospital:</label>
                <input type="text" id="hospital" name="hospital" value="<?php echo ($hospitalFilter); ?>">
                <label for="blood_type">Blood Type:</label>
                <input type="text" id="blood_type" name="blood_type" value="<?php echo ($bloodTypeFilter); ?>">
                <button type="submit">Filter</button>
            </form>
            <table>
                <thead>
                <tr>
                    <th>Distribution ID</th>
                    <th>Date</th>
                    <th>Blood Type</th>
                    <th>Requested Quantity</th>
                    <th>Delivery Status</th>
                    <th>Hospital</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo ($row['DistributionID']); ?></td>
                        <td><?php echo ($row['DistributionDate']); ?></td>
                        <td><?php echo ($row['BloodType']); ?></td>
                        <td><?php echo ($row['RequestedQuantity']); ?></td>
                        <td><?php echo ($row['DeliveryStatus']); ?></td>
                        <td><?php echo ($row['HospitalName']); ?></td>
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