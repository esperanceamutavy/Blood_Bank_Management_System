<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Registration Form</title>
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
    <h2>Hospital Registration Form</h2>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $hospital_name = $_POST['hospital_name'];
        $address = $_POST['address'];
        $contact_person_name = $_POST['contact_person_name'];
        $contact_number = $_POST['contact_number'];
        $email = $_POST['email'];


//Database connection
        $servername = "localhost";
        $username = "user";
        $password = "    ";//4 spaces
        $dbname = "bloodbank_db";

        $conn = new mysqli($servername, $username, $password, $dbname);


        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare SQL statement to insert data into hospital table
        $sql = "INSERT INTO hospital (HospitalName, Address, ContactPersonName, ContactNumber, Email)
                VALUES ('$hospital_name', '$address', '$contact_person_name', '$contact_number', '$email')";

        // Execute SQL statement
        if ($conn->query($sql) === TRUE) {
            echo "<p>New hospital registered successfully</p>";
        } else {
            echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
        }

        // Close connection
        $conn->close();
    }
    ?>
    <form method="POST" action="<?php echo ($_SERVER["PHP_SELF"]); ?>">
        <label for="hospital_name">Hospital Name:</label>
        <input type="text" id="hospital_name" name="hospital_name" ><br><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" ><br><br>

        <label for="contact_person_name">Contact Person Name:</label>
        <input type="text" id="contact_person_name" name="contact_person_name" ><br><br>

        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" ><br><br>

        <label for="email">Email:</label>
        <input type="text" id="email" name="email" ><br><br>

        <input type="submit" value="Submit">
    </form>
</main>
</body>
</html>
