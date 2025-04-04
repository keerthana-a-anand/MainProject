<?php 
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}

// Database Connection
$conn = new mysqli("localhost", "root", "", "tcms");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$superUsername = $_SESSION['login'];
$successMsg = "";
$errorMsg = "";

// Fetch superuser details
$query = $conn->prepare("SELECT username, email, mobile, reg_date FROM superuser WHERE username = ?");
$query->bind_param("s", $superUsername);
$query->execute();
$query->bind_result($username, $email, $mobile, $regDate);
$query->fetch();
$query->close();

// Update Profile (on form submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newEmail = trim($_POST['email']);
    $newMobile = trim($_POST['mobile']);
    $emailError = "";
    $mobileError = "";

    // Validate email
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format!";
    }

    // Validate mobile number (10 digits, starts with 6-9)
    if (!preg_match("/^[6-9]\d{9}$/", $newMobile)) {
        $mobileError = "Invalid mobile number! Must be 10 digits.";
    }

    if (empty($emailError) && empty($mobileError)) {
        $updateQuery = $conn->prepare("UPDATE superuser SET email = ?, mobile = ?, reg_date = NOW() WHERE username = ?");
        $updateQuery->bind_param("sss", $newEmail, $newMobile, $superUsername);

        if ($updateQuery->execute()) {
            $successMsg = "✅ Profile updated successfully!";
            $email = $newEmail;
            $mobile = $newMobile;

            // Fetch updated registration date
            $timeQuery = $conn->prepare("SELECT DATE_FORMAT(reg_date, '%d %M %Y, %h:%i %p') FROM superuser WHERE username = ?");
            $timeQuery->bind_param("s", $superUsername);
            $timeQuery->execute();
            $timeQuery->bind_result($dbRegDate);
            $timeQuery->fetch();
            $timeQuery->close();

            $regDate = $dbRegDate;
        } else {
            $errorMsg = "❌ Error updating profile. Try again!";
        }
        $updateQuery->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Normaluser Profile</title>
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
    </style>
</head>

<?php include 'normalside.php'; ?>

<body>

    <div class="container">
        <h2>Normaluser Profile</h2>

        <?php if (!empty($successMsg)): ?>
            <p class="message success"><?php echo $successMsg; ?></p>
        <?php elseif (!empty($errorMsg)): ?>
            <p class="message error"><?php echo $errorMsg; ?></p>
        <?php endif; ?>

        <form action="normalprofile.php" method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <?php if (!empty($emailError)): ?>
                    <p class="message error"><?php echo $emailError; ?></p>
                <?php endif; ?>
            </div>

            <div class="input-group">
                <label>Mobile Number</label>
                <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>" required>
                <?php if (!empty($mobileError)): ?>
                    <p class="message error"><?php echo $mobileError; ?></p>
                <?php endif; ?>
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
