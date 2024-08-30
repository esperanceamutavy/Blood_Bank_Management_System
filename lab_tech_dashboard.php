<?php
session_start();

// Check if user is logged in as Lab Technician
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] !== 'Lab Technician') {
    header("Location: login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "Loveyourself@1";
$dbname = "bloodbank_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for updating test results
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_test'])) {
    $testID = $conn->real_escape_string($_POST['TestID']);
    $testStatus = $conn->real_escape_string($_POST['TestStatus']);
    $hivAIDS = $conn->real_escape_string($_POST['HIVAIDS']);
    $hepatitisB = $conn->real_escape_string($_POST['HepatitisB']);
    $hepatitisC = $conn->real_escape_string($_POST['HepatitisC']);
    $syphilis = $conn->real_escape_string($_POST['Syphilis']);

    $updateQuery = "UPDATE bloodtesting 
                    SET TestStatus = '$testStatus', HIVAIDS = '$hivAIDS', HepatitisB = '$hepatitisB', HepatitisC = '$hepatitisC', Syphilis = '$syphilis' 
                    WHERE TestID = $testID";

    if ($conn->query($updateQuery) === TRUE) {
        $successMessage = "Test results updated successfully!";
    } else {
        $errorMessage = "Error updating record: " . $conn->error;
    }
}

// Fetch untested blood donations
$fetchQuery = "SELECT b.BloodUnitID, b.BloodType, b.ExpirationDate, b.DonationID
               FROM bloodunit b
               LEFT JOIN bloodtesting bt ON b.BloodUnitID = bt.BloodUnitID
               WHERE bt.BloodUnitID IS NULL";
$results = $conn->query($fetchQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Technician Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">
    <h2>Lab Technician Dashboard</h2>
    <nav>
        <ul>
            <li><a href="bloodtesting.php">Blood Testing Form</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
<main>
    <section>
        <h2>Untested Blood Donations</h2>

        <?php if ($results->num_rows > 0): ?>
            <table>
                <thead>
                <tr>
                    <th>Blood Unit ID</th>
                    <th>Blood Type</th>
                    <th>Expiration Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $results->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo ($row['BloodUnitID']); ?></td>
                        <td><?php echo ($row['BloodType']); ?></td>
                        <td><?php echo ($row['ExpirationDate']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="TestID" value="<?php echo ($row['BloodUnitID']); ?>">
                                <button type="submit" name="edit_test">Update Test Results</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No untested blood donations found.</p>
        <?php endif; ?>

        <?php if (isset($_POST['edit_test'])): ?>
            <h2>Update Test Results</h2>
            <form method="POST" action="">
                <input type="hidden" name="TestID" value="<?php echo ($_POST['TestID']); ?>">
                <label for="TestStatus">Test Status:</label>
                <select id="TestStatus" name="TestStatus" >
                    <option value="P">Pending</option>
                    <option value="F">Failed</option>
                    <option value="P">Passed</option>
                </select>
                <label for="HIVAIDS">HIV/AIDS:</label>
                <select id="HIVAIDS" name="HIVAIDS" >
                    <option value="P">Positive</option>
                    <option value="N">Negative</option>
                </select>
                <label for="HepatitisB">Hepatitis B:</label>
                <select id="HepatitisB" name="HepatitisB" >
                    <option value="P">Positive</option>
                    <option value="N">Negative</option>
                </select>
                <label for="HepatitisC">Hepatitis C:</label>
                <select id="HepatitisC" name="HepatitisC" >
                    <option value="P">Positive</option>
                    <option value="N">Negative</option>
                </select>
                <label for="Syphilis">Syphilis:</label>
                <select id="Syphilis" name="Syphilis" >
                    <option value="P">Positive</option>
                    <option value="N">Negative</option>
                </select>
                <button type="submit" name="update_test">Update Results</button>
            </form>

            <?php if (isset($successMessage)): ?>
                <p><?php echo ($successMessage); ?></p>
            <?php elseif (isset($errorMessage)): ?>
                <p><?php echo ($errorMessage); ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </section>

</main>
<footer>
    <p>&copy; 2024 Blood Bank Management System</p>
</footer>
</body>
</html>

<?php $conn->close(); ?>
