<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['alogin']) || $_SESSION['alogin'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "tcms");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is provided in URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid User ID!'); window.location.href='managesuper.php';</script>";
    exit;
}

$id = $_GET['id'];

// Fetch user details
$sql = "SELECT * FROM superuser WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('User not found!'); window.location.href='managesuper.php';</script>";
    exit;
}

$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $user_type = $_POST['type']; // Ensure 'type' is used as per your database column

    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']); // Store password in MD5 format
        $sql = "UPDATE superuser SET username = ?, password = ?, type = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $user_type, $id);
    } else {
        $sql = "UPDATE superuser SET username = ?, type = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $user_type, $id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully!'); window.location.href='managesuper.php';</script>";
    } else {
        echo "<script>alert('Error updating user!');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(255, 255, 255);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: rgb(204, 204, 204);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 49, 39, 0.1);
            width: 400px;
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
        input, select {
            width: 95%;
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

<div class="form-container">
    <h2>Update User</h2>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label>New Password (leave blank to keep current password):</label>
        <input type="password" name="password">

        <label>User Type:</label>
        <select name="type" required>  
            <option value="superuser" <?php if ($user['type'] == "superuser") echo "selected"; ?>>Super User</option>
            <option value="user" <?php if ($user['type'] == "user") echo "selected"; ?>>Normal User</option>
        </select>

        <button type="submit" class="submit-btn">Update User</button>
    </form>
</div>

</body>
</html>
