<?php
session_start(); // Start session to access user info
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit(); // Redirect to login if not logged in
}

$username = $_SESSION['username'];
$login_type = $_SESSION['login_type'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'gym1');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($login_type === 'user') {
    // Fetch packages and slots for users
    $packages_sql = "SELECT p.package_id, p.package_name, p.price, p.duration, p.description, ps.slot_time
                     FROM packages p
                     LEFT JOIN package_slots ps ON p.package_id = ps.package_id";
    $packages_result = $conn->query($packages_sql);

    echo "<h1>Welcome, " . htmlspecialchars($username) . "!</h1>";
    echo "<h2>Available Packages</h2>";

    if ($packages_result->num_rows > 0) {
        echo "<form action='select_package.php' method='POST'>"; // Form to select package
        while ($row = $packages_result->fetch_assoc()) {
            echo "<div>";
            echo "<h3>" . htmlspecialchars($row['package_name']) . "</h3>";
            echo "Price: $" . htmlspecialchars($row['price']) . "<br>";
            echo "Duration: " . htmlspecialchars($row['duration']) . " days<br>";
            echo "Description: " . htmlspecialchars($row['description']) . "<br>";
            echo "Available Time Slot: " . htmlspecialchars($row['slot_time']) . "<br>";

            // Include package ID as a hidden input
            echo "<input type='hidden' name='package_id' value='" . htmlspecialchars($row['package_id']) . "' />";
            // Dropdown for the user to select the time slot
            echo "<label for='slot_time'>Select Time Slot:</label>";
            echo "<select name='slot_time' required>";
            echo "<option value='" . htmlspecialchars($row['slot_time']) . "'>" . htmlspecialchars($row['slot_time']) . "</option>"; // Add other options as needed
            echo "</select><br>";

            echo "<button type='submit'>Select this package</button>"; // Submit button for selection
            echo "</div><br>";
        }
        echo "</form>";
    } else {
        echo "<p>No packages available at the moment.</p>";
    }
} else {
    // For trainers
    echo "<h1>Welcome, Trainer " . htmlspecialchars($username) . "!</h1>";
}
?>