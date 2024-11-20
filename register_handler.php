<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Handler</title>
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1571902943202-507ec2618e8f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwxMjA3fDB8MXxzZWFyY2h8Mnx8Zml0bmVzcyUyMGd5bXx8MHx8fHwxNjE5MDg4NDky&ixlib=rb-1.2.1&q=80&w=1080');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        
        .message-container {
            background-color: rgba(0, 0, 0, 0.8); /* Semi-transparent for readability */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 80%;
            max-width: 400px;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="message-container">
        <?php
        // Check if form data is submitted
        if (isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['role'])) {
            $username = htmlspecialchars($_POST['username']);
            $email = htmlspecialchars($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
            $role = htmlspecialchars($_POST['role']);

            // Database connection
            $conn = new mysqli('localhost', 'root', '', 'gym1'); // Update credentials if needed

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Insert into the correct table based on the role
            if ($role === 'user') {
                // Prepare SQL for inserting into the users table
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            } elseif ($role === 'trainer') {
                // Prepare SQL for inserting into the trainers table
                $stmt = $conn->prepare("INSERT INTO trainers (username, email, password) VALUES (?, ?, ?)");
            } else {
                echo "<h2>Invalid role selected.</h2>";
                exit();
            }

            // Bind parameters and execute
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                echo "<h2>Registration successful! You can now <a href='login.php'>log in</a>.</h2>";
            } else {
                echo "<h2>Registration failed. Please try again.</h2>";
            }

            // Close the connection
            $stmt->close();
            $conn->close();
        } else {
            echo "<h2>All fields are required.</h2>";
        }
        ?>
    </div>
</body>
</html>
