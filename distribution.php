<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Distribution Form</title>
    <link rel="stylesheet" href="styles.css">
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
    <h2>Blood Distribution Form</h2>
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

    // Processing form data when form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $request_id = $_POST['request_id'];
        $blood_unit_id = $_POST['blood_unit_id'];
        $distribution_date = $_POST['distribution_date'];

        // SQL statement to insert data into distribution table
        $sql = "INSERT INTO distribution (RequestID, BloodUnitID, DistributionDate, DeliveryStatus)
            VALUES ('$request_id', '$blood_unit_id', '$distribution_date', 'Pending')";

        // Execute SQL statement
        if ($conn->query($sql) === TRUE) {
            echo "<p>New blood distribution recorded successfully</p>";
        } else {
            echo "<p>Error: " . $conn->error . "</p>";
        }
    }

    // Fetch available blood units that have not yet been distributed
    $blood_units_sql = "SELECT BloodUnitID FROM bloodunit WHERE BloodUnitID NOT IN (SELECT BloodUnitID FROM distribution)";
    $blood_units_result = $conn->query($blood_units_sql);

    if (!$blood_units_result) {
        die("Error fetching blood units: " . $conn->error);
    }

    // Fetch request IDs
    $requests_sql = "SELECT RequestID FROM bloodrequests";
    $requests_result = $conn->query($requests_sql);

    if (!$requests_result) {
        die("Error fetching requests: " . $conn->error);
    }
    ?>
    <form method="POST" action="">
        <label for="request_id">Request ID:</label>
        <select id="request_id" name="request_id" >
            <option value="">Select a Request</option>
            <?php
            if ($requests_result->num_rows > 0) {
                while ($row = $requests_result->fetch_assoc()) {
                    echo "<option value='" . $row['RequestID'] . "'>" . $row['RequestID'] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="blood_unit_id">Blood Unit ID:</label>
        <select id="blood_unit_id" name="blood_unit_id" >
            <option value="">Select a Blood Unit</option>
            <?php
            if ($blood_units_result->num_rows > 0) {
                while ($row = $blood_units_result->fetch_assoc()) {
                    echo "<option value='" . $row['BloodUnitID'] . "'>" . $row['BloodUnitID'] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="distribution_date">Distribution Date:</label>
        <input type="text" id="distribution_date" name="distribution_date" placeholder="YYYY-MM-DD" ><br><br>

        <input type="submit" value="Submit">
    </form>
</main>
<script src="validation.js"></script>
</body>
</html>
