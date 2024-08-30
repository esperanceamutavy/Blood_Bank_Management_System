<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Ensure password input text is hidden */
        .password-input {
            font-family: 'Arial', sans-serif;
            font-size: 16px;
        }
    </style>
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
            <li><a href="bloodtesting.php">Blood Testing</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
<main>
    <h2>Staff Registration Form</h2>
    <?php
    // Define variables and initialize with empty values
    $staff_name = $staff_position = $password = "";
    $errors = [];

    // Processing form data when form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $staff_name = trim($_POST['staff_name']);
        $staff_position = trim($_POST['staff_position']);
        $password = trim($_POST['password']);

        // Validate form data
        if (empty($staff_name)) {
            $errors[] = "Staff name is required.";
        }

        if (empty($staff_position)) {
            $errors[] = "Staff position is required.";
        }

        if (empty($password)) {
            $errors[] = "Password is required.";
        }

        // If there are no validation errors, proceed with database operations
        if (empty($errors)) {
            // Database connection
            $servername = "localhost";
            $username = "user";
            $password_db = "    "; // 4 spaces
            $dbname = "bloodbank_db";

            // Create connection
            $conn = new mysqli($servername, $username, $password_db, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Begin transaction
            $conn->begin_transaction();

            try {
                // Check if the username already exists
                $sql_check_user = "SELECT UserID FROM users WHERE Username = ?";
                $stmt_check_user = $conn->prepare($sql_check_user);
                $stmt_check_user->bind_param("s", $staff_name);
                $stmt_check_user->execute();
                $stmt_check_user->store_result();

                if ($stmt_check_user->num_rows > 0) {
                    echo "<p>User already exists.</p>";
                    $stmt_check_user->close();
                    $conn->rollback();
                } else {
                    // Insert into users table
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $sql_users = "INSERT INTO users (Username, Password, Role) VALUES (?, ?, ?)";
                    $stmt_users = $conn->prepare($sql_users);
                    $stmt_users->bind_param("sss", $staff_name, $hashed_password, $staff_position);
                    $stmt_users->execute();

                    // Insert into staff table
                    $sql_staff = "INSERT INTO staff (Name, Role) VALUES (?, ?)";
                    $stmt_staff = $conn->prepare($sql_staff);
                    $stmt_staff->bind_param("ss", $staff_name, $staff_position);
                    $stmt_staff->execute();

                    // Commit transaction
                    $conn->commit();
                    echo "<p>New staff registered successfully</p>";

                    // Close statements
                    $stmt_users->close();
                    $stmt_staff->close();
                }

                // Close connection
                $conn->close();
            } catch (Exception $e) {
                // Rollback transaction if something goes wrong
                $conn->rollback();
                echo "<p>Error: " . $e->getMessage() . "</p>";
            }
        } else {
            // Display validation errors
            foreach ($errors as $error) {
                echo "<p>$error</p>";
            }
        }
    }
    ?>
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="staffRegistrationForm">
        <label for="staff_name">Staff Name:</label>
        <input type="text" id="staff_name" name="staff_name" value="<?php echo $staff_name; ?>"><br><br>

        <label for="staff_position">Staff Position:</label>
        <select id="staff_position" name="staff_position">
            <option value="" disabled <?php echo empty($staff_position) ? 'selected' : ''; ?>>Select role</option>
            <option value="Admin" <?php echo ($staff_position == "Admin") ? 'selected' : ''; ?>>Admin</option>
            <option value="Phlebotomist" <?php echo ($staff_position == "Phlebotomist") ? 'selected' : ''; ?>>Phlebotomist</option>
            <option value="Nurse" <?php echo ($staff_position == "Nurse") ? 'selected' : ''; ?>>Nurse</option>
            <option value="Doctor" <?php echo ($staff_position == "Doctor") ? 'selected' : ''; ?>>Doctor</option>
            <option value="Lab Technician" <?php echo ($staff_position == "Lab Technician") ? 'selected' : ''; ?>>Lab Technician</option>
        </select><br><br>

        <label for="password">Password:</label>
        <input type="text" id="password" name="password" class="password-input" value="<?php echo $password; ?>"><br><br>

        <input type="submit" value="Submit">
    </form>
</main>
<script src="validation.js"></script>
</body>
</html>
