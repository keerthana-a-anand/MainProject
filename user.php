<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id'])==0)
    {   
header('location:index.php');
}
else{
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Normal User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .container {
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        .dashboard-section {
            margin: 20px 0;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .logout-btn {
            padding: 10px 20px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

    <header>
        <h1>Normal User Dashboard</h1>
    </header>

    <div class="container">
        <div class="dashboard-section">
            <h2>Welcome, User!</h2>
            <p>You are logged in as a normal user. Here you can view your profile and access other basic features.</p>
            
            <h3>User Tasks</h3>
            <ul>
                <li><a href="view_profile.php">View Profile</a></li>
                <li><a href="edit_profile.php">Edit Profile</a></li>
                <li><a href="change_password.php">Change Password</a></li>
                <!-- Add other normal user-specific tasks here -->
            </ul>
        </div>

        <div class="dashboard-section">
            <h3>User Controls</h3>
            <p>Perform actions like updating your profile or changing your password.</p>
            <a href="logout.php" class="logout-btn">Logout</a> <!-- Logout link -->
        </div>
    </div>

</body>
</html>
<?php } ?>