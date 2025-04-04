<?php
session_start();
include("include/config.php"); // Ensure database connection is included

if (isset($_SESSION['login'])) {
    $username = $_SESSION['login'];
    $role = $_SESSION['role'];

    // Determine which table to update
    if ($role == 'superuser') {
        $updateQuery = "UPDATE superlog SET logoutTime = NOW() WHERE username = '$username' AND logoutTime IS NULL ORDER BY id DESC LIMIT 1";
    } elseif ($role == 'normaluser') {  // Ensure correct role name
        $updateQuery = "UPDATE normaluserlog SET logoutTime = NOW() WHERE username = '$username' AND logoutTime IS NULL ORDER BY id DESC LIMIT 1";
    }

    // Execute the query before session destruction
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
}

// Unset session variables and destroy session
session_unset();
session_destroy();
header("location: index.php");
exit;
?>
