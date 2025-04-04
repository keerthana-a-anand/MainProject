<?php
session_start();
if (!isset($_SESSION['alogin'])) {
    header("Location: index.php");
    exit;
}

// Database Connection
$conn = new mysqli("localhost", "root", "", "tcms");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMsg = "";
$errorMsg = "";

// Handle password change request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $adminUsername = $_SESSION['alogin'];

    // Fetch admin's current password (MD5 encrypted) from the database
    $query = $conn->prepare("SELECT password FROM admin WHERE username = ?");
    $query->bind_param("s", $adminUsername);
    $query->execute();
    $query->bind_result($storedPassword);
    $query->fetch();
    $query->close();

    if (!$storedPassword) {
        $errorMsg = "User not found!";
    } elseif (md5($currentPassword) !== $storedPassword) {
        $errorMsg = "Current password is incorrect!";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMsg = "New password and confirm password do not match!";
    } else {
        // Encrypt new password using MD5
        $hashedPassword = md5($newPassword);
        $updateQuery = $conn->prepare("UPDATE admin SET password = ? WHERE username = ?");
        $updateQuery->bind_param("ss", $hashedPassword, $adminUsername);
        
        if ($updateQuery->execute()) {
            $successMsg = "Password changed successfully!";
        } else {
            $errorMsg = "Error updating password. Try again!";
        }
        $updateQuery->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
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

        .container {
            background: rgb(241, 241, 241);
            padding: 20px;
            border-radius: 8px;
            width: 600px;
            box-shadow: 0 0 10px rgba(31, 29, 29, 0.91);
            margin-left: 230px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input {
            width: 92%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn {
            background-color: rgb(197, 77, 77);
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #218838;
        }

        .message {
            margin-top: 10px;
            font-size: 14px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            display: none;
            transition: opacity 1s ease-out;
        }

        .success {
            color: green;
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .error {
            color: red;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .password-toggle {
            display: flex;
            align-items: center;
            margin-top: 5px;
        }

        .password-toggle input {
            width: auto;
            margin-right: 5px;
        }

        .password-match {
            color: red;
            font-size: 12px;
            display: none;
        }
    </style>
    <script>
        function showMessage(type, message) {
            var msgDiv = document.getElementById("msgDiv");
            msgDiv.innerHTML = message;
            msgDiv.className = "message " + type;
            msgDiv.style.display = "block";
            
            // Hide message after 3 seconds
            setTimeout(function() {
                msgDiv.style.opacity = "0";
                setTimeout(function() {
                    msgDiv.style.display = "none";
                    msgDiv.style.opacity = "1"; // Reset opacity for next message
                }, 1000);
            }, 3000);
        }

        function togglePasswordVisibility() {
            var passwords = document.querySelectorAll(".password-input");
            passwords.forEach(password => {
                if (password.type === "password") {
                    password.type = "text";
                } else {
                    password.type = "password";
                }
            });
        }

        function checkPasswordMatch() {
            var newPassword = document.getElementById("new_password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            var matchMessage = document.getElementById("matchMessage");

            if (newPassword !== confirmPassword) {
                matchMessage.style.display = "block";
            } else {
                matchMessage.style.display = "none";
            }
        }
    </script>
</head>
<?php include 'sidebar.php'; ?>
<body onload="<?php echo ($successMsg || $errorMsg) ? "showMessage('" . ($successMsg ? 'success' : 'error') . "', '" . ($successMsg ? $successMsg : $errorMsg) . "')" : ''; ?>">

    <div class="container">
        <h2> Change Password</h2>

        <div id="msgDiv"></div>

        <form action="changepassword.php" method="POST">
            <div class="input-group">
                <label>Current Password</label>
                <input type="password" name="current_password" class="password-input" required>
            </div>

            <div class="input-group">
                <label>New Password</label>
                <input type="password" id="new_password" name="new_password" class="password-input" required onkeyup="checkPasswordMatch()">
            </div>

            <div class="input-group">
                <label>Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="password-input" required onkeyup="checkPasswordMatch()">
                <span id="matchMessage" class="password-match">Passwords do not match!</span>
            </div>

            <div class="password-toggle">
                <input type="checkbox" onclick="togglePasswordVisibility()"> Show Password
            </div>

            <button type="submit" class="btn">Update Password</button>
        </form>
    </div>

</body>
</html>
