<?php
//Database connection
$servername = "localhost";
$username = "user";
$password = "    ";//4 spaces
$dbname = "bloodbank_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $donation_id = $_POST['donation_id'];
    $blood_type = $_POST['blood_type'];
    $expiration_date = $_POST['expiration_date'];
    $storage_location = $_POST['storage_location'];


// SQL statement to insert data into bloodunit table
    $sql = "INSERT INTO bloodunit (DonationID, BloodType, ExpirationDate, StorageLocation)
            VALUES ('$donation_id', '$blood_type', '$expiration_date', '$storage_location')";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        $successMessage = "New blood unit record created successfully";
    } else {
        $errorMessage = "Error: " . $conn->error;
    }
}
// Fetch donations that haven't been associated with a blood unit yet
$donations_sql = "SELECT bd.DonationID, d.FirstName, d.LastName, bd.BloodType 
                  FROM blooddonation bd
                  JOIN donor d ON bd.DonorID = d.DonorID
                  WHERE bd.DonationID NOT IN (SELECT DonationID FROM bloodunit)";
$donations_result = $conn->query($donations_sql);

// Fetch available storage locations
$locations_sql = "SELECT Name FROM location";
$locations_result = $conn->query($locations_sql);
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Blood Unit Registration</title>
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
        <h2>Blood Unit Registration Form</h2>

        <?php
        if (isset($successMessage)) {
            echo "<p class='success'>$successMessage</p>";
        } elseif (isset($errorMessage)) {
            echo "<p class='error'>$errorMessage</p>";
        }
        ?>

        <form method="POST" action="">
            <label for="donation_id">Donation ID:</label>
            <select id="donation_id" name="donation_id">
                <option value="">Select a Donation</option>
                <?php
                if ($donations_result->num_rows > 0) {
                    while ($row = $donations_result->fetch_assoc()) {
                        echo "<option value='" . $row['DonationID'] . "'>" .
                            "ID: " . $row['DonationID'] . " - " .
                            $row['FirstName'] . " " . $row['LastName'] .
                            " (Blood Type: " . $row['BloodType'] . ")</option>";
                    }
                }
                ?>
            </select><br><br>

            <label for="blood_type">Blood Type:</label>
            <input type="text" id="blood_type" name="blood_type" list="blood_types">
            <datalist id="blood_types">
                <option value="A+">
                <option value="A-">
                <option value="B+">
                <option value="B-">
                <option value="AB+">
                <option value="AB-">
                <option value="O+">
                <option value="O-">
            </datalist><br><br>

            <label for="expiration_date">Expiration Date:</label>
            <input type="text" id="expiration_date" name="expiration_date" placeholder="YYYY-MM-DD"><br><br>

            <label for="storage_location">Storage Location:</label>
            <input type="text" id="storage_location" name="storage_location" list="locations">
            <datalist id="locations">
                <?php
                if ($locations_result->num_rows > 0) {
                    while ($row = $locations_result->fetch_assoc()) {
                        echo "<option value='" . $row['Name'] . "'>";
                    }
                }
                ?>
            </datalist><br><br>

            <input type="submit" value="Submit">
        </form>
    </main>
    <footer>
        <p>&copy; 2024 Blood Bank Management System</p>
    </footer>
    <script src="validation.js"></script>
    </body>
    </html>

<?php $conn->close(); ?>