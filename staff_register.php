<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "Loveyourself@1";
$dbname = "bloodbank_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $role = $_POST['role'];

    $sql = "INSERT INTO staff (Name, Role) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $role);

    if ($stmt->execute()) {
        $message = "Staff registration successful!";
    } else {
        $message = "There was an error in staff registration: " . $stmt->error;
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
    <title>Staff Registration</title>
    <link rel="stylesheet" href="styles.css">

</head>
<body>
<header>
    <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">

</header>

<main>
    <div class="staff-form">
        <h2>Staff Registration</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="<?php echo ($_SERVER["PHP_SELF"]); ?>" method="post" id="staffForm">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" >

            <label for="role">Role:</label>
            <select id="role" name="role" >
                <option value="">Select a role</option>
                <option value="Phlebotomist">Phlebotomist</option>
                <option value="Nurse">Nurse</option>
                <option value="Doctor">Doctor</option>
                <option value="Lab Technician">Lab Technician</option>
            </select>

            <input type="submit" value="Register Staff">
        </form>
    </div>
</main>

<footer>

</footer>

<script src="validation.js"></script>
</body>
</html>
