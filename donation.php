<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "Loveyourself@1";
$dbname = "bloodbank_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch staff working in relevant roles: Nurse, Doctor, Phlebotomist
$staff_sql = "SELECT StaffID, Name FROM staff WHERE Role IN ('Nurse', 'Doctor', 'Phlebotomist') ORDER BY Name";
$staff_result = $conn->query($staff_sql);

if (!$staff_result) {
    die("Error fetching staff: " . $conn->error);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $donor_id = $_POST['donor_id'];
    $blood_group = $_POST['blood_group'];
    $donation_date = $_POST['donation_date'];
    $quantity = $_POST['quantity'];
    $location_name = $_POST['location_name'];
    $staff_id = $_POST['staff_id'];
    $comment = $_POST['comment'];

    // SQL statement to insert data into blooddonation table
    $sql = "INSERT INTO blooddonation (DonorID, BloodType, DonationDate, Quantity, LocationName, StaffID, Comment)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    // SQL statement to insert data into blooddonation table
    $sql = "INSERT INTO blooddonation (DonorID, BloodType, DonationDate, Quantity, LocationName, StaffID, Comment)
            VALUES ('$donor_id', '$blood_group', '$donation_date', '$quantity', '$location_name', '$staff_id', '$comment')";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        $successMessage = "New donation record created successfully.";
    } else {
        $errorMessage = "Error: " . $conn->error;
    }
}


// Fetch registered donors
$donors_sql = "SELECT DonorID, CONCAT(FirstName, ' ', LastName) AS FullName FROM donor";
$donors_result = $conn->query($donors_sql);

if (!$donors_result) {
    die("Error fetching donors: " . $conn->error);
}

// Fetch available locations
$locations_sql = "SELECT DISTINCT Name FROM location";
$locations_result = $conn->query($locations_sql);

if (!$locations_result) {
    die("Error fetching locations: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation</title>
    <link rel="stylesheet" href="styles.css">
    <script src="validation.js"></script>
</head>
<body>
<header>
    <h1>Blood Bank Management System</h1>
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
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
<main>
    <h2>Blood Donation Form</h2>

    <?php
    if (isset($successMessage)) {
        echo "<p class='success'>$successMessage</p>";
    } elseif (isset($errorMessage)) {
        echo "<p class='error'>$errorMessage</p>";
    }
    ?>

    <!-- Blood Donation Form -->
    <form method="POST" action="">
        <!-- Donor Selection -->
        <label for="donor_id">Donor:</label>
        <select id="donor_id" name="donor_id" >
            <option value="">Select a Donor</option>
            <?php
            if ($donors_result->num_rows > 0) {
                while ($row = $donors_result->fetch_assoc()) {
                    echo "<option value='" . $row['DonorID'] . "'>" .
                        "ID: " . $row['DonorID'] . " - " .
                        $row['FullName'] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <!-- Blood Group Selection -->
        <label for="blood_group">Blood Group:</label>
        <select id="blood_group" name="blood_group" >
            <option value="">Select Blood Group</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
        </select><br><br>

        <label for="donation_date">Donation Date:</label>
        <input type="text" id="donation_date" name="donation_date" placeholder="YYYY-MM-DD" ><br><br>

        <label for="quantity">Quantity (units):</label>
        <input type="text" id="quantity" name="quantity" ><br><br>

        <label for="location_name">Location:</label>
        <select id="location_name" name="location_name" >
            <option value="">Select a Location</option>
            <?php
            if ($locations_result->num_rows > 0) {
                while ($row = $locations_result->fetch_assoc()) {
                    echo "<option value='" . $row['Name'] . "'>" . $row['Name'] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="staff_id">Staff Member:</label>
        <select id="staff_id" name="staff_id" >
            <option value="">Select a Staff Member</option>
            <?php
            if ($staff_result->num_rows > 0) {
                while ($row = $staff_result->fetch_assoc()) {
                    echo "<option value='" . $row['StaffID'] . "'>" .
                        "ID: " . $row['StaffID'] . " - " .
                        $row['Name'] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="comment">Comment:</label>
        <textarea id="comment" name="comment" rows="4" cols="50" placeholder="Enter any comments here..."></textarea><br><br>

        <input type="submit" value="Submit">
    </form>
</main>
<footer>
    <p>&copy; 2024 Blood Bank Management System</p>
</footer>
<script src="validation.js"></script>
</body>
</html>

<?php
$conn->close();
?>
