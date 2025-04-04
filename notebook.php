





superlog
   

<?php session_start();
error_reporting(0);
// Database Connection
include('includes/config.php');
// Validating Session
if(strlen($_SESSION['aid']) == 0) { 
    header('location:index.php');
} else {
    // Code For Deleting the subadmin
    if(isset($_GET['action']) && $_GET['action'] == 'delete') {
        $subadminid = intval($_GET['said']);
        $query = mysqli_query($con, "DELETE FROM tbladmin WHERE ID='$subadminid'");
        if($query) {
            echo "<script>alert('Sub admin record deleted successfully.');</script>";
            echo "<script type='text/javascript'> document.location = 'manage-subadmins.php'; </script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Super Users Logs</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .container { max-width: 900px; margin: auto; }
        .header { text-align: center; padding: 10px 0; }
        .breadcrumb { list-style: none; padding: 0; }
        .breadcrumb li { display: inline; margin-right: 5px; }
        .breadcrumb a { text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Super Users</h1>
            <ul class="breadcrumb">

            </ul>
        </div>
        
        <h2>Super User Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Sl No</th>
                    <th>Username</th>
                    <th>User IP</th>
                    <th>Login Time</th>
                    <th>Logout Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $query = mysqli_query($con, "SELECT * FROM superlog ORDER BY id DESC");
                $cnt = 1;
                while($result = mysqli_fetch_array($query)) { ?>
                    <tr>
                        <td><?php echo $cnt; ?></td>
                        <td><?php echo $result['username']; ?></td>
                        <td><?php echo $result['userip']; ?></td>
                        <td><?php echo $result['loginTime']; ?></td>
                        <td><?php echo $result['logoutTime']; ?></td>
                        <td><?php echo $result['status']; ?></td>
                    </tr>
                <?php 
                    $cnt++; 
                } 
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php } ?>





index


<?php
session_start();
include("include/config.php");

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);  
    $user = $_POST['user'];

    if (empty($user)) {
        header("location: index.php?error=Please select a role");
        exit();
    }

    // Capture IP Address
    $host = $_SERVER['HTTP_HOST'];
    $userip = $_SERVER['REMOTE_ADDR'];

    // Process login based on user role
    if ($user == 'admin') {
        $ret = mysqli_query($con, "SELECT * FROM admin WHERE username='$username' and password='$password'");
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
        $ret = mysqli_query($con, "SELECT * FROM superuser WHERE username='$username' and type='$user'");
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
        $ret = mysqli_query($con, "SELECT * FROM superuser WHERE username='$username' and type='$user'");
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
            mysqli_query($con, "INSERT INTO Normaluserlog (username, userip, loginTime, status) VALUES ('$username', '$userip', NOW(), 'Failure')");
            
            header("location: index.php?error=Invalid username or password");
            exit();
        }
    }
}

// Logout Functionality for Superuser and Normal User
if(isset($_SESSION['login'])) {
    $username = $_SESSION['login'];
    $role = $_SESSION['role'];
    
    if ($role == 'superuser') {
        mysqli_query($con, "UPDATE superlog SET logoutTime=NOW() WHERE username='$username' AND logoutTime IS NULL ORDER BY id DESC LIMIT 1");
    } elseif ($role == 'user') {
        mysqli_query($con, "UPDATE normaluserlog SET logoutTime=NOW() WHERE username='$username' AND logoutTime IS NULL ORDER BY id DESC LIMIT 1");
    }
    
    session_destroy();
    header("location: index.php");
    exit();
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




view junction


<?php
session_start();
if (!isset($_SESSION['alogin']) || $_SESSION['alogin'] != 'admin') {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "tcms");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, junction_name, location, status FROM junction";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Junctions</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
    }

    /* Topbar Styling */
    .topnav {
        background-color: rgb(15, 170, 20);
        overflow: hidden;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        padding: 8px;
        margin-left: -10px;
    }

    .topnav a {
        float: right;
        background-color: red;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        font-size: 17px;
        border-radius: 5px;
        margin: 8px;
    }

    .topnav a:hover {
        background-color: #ddd;
        color: black;
    }

    /* Sidebar Styling */
    .sidebar {
        height: 100%;
        width: 230px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: rgb(3, 3, 3);
        color: white;
        padding-top: 60px;
        z-index: 999;
        padding-left: 20px;
    }

    .sidebar a {
        display: block;
        color: white;
        padding: 10px;
        text-decoration: none;
        font-size: 18px;
    }

    .sidebar a:hover {
        background-color: rgb(50, 95, 70);
    }

    /* Styling for collapsed subfields */
    .content a {
        display: block;
        color: white;
        padding: 5px 20px;
        text-decoration: none;
        font-size: 16px;
    }

    .content a:hover {
        background-color: rgb(59, 179, 113);
    }

    .container {
      margin-left: 260px;
      padding: 20px;
      padding-top: 60px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: white;
    }

    table, th, td {
      border: 1px solid black;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #4CAF50;
      color: white;
    }
    
    .status-active {
      color: green;
      font-weight: bold;
    }

    .status-inactive {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <!-- Topbar -->
  <div class="topnav">
      <h style="font-size: 30px;font-family: popines;">🖥️ TCMS</h>
      <a href="logout.php" class="logout-btn">Logout</a>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
      <h3 style="font-size: 30px; display: flex; align-items: center; gap: 1px;font-family: popines;">
          <span style="font-size: 30px;">🧑‍💼</span><u>Admin</u>
      </h3>

      <details>
          <summary style="cursor: pointer; padding: 10px; color: white; font-size: 18px;">Manage User</summary>
          <div class="content">
              <a href="addform.php">Add User</a>
              <a href="managesuper.php">User Management</a>
              <a href="normallog.php">NormalUser Log</a>
              <a href="superuserlog.php">SuperUser Log</a>
          </div>
      </details>

      <details>
          <summary style="cursor: pointer; padding: 10px; color: white; font-size: 18px;">Junctions</summary>
          <div class="content">
          <a href="addjunction.php">Add Junction</a>
              <a href="view_junctions.php">View Junction</a>
          </div>
      </details>

      <a href="#profile">View Reports</a>
      <a href="#settings">Settings</a>
  </div>

  <div class="container">
    <h2>Junctions List</h2>
    
    <table>
      <tr>
        <th>ID</th>
        <th>Junction Name</th>
        <th>Status</th>
        <th>Live View</th>
      </tr>
      
      <?php
      $count = 0;
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $statusClass = ($row['status'] == 'Active') ? "status-active" : "status-inactive";
              echo "<tr>
                      <td>{$row['id']}</td>
                      <td>{$row['junction_name']}</td>
                      <td class='$statusClass'>{$row['status']}</td>";

              if ($count == 0) {
                  echo "<td><a href='junction.html?id={$row['id']}' target='_blank'>Live View</a></td>";
              } else {
                  echo "<td>-</td>";
              }

              echo "</tr>";
              $count++;
          }
      } else {
          echo "<tr><td colspan='5'>No junctions found</td></tr>";
      }
      ?>
    </table>
  </div>
</body>
</html>





<?php
session_start();
include("include/config.php");

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);  
    $user = $_POST['user'];

    if (empty($user)) {
        header("location: index.php?error=Please select a role");
        exit();
    }

    // Capture IP Address
    //$userip = $_SERVER['REMOTE_ADDR'];

    
// Capture and validate IPv4 Address
$userip = $_SERVER['REMOTE_ADDR'];
if (filter_var($userip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    $userip = gethostbyname($userip);
}

    // Process login based on user role
    if ($user == 'admin') {
        $ret = mysqli_query($con, "SELECT * FROM admin WHERE username='$username' and password='$password'");
        $num = mysqli_fetch_array($ret);
        if ($num) {
            $_SESSION['alogin'] = $username;
            $_SESSION['role'] = 'admin'; 
            $_SESSION['aid'] = $num['id'];
            header("location: ./admin_dashboard.php");
            exit();
        } else {
            header("location: index.php?error=Invalid username or password");
            exit();
        }
    }

    if ($user == 'superuser') {
        $ret = mysqli_query($con, "SELECT * FROM superuser WHERE username='$username' and type='$user'");
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
        $ret = mysqli_query($con, "SELECT * FROM superuser WHERE username='$username' and type='$user' ");
        $num = mysqli_fetch_array($ret);
        if ($num) {
            $_SESSION['login'] = $username;
            $_SESSION['role'] = 'user'; 
            $_SESSION['id'] = $num['id'];
            
            // Log successful login for normal user
            mysqli_query($con, "INSERT INTO normalog (username, userip, loginTime, status) VALUES ('$username', '$userip', NOW(), 'Success')");
                      header("location: ./user.php");
            exit();
        } else {
            // Log failed login attempt
            mysqli_query($con, "INSERT INTO normalog (username, userip, loginTime, status) VALUES ('$username', '$userip', NOW(), 'Failure')");
            
            header("location: index.php?error=Invalid username or password");
            exit();
        }
    }
}

// Logout Functionality for Superuser and Normal User
if (isset($_SESSION['login'])) {
    $username = $_SESSION['login'];
    $role = $_SESSION['role'];

    // Ensure logoutTime is updated before destroying the session
    if ($role == 'superuser') {
        $updateQuery = "UPDATE superlog SET logoutTime = NOW() WHERE username = '$username' AND logoutTime IS NULL ORDER BY id DESC LIMIT 1";
    } elseif ($role == 'user') {
        $updateQuery = "UPDATE normalog SET logoutTime = NOW() WHERE username = '$username' AND logoutTime IS NULL ORDER BY id DESC LIMIT 1";
    }

    // Execute the query and check if logout time is updated
    if (isset($updateQuery)) {
        if (mysqli_query($con, $updateQuery)) {
            if (mysqli_affected_rows($con) > 0) {
                // Logout time successfully updated
            } else {
                die("Error: No row updated. Check if logoutTime is NULL in your database.");
            }
        } else {
            die("MySQL Error: " . mysqli_error($con));
        }
    }

    // Destroy session AFTER updating logout time
    session_destroy();
    header("location: index.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="page.css">
    <title>MultiUserLogin</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('traff.jpg') no-repeat center center/cover;
            font-family: Arial, sans-serif;
        }

        .login-container {
            text-align: right;
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

        .login-button {
            padding: 60px 120px;
            font-size: 60px;
            top: 130;
            right: 120;
            color: rgb(255, 255, 255);
            background: url('traff.jpg') no-repeat center center/cover;
            border: none;
            border-radius: 30px;
        }

        .login-button:hover {
            background: linear-gradient(45deg, #25fc7f00, #11cb4f00);
            transform: scale(1.05);
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.3);
        }
        .error-message {
            color: red;
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <?php if (isset($_GET['error'])) { echo '<p class="error-message">'.htmlspecialchars($_GET['error']).'</p>'; } ?>
    <form method="post">
        <div class="input-group">
            <label for="username"></label>
            <input class="form-control" id="username" name="username" type="text" placeholder="Username" required />
        </div>
        <div class="input-group">
            <label for="password"></label>
            <input class="form-control" id="password" name="password" type="password" placeholder="Password" required />
        </div>
        <div class="input-group">
            <label for="user"></label>
            <select id="user" name="user" required>
                <option value="">-select-</option>
                <option value="admin">Admin</option>
                <option value="superuser">superuser</option>
                <option value="user">user</option>
            </select>
        </div>
        <button class="login-btn" type="submit" name="submit">Login</button>
    </form>
</div>

</body>
</html>






<?php
session_start();
include("include/config.php");

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);  
    $user = $_POST['user'];

    if (empty($user)) {
        header("location: index.php?error=Please select a role");
        exit();
    }

    // Capture IP Address
    //$userip = $_SERVER['REMOTE_ADDR'];

    
// Capture and validate IPv4 Address
$userip = $_SERVER['REMOTE_ADDR'];
if (filter_var($userip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    $userip = gethostbyname($userip);
}

    // Process login based on user role
    if ($user == 'admin') {
        $ret = mysqli_query($con, "SELECT * FROM admin WHERE username='$username' and password='$password'");
        $num = mysqli_fetch_array($ret);
        if ($num) {
            $_SESSION['alogin'] = $username;
            $_SESSION['role'] = 'admin'; 
            $_SESSION['aid'] = $num['id'];
            header("location: ./admin_dashboard.php");
            exit();
        } else {
            header("location: index.php?error=Invalid username or password");
            exit();
        }
    }

    if ($user == 'superuser') {
        $ret = mysqli_query($con, "SELECT * FROM superuser WHERE username='$username' and type='$user'");
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
        $ret = mysqli_query($con, "SELECT * FROM superuser WHERE username='$username' and type='$user' ");
        $num = mysqli_fetch_array($ret);
        if ($num) {
            $_SESSION['login'] = $username;
            $_SESSION['role'] = 'user'; 
            $_SESSION['id'] = $num['id'];
            
            // Log successful login for normal user
            mysqli_query($con, "INSERT INTO normalog (username, userip, loginTime, status) VALUES ('$username', '$userip', NOW(), 'Success')");
                      header("location: ./user.php");
            exit();
        } else {
            // Log failed login attempt
            mysqli_query($con, "INSERT INTO normalog (username, userip, loginTime, status) VALUES ('$username', '$userip', NOW(), 'Failure')");
            
            header("location: index.php?error=Invalid username or password");
            exit();
        }
    }
}

// Logout Functionality for Superuser and Normal User
if (isset($_SESSION['login'])) {
    $username = $_SESSION['login'];
    $role = $_SESSION['role'];

    // Ensure logoutTime is updated before destroying the session
    if ($role == 'superuser') {
        $updateQuery = "UPDATE superlog SET logoutTime = NOW() WHERE username = '$username' AND logoutTime IS NULL ORDER BY id DESC LIMIT 1";
    } elseif ($role == 'user') {
        $updateQuery = "UPDATE normalog SET logoutTime = NOW() WHERE username = '$username' AND logoutTime IS NULL ORDER BY id DESC LIMIT 1";
    }

    // Execute the query and check if logout time is updated
    if (isset($updateQuery)) {
        if (mysqli_query($con, $updateQuery)) {
            if (mysqli_affected_rows($con) > 0) {
                // Logout time successfully updated
            } else {
                die("Error: No row updated. Check if logoutTime is NULL in your database.");
            }
        } else {
            die("MySQL Error: " . mysqli_error($con));
        }
    }

    // Destroy session AFTER updating logout time
    session_destroy();
    header("location: index.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="page.css">
    <title>MultiUserLogin</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('traff.jpg') no-repeat center center/cover;
            font-family: Arial, sans-serif;
        }

        .login-container {
            text-align: right;
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

        .login-button {
            padding: 60px 120px;
            font-size: 60px;
            top: 130;
            right: 120;
            color: rgb(255, 255, 255);
            background: url('traff.jpg') no-repeat center center/cover;
            border: none;
            border-radius: 30px;
        }

        .login-button:hover {
            background: linear-gradient(45deg, #25fc7f00, #11cb4f00);
            transform: scale(1.05);
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.3);
        }
        .error-message {
            color: red;
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <?php if (isset($_GET['error'])) { echo '<p class="error-message">'.htmlspecialchars($_GET['error']).'</p>'; } ?>
    <form method="post">
        <div class="input-group">
            <label for="username"></label>
            <input class="form-control" id="username" name="username" type="text" placeholder="Username" required />
        </div>
        <div class="input-group">
            <label for="password"></label>
            <input class="form-control" id="password" name="password" type="password" placeholder="Password" required />
        </div>
        <div class="input-group">
            <label for="user"></label>
            <select id="user" name="user" required>
                <option value="">-select-</option>
                <option value="admin">Admin</option>
                <option value="superuser">superuser</option>
                <option value="user">user</option>
            </select>
        </div>
        <button class="login-btn" type="submit" name="submit">Login</button>
    </form>
</div>

</body>
</html>





index.php  

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

// Logout Functionality for Superuser and Normal User
if (isset($_SESSION['login'])) {
    $username = $_SESSION['login'];
    $role = $_SESSION['role'];

    // Ensure logoutTime is updated before destroying the session
    if ($role == 'superuser') {
        $updateQuery = "UPDATE superlog SET logoutTime = NOW() WHERE username = '$username' AND logoutTime IS NULL ORDER BY id DESC LIMIT 1";
    } elseif ($role == 'user') {
        $updateQuery = "UPDATE normaluserlog SET logoutTime = NOW() WHERE username = '$username' AND logoutTime IS NULL ORDER BY id DESC LIMIT 1";
    }

    // Execute the query and check if logout time is updated
    if (isset($updateQuery)) {
        if (mysqli_query($con, $updateQuery)) {
            if (mysqli_affected_rows($con) > 0) {
                // Logout time successfully updated
            } else {
                die("Error: No row updated. Check if logoutTime is NULL in your database.");
            }
        } else {
            die("MySQL Error: " . mysqli_error($con));
        }
    }
    // Destroy session AFTER updating logout time
    session_destroy();
    header("location: index.php");
    exit();
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


view_junction .php(with live view active)



<?php
session_start();
if (!isset($_SESSION['alogin']) || $_SESSION['alogin'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Database Connection
$conn = new mysqli("localhost", "root", "", "tcms");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch junction details from 'addjunction' table
$sql = "SELECT junction, ip_address FROM addjunction ORDER BY id ASC";
$result = $conn->query($sql);

// Function to check network status using ping command
function getNetworkStatus($ip) {
    $ping_result = shell_exec("ping -c 1 " . escapeshellarg($ip)); 
    return (strpos($ping_result, '1 received') !== false) ? 'Active' : 'Inactive';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Junctions</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
    }

    /* Topbar Styling */
    .topnav {
        background-color: rgb(15, 170, 20);
        overflow: hidden;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        padding: 8px;
        margin-left: -10px;
    }

    .topnav a {
        float: right;
        background-color: red;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        font-size: 17px;
        border-radius: 5px;
        margin: 8px;
    }

    .topnav a:hover {
        background-color: #ddd;
        color: black;
    }

    /* Sidebar Styling */
    .sidebar {
        height: 100%;
        width: 230px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: rgb(3, 3, 3);
        color: white;
        padding-top: 60px;
        z-index: 999;
        padding-left: 20px;
    }

    .sidebar a {
        display: block;
        color: white;
        padding: 10px;
        text-decoration: none;
        font-size: 18px;
    }

    .sidebar a:hover {
        background-color: rgb(50, 95, 70);
    }

    /* Styling for collapsed subfields */
    .content a {
        display: block;
        color: white;
        padding: 5px 20px;
        text-decoration: none;
        font-size: 16px;
    }

    .content a:hover {
        background-color: rgb(59, 179, 113);
    }

    .container {
      margin-left: 260px;
      padding: 20px;
      padding-top: 60px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: white;
    }

    table, th, td {
      border: 1px solid black;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #4CAF50;
      color: white;
    }
    
    .status-active {
      color: green;
      font-weight: bold;
    }

    .status-inactive {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <!-- Topbar -->
  <div class="topnav">
      <h style="font-size: 30px;font-family: popines;">🖥️ TCMS</h>
      <a href="logout.php" class="logout-btn">Logout</a>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
      <h3 style="font-size: 30px; display: flex; align-items: center; gap: 1px;font-family: popines;">
          <span style="font-size: 30px;">🧑‍💼</span><u>Admin</u>
      </h3>

      <details>
          <summary style="cursor: pointer; padding: 10px; color: white; font-size: 18px;">Manage User</summary>
          <div class="content">
              <a href="addform.php">Add User</a>
              <a href="managesuper.php">User Management</a>
              <a href="normallog.php">NormalUser Log</a>
              <a href="superuserlog.php">SuperUser Log</a>
          </div>
      </details>

      <details>
          <summary style="cursor: pointer; padding: 10px; color: white; font-size: 18px;">Junctions</summary>
          <div class="content">
          <a href="addjunction.php">Add Junction</a>
              <a href="view_junctions.php">View Junction</a>
          </div>
      </details>

      <a href="#profile">View Reports</a>
      <a href="#settings">Settings</a>
  </div>

  <div class="container">
    <h2>Junctions List</h2>
    
    <table>
      <tr>
        <th>#</th>
        <th>Junction Name</th>
        <th>IP Address</th>
        <th>Network Status</th>
        <th>Live View</th>
      </tr>
      
      <?php
      $counter = 1; // Initialize counter for sequential numbering

      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $status = getNetworkStatus($row['ip_address']); // Get network status using ping
              $statusClass = ($status == 'Active') ? "status-active" : "status-inactive";

              echo "<tr>
                      <td>{$counter}</td>
                      <td>{$row['junction']}</td>
                      <td>{$row['ip_address']}</td>
                      <td class='$statusClass'>{$status}</td>
                      <td><a href='junction.html?id={$counter}' target='_blank'>Live View</a></td>
                    </tr>";
              $counter++; // Increment counter
          }
      } else {
          echo "<tr><td colspan='5'>No junctions found</td></tr>";
      }
      ?>
    </table>
  </div>
</body>
</html>


junction.html 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traffic Control Dashboard</title>
    <link rel="stylesheet" href="style3.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .dashboard {
            display: flex;
            gap: 20px;
        }
        .camera-feed {
            flex: 2;
            background: #fff;
            padding: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }
        .camera-feed img {
            width: 100%;
            transition: transform 1s ease-in-out, opacity 1s ease-in-out;
            position: absolute;
            top: 0;
            left: 0;
        }
        .controls {
            flex: 1;
            background: #9bd4df;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .signal-mode, .hurry-call {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            padding: 10px;
            background: #eaeaea;
            border-radius: 5px;
        }
        .signal-mode label, .hurry-call label {
            font-weight: bold;
        }
        .signal-mode button, .hurry-call button {
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
        .auto { background-color: green; color: white; }
        .flash { background-color: red; color: white; }
        .hurry-call button {
            margin: 5px;
            background-color: green;
            color: white;
            border: none;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .time-bar {
            display: flex;
            width: 100%;
            height: 20px;
            border: 1px solid #ccc;
            overflow: hidden;
            margin-top: 5px;
        }
        .time-utilized {
            background-color: #b33e57;
            text-align: center;
            line-height: 20px;
        }
        .time-remaining {
            background-color: #1ecc52;
            text-align: center;
            line-height: 20px;
        }
    </style>
</head>
<body>
    <h2>Live Junction View</h2>
    <a href="view_junctions.php" style="position: absolute; top: 20px; right: 20px; background-color: #1098b9; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">🔙 Back</a>
    <div class="dashboard">
        <div class="camera-feed">
            <img id="traffic-image" src="r1.jpg" class="active" alt="Traffic Camera">
        </div>
        <div class="controls">
            <h3>Junction Details</h3>
            <p><strong>Junction Name: <input type="text"></strong></p>
            <p><strong>Junction Mode: <input type="text"></strong></p>
            <p><strong>Current Stage: <span id="current-stage"> 1</span></strong></p>
            <p><strong>Current Timings: <span id="current-timing">10</span> sec</strong></p>
            <table>
                <tr><th>Stage</th><th>Allocated Time (sec)</th><th>Utilized Time (Sec)</th></tr>
                <tr data-stage="1" data-utilized="0"><td>1</td><td>10</td><td><div class="time-bar"><div class="time-utilized" style="width: 0%"></div><div class="time-remaining" style="width: 100%">10 sec</div></div></td></tr>
                <tr data-stage="2" data-utilized="0"><td>2</td><td>8</td><td><div class="time-bar"><div class="time-utilized" style="width: 0%"></div><div class="time-remaining" style="width: 100%">10 sec</div></div></td></tr>
                <tr data-stage="3" data-utilized="0"><td>3</td><td>5</td><td><div class="time-bar"><div class="time-utilized" style="width: 0%"></div><div class="time-remaining" style="width: 100%">10 sec</div></div></td></tr>
                <tr data-stage="4" data-utilized="0"><td>4</td><td>3</td><td><div class="time-bar"><div class="time-utilized" style="width: 0%"></div><div class="time-remaining" style="width: 100%">10 sec</div></div></td></tr>
            </table>
            <h3>Remote Administration</h3>
            <label>Signal Mode:</label>
            <button class="mode-btn">Auto</button>
            <button class="mode-btn">Flash</button>
            <br /><br />
            <label>Hurry Call:</label>
            <button class="hurry-btn">1</button>
            <button class="hurry-btn">2</button>
            <button class="hurry-btn">3</button>
            <button class="hurry-btn">4</button>
            <button class="hurry-btn">5</button>
            </div>
        </div>
    </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let currentStage = 1;
            const stageImages = ["r1.jpg", "r2.jpg", "r3.jpg", "r4.jpg"];
            const totalStages = stageImages.length;

            function updateStage() {
                let currentRow = document.querySelector(`tr[data-stage='${currentStage}']`);
                let utilizedTime = parseInt(currentRow.getAttribute("data-utilized")) || 0;
                let allocatedTime = parseInt(currentRow.children[1].innerText);
                let utilizedDiv = currentRow.querySelector(".time-utilized");
                let remainingDiv = currentRow.querySelector(".time-remaining");
                
                if (utilizedTime >= allocatedTime) {
                    currentStage = (currentStage % totalStages) + 1;
                    if (currentStage === 1) {
                        document.querySelectorAll("tr[data-stage]").forEach(row => {
                            row.setAttribute("data-utilized", "0");
                            row.querySelector(".time-utilized").style.width = "0%";
                            row.querySelector(".time-remaining").style.width = "100%";
                            row.querySelector(".time-remaining").innerText = row.children[1].innerText + " sec";
                        });
                    }
                    document.getElementById("traffic-image").src = stageImages[currentStage - 1];
                } else {
                    utilizedTime++;
                    currentRow.setAttribute("data-utilized", utilizedTime);
                    let percentUsed = (utilizedTime / allocatedTime) * 100;
                    utilizedDiv.style.width = percentUsed + "%";
                    remainingDiv.style.width = (100 - percentUsed) + "%";
                    remainingDiv.innerText = (allocatedTime - utilizedTime) + " sec";
                }
                document.getElementById("current-stage").innerText = currentStage;
                document.getElementById("current-timing").innerText = allocatedTime;
            }
            setInterval(updateStage, 1000);
        });
    </script>
</body>
</html>



junction.html 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traffic Control Dashboard</title>
    <link rel="stylesheet" href="style3.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .dashboard {
            display: flex;
            gap: 20px;
        }
        .camera-feed {
            flex: 2;
            background: #fff;
            padding: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }
        .camera-feed img {
            width: 100%;
            transition: transform 1s ease-in-out, opacity 1s ease-in-out;
            position: absolute;
            top: 0;
            left: 0;
        }
        .controls {
            flex: 1;
            background: #9bd4df;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .signal-mode, .hurry-call {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            padding: 10px;
            background: #eaeaea;
            border-radius: 5px;
        }
        .signal-mode label, .hurry-call label {
            font-weight: bold;
        }
        .signal-mode button, .hurry-call button {
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
        .auto { background-color: green; color: white; }
        .flash { background-color: red; color: white; }
        .hurry-call button {
            margin: 5px;
            background-color: green;
            color: white;
            border: none;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .time-bar {
            display: flex;
            width: 100%;
            height: 20px;
            border: 1px solid #ccc;
            overflow: hidden;
            margin-top: 5px;
        }
        .time-utilized {
            background-color: #b33e57;
            text-align: center;
            line-height: 20px;
        }
        .time-remaining {
            background-color: #1ecc52;
            text-align: center;
            line-height: 20px;
        }
    </style>
</head>
<body>
    <h2>Live Junction View</h2>
    <a href="view_junctions.php" style="position: absolute; top: 20px; right: 20px; background-color: #1098b9; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">🔙 Back</a>
    <div class="dashboard">
        <div class="camera-feed">
            <img id="traffic-image" src="r1.jpg" class="active" alt="Traffic Camera">
        </div>
        <div class="controls">
            <h3>Junction Details</h3>
            <p><strong>Junction Name: <input type="text" id="junction-name" readonly></strong></p>
            <p><strong>Junction Mode: <input type="text"></strong></p>
            <p><strong>Current Stage: <span id="current-stage">1</span></strong></p>
            <p><strong>Current Timings: <span id="current-timing">10</span> sec</strong></p>
            <table>
                <tr><th>Stage</th><th>Allocated Time (sec)</th><th>Utilized Time (Sec)</th></tr>
                <tr data-stage="1" data-utilized="0"><td>1</td><td>10</td><td><div class="time-bar"><div class="time-utilized" style="width: 0%"></div><div class="time-remaining" style="width: 100%">10 sec</div></div></td></tr>
                <tr data-stage="2" data-utilized="0"><td>2</td><td>8</td><td><div class="time-bar"><div class="time-utilized" style="width: 0%"></div><div class="time-remaining" style="width: 100%">8 sec</div></div></td></tr>
                <tr data-stage="3" data-utilized="0"><td>3</td><td>5</td><td><div class="time-bar"><div class="time-utilized" style="width: 0%"></div><div class="time-remaining" style="width: 100%">5 sec</div></div></td></tr>
                <tr data-stage="4" data-utilized="0"><td>4</td><td>3</td><td><div class="time-bar"><div class="time-utilized" style="width: 0%"></div><div class="time-remaining" style="width: 100%">3 sec</div></div></td></tr>
            </table>
            <h3>Remote Administration</h3>
            <label>Signal Mode:</label>
            <button class="mode-btn">Auto</button>
            <button class="mode-btn">Flash</button>
            <br /><br />
            <label>Hurry Call:</label>
            <button class="hurry-btn">1</button>
            <button class="hurry-btn">2</button>
            <button class="hurry-btn">3</button>
            <button class="hurry-btn">4</button>
            <button class="hurry-btn">5</button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Get junction name from URL
            const urlParams = new URLSearchParams(window.location.search);
            const junctionName = urlParams.get("junction");

            // Display junction name in the input field
            if (junctionName) {
                document.getElementById("junction-name").value = junctionName;
            }

            let currentStage = 1;
            const stageImages = ["r1.jpg", "r2.jpg", "r3.jpg", "r4.jpg"];
            const totalStages = stageImages.length;

            function updateStage() {
                let currentRow = document.querySelector(`tr[data-stage='${currentStage}']`);
                let utilizedTime = parseInt(currentRow.getAttribute("data-utilized")) || 0;
                let allocatedTime = parseInt(currentRow.children[1].innerText);
                let utilizedDiv = currentRow.querySelector(".time-utilized");
                let remainingDiv = currentRow.querySelector(".time-remaining");
                
                if (utilizedTime >= allocatedTime) {
                    currentStage = (currentStage % totalStages) + 1;
                    if (currentStage === 1) {
                        document.querySelectorAll("tr[data-stage]").forEach(row => {
                            row.setAttribute("data-utilized", "0");
                            row.querySelector(".time-utilized").style.width = "0%";
                            row.querySelector(".time-remaining").style.width = "100%";
                            row.querySelector(".time-remaining").innerText = row.children[1].innerText + " sec";
                        });
                    }
                    document.getElementById("traffic-image").src = stageImages[currentStage - 1];
                } else {
                    utilizedTime++;
                    currentRow.setAttribute("data-utilized", utilizedTime);
                    let percentUsed = (utilizedTime / allocatedTime) * 100;
                    utilizedDiv.style.width = percentUsed + "%";
                    remainingDiv.style.width = (100 - percentUsed) + "%";
                    remainingDiv.innerText = (allocatedTime - utilizedTime) + " sec";
                }
                document.getElementById("current-stage").innerText = currentStage;
                document.getElementById("current-timing").innerText = allocatedTime;
            }
            setInterval(updateStage, 1000);
        });
    </script>
</body>
</html>
 another 
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traffic Control Dashboard</title>
    <link rel="stylesheet" href="style3.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .dashboard {
            display: flex;
            gap: 20px;
        }
        .camera-feed {
            flex: 2;
            background: #fff;
            padding: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }
        .camera-feed img {
            width: 100%;
            transition: transform 1s ease-in-out, opacity 1s ease-in-out;
            position: absolute;
            top: 0;
            left: 0;
        }
        .controls {
            flex: 1;
            background: #9bd4df;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .signal-mode, .hurry-call {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            padding: 10px;
            background: #eaeaea;
            border-radius: 5px;
        }
        .signal-mode label, .hurry-call label {
            font-weight: bold;
        }
        .signal-mode button, .hurry-call button {
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
        .auto { background-color: green; color: white; }
        .flash { background-color: red; color: white; }
        .hurry-call button {
            margin: 5px;
            background-color: green;
            color: white;
            border: none;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .time-bar {
            display: flex;
            width: 100%;
            height: 20px;
            border: 1px solid #ccc;
            overflow: hidden;
            margin-top: 5px;
        }
        .time-utilized {
            background-color: #b33e57;
            text-align: center;
            line-height: 20px;
        }
        .time-remaining {
            background-color: #1ecc52;
            text-align: center;
            line-height: 20px;
        }
    </style>
</head>
<body>
    <h2>Live Junction View</h2>
    <a href="view_junctions.php" style="position: absolute; top: 20px; right: 20px; background-color: #1098b9; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">🔙 Back</a>
    <div class="dashboard">
        <div class="camera-feed">
            <img id="traffic-image" src="r1.jpg" class="active" alt="Traffic Camera">
        </div>
        <div class="controls">
            <h3>Junction Details</h3>
            <p><strong>Junction Name: <input type="text" id="junction-name" readonly></strong></p>
            <p><strong>Junction Mode: <input type="text"></strong></p>
            <p><strong>Current Stage: <span id="current-stage">1</span></strong></p>
            <p><strong>Current Timings: <span id="current-timing">10</span> sec</strong></p>
            <table>
                <tr><th>Stage</th><th>Allocated Time (sec)</th><th>Utilized Time (Sec)</th></tr>
                <tr data-stage="1" data-utilized="0"><td>1</td><td>10</td><td><div class="time-bar"><div class="time-utilized" style="width: 0%"></div><div class="time-remaining" style="width: 100%">10 sec</div></div></td></tr>
                <tr data-stage="2" data-utilized="0"><td>2</td><td>8</td><td><div class="time-bar"><div class="time-utilized" style="width: 0%"></div><div class="time-remaining" style="width: 100%">8 sec</div></div></td></tr>
                <tr data-stage="3" data-utilized="0"><td>3</td><td>5</td><td><div class="time-bar"><div class="time-utilized" style="width: 0%"></div><div class="time-remaining" style="width: 100%">5 sec</div></div></td></tr>
                <tr data-stage="4" data-utilized="0"><td>4</td><td>3</td><td><div class="time-bar"><div class="time-utilized" style="width: 0%"></div><div class="time-remaining" style="width: 100%">3 sec</div></div></td></tr>
            </table>
            <h3>Remote Administration</h3>
            <label>Signal Mode:</label>
            <button class="mode-btn">Auto</button>
            <button class="mode-btn">Flash</button>
            <br /><br />
            <label>Hurry Call:</label>
            <button class="hurry-btn">1</button>
            <button class="hurry-btn">2</button>
            <button class="hurry-btn">3</button>
            <button class="hurry-btn">4</button>
            <button class="hurry-btn">5</button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Get junction name from URL
            const urlParams = new URLSearchParams(window.location.search);
            const junctionName = urlParams.get("junction");

            // Display junction name in the input field
            if (junctionName) {
                document.getElementById("junction-name").value = junctionName;
            }

            let currentStage = 1;
            const stageImages = ["r1.jpg", "r2.jpg", "r3.jpg", "r4.jpg"];
            const totalStages = stageImages.length;

            function updateStage() {
                let currentRow = document.querySelector(`tr[data-stage='${currentStage}']`);
                let utilizedTime = parseInt(currentRow.getAttribute("data-utilized")) || 0;
                let allocatedTime = parseInt(currentRow.children[1].innerText);
                let utilizedDiv = currentRow.querySelector(".time-utilized");
                let remainingDiv = currentRow.querySelector(".time-remaining");
                
                if (utilizedTime >= allocatedTime) {
                    currentStage = (currentStage % totalStages) + 1;
                    if (currentStage === 1) {
                        document.querySelectorAll("tr[data-stage]").forEach(row => {
                            row.setAttribute("data-utilized", "0");
                            row.querySelector(".time-utilized").style.width = "0%";
                            row.querySelector(".time-remaining").style.width = "100%";
                            row.querySelector(".time-remaining").innerText = row.children[1].innerText + " sec";
                        });
                    }
                    document.getElementById("traffic-image").src = stageImages[currentStage - 1];
                } else {
                    utilizedTime++;
                    currentRow.setAttribute("data-utilized", utilizedTime);
                    let percentUsed = (utilizedTime / allocatedTime) * 100;
                    utilizedDiv.style.width = percentUsed + "%";
                    remainingDiv.style.width = (100 - percentUsed) + "%";
                    remainingDiv.innerText = (allocatedTime - utilizedTime) + " sec";
                }
                document.getElementById("current-stage").innerText = currentStage;
                document.getElementById("current-timing").innerText = allocatedTime;
            }
            setInterval(updateStage, 1000);
        });
    </script>
</body>
</html>



........

<script>
        document.addEventListener("DOMContentLoaded", function () {
            let currentStage = 1;
            let autoModeEnabled = false;
            let flashModeEnabled = false;
            const stageImages = ["r1.jpg", "r2.jpg", "r3.jpg", "r4.jpg"];
            const totalStages = stageImages.length;
            let autoInterval = null;
            let flashInterval = null;
        
            function updateStage() {
                if (!autoModeEnabled) return;
        
                let currentRow = document.querySelector(tr[data-stage='${currentStage}']);
                if (!currentRow) return;
        
                let utilizedTime = parseInt(currentRow.getAttribute("data-utilized")) || 0;
                let allocatedTime = parseInt(currentRow.children[1].innerText) || 0;
        
                let utilizedDiv = currentRow.querySelector(".time-utilized");
                let remainingDiv = currentRow.querySelector(".time-remaining");
        
                if (utilizedTime >= allocatedTime) {
                    currentStage = (currentStage % totalStages) + 1;
        
                    if (currentStage === 1) {
                        document.querySelectorAll("tr[data-stage]").forEach(row => {
                            row.setAttribute("data-utilized", "0");
                            row.querySelector(".time-utilized").style.width = "0%";
                            row.querySelector(".time-remaining").style.width = "100%";
                            row.querySelector(".time-remaining").innerText = row.children[1].innerText + " sec";
                        });
                    }
        
                    document.getElementById("traffic-image").src = stageImages[currentStage - 1];
                } else {
                    utilizedTime++;
                    currentRow.setAttribute("data-utilized", utilizedTime);
                    
                    let percentUsed = (utilizedTime / allocatedTime) * 100;
                    utilizedDiv.style.width = percentUsed + "%";
                    remainingDiv.style.width = (100 - percentUsed) + "%";
                    remainingDiv.innerText = (allocatedTime - utilizedTime) + " sec";
                }
        
                document.getElementById("current-stage").innerText = currentStage;
                document.getElementById("current-timing").innerText = allocatedTime;
            }
        
            document.getElementById("auto-mode").addEventListener("click", function () {
                alert("Switched to Auto Mode");
                autoModeEnabled = true;
                flashModeEnabled = false;
                clearInterval(flashInterval);
                if (autoInterval) clearInterval(autoInterval);
                autoInterval = setInterval(updateStage, 1000);
            });
        
            document.getElementById("flash-mode").addEventListener("click", function () {
                alert("Switched to Flash Mode");
                autoModeEnabled = false;
                flashModeEnabled = true;
                clearInterval(autoInterval);
                if (flashInterval) clearInterval(flashInterval);
                let flashImages = ["white.jpg", "yellow.jpg"];
                let index = 0;
                flashInterval = setInterval(() => {
                    document.getElementById("traffic-image").src = flashImages[index];
                    index = (index + 1) % flashImages.length;
                }, 1000);
            });
        
            document.querySelectorAll(".hurry-btn").forEach(button => {
                button.addEventListener("click", function () {
                    let stage = this.getAttribute("data-stage");
                    alert(Hurry Call Activated for Stage ${stage});
                    autoModeEnabled = false;
                    flashModeEnabled = false;
                    clearInterval(autoInterval);
                    clearInterval(flashInterval);
                    document.getElementById("traffic-image").src = stageImages[stage - 1];
                    document.getElementById("current-stage").innerText = "0";
                    document.getElementById("current-timing").innerText = "0";
                });
            });
        
            const urlParams = new URLSearchParams(window.location.search);
            const junctionName = urlParams.get("junction_name");
        
            if (junctionName) {
                const junctionInput = document.getElementById("junction-name");
                if (junctionInput) {
                    junctionInput.value = junctionName;
                }
            }
        });
        </script>
   
