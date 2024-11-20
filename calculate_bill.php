<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Summary</title>
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
            text-align: center;
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
    </style>
</head>
<body>
    <div class="container">
        <?php
        if (isset($_POST['username']) && isset($_POST['total_cost'])) {
            $username = htmlspecialchars($_POST['username']);
            $total_cost = htmlspecialchars($_POST['total_cost']);

            echo "<h1>Bill Summary</h1>";
            echo "<p>Name: " . htmlspecialchars($username) . "</p>";
            echo "<h2>Total Amount Due: $" . htmlspecialchars($total_cost) . "</h2>";
        } else {
            echo "<div class='error-message'>Bill information not available. Please try again.</div>";
        }
        ?>
        <div class="package-card">
            <h3>Thank you for choosing our service!</h3>
            <p>We appreciate your business and look forward to serving you again.</p>
        </div>
        <form action="login_handle.php" method="post" style="display: inline;">
    <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
    <input type="hidden" name="password" value="password_placeholder">
    <input type="hidden" name="login_type" value="user">
    <button type="submit">Dashboard</button>
    </div>
</body>
</html>
