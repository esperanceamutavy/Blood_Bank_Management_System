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

$email = "";
$donor_details = null;
$eligibility_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT * FROM donor WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $donor_details = $result->fetch_assoc();
        $current_date = new DateTime();
        $last_donation_date = new DateTime($donor_details['DateOfLastDonation']);
        $interval = $current_date->diff($last_donation_date);
        $days_remaining = max(0, 42 - $interval->days);
        $eligibility_message = $days_remaining == 0 ? "You are eligible to donate again." : "You will be eligible to donate again in $days_remaining days.";
    } else {
        $eligibility_message = "No donor found with the provided email address.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">
    <h2>Donor Dashboard</h2>
    <nav>
        <ul>
            <li><a href="index.html">Home</a></li>
        </ul>
    </nav>
</header>
<main>
    <form method="POST" action="<?php echo ($_SERVER["PHP_SELF"]); ?>">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <input type="submit" value="Search">
    </form>
    <?php if ($donor_details): ?>
        <h3>Donor Details</h3>
        <p><strong>First Name:</strong> <?php echo $donor_details['FirstName']; ?></p>
        <p><strong>Last Name:</strong> <?php echo $donor_details['LastName']; ?></p>
        <p><strong>Date of Birth:</strong> <?php echo $donor_details['DateOfBirth']; ?></p>
        <p><strong>Gender:</strong> <?php echo $donor_details['Gender']; ?></p>
        <p><strong>Email:</strong> <?php echo $donor_details['Email']; ?></p>
        <p><strong>Date of Last Donation:</strong> <?php echo $donor_details['DateOfLastDonation']; ?></p>
        <p><strong>Eligibility:</strong> <?php echo $eligibility_message; ?></p>
    <?php else: ?>
        <p><?php echo $eligibility_message; ?></p>
    <?php endif; ?>
</main>
<script src="validation.js"></script>

<footer>
    <p>&copy; 2024 Blood Bank Management System</p>
</footer>
</body>
</html>