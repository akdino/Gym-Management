<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'gym1');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch selected packages for the logged-in user
$sql = "
    SELECT p.package_name, p.price, p.duration, us.slot_time, us.start_date
    FROM user_selections us
    JOIN packages p ON us.package_id = p.package_id
    WHERE us.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Selected Packages</title>
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
            max-width: 800px;
            margin: auto;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            text-align: center;
        }
        h1 {
            color: #ffdd57;
            font-size: 2em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #ffdd57;
            color: black;
        }
        
        tr:hover {
            background-color: #e0e0e0;
        }
        button {
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #ffdd57;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: black;
        }
        button:hover {
            background-color: #e0c20b;
        }
        .no-packages {
            margin-top: 20px;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Selected Packages for <?php echo htmlspecialchars($username); ?></h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Package Name</th>
                    <th>Price</th>
                    <th>Duration (days)</th>
                    <th>Slot Time</th>
                    <th>Start Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['package_name']); ?></td>
                        <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['duration']); ?></td>
                        <td><?php echo htmlspecialchars($row['slot_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-packages">No packages selected yet.</div>
    <?php endif; ?>

    
</div>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
