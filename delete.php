<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "tcms");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Delete query
    $stmt = $conn->prepare("DELETE FROM superuser WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href='managesuper.php';</script>";
    } else {
        echo "<script>alert('Error deleting user!'); window.location.href='managesuper.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request!'); window.location.href='managesuper.php';</script>";
}

$conn->close();
?>
