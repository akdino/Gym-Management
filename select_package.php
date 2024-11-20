<?php
session_start();

// Assuming the database connection is made here
$conn = new mysqli('localhost', 'root', '', 'gym1'); // Update credentials if needed

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['login_type'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$login_type = $_SESSION['login_type'];

echo "<h1>Welcome, " . htmlspecialchars($username) . "!</h1>";

// Add Logout Button
echo "<form action='login.php' method='post'>";
echo "<button type='submit' name='logout'>Logout</button>";
echo "</form>";

if ($login_type === 'user') {
    // Fetch packages and slots for users
    $packages_sql = "
        SELECT p.package_id, p.package_name, p.price, p.duration, p.description, ps.slot_time 
        FROM packages p 
        LEFT JOIN package_slots ps ON p.package_id = ps.package_id
    ";
    
    $packages_result = $conn->query($packages_sql);

    echo "<h2>Available Packages</h2>";

    if ($packages_result->num_rows > 0) {
        echo "<form action='store_selection.php' method='POST'>"; // Form for selection
        while ($row = $packages_result->fetch_assoc()) {
            echo "<div>";
            echo "<h3>" . htmlspecialchars($row['package_name']) . "</h3>";
            echo "<p>Price: $" . htmlspecialchars($row['price']) . "</p>";
            echo "<p>Duration: " . htmlspecialchars($row['duration']) . " days</p>";
            echo "<p>Description: " . htmlspecialchars($row['description']) . "</p>";
            echo "<p>Available Time Slot: " . htmlspecialchars($row['slot_time']) . "</p>";

            // Hidden input to store package_id
            echo "<input type='hidden' name='package_id[]' value='" . htmlspecialchars($row['package_id']) . "'>";
            // Dropdown for the user to select the slot time
            echo "<select name='slot_time[]' required>";
            echo "<option value='" . htmlspecialchars($row['slot_time']) . "'>" . htmlspecialchars($row['slot_time']) . "</option>";
            echo "</select>";

            echo "</div>";
        }
        echo "<input type='submit' value='Select Package'>";
        echo "</form>"; // End of form
    } else {
        echo "<p>No packages available at the moment.</p>";
    }
} else {
    // If logged in as trainer, just show a welcome message
    echo "<h1>Welcome, " . htmlspecialchars($username) . " (Trainer)!</h1>";
    echo "<p>You have logged in as a trainer. No package selections available.</p>";
}

// Close the database connection
$conn->close();
?>
