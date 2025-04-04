<?php
session_start();
error_reporting(0);
include("include/config.php");

// Define number of results per page
$results_per_page = 5;

// Find out total records
$total_query = mysqli_query($con, "SELECT COUNT(*) AS total FROM superlog");
$total_row = mysqli_fetch_assoc($total_query);
$total_records = $total_row['total'];

// Calculate total pages
$total_pages = ceil($total_records / $results_per_page);

// Get the current page number
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$page = max(1, min($page, $total_pages)); // Ensure page is within valid range

// Calculate OFFSET
$offset = ($page - 1) * $results_per_page;

// Fetch records with LIMIT and OFFSET
$query = mysqli_query($con, "SELECT * FROM superlog ORDER BY id DESC LIMIT $results_per_page OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superuser Login Log</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        .container {
            width: 80%;
            max-width: 800px;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
            margin-left: 230px; /* Adjust this value to move further right */
  
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #ff6600;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .pagination {
            margin-top: 15px;
        }
        .pagination a {
            text-decoration: none;
            padding: 8px 12px;
            background: #ff6600;
            color: white;
            margin: 3px;
            border-radius: 5px;
        }
        .pagination a:hover {
            background: #333;
        }
        .pagination .active {
            background: #333;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<?php include 'sidebar.php'; ?>
<body>

<div class="container">
    <h2>Superuser Login Log</h2>
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
            $cnt = $offset + 1;
            while ($result = mysqli_fetch_array($query)) { ?>
                <tr>
                <td><?php echo $cnt; ?></td>
                <td><?php echo htmlspecialchars($result['username']); ?></td>
                <td><?php echo htmlspecialchars($result['userip']); ?></td>
                <td><?php echo date("d-m-Y h:i A", strtotime($result['loginTime'])); ?></td>
                <td>
    <?php 
   if ($result['status'] === 'Success') { // Only consider successful logins
    if (!empty($result['logoutTime']) && $result['logoutTime'] !== '0000-00-00 00:00:00') {
        // Show logout time if available
        echo date("d-m-Y h:i A", strtotime($result['logoutTime']));
    } else {
        // If logout time is empty, user is still logged in
        echo "<span style='color:red;' title='User is still logged in. Please check logs for details.'>Still Logged In</span>";
    }
} else {
    // No logout time for failed login attempts
    echo "<span style='color:gray;'>N/A</span>"; 
}
?>
</td>
<td><?php echo htmlspecialchars($result['status']); ?></td>

                </tr>
            <?php 
                $cnt++; 
            } 
            ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1) { ?>
            <a href="?page=<?php echo ($page - 1); ?>">Previous</a>
        <?php } ?>

        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php } ?>

        <?php if ($page < $total_pages) { ?>
            <a href="?page=<?php echo ($page + 1); ?>">Next</a>
        <?php } ?>
    </div>
</div>

</body>
</html>
