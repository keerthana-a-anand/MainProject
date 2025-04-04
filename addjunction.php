<?php
include("include/config.php");

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $junction = trim($_POST['junction']);
    $ip_address = trim($_POST['ip_address']);
    $url = trim($_POST['url']);

    // Validate required fields
    if (empty($junction) || empty($ip_address)) {
        $error = "Junction and IP Address are required fields.";
    } elseif (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
        $error = "Invalid IP Address format.";
    } elseif (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
        $error = "Invalid URL format.";
    } else {
        // Insert data into the database
        $query = "INSERT INTO addjunction (junction, ip_address, url) VALUES ('$junction', '$ip_address', '$url')";
        if (mysqli_query($con, $query)) {
            $success = "Junction added successfully!";
        } else {
            $error = "Error: " . mysqli_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Junction</title>
    <link rel="stylesheet" href="styles.css"> <!-- External CSS File -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background: rgb(241, 241, 241);
            padding: 20px;
            border-radius: 8px;
            width: 600px;
            box-shadow: 0 0 10px rgba(31, 29, 29, 0.91);
            margin-left: 230px; /* Adjust this value to move further right */
            
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .input-group input {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .submit-btn {
            width: 101%;
            padding: 10px;
            background-color: #ff6600;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-btn:hover {
            background-color: #1bd161;
        }

        .message {
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<?php include 'sidebar.php'; ?>
<body>

<div class="form-container">
    <h2>Add Junction</h2>

    <?php if ($error) { echo "<p class='message error'>$error</p>"; } ?>
    <?php if ($success) { echo "<p class='message success'>$success</p>"; } ?>

    <form method="POST">
        <div class="input-group">
            <label for="junction">Junction Name *</label>
            <input type="text" id="junction" name="junction" required>
        </div>

        <div class="input-group">
            <label for="ip_address">IP Address *</label>
            <input type="text" id="ip_address" name="ip_address" required>
        </div>

        <div class="input-group">
            <label for="url">URL</label>
            <input type="text" id="url" name="url">
        </div>

        <button class="submit-btn" type="submit">Add Junction</button>
    </form>
</div>

</body>
</html>
