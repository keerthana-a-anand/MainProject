<?php
session_start();
if (!isset($_SESSION['alogin']) || $_SESSION['alogin'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "tcms";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set initial query
$sql = "SELECT id, junctionname, status, logintime FROM updown WHERE 1=1";

// Apply filters if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET['junctionname'])) {
        $junction = $conn->real_escape_string($_GET['junctionname']);
        $sql .= " AND junctionname LIKE '%$junction%'";
    }
    if (!empty($_GET['status'])) {
        $status = $conn->real_escape_string($_GET['status']);
        $sql .= " AND status = '$status'";
    }
    if (!empty($_GET['start_date'])) {
        $start_date = $_GET['start_date'];
        $sql .= " AND DATE(logintime) >= '$start_date'";
    }
    if (!empty($_GET['end_date'])) {
        $end_date = $_GET['end_date'];
        $sql .= " AND DATE(logintime) <= '$end_date'";
    }
    $sql .= " ORDER BY id ASC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Report</title>
    <style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f2f2f2;
}

.main-content {
    margin-left: 250px; /* Adjust this based on sidebar width */
    padding: 20px;
}

h2 {
    margin-top: 60px;
    margin-bottom: 20px;
    font-size: 28px;
    color: #333;
}

/* Filter form */
.filter-form {
    width: 50%;
    margin-left: 450px; /* Pushes the form to the right */
    margin-bottom: 40px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f8f8f8;
    box-shadow: 0 0 10px #ccc;
}

.filter-form label {
    
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
}

.filter-form input {
    width: 100%;
    padding: 8px 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.filter-form select {
    width: 103%;
    padding: 8px 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.filter-form button {
    background-color: #333;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.filter-form button:hover {
    background-color: #555;
}

/* Centered table */
table {
    width: 60%;
    margin-left: 410px; /* Same push as filter-form */
    margin-bottom: 40px;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 0 10px #ccc;
}

th, td {
    padding: 12px 15px;
    border: 1px solid #ccc;
    text-align: center;
}

th {
    background-color: #333;
    color: white;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

    </style>
</head>
<body>
    
<?php include 'sidebar.php'; ?>


<!-- Filter Form -->
<form method="GET" action="view_report.php" class="filter-form">
<h2 style="text-align: center; margin-bottom: 10px;">View Report</h2>
    <label for="junctionname">Junction Name:</label>
    <input type="text" name="junctionname" id="junctionname" value="<?php echo isset($_GET['junctionname']) ? htmlspecialchars($_GET['junctionname']) : ''; ?>">

    <label for="status">Status:</label>
    <select name="status" id="status">
        <option value="">--Select--</option>
        <option value="up" <?php if (isset($_GET['status']) && $_GET['status'] == 'up') echo 'selected'; ?>>Up</option>
        <option value="down" <?php if (isset($_GET['status']) && $_GET['status'] == 'down') echo 'selected'; ?>>Down</option>
    </select>

    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" id="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">

    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" id="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">

    <button type="submit">Submit</button>
</form>

<!-- Data Table -->
<table>
    <tr>
        <th>SI No</th>
        <th>Junction Name</th>
        <th>Status</th>
        <th>Status Time</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        $si = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $si++ . "</td>
                    <td>" . htmlspecialchars($row["junctionname"]) . "</td>
                    <td>" . htmlspecialchars($row["status"]) . "</td>
                    <td>" . htmlspecialchars($row["logintime"]) . "</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No records found</td></tr>";
    }
    $conn->close();
    ?>
</table>

</body>
</html>