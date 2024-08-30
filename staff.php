<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration</title>
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
    <h2>Staff Registration Form</h2>
    <?php
    // Define variables and initialize with empty values
    $staff_id = $staff_name = $staff_position = "";

    // Processing form data when form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $staff_name = $_POST['staff_name'];
        $staff_position = $_POST['staff_position'];

        // Database connection
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

        // SQL statement to insert data into staff table
        $sql = "INSERT INTO staff (Name, Role) VALUES (?, ?)";

        // Prepare statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $staff_name, $staff_position);

        // Execute SQL statement
        if ($stmt->execute()) {
            echo "<p>New staff registered successfully</p>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
    ?>
    <form method="POST" action="<?php echo ($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateStaffForm()">
        <label for="staff_name">Staff Name:</label>
        <input type="text" id="staff_name" name="staff_name" value="<?php echo ($staff_name); ?>" ><br><br>

        <label for="staff_position">Staff Position:</label>
        <select id="staff_position" name="staff_position" >
            <option value="" disabled selected>Select role</option>
            <option value="Phlebotomist" <?php echo ($staff_position == "Phlebotomist") ? 'selected' : ''; ?>>Phlebotomist</option>
            <option value="Nurse" <?php echo ($staff_position == "Nurse") ? 'selected' : ''; ?>>Nurse</option>
            <option value="Doctor" <?php echo ($staff_position == "Doctor") ? 'selected' : ''; ?>>Doctor</option>
            <option value="Lab Technician" <?php echo ($staff_position == "Lab Technician") ? 'selected' : ''; ?>>Lab Technician</option>
        </select><br><br>

        <input type="submit" value="Submit">
    </form>
</main>
<script src="validation.js"></script>
</body>
</html>
