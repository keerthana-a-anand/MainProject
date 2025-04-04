<?php
session_start();
include("include/config.php");

// Function to get the real IP address
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Convert ::1 (IPv6 localhost) to local IP
    if ($ip == '::1') {
        $ip = '192.168.171.230';
    }
    return $ip;
}

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $user = $_POST['user'];

    if (empty($user)) {
        header("location: index.php?error=Please select a role");
        exit();
    }

    // Capture IP Address
    $userip = getUserIP();

    // Process login based on user role
    if ($user == 'admin') {
        $ret = mysqli_query($con, "SELECT * FROM admin WHERE BINARY username='$username' AND password='$password'");
        $num = mysqli_fetch_array($ret);
        if ($num) {
            $_SESSION['alogin'] = $username;
            $_SESSION['role'] = 'admin';
            $_SESSION['aid'] = $num['id'];
            header("location: ./admin.php");
            exit();
        } else {
            header("location: index.php?error=Invalid username or password");
            exit();
        }
    }

    if ($user == 'superuser') {
        $username = mysqli_real_escape_string($con, $username);
        $password = mysqli_real_escape_string($con, $password);

        $ret = mysqli_query($con, "SELECT * FROM superuser WHERE BINARY username='$username' AND password='$password' AND type='$user'");
        $num = mysqli_fetch_array($ret);
        if ($num) {
            $_SESSION['login'] = $username;
            $_SESSION['role'] = 'superuser';
            $_SESSION['id'] = $num['id'];

            // Log successful login
            mysqli_query($con, "INSERT INTO superlog (username, userip, loginTime, status) VALUES ('$username', '$userip', NOW(), 'Success')");

            header("location: ./superuser.php");
            exit();
        } else {
            // Log failed login attempt
            mysqli_query($con, "INSERT INTO superlog (username, userip, loginTime, status) VALUES ('$username', '$userip', NOW(), 'Failure')");

            header("location: index.php?error=Invalid username or password");
            exit();
        }
    }

    if ($user == 'user') {
        $username = mysqli_real_escape_string($con, $username);
        $password = mysqli_real_escape_string($con, $password);

        $ret = mysqli_query($con, "SELECT * FROM superuser WHERE BINARY username='$username' AND password='$password' AND type='$user'");
        $num = mysqli_fetch_array($ret);
        if ($num) {
            $_SESSION['login'] = $username;
            $_SESSION['role'] = 'normaluser';
            $_SESSION['id'] = $num['id'];

            // Log successful login for normal user
            mysqli_query($con, "INSERT INTO normaluserlog (username, userip, loginTime, status) VALUES ('$username', '$userip', NOW(), 'Success')");
            header("location: ./Normaluser.php");
            exit();
        } else {
            // Log failed login attempt
            mysqli_query($con, "INSERT INTO normaluserlog (username, userip, loginTime, status) VALUES ('$username', '$userip', NOW(), 'Failure')");

            header("location: index.php?error=Invalid username or password");
            exit();
        }
    }
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style1.css">
    <title>MultiUserLogin</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            background: url('traf1.webp') no-repeat center center/cover;
            font-family: Arial, sans-serif;
            padding-right: 50px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            width: 300px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group input {
            width: 90%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .input-group select {
            width: 96%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-btn {
            width: 96%;
            padding: 10px;
            background-color:rgb(255, 102, 0);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .login-btn:hover {
            background-color:rgb(27, 209, 97);
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <?php if (isset($_GET['error'])) { echo '<p class="error-message">'.htmlspecialchars($_GET['error']).'</p>'; } ?>
    <form method="post">
        <div class="input-group">
            <input class="form-control" id="username" name="username" type="text" placeholder="Username" required />
        </div>
        <div class="input-group">
            <input class="form-control" id="password" name="password" type="password" placeholder="Password" required />
        </div>
        <div class="input-group">
            <select id="user" name="user" required>
                <option value="">-select-</option>
                <option value="admin">Admin</option>
                <option value="superuser">Super User</option>
                <option value="user">Normal User</option>
            </select>
        </div>
        <button class="login-btn" type="submit" name="submit">Login</button>
    </form>
</div>

</body>
</html>
    