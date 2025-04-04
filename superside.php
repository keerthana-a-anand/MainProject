
<!-- Topbar Section -->
<div class="topnav">
    <h style="font-size: 30px;font-family: popines;">🖥️ TCMS</h>
    <a href="logout.php" class="logout-btn">Logout</a> <!-- Logout button -->
</div>

<!-- Sidebar Section -->
<div class="sidebar">
    <h3 style="font-size: 30px; display: flex; align-items: center; gap: 1px;font-family: popines;">
        <span style="font-size: 30px;">🧑‍💼</span><u>Super User</u>
    </h3>

    
   

    <!-- Junctions (Collapsible Section) -->
    <details>
        <summary style="cursor: pointer; padding: 10px; color: white; font-size: 18px;">Junctions</summary>
        <div class="content">
            <a href="superview.php">View Junction</a>
        </div>
    </details>

    <!-- Settings (Collapsible Section) -->
    <details>
        <summary style="cursor: pointer; padding: 10px; color: white; font-size: 18px;"> Settings</summary>
        <div class="content">
            <a href="superprofile.php">👤 Profile</a>
            <a href="superpassword.php">🔑 Change Password</a>
        </div>
    </details>

    <!-- View Reports -->
    <details>
        <summary style="cursor: pointer; padding: 10px; color: white; font-size: 18px;"> View Report</summary>
        <div class="content">
            <a href="superevent.php">🚦Junction Event</a>
        </div>
    </details>
</div>
<style>
    /* Topbar Styling */
    .topnav {
        background-color: rgb(15, 170, 20);
        overflow: hidden;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        padding: 10px;
    }

    .topnav a {
        float: right;
        background-color: red;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        font-size: 17px;
        border-radius: 5px;
        margin: 5px;
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

    /* Hover effect for links */
    .content a:hover {
        background-color: rgb(59, 179, 113);
    }
</style>
