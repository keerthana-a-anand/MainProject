<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['alogin']) || $_SESSION['alogin'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "tcms");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST['username'];
    $password = md5($_POST['password']); // Encrypt password using MD5
    $user_type = $_POST['user_type'];
    
    // Insert into `superuser` table
    $stmt = $conn->prepare("INSERT INTO superuser (username, password, type) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $user_type);

    if ($stmt->execute()) {
        echo "<script>alert('User added successfully!'); window.location.href='managesuper.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: rgb(241, 241, 241);
            padding: 20px;
            border-radius: 8px;
            width: 600px;
            box-shadow: 0 0 10px rgba(31, 29, 29, 0.91);
            margin-left: 230px; /* Adjust this value to move further right */
            
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input {
            width: 97%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .submit-btn {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background-color: rgb(167, 40, 40);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="form-container">
    <h2>Add User</h2>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>User Type:</label>
        <select name="user_type" required>
            <option value="" disabled selected>Select User Type</option>
            <option value="superuser">Super User</option>
            <option value="user">Normal User</option>
        </select>

        <button type="submit" class="submit-btn">Add User</button>
    </form>
</div>

</body>
</html>
