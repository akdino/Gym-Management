<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1571902943202-507ec2618e8f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwxMjA3fDB8MXxzZWFyY2h8Mnx8Zml0bmVzcyUyMGd5bXx8MHx8fHwxNjE5MDg4NDky&ixlib=rb-1.2.1&q=80&w=1080');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
            color: white;
        }

        .register-container {
            display: inline-block;
            background-color: rgba(0, 0, 0, 0.8); /* Semi-transparent black for readability */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            color: white;
            width: 300px;
        }

        input[type="text"], input[type="password"], input[type="email"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            color: black;
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
        }
    </style>
</head>
<body>

    <h1>Register</h1>

    <div class="register-container">
        <form action="register_handler.php" method="POST">
            <div>
                <input type="text" name="username" placeholder="Enter Username" required>
            </div>
            <div>
                <input type="email" name="email" placeholder="Enter Email" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Enter Password" required>
            </div>
            <div>
                <!-- Dropdown to select user type -->
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="user">User</option>
                    <option value="trainer">Trainer</option>
                </select>
            </div>
            <div>
                <input type="submit" value="Register">
            </div>
        </form>
    </div>

</body>
</html>
