<?php
// Start the session to check for logged-in user
session_start();

// Check if the user is logged in and is an admin
if(!isset($_SESSION['alogin']) || $_SESSION['alogin'] != 'admin') {
    // If not logged in as admin, redirect to the login page
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <script src="chart.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
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
    }

    .topnav a {
      float: left;
      display: block;
      color: white;
      text-align: center;
      padding: 14px 20px;
      text-decoration: none;
      font-size: 17px;
    }

    .topnav a:hover {
      background-color: #ddd;
      color: black;
    }

    .topnav .active {
      background-color: rgb(77, 190, 81);
      color: white;
    }

    /* Logout Button on Top Right */
    .topnav .logout-btn {
      float: right;
      background-color: red;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      font-size: 17px;
      border-radius: 5px;
      margin: 5px;
    }

    .topnav .logout-btn:hover {
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
      padding-top: 20px;
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
      background-color:rgb(59, 179, 113);
    }

    /* Main content area */
    .container {
      margin-left: 260px;
      padding: 20px;
      flex: 1;
      padding-top: 60px;
    }

    /* Dashboard Layout */
    .dashboard {
      display: flex;
      justify-content: space-between;
      grid-template-columns: 1fr 1fr;
      align-items: center;
      padding: 50px;
      width: 100%;
      margin: 0 auto;
    }

    /* Chart Container */
    .chart-container {
      display: flex;
      flex: 1;
      justify-content: center;
      align-items: center;
      background: #f9f9f9;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      max-width: 200px;
      max-height: 200px;
      margin: 0 20px;
    }

    .chart-container:last-child {
      margin-right: auto;
    }

    canvas {
      max-width: 100%;
      height: 100px;
    }

    /* Notification Bar Styling */
    .notification-bar {
      position: fixed;
      top: 120px;
      right: 20px;
      background-color: rgb(190, 97, 34);
      color: white;
      text-align: center;
      width: 300px;
      height: 200px;
      padding: 10px;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(22, 12, 77, 0.1);
      overflow: hidden;
      z-index: 999;
    }

    /* Scrolling Text */
    .notification-content {
      display: flex;
      flex-direction: column;
      gap: 10px;
      animation: slideText 10s linear infinite;
    }

    @keyframes slideText {
      0% { transform: translateY(0); }
      100% { transform: translateY(-100%); }
    }

    /* Pause scrolling on hover */
    .notification-bar:hover .notification-content {
      animation-play-state: paused;
    }
  </style>
</head>
<body>

  <!-- Topbar Section -->
  <div class="topnav">
      <h style="font-size: 30px;font-family: popines;">🖥️ TCMS</h>
      <a href="logout.php" class="logout-btn">Logout</a> <!-- Logout button added here -->
  </div>

  <!-- Sidebar Section -->
<div class="sidebar">
  <br><br>
    <h3 style="font-size: 30px; display: flex; align-items: center; gap: 1px;font-family: popines;">
        <span style="font-size: 30px;">🧑‍💼</span><u>Admin</u>
    </h3>

    <!-- Manage User (Collapsible Section) -->
    <details>
        <summary style="cursor: pointer; padding: 10px; color: white; font-size: 18px;"> Manage User</summary>
        <div class="content">
            <a href="addform.php">➕ Add User</a>
            <a href="managesuper.php">🛠️ User Management</a>
            <a href="normallog.php">📜 NormalUser Log</a>
            <a href="superuserlog.php">📄 SuperUser Log</a>
        </div>
    </details>

    <!-- Junctions (Collapsible Section) -->
    <details>
        <summary style="cursor: pointer; padding: 10px; color: white; font-size: 18px;"> Junctions</summary>
        <div class="content">
            <a href="addjunction.php">➕ Add Junction</a>
            <a href="view_junctions.php">📍 View Junction</a>
        </div>
    </details>

    <!-- Settings (Collapsible Section) -->
    <details>
        <summary style="cursor: pointer; padding: 10px; color: white; font-size: 18px;"> Settings</summary>
        <div class="content">
            <a href="profile.php">👤 Profile</a>
            <a href="changepassword.php">🔑 Change Password</a>
        </div>
    </details>

    <!-- View Reports -->
    <details>
        <summary style="cursor: pointer; padding: 10px; color: white; font-size: 18px;"> View Report</summary>
        <div class="content">
            <a href="event.php">🚦Junction Event</a>
        </div>
    </details>
</div>
<style>
    
    /* Styling for collapsed subfields */
    .content a {
        display: block;
        color: white;
        padding: 5px 20px;
        text-decoration: none;
        font-size: 16px;
    }

    /* Hover effect for links */
    .content a:hover {
        background-color: rgb(59, 179, 113);
    }
</style>


  <!-- Main Content Section -->
  <div class="container">
    <div class="dashboard-container">

      <!-- Notification Bar -->
      <div class="notification-bar">
        <div class="notification-content">
          <p>🔔 System Update: New features available!</p>
          <p>⚠️ Maintenance:Server down from 12:00 AM - 2:00 AM.</p>
          <p>🔑 Security Alert: Password reset required.</p>
        </div>
      </div>

     <!-- Dashboard Content -->
     <div class="dashboard">
        <div class="chart-container">
          <canvas id="myPieChart"></canvas>
        </div>
        <div class="chart-container">
          <canvas id="myPieChart1"></canvas>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Data and configurations for the first pie chart
    const chartData1 = {
      labels: ['Category A', 'Category B', 'Category C', 'Category D'],
      datasets: [{
        label: 'Categories',
        data: [30, 20, 25, 25],
        backgroundColor: ['#5ef354', '#FF00FF', '#FF0000', '#FFFF00'],
        hoverOffset: 4
      }]
    };
    const chartConfig1 = {
      type: 'pie',
      data: chartData1,
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' },
          title: { display: true, text: 'Distribution of Categories - Chart 1' }
        }
      }
    };

    // Data and configurations for the second pie chart
    const chartData2 = {
      labels: ['Category A', 'Category B', 'Category C', 'Category D'],
      datasets: [{
        label: 'Categories',
        data: [40, 20, 20, 20],
        backgroundColor: ['#5ef354', '#FF00FF', '#FF0000', '#FFFF00'],
        hoverOffset: 4
      }]
    };
    const chartConfig2 = {
      type: 'pie',
      data: chartData2,
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' },
          title: { display: true, text: 'Distribution of Categories - Chart 2' }
        }
      }
    };

    // Render both pie charts
    new Chart(document.getElementById('myPieChart'), chartConfig1);
    new Chart(document.getElementById('myPieChart1'), chartConfig2);
  </script>

</body>
</html>
