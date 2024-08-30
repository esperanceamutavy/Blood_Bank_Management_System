<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">
<h2>Donor Registration Form</h2>
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
    <h2>Donor Registration Form</h2>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];

//database connectivity
    $servername = "localhost";
    $username = "root";
    $password = "Loveyourself@1";
    $dbname = "bloodbank_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

   $sql = "INSERT INTO donor (FirstName, LastName, DateOfBirth, Gender, Email) 
            VALUES ('$first_name', '$last_name', '$dob', '$gender', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>New donor record created successfully</p>";
    } else {
        echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }

    $conn->close();
}
?>
<form method="POST" action="">
    <label for="first_name">First Name:</label>
    <input type="text" id="first_name" name="first_name"><br><br>

    <label for="last_name">Last Name:</label>
    <input type="text" id="last_name" name="last_name"><br><br>

    <label for="dob">Date of Birth:</label>
    <input type="text" id="dob" name="dob" placeholder="YYYY-MM-DD"><br><br>

    <label for="gender">Gender:</label>
    <select id="gender" name="gender">
        <option value="M">Male</option>
        <option value="F">Female</option>
    </select><br><br>

    <label for="email">Email:</label>
    <input type="text" id="email" name="email"><br><br>

    <input type="submit" value="Submit">
</form>
</main>
<script src="validation.js"></script>
<footer>
    <p>&copy; 2024 Blood Bank Management System</p>
</footer>
</body>
</html>