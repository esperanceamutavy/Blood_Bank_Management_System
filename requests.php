<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Request Form</title>
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
    <h2>Blood Request Form</h2>
    <?php
    // Define variables and initialize with empty values
    $hospital_id = $blood_group = $quantity = $request_date = $urgency = "";

    // Fetch list of hospitals
    $conn = new mysqli("localhost", "root", "Loveyourself@1", "bloodbank_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $hospital_query = "SELECT HospitalID, HospitalName FROM hospital";
    $hospital_result = $conn->query($hospital_query);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $hospital_id = $_POST['hospital_id'];
        $blood_group = $_POST['blood_group'];
        $quantity = $_POST['quantity'];
        $request_date = $_POST['request_date'];
        $urgency = $_POST['urgency'];

        // SQL statement to insert data into bloodrequests table
        $sql = "INSERT INTO bloodrequests (HospitalID, BloodType, Quantity, RequestDate, Urgency)
                VALUES ('$hospital_id', '$blood_group', '$quantity', '$request_date', '$urgency')";

        // Execute SQL statement
        if ($conn->query($sql) === TRUE) {
            echo "<p>New blood request created successfully</p>";
        } else {
            echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
        }
    }

    $conn->close();
    ?>
    <form method="POST" action="<?php echo ($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateRequestForm()">
        <label for="hospital_id">Hospital ID:</label>
        <select id="hospital_id" name="hospital_id" >
            <?php
            // Display the hospital options
            if ($hospital_result->num_rows > 0) {
                while($row = $hospital_result->fetch_assoc()) {
                    echo "<option value='" . $row["HospitalID"] . "'>" . $row["HospitalName"] . " (ID: " . $row["HospitalID"] . ")</option>";
                }
            } else {
                echo "<option value=''>No hospitals available</option>";
            }
            ?>
        </select><br><br>

        <label for="blood_group">Blood Group:</label>
        <select id="blood_group" name="blood_group" >
            <option value="A+" <?php if ($blood_group == "A+") echo "selected"; ?>>A+</option>
            <option value="A-" <?php if ($blood_group == "A-") echo "selected"; ?>>A-</option>
            <option value="B+" <?php if ($blood_group == "B+") echo "selected"; ?>>B+</option>
            <option value="B-" <?php if ($blood_group == "B-") echo "selected"; ?>>B-</option>
            <option value="AB+" <?php if ($blood_group == "AB+") echo "selected"; ?>>AB+</option>
            <option value="AB-" <?php if ($blood_group == "AB-") echo "selected"; ?>>AB-</option>
            <option value="O+" <?php if ($blood_group == "O+") echo "selected"; ?>>O+</option>
            <option value="O-" <?php if ($blood_group == "O-") echo "selected"; ?>>O-</option>
        </select><br><br>

        <label for="quantity">Quantity (in units):</label>
        <input type="text" id="quantity" name="quantity" value="<?php echo ($quantity); ?>" ><br><br>

        <label for="request_date">Request Date:</label>
        <input type="text" id="request_date" name="request_date" value="<?php echo ($request_date); ?>" ><br><br>

        <label for="urgency">Urgency:</label>
        <select id="urgency" name="urgency" >
            <option value="H" <?php if ($urgency == "H") echo "selected"; ?>>High</option>
            <option value="M" <?php if ($urgency == "M") echo "selected"; ?>>Medium</option>
            <option value="L" <?php if ($urgency == "L") echo "selected"; ?>>Low</option>
        </select><br><br>

        <input type="submit" value="Submit">
    </form>
</main>
<script src="validation.js"></script>
</body>
</html>
