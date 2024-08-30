<?php
//Database connection
$servername = "localhost";
$username = "user";
$password = "    ";//4 spaces
$dbname = "bloodbank_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bloodUnitID = $conn->real_escape_string($_POST['bloodUnitID']);
    $hivAIDS = $conn->real_escape_string($_POST['HIVAIDS']);
    $hepatitisB = $conn->real_escape_string($_POST['HepatitisB']);
    $hepatitisC = $conn->real_escape_string($_POST['HepatitisC']);
    $syphilis = $conn->real_escape_string($_POST['Syphilis']);
    $test_date = $conn->real_escape_string($_POST['test_date']);

    // Determine overall test status
    $testStatus = ($hivAIDS == 'N' && $hepatitisB == 'N' && $hepatitisC == 'N' && $syphilis == 'N') ? 'P' : 'F';

    // SQL statement to insert data into bloodtesting table
    $sql = "INSERT INTO bloodtesting (BloodUnitID, TestStatus, HIVAIDS, HepatitisB, HepatitisC, Syphilis, TestDate)
            VALUES ('$bloodUnitID', '$testStatus', '$hivAIDS', '$hepatitisB', '$hepatitisC', '$syphilis', '$test_date')";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        $successMessage = "New blood test recorded successfully";
    } else {
        $errorMessage = "Error: " . $conn->error;
    }
}

// Fetch all blood units
$fetchQuery = "SELECT b.BloodUnitID, b.BloodType, b.ExpirationDate
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
        <title>Blood Testing Form</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <header>
        <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">
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
        <h2>Blood Testing Form</h2>

        <?php
        if (isset($successMessage)) {
            echo "<p class='success'>$successMessage</p>";
        } elseif (isset($errorMessage)) {
            echo "<p class='error'>$errorMessage</p>";
        }
        ?>

        <form method="POST" action="">
            <label for="bloodUnitID">Blood Unit ID:</label>
            <select id="bloodUnitID" name="bloodUnitID" >
                <option value="">Select a Blood Unit</option>
                <?php while ($row = $results->fetch_assoc()): ?>
                    <option value="<?php echo ($row['BloodUnitID']); ?>">
                        ID: <?php echo ($row['BloodUnitID']); ?> -
                        Type: <?php echo ($row['BloodType']); ?> -
                        Expires: <?php echo ($row['ExpirationDate']); ?>
                    </option>
                <?php endwhile; ?>
            </select><br><br>

            <label for="HIVAIDS">HIV/AIDS:</label>
            <select id="HIVAIDS" name="HIVAIDS">
                <option value="P">Positive</option>
                <option value="N">Negative</option>
            </select><br><br>

            <label for="HepatitisB">Hepatitis B:</label>
            <select id="HepatitisB" name="HepatitisB" >
                <option value="P">Positive</option>
                <option value="N">Negative</option>
            </select><br><br>

            <label for="HepatitisC">Hepatitis C:</label>
            <select id="HepatitisC" name="HepatitisC" >
                <option value="P">Positive</option>
                <option value="N">Negative</option>
            </select><br><br>

            <label for="Syphilis">Syphilis:</label>
            <select id="Syphilis" name="Syphilis" >
                <option value="P">Positive</option>
                <option value="N">Negative</option>
            </select><br><br>

            <label for="test_date">Test Date:</label>
            <input type="text" id="test_date" name="test_date" placeholder="YYY-MM-DD"><br><br>

            <input type="submit" value="Submit">
        </form>
    </main>
    <footer>
        <p>&copy; 2024 Blood Bank Management System</p>
    </footer>
    </body>
    <script src="validation.js"></script>
    </html>

<?php $conn->close(); ?>