<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'gym1');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Packages</title>
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1571902943202-507ec2618e8f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwxMjA3fDB8MXxzZWFyY2h8Mnx8Zml0bmVzcyUyMGd5bXx8MHx8fHwxNjE5MDg4NDky&ixlib=rb-1.2.1&q=80&w=1080');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 8px;
        }
        h1, h2, h3 {
            color: #ffdd57;
        }
        .error-message {
            color: #ff4444;
            background-color: rgba(255, 68, 68, 0.1);
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .warning-message {
            color: #ffdd57;
            background-color: rgba(255, 221, 87, 0.1);
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .package-card {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
        }
        select, button, input[type="date"], input[type="text"], input[type="password"] {
            padding: 8px;
            margin-top: 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        button {
            background-color: #ffdd57;
            color: black;
            margin-right: 10px;
        }
        .user-actions {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
        }
        .user-actions form {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Display any error messages passed through URL parameters
        if (isset($_GET['error'])) {
            $error = htmlspecialchars($_GET['error']);
            echo "<div class='error-message'>$error</div>";
        }

        // Check for existing session first
        if (isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['login_type'])) {
            $username = $_SESSION['username'];
            $login_type = $_SESSION['login_type'];
            $user_id = $_SESSION['user_id'];

            echo "<h1>Welcome, " . htmlspecialchars($username) . "!</h1>";

            if ($login_type === 'user') {
                // User-specific buttons at the top
                echo "<div class='user-actions'>";
                
                // Profile button
                echo "<form action='profile.php' method='get'>";
                echo "<input type='hidden' name='user_id' value='" . htmlspecialchars($user_id) . "'>";
                echo "<button type='submit'>Profile</button>";
                echo "</form>";

                // Selected Packages button
                echo "<form action='view_selected_packages.php' method='post'>";
                echo "<input type='hidden' name='user_id' value='" . htmlspecialchars($user_id) . "'>";
                echo "<button type='submit'>View Selected Packages</button>";
                echo "</form>";

                // Generate Bill button
                echo "<form action='calculate_bill.php' method='post'>";
                echo "<input type='hidden' name='user_id' value='" . htmlspecialchars($user_id) . "'>";
                echo "<input type='hidden' name='username' value='" . htmlspecialchars($username) . "'>";

                // Fetch total cost using SQL function
                $bill_stmt = $conn->prepare("SELECT calculate_total_bill(?) AS total_cost");
                $bill_stmt->bind_param("i", $user_id);
                $bill_stmt->execute();
                $bill_result = $bill_stmt->get_result();
                $total_cost = $bill_result->fetch_assoc()['total_cost'];
                echo "<input type='hidden' name='total_cost' value='" . htmlspecialchars($total_cost) . "'>";

                echo "<button type='submit'>Generate Bill</button>";
                echo "</form>";
                echo "</div>";

                // Display available packages
                $packages_sql = "
                    SELECT p.package_id, p.package_name, p.price, p.duration, p.description, 
                           GROUP_CONCAT(DISTINCT e.exercise_name SEPARATOR ', ') AS exercises,
                           GROUP_CONCAT(DISTINCT ps.slot_time SEPARATOR ', ') AS slot_times
                    FROM packages p 
                    LEFT JOIN package_exercises pe ON p.package_id = pe.package_id
                    LEFT JOIN exercises e ON pe.exercise_id = e.exercise_id
                    LEFT JOIN package_slots ps ON p.package_id = ps.package_id
                    GROUP BY p.package_id";
                $packages_result = $conn->query($packages_sql);

                echo "<h2>Available Packages</h2>";
                if ($packages_result && $packages_result->num_rows > 0) {
                    while ($row = $packages_result->fetch_assoc()) {
                        echo "<div class='package-card'>";
                        echo "<h3>" . htmlspecialchars($row['package_name']) . "</h3>";
                        echo "<p>Price: $" . htmlspecialchars($row['price']) . "</p>";
                        echo "<p>Duration: " . htmlspecialchars($row['duration']) . " days</p>";
                        echo "<p>Description: " . htmlspecialchars($row['description']) . "</p>";
                        echo "<p>Exercises: " . htmlspecialchars($row['exercises']) . "</p>";

                        // Check if user has already selected this package
                        $package_check_sql = "SELECT COUNT(*) as count FROM user_selections WHERE user_id = ? AND package_id = ?";
                        $check_stmt = $conn->prepare($package_check_sql);
                        $check_stmt->bind_param("ii", $user_id, $row['package_id']);
                        $check_stmt->execute();
                        $check_result = $check_stmt->get_result();
                        $has_package = $check_result->fetch_assoc()['count'] > 0;

                        if ($has_package) {
                            echo "<div class='warning-message'>You have already selected this package.</div>";
                        } else {
                            echo "<form action='store_selection.php' method='post'>";
                            echo "<input type='hidden' name='package_id' value='" . htmlspecialchars($row['package_id']) . "'>";
                            echo "<input type='hidden' name='user_id' value='" . htmlspecialchars($user_id) . "'>";
                            
                            echo "<label for='slot_time_" . $row['package_id'] . "'>Select a time slot:</label>";
                            echo "<select name='slot_time' id='slot_time_" . $row['package_id'] . "'>";
                            $slots = explode(', ', $row['slot_times']);
                            foreach ($slots as $slot) {
                                echo "<option value='" . htmlspecialchars($slot) . "'>" . htmlspecialchars($slot) . "</option>";
                            }
                            echo "</select><br>";

                            echo "<label for='start_date_" . $row['package_id'] . "'>Select a start date:</label>";
                            echo "<input type='date' name='start_date' id='start_date_" . $row['package_id'] . "' required><br>";

                            echo "<button type='submit'>Select Package</button>";
                            echo "</form>";
                        }
                        echo "</div>";
                    }
                } else {
                    echo "<p>No packages available at the moment.</p>";
                }
            } elseif ($login_type === 'trainer') {
                // Retrieve trainer details from the database
                $stmt = $conn->prepare("SELECT * FROM trainers WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $trainer_result = $stmt->get_result();
                
                // Fetch trainer ID from the logged-in user
                $trainer_id = $user_id;
                
                // SQL query to fetch users assigned to the trainer and their selected packages
                $assigned_users_sql = "
                    SELECT u.username, p.package_name, p.price, us.start_date, us.end_date
                    FROM users u
                    JOIN user_selections us ON u.id = us.user_id
                    JOIN packages p ON us.package_id = p.package_id
                    JOIN user_trainer ut ON u.id = ut.user_id
                    WHERE ut.trainer_id = ?";
                
                // Prepare and execute the query
                $stmt = $conn->prepare($assigned_users_sql);
                $stmt->bind_param("i", $trainer_id);
                $stmt->execute();
                $assigned_users_result = $stmt->get_result();
                
                echo "<h2>Your Assigned Users and Their Selected Packages:</h2>";
                
                // Check if there are any results
                if ($assigned_users_result->num_rows > 0) {
                    while ($row = $assigned_users_result->fetch_assoc()) {
                        // Display user details and their selected package
                        echo "<div class='package-card'>";
                        echo "<h3>User: " . htmlspecialchars($row['username']) . "</h3>";
                        echo "Package: " . htmlspecialchars($row['package_name']) . "<br>";
                        echo "Price: $" . htmlspecialchars($row['price']) . "<br>";
                        echo "Start Date: " . htmlspecialchars($row['start_date']) . "<br>";
                        echo "End Date: " . htmlspecialchars($row['end_date']) . "<br>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No users are currently assigned to you.</p>";
                }
            }

            // Logout button
            echo "<form action='logout.php' method='post' style='margin-top: 20px;'>";
            echo "<button type='submit'>Logout</button>";
            echo "</form>";
        } 
        // If no existing session, proceed with login verification
        elseif (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['login_type'])) {
            $username = htmlspecialchars($_POST['username']);
            $password = htmlspecialchars($_POST['password']);
            $login_type = $_POST['login_type'];

            // Select table based on login type
            $table = ($login_type === 'trainer') ? 'trainers' : 'users';

            // Prepare SQL query
            $stmt = $conn->prepare("SELECT * FROM $table WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $username;
                    $_SESSION['login_type'] = $login_type;

                    // Reload the page to use session-based rendering
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    echo "<div class='error-message'>Invalid username or password.</div>";
                }
            } else {
                echo "<div class='error-message'>User not found.</div>";
            }
        } else {
            // Default login form if no session exists
            echo "<h2>Login to your Gym Account</h2>";
            echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post'>";
            echo "<input type='text' name='username' placeholder='Username' required><br>";
            echo "<input type='password' name='password' placeholder='Password' required><br>";
            echo "<select name='login_type' required>";
            echo "<option value='user'>User</option>";
            echo "<option value='trainer'>Trainer</option>";
            echo "</select><br>";
            echo "<button type='submit'>Login</button>";
            echo "</form>";
        }

        // Close database connection
        $conn->close();
        ?>
    </div>
</body>
</html>