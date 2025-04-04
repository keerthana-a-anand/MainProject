<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "tcms"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination settings
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";
$searchQuery = $search ? " WHERE username LIKE '%$search%'" : "";

// Fetch data from the database with pagination
$sql = "SELECT id, username, type FROM superuser $searchQuery LIMIT $start, $limit";
$result = $conn->query($sql);

// Get total records
$sqlTotal = "SELECT COUNT(id) AS total FROM superuser $searchQuery";
$totalResult = $conn->query($sqlTotal);
$totalRow = $totalResult->fetch_assoc();
$total = $totalRow['total'];
$totalPages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(245, 245, 245);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
            margin: 0;
            flex-direction: column;
            margin-top: 20px;
        }
        .container {
            text-align: center;
            background: white;
            width: 50%;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(31, 29, 29, 0.91);
            margin-top: 100px;
            margin-left: 230px; /* Adjust this value to move further right */
            
        }
        h2 {
            margin-bottom: 10px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        td {
            background-color: #ecf0f1;
        }
        .action-links a {
            text-decoration: none;
            padding: 5px 10px;
            margin: 5px;
            display: inline-block;
            color: white;
            border-radius: 5px;
        }
        .update-btn {
            background-color: #2ecc71;
        }
        .delete-btn {
            background-color: #e74c3c;
        }
        .pagination {
            margin-top: 15px;
        }
        .pagination a {
            text-decoration: none;
            padding: 8px 12px;
            background: #3498db;
            color: white;
            margin: 5px;
            border-radius: 5px;
        }
        .pagination a.disabled {
            background: grey;
            pointer-events: none;
        }
        .search-bar {
            margin-bottom: 10px;
        }
        .search-bar input {
            padding: 5px;
            width: 70%;
        }
        .search-bar button {
            padding: 5px 10px;
            background: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="container">
        <h2>User Management</h2>
        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Search username..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
        <table>
            <tr>
                <th>SL No</th>
                <th>Username</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
            <?php if ($result->num_rows > 0) { $sl = $start + 1;
                while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $sl++; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['type']); ?></td>
                <td class="action-links">
                    <a href="update.php?id=<?php echo $row['id']; ?>" class="update-btn">Update</a> 
                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
            <?php } } else { echo "<tr><td colspan='4'>No records found</td></tr>"; } ?>
        </table>
        <div class="pagination">
            <?php if ($page > 1) { ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Previous</a>
            <?php } else { ?>
                <a class="disabled">Previous</a>
            <?php } ?>

            <?php if ($page < $totalPages) { ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Next</a>
            <?php } else { ?>
                <a class="disabled">Next</a>
            <?php } ?>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>
