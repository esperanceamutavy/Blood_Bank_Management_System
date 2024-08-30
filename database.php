<?php
//Database connection
$servername = "localhost";
$username = "user";
$password = "    ";//4 spaces
$dbname = "bloodbank_db";

// Create connection using conn variable to connect to database
$conn = new mysqli($servername, $username, $password, $dbname);

/* Checks connection, if it is not created an error message should be displayed
Die is used to end execution of the script any further and displays the error
$conn->connect_error checks if there was an error during the connection attempt.
*/
if ($conn->connect_error) {

    die("Connection failed: " . $conn->connect_error);
}
?>
