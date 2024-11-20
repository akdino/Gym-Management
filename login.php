<?php
session_start();
// Destroy the session and clear session data
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1571902943202-507ec2618e8f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwxMjA3fDB8MXxzZWFyY2h8Mnx8Zml0bmVzcyUyMGd5bXx8MHx8fHwxNjE5MDg4NDky&ixlib=rb-1.2.1&q=80&w=1080');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
            color: white;
        }

        h1 {
            color: white;
            font-size: 36px;
            text-shadow: 2px 2px 5px #000;
        }

        .login-container {
            display: inline-block;
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent black background */
            padding: 20px;
            border-radius: 10px;
        }

        input[type="text"], input[type="password"], select {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            font-size: 18px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <h1>Welcome to Cult Fit</h1>

    <div class="login-container">
        <form action="login_handle.php" method="POST">
            <div>
                <input type="text" name="username" placeholder="Enter Username" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Enter Password" required>
            </div>
            <div>
                <!-- Dropdown to select User or Trainer login -->
                <select name="login_type" required>
                    <option value="user">User</option>
                    <option value="trainer">Trainer</option>
                </select>
            </div>
            <div>
                <input type="submit" value="Login">
            </div>
        </form>

        <div>
            <p>New here? <a href="register.php">Register now</a></p>
        </div>
    </div>

</body>
</html>