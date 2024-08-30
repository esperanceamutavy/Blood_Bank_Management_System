<?php
session_start();

if (!isset($_SESSION['UserID']) || ($_SESSION['Role'] !== 'Admin' && $_SESSION['Role'] !== 'Phlebotomist')) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "Loveyourself@1", "bloodbank_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$filters = [
    'search' => $_GET['search'] ?? '',
    'blood_type' => $_GET['blood_type'] ?? '',
    'location' => $_GET['location'] ?? '',
    'start_date' => $_GET['start_date'] ?? '',
    'end_date' => $_GET['end_date'] ?? ''
];

$query = "
SELECT 
    donor.*, 
    COUNT(blooddonation.DonationID) AS TotalDonations, 
    MAX(blooddonation.DonationDate) AS LastDonationDate, 
    GROUP_CONCAT(DISTINCT location.Name) AS LocationNames
FROM donor
LEFT JOIN blooddonation ON donor.DonorID = blooddonation.DonorID
LEFT JOIN location ON blooddonation.LocationName = location.Name
WHERE 1 = 1
";

foreach ($filters as $key => $value) {
    if (!empty($value)) {
        $escapedValue = $conn->real_escape_string($value);
        switch ($key) {
            case 'search':
                $query .= " AND (donor.FirstName LIKE '%$escapedValue%' OR donor.LastName LIKE '%$escapedValue%' OR donor.Email LIKE '%$escapedValue%')";
                break;
            case 'blood_type':
                $query .= " AND blooddonation.BloodType = '$escapedValue'";
                break;
            case 'location':
                $query .= " AND location.Name LIKE '%$escapedValue%'";
                break;
            case 'start_date':
                if (!empty($filters['end_date'])) {
                    $query .= " AND blooddonation.DonationDate BETWEEN '$escapedValue' AND '{$filters['end_date']}'";
                }
                break;
        }
    }
}

$query .= " GROUP BY donor.DonorID";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

function calculateNextEligibility($lastDonationDate) {
    return $lastDonationDate ? (new DateTime($lastDonationDate))->modify('+56 days')->format('Y-m-d') : 'Eligible now';
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Donor Report</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <header>
        <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">
        <h2>Donor Report</h2>
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
            <h2>Donor Report</h2>
            <form method="GET">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search" placeholder="Search by name or email" value="<?php echo $filters['search']; ?>">

                <label for="blood_type">Blood Type:</label>
                <select id="blood_type" name="blood_type">
                    <option value="">All</option>
                    <?php
                    $bloodTypes = ['O+', 'A+', 'B+', 'AB+', 'O-', 'A-', 'B-', 'AB-'];
                    foreach ($bloodTypes as $type) {
                        echo "<option value=\"$type\"" . ($filters['blood_type'] === $type ? ' selected' : '') . ">$type</option>";
                    }
                    ?>
                </select>

                <label for="location">Location:</label>
                <input type="text" id="location" name="location" placeholder="Search by location" value="<?php echo $filters['location']; ?>">

                <label for="start_date">Start Date:</label>
                <input type="text" id="start_date" name="start_date" placeholder="YYYY-MM-DD" value="<?php echo $filters['start_date']; ?>">

                <label for="end_date">End Date:</label>
                <input type="text" id="end_date" name="end_date" placeholder="YYYY-MM-DD" value="<?php echo $filters['end_date']; ?>">

                <button type="submit">Filter</button>
            </form>
            <table>
                <thead>
                <tr>
                    <th>Donor ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Total Donations</th>
                    <th>Last Donation Date</th>
                    <th>Next Eligible Date</th>
                    <th>Location</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <?php
                        $fields = ['DonorID', 'FirstName', 'LastName', 'DateOfBirth', 'Gender', 'Email', 'TotalDonations', 'LastDonationDate'];
                        foreach ($fields as $field) {
                            echo "<td>" . ($row[$field] ?? ($field === 'TotalDonations' ? '0' : 'N/A')) . "</td>";
                        }
                        echo "<td>" . calculateNextEligibility($row['LastDonationDate']) . "</td>";
                        echo "<td>" . ($row['LocationNames'] ?? 'N/A') . "</td>";
                        ?>
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