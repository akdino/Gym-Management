profile.php
<?php
session_start(); // Start the session

// Database connection
$conn = new mysqli('localhost', 'root', '', 'gym1');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ""; // Variable to store the error message

// Check if user_id is set in the URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // SQL query to fetch user details
    $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "<p>User not found.</p>";
    }

    // Handling username change request
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_username']) && isset($_POST['confirm_password'])) {
        $new_username = $_POST['new_username'];
        $confirm_password = $_POST['confirm_password'];

        // Check if the entered password matches the stored password
        if (password_verify($confirm_password, $user['password'])) {
            // Update username in the database
            $update_stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_username, $user_id);
            if ($update_stmt->execute()) {
                echo "<p>Username updated successfully!</p>";
                // Refresh the user data after the update
                $user['username'] = $new_username;
            } else {
                echo "<p>Error updating username. Please try again.</p>";
            }
        } else {
            $error_message = "Incorrect password. Username not updated.";
        }
    }

    // Handling password change request
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password']) && isset($_POST['confirm_password']) && isset($_POST['current_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if the entered current password matches the stored password
        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                // Update password in the database
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $user_id);
                if ($update_stmt->execute()) {
                    echo "<p>Password updated successfully!</p>";
                    // Refresh the user data after the update
                    $user['password'] = $hashed_password;
                } else {
                    echo "<p>Error updating password. Please try again.</p>";
                }
            } else {
                $error_message = "New passwords do not match. Please try again.";
            }
        } else {
            $error_message = "Incorrect current password. Password not updated.";
        }
    }

} else {
    echo "<p>No user ID provided.</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            text-align: center;
        }
        h1, h2, h3 {
            color: #ffdd57;
        }
        .profile-info p {
            font-size: 18px;
            margin: 10px 0;
        }
        .btn-container {
            margin-top: 20px;
        }
        button {
            padding: 8px;
            margin: 5px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            background-color: #ffdd57;
            color: black;
        }
        input[type="text"], input[type="password"] {
            padding: 8px;
            margin: 10px 0;
            width: 100%;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .change-username-form, .change-password-form {
            display: none;
            margin-top: 20px;
        }
        .error-message {
            color: red;
            font-size: 18px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Profile</h1>

        <?php if ($error_message): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($user)): ?>
            <div class="profile-info">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

                <!-- Change Username Button -->
                <button id="changeUsernameBtn">Change Username</button>
                <button id="changePasswordBtn">Change Password</button>
            </div>

            <!-- Change Username Form -->
            <div id="changeUsernameForm" class="change-username-form">
                <h2>Change Username</h2>
                <form method="POST">
                    <label for="new_username">New Username:</label>
                    <input type="text" id="new_username" name="new_username" required>

                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>

                    <button type="submit">Change Username</button>
                </form>
            </div>

            <!-- Change Password Form -->
            <div id="changePasswordForm" class="change-password-form">
                <h2>Change Password</h2>
                <form method="POST">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>

                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>

                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>

                    <button type="submit">Change Password</button>
                </form>
            </div>

            <div class="btn-container">
                <!-- Link back to login or homepage -->
                <form action="login_handle.php" method="post" style="display: inline;">
    <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
    <input type="hidden" name="password" value="password_placeholder">
    <input type="hidden" name="login_type" value="user">
    <button type="submit">Dashboard</button>
</form>


                <form action="login.php" method="get">
                    <button type="submit">Back to Login</button>
                </form>

            </div>

        <?php else: ?>
            <p>User not found.</p>
        <?php endif; ?>
    </div>

    <script>
        // Toggle the display of the Change Username form
        const changeUsernameBtn = document.getElementById('changeUsernameBtn');
        const changePasswordBtn = document.getElementById('changePasswordBtn');
        const changeUsernameForm = document.getElementById('changeUsernameForm');
        const changePasswordForm = document.getElementById('changePasswordForm');

        changeUsernameBtn.addEventListener('click', function() {
            // Toggle visibility of the form
            if (changeUsernameForm.style.display === 'none' || changeUsernameForm.style.display === '') {
                changeUsernameForm.style.display = 'block';
            } else {
                changeUsernameForm.style.display = 'none';
            }
        });

        changePasswordBtn.addEventListener('click', function(){ 
            // Toggle visibility of the form
            if (changePasswordForm.style.display === 'none' || changePasswordForm.style.display === '') {
                changePasswordForm.style.display = 'block';
            } else {
                changePasswordForm.style.display = 'none';
            }
        })
    </script>
</body>
</html>
