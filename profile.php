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

$adminUsername = $_SESSION['alogin'];
$successMsg = "";
$errorMsg = "";

// Fetch admin details
$query = $conn->prepare("SELECT username, email, mobile, reg_date FROM admin WHERE username = ?");
$query->bind_param("s", $adminUsername);
$query->execute();
$query->bind_result($username, $email, $mobile, $regDate);
$query->fetch();
$query->close();

// Update Profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newEmail = trim($_POST['email']);
    $newMobile = trim($_POST['mobile']);
    $emailError = "";
    $mobileError = "";

    // Server-side validation
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format!";
    }
    if (!preg_match("/^[6-9]\d{9}$/", $newMobile)) {
        $mobileError = "Invalid mobile number! Must be 10 digits.";
    }

    if (empty($emailError) && empty($mobileError)) {
        // Update Query with Last Modified Date using NOW()
        $updateQuery = $conn->prepare("UPDATE admin SET email = ?, mobile = ?, reg_date = NOW() WHERE username = ?");
        $updateQuery->bind_param("sss", $newEmail, $newMobile, $adminUsername);
    
        if ($updateQuery->execute()) {
            $successMsg = "Profile updated successfully!";
            $email = $newEmail;
            $mobile = $newMobile;
    
            // Fetch the updated registration date using NOW()
            $timeQuery = $conn->prepare("SELECT DATE_FORMAT(NOW(), '%d %M %Y, %h:%i %p')");
            $timeQuery->execute();
            $timeQuery->bind_result($dbRegDate);
            $timeQuery->fetch();
            $timeQuery->close();
    
            // Store formatted date for display
            $regDate = $dbRegDate;
        } else {
            $errorMsg = "Error updating profile. Try again!";
        }
        $updateQuery->close();
    }
    
    $conn->close();
}    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
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
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        input[readonly] {
            background-color: #e9ecef;
            cursor: not-allowed;
        }

        .btn {
            background-color: rgb(167, 40, 40);
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
            text-align: center;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .error-message {
            color: red;
            font-size: 12px;
            display: none;
        }
    </style>

    <script>
        function validateEmail() {
            var email = document.getElementById("email").value;
            var emailError = document.getElementById("emailError");
            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (!emailPattern.test(email)) {
                emailError.style.display = "block";
                return false;
            } else {
                emailError.style.display = "none";
                return true;
            }
        }

        function validateMobile() {
            var mobile = document.getElementById("mobile").value;
            var mobileError = document.getElementById("mobileError");
            var mobilePattern = /^[6-9]\d{9}$/;

            if (!mobilePattern.test(mobile)) {
                mobileError.style.display = "block";
                return false;
            } else {
                mobileError.style.display = "none";
                return true;
            }
        }

        function validateForm() {
            var isEmailValid = validateEmail();
            var isMobileValid = validateMobile();
            return isEmailValid && isMobileValid;
        }
    </script>
</head>

<?php include 'sidebar.php'; ?>

<body>

    <div class="container">
        <h2> Admin Profile</h2>

        <?php if ($successMsg): ?>
            <p class="message success"><?php echo $successMsg; ?></p>
        <?php elseif ($errorMsg): ?>
            <p class="message error"><?php echo $errorMsg; ?></p>
        <?php endif; ?>

        <form action="profile.php" method="POST" onsubmit="return validateForm()">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" onkeyup="validateEmail()" required>
                <span id="emailError" class="error-message">Invalid email format!</span>
            </div>

            <div class="input-group">
                <label>Mobile Number</label>
                <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>" onkeyup="validateMobile()" required>
                <span id="mobileError" class="error-message">Mobile number must be 10 digits and start with 6-9!</span>
            </div>

            <div class="input-group">
                <label>Last Modified</label>
                <input type="text" name="reg_date" value="<?php echo htmlspecialchars($regDate); ?>" readonly>
            </div>

            <button type="submit" class="btn">Update Profile</button>
        </form>
    </div>

</body>
</html>
