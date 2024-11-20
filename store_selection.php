<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Selection Confirmation</title>
    <style>
        /* Background and overall styling */
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
        select, button, input[type="date"] {
            padding: 8px;
            margin-top: 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        button {
            background-color: #ffdd57;
            color: black;
        }

        /* Previous styles remain the same */
        .error-message {
            color: #ff4444;
            background-color: rgba(255, 68, 68, 0.1);
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        if (isset($_POST['user_id']) && isset($_POST['package_id']) && isset($_POST['slot_time']) && isset($_POST['start_date'])) {
            $user_id = intval($_POST['user_id']);
            $package_id = intval($_POST['package_id']);
            $slot_time = htmlspecialchars($_POST['slot_time']);
            $start_date = $_POST['start_date'];

            try {
                $conn = new mysqli('localhost', 'root', '', 'gym1');

                if ($conn->connect_error) {
                    throw new Exception("Connection failed: " . $conn->connect_error);
                }

                // Retrieve package duration
                $stmt = $conn->prepare("SELECT duration FROM packages WHERE package_id = ?");
                $stmt->bind_param("i", $package_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $package = $result->fetch_assoc();

                if ($package) {
                    $duration = intval($package['duration']);
                    $end_date = date('Y-m-d', strtotime($start_date . " + $duration days"));

                    // Try to insert the selection
                    $stmt = $conn->prepare("INSERT INTO user_selections (user_id, package_id, slot_time, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("iisss", $user_id, $package_id, $slot_time, $start_date, $end_date);
                    
                    if ($stmt->execute()) {
                        echo "<h1>Your Selection</h1>";
                        echo "<p>You have selected:</p>";
                        echo "<strong>Package ID:</strong> " . htmlspecialchars($package_id) . "<br>";
                        echo "<strong>Slot Time:</strong> " . htmlspecialchars($slot_time) . "<br>";
                        echo "<strong>Start Date:</strong> " . htmlspecialchars($start_date) . "<br>";
                        echo "<strong>End Date:</strong> " . htmlspecialchars($end_date) . "<br>";
                        echo "<p>Thank you for your selection!</p>";
                    }
                } else {
                    throw new Exception("Package not found.");
                }
            } catch (mysqli_sql_exception $e) {
                // Check if it's the duplicate package error from our trigger
                if (strpos($e->getMessage(), 'Package already selected by the user') !== false) {
                    header("Location: login_handle.php?error=" . urlencode("You have already selected this package. Please choose a different package."));
                    exit();
                } else {
                    header("Location: login_handle.php?error=" . urlencode("An error occurred while processing your request. Please try again."));
                    exit();
                }
            } catch (Exception $e) {
                header("Location: login_handle.php?error=" . urlencode($e->getMessage()));
                exit();
            } finally {
                if (isset($stmt)) {
                    $stmt->close();
                }
                if (isset($conn)) {
                    $conn->close();
                }
            }
        } else {
            header("Location: login.php");
            exit();
        }
        ?>
        <button onclick="window.location.href='login_handle.php'">Return to Package Selection</button>
    </div>
</body>
</html>
