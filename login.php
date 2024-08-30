<?php
//initialize session
session_start();

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

$message = '';

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL statement to fetch user
    $sql = "SELECT * FROM users WHERE Username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {  //checks to see if a user with the specified username actually exists in the database
        //fetch_assoc is an associative array used to retrieve a single row of data from a result set
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['Password'])) {
            // Set session variables
            $_SESSION['UserID'] = $user['UserID'];
            $_SESSION['Username'] = $user['Username'];
            $_SESSION['Role'] = $user['Role'];

            // Redirect based on user role
            switch ($user['Role']) {
                case 'Admin':
                case 'Phlebotomist':
                    header("Location: admin_dashboard.php");
                    break;
                case 'Doctor':
                case 'Nurse':
                    header("Location: patient_care_dashboard.php");
                    break;
                case 'Lab Technician':
                    header("Location: lab_tech_dashboard.php");
                    break;
                case 'Donor':
                    header("Location: donor_dashboard.php");
                    break;
                default:
                    header("Location: index.html");
                    break;
            }
            exit;
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Invalid username.";
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <img src="Blood Bank.jpeg" alt="Blood Bank Logo" class="logo">
    <nav>
        <ul>
            <li><a href="index.html">About</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
<main>
    <h2>Login</h2>
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form action="" method="post" id="loginForm">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username"><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password"><br>

        <input type="submit" value="Login">
    </form>
</main>
<script src="validation.js"></script>
</body>
</html>
