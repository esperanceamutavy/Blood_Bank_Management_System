<?php
session_start();

// Check if user is logged in as Nurse or Doctor
if (!isset($_SESSION['UserID']) || ($_SESSION['Role'] !== 'Nurse' && $_SESSION['Role'] !== 'Doctor')) {
    header("Location: login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "Loveyourself@1";
$dbname = "bloodbank_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle donor addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_donor'])) {
    $firstName = $conn->real_escape_string($_POST['FirstName']);
    $lastName = $conn->real_escape_string($_POST['LastName']);
    $dob = $conn->real_escape_string($_POST['DOB']);
    $gender = $conn->real_escape_string($_POST['Gender']);
    $email = $conn->real_escape_string($_POST['Email']);
    $lastDonationDate = $conn->real_escape_string($_POST['LastDonationDate']);

    $insertDonorQuery = "INSERT INTO donor (FirstName, LastName, DOB, Gender, Email, LastDonationDate) 
                         VALUES ('$firstName', '$lastName', '$dob', '$gender', '$email', '$lastDonationDate')";

    if ($conn->query($insertDonorQuery) === TRUE) {
        $donorSuccessMessage = "Donor added successfully!";
    } else {
        $donorErrorMessage = "Error adding donor: " . $conn->error;
    }
}

// Handle blood donation addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_donation'])) {
    $donorID = $conn->real_escape_string($_POST['DonorID']);
    $locationName = $conn->real_escape_string($_POST['LocationName']);
    $donationDate = $conn->real_escape_string($_POST['DonationDate']);
    $bloodType = $conn->real_escape_string($_POST['BloodType']);
    $quantity = $conn->real_escape_string($_POST['Quantity']);
    $notes = $conn->real_escape_string($_POST['Notes']);
    $staffID = $conn->real_escape_string($_SESSION['UserID']);

    $insertDonationQuery = "INSERT INTO blooddonation (DonorID, LocationName, DonationDate, BloodType, Quantity, Comment, StaffID) 
                            VALUES ('$donorID', '$locationName', '$donationDate', '$bloodType', '$quantity', '$notes', '$staffID')";

    if ($conn->query($insertDonationQuery) === TRUE) {
        $donationSuccessMessage = "Donation recorded successfully!";

        // Send to Lab Technician dashboard
        $insertBloodUnitQuery = "INSERT INTO bloodunit (DonationID, BloodType, ExpirationDate, StorageLocation) 
                                 VALUES ((SELECT LAST_INSERT_ID()), '$bloodType', DATE_ADD('$donationDate', INTERVAL 6 MONTH), 'Unassigned')";
        $conn->query($insertBloodUnitQuery);
    } else {
        $donationErrorMessage = "Error recording donation: " . $conn->error;
    }
}

// Fetch donors for selection
$donorsQuery = "SELECT DonorID, CONCAT(FirstName, ' ', LastName) AS FullName FROM donor";
$donorsResult = $conn->query($donorsQuery);

// Fetch locations for dropdown
$locationsQuery = "SELECT DISTINCT Name FROM location";
$locationsResult = $conn->query($locationsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Care Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h2>Patient Care Dashboard</h2>
</header>
<main>
    <section>
        <h2>Add New Donor</h2>
        <form method="POST" action="">
            <label for="FirstName">First Name:</label>
            <input type="text" id="FirstName" name="FirstName" >
            <label for="LastName">Last Name:</label>
            <input type="text" id="LastName" name="LastName" >
            <label for="DOB">Date of Birth:</label>
            <input type="date" id="DOB" name="DOB" >
            <label for="Gender">Gender:</label>
            <select id="Gender" name="Gender" >
                <option value="M">Male</option>
                <option value="F">Female</option>
            </select>
            <label for="Email">Email:</label>
            <input type="email" id="Email" name="Email" >
            <label for="LastDonationDate">Last Donation Date:</label>
            <input type="date" id="LastDonationDate" name="LastDonationDate">
            <button type="submit" name="add_donor">Add Donor</button>
        </form>

        <?php if (isset($donorSuccessMessage)): ?>
            <p><?php echo ($donorSuccessMessage); ?></p>
        <?php elseif (isset($donorErrorMessage)): ?>
            <p><?php echo ($donorErrorMessage); ?></p>
        <?php endif; ?>
    </section>

    <section>
        <h2>Add Blood Donation</h2>
        <form method="POST" action="">
            <label for="DonorID">Donor:</label>
            <select id="DonorID" name="DonorID" >
                <?php while ($row = $donorsResult->fetch_assoc()): ?>
                    <option value="<?php echo ($row['DonorID']); ?>">
                        <?php echo ($row['FullName']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label for="LocationName">Donation Location:</label>
            <select id="LocationName" name="LocationName" >
                <?php while ($row = $locationsResult->fetch_assoc()): ?>
                    <option value="<?php echo ($row['Name']); ?>">
                        <?php echo ($row['Name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label for="DonationDate">Donation Date:</label>
            <input type="date" id="DonationDate" name="DonationDate" >
            <label for="BloodType">Blood Type:</label>
            <input type="text" id="BloodType" name="BloodType" >
            <label for="Quantity">Quantity (in pints):</label>
            <input type="number" id="Quantity" name="Quantity" step="0.01" >
            <label for="Notes">Notes:</label>
            <textarea id="Notes" name="Notes"></textarea>
            <button type="submit" name="add_donation">Add Donation</button>
        </form>

        <?php if (isset($donationSuccessMessage)): ?>
            <p><?php echo ($donationSuccessMessage); ?></p>
        <?php elseif (isset($donationErrorMessage)): ?>
            <p><?php echo ($donationErrorMessage); ?></p>
        <?php endif; ?>
    </section>
</main>
<footer>
    <p>&copy; 2024 Blood Bank Management System</p>
</footer>
</body>
</html>

<?php $conn->close(); ?>
