<?php
session_start();
include("include/config.php");

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];  // No hashing here
    $user = $_POST['user'];

    if (empty($user)) {
        header("location: index.php?error=Please select a role");
        exit();
    }

    // Function to verify login for different user roles
    function verifyLogin($con, $table, $username, $password, $redirectPage, $sessionName) {
        $stmt = $con->prepare("SELECT * FROM $table WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $num = $result->fetch_assoc();

        if ($num && password_verify($password, $num['password'])) {
            $_SESSION[$sessionName] = $username;
            $_SESSION['role'] = $table; // Role is table name
            $_SESSION['id'] = $num['id'];
            header("location: $redirectPage");
            exit();
        } else {
            header("location: index.php?error=Invalid username or password");
            exit();
        }
    }

    // Role-based login
    if ($user == 'admin') {
        verifyLogin($con, "admin", $username, $password, "./admin.php", "alogin");
    } elseif ($user == 'superuser') {
        verifyLogin($con, "superuser", $username, $password, "./superuser.php", "login");
    } elseif ($user == 'user') {
        verifyLogin($con, "user", $username, $password, "./user.php", "login");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        .input-group input, .input-group select {
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
                <option value="user">User</option>
            </select>
        </div>
        <button class="login-btn" type="submit" name="submit">Login</button>
    </form>
</div>

</body>
</html>
