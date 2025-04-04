<?php
session_start();
if (!isset($_SESSION['login'])) {
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
<?php include 'superside.php'; ?>
<body>
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
      $counter = 1;
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $status = getNetworkStatus($row['ip_address']);
              $statusClass = ($status == 'Active') ? "status-active" : "status-inactive";
              $liveView = ($status == 'Active') ? "junction.html" : "junction.html";
              echo "<tr>
                      <td>{$counter}</td>
                      <td>{$row['junction']}</td>
                      <td>{$row['ip_address']}</td>
                      <td class='$statusClass'>{$status}</td>
                      <td><a href='$liveView?junction=" . urlencode($row['junction']) . "' target='_blank'>Live View</a></td>
                    </tr>";
              $counter++;
          }
      } else {
          echo "<tr><td colspan='5'>No junctions found</td></tr>";
      }
      ?>
    </table>
  </div>
</body>
</html>
