<?php
session_start();

// Check if user is logged in as Admin or Phlebotomist
if (!isset($_SESSION['UserID']) || ($_SESSION['Role'] !== 'Admin' && $_SESSION['Role'] !== 'Phlebotomist')) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "Loveyourself@1", "bloodbank_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$bloodTypeFilter = $_GET['blood_type'] ?? null;
$locationFilter = $_GET['location'] ?? null;

$queries = [
    'total_donations' => "SELECT BloodType, LocationName, COUNT(DonationID) AS TotalDonations FROM blooddonation 
                          LEFT JOIN location ON blooddonation.LocationName = location.Name 
                          WHERE 1=1 " .
        (!empty($bloodTypeFilter) ? " AND BloodType = '$bloodTypeFilter'" : "") .
        (!empty($locationFilter) ? " AND LocationName LIKE '%$locationFilter%'" : "") .
        (!empty($startDate) && !empty($endDate) ? " AND DonationDate BETWEEN '$startDate' AND '$endDate'" : "") .
        " GROUP BY BloodType, LocationName",
    'monthly_stats' => "SELECT YEAR(DonationDate) AS Year, MONTH(DonationDate) AS Month, BloodType, COUNT(DonationID) AS TotalDonations 
                        FROM blooddonation GROUP BY Year, Month, BloodType ORDER BY Year, Month",
    'quarterly_stats' => "SELECT YEAR(DonationDate) AS Year, CEIL(MONTH(DonationDate) / 3) AS Quarter, BloodType, COUNT(DonationID) AS TotalDonations 
                          FROM blooddonation GROUP BY Year, Quarter, BloodType ORDER BY Year, Quarter",
    'comparison' => "SELECT BloodType, 
                     SUM(CASE WHEN DonationDate BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE() THEN 1 ELSE 0 END) AS LastYearDonations,
                     SUM(CASE WHEN DonationDate BETWEEN DATE_SUB(CURDATE(), INTERVAL 2 YEAR) AND DATE_SUB(CURDATE(), INTERVAL 1 YEAR) THEN 1 ELSE 0 END) AS TwoYearsAgoDonations
                     FROM blooddonation GROUP BY BloodType",
    'high_demand' => "SELECT bd.BloodType, COUNT(bd.DonationID) AS TotalDonations, MIN(bu.ExpirationDate) AS EarliestExpirationDate
                      FROM blooddonation bd JOIN bloodunit bu ON bd.DonationID = bu.DonationID
                      GROUP BY bd.BloodType ORDER BY TotalDonations DESC"
];

$results = [];
foreach ($queries as $key => $query) {
    $result = $conn->query($query);
    if ($result) {
        $results[$key] = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        die("Query failed: " . $conn->error);
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Report</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h2>Donation Report</h2>
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
    <form method="GET">
        <div>
            <label for="start_date">Start Date:</label>
            <input type="text" id="start_date" name="start_date" value="<?php echo ($startDate); ?>" placeholder="YYYY-MM-DD">
        </div>
        <div>
            <label for="end_date">End Date:</label>
            <input type="text" id="end_date" name="end_date" value="<?php echo ($endDate); ?>" placeholder="YYYY-MM-DD">
        </div>
        <div>
            <label for="blood_type">Blood Type:</label>
            <input type="text" id="blood_type" name="blood_type" value="<?php echo ($bloodTypeFilter); ?>" placeholder="Blood Type">
        </div>
        <div>
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" value="<?php echo ($locationFilter); ?>" placeholder="Location">
        </div>
        <button type="submit">Filter</button>
    </form>
    </form>

    <?php foreach ($results as $key => $data): ?>
        <h3><?php echo ucwords(str_replace('_', ' ', $key)); ?></h3>
        <table>
            <thead>
            <tr>
                <?php foreach (array_keys($data[0]) as $column): ?>
                    <th><?php echo ($column); ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($row as $value): ?>
                        <td><?php echo ($value); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</main>
</body>
</html>