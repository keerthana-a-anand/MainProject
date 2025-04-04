<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tcms"; // Ensure the database name matches

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sample JSON data (Replace this with an actual API call if needed)
$json_data = '{
    "Intersection": "JNT",
    "System Time": "",
    "nCurrent Stage no": 3,
    "S mode": "Full VPSplit",
    "S Status": "Junction on",
    "aLinked Stage JSON": [
        { "nStage no": 1, "nAllocated Green": 30, "nUtilized Green": 20 },
        { "nStage no": 2, "nAllocated Green": 25, "nUtilized Green": 18 },
        { "nStage no": 3, "nAllocated Green": 35, "nUtilized Green": 30 },
        { "nStage no": 4, "nAllocated Green": 40, "nUtilized Green": 35 }
    ]
}';

$data = json_decode($json_data, true);

$intersection = $data['Intersection'];
$current_stage = $data['nCurrent Stage no'];
$mode = $data['S mode'];
$status = $data['S Status'];

// Ensure table has the required columns
$sql_alter = "ALTER TABLE traffic_control 
    DROP PRIMARY KEY, 
    ADD COLUMN IF NOT EXISTS intersection VARCHAR(255) NOT NULL,
    ADD COLUMN IF NOT EXISTS current_stage INT NOT NULL,
    ADD COLUMN IF NOT EXISTS mode VARCHAR(50) NOT NULL,
    ADD COLUMN IF NOT EXISTS status VARCHAR(50) NOT NULL,
    ADD COLUMN IF NOT EXISTS stage_no INT NOT NULL,
    ADD COLUMN IF NOT EXISTS allocated_green INT NOT NULL,
    ADD COLUMN IF NOT EXISTS utilized_green INT NOT NULL,
    ADD COLUMN IF NOT EXISTS last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ADD PRIMARY KEY (intersection, stage_no)";

if (!$conn->query($sql_alter)) {
    echo "Table Alter Error: " . $conn->error;
}

// Insert or update data for each stage
foreach ($data['aLinked Stage JSON'] as $stage) {
    $stage_no = $stage['nStage no'];
    $allocated_green = $stage['nAllocated Green'];
    $utilized_green = $stage['nUtilized Green'];

    $sql = "INSERT INTO traffic_control (intersection, current_stage, mode, status, stage_no, allocated_green, utilized_green)
            VALUES ('$intersection', '$current_stage', '$mode', '$status', '$stage_no', '$allocated_green', '$utilized_green')
            ON DUPLICATE KEY UPDATE 
                current_stage = VALUES(current_stage),
                mode = VALUES(mode),
                status = VALUES(status),
                allocated_green = VALUES(allocated_green),
                utilized_green = VALUES(utilized_green),
                last_updated = CURRENT_TIMESTAMP";

    if (!$conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch latest data for display
$sql = "SELECT * FROM traffic_control WHERE intersection = '$intersection' ORDER BY stage_no ASC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
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
            <p><strong>Junction Name: <?php echo $intersection; ?></strong></p>
            <p><strong>Junction Mode: <?php echo $mode; ?></strong></p>
            <p><strong>Current Stage: <span id="current-stage"><?php echo $current_stage; ?></span></strong></p>

            <table>
                <tr><th>Stage</th><th>Allocated Time (sec)</th><th>Utilized Time (Sec)</th></tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['stage_no']; ?></td>
                        <td><?php echo $row['allocated_green']; ?></td>
                        <td>
                            <div class="time-bar">
                                <div class="time-utilized" style="width: <?php echo ($row['utilized_green'] / $row['allocated_green']) * 100; ?>%"></div>
                                <div class="time-remaining" style="width: <?php echo (1 - $row['utilized_green'] / $row['allocated_green']) * 100; ?>%"><?php echo $row['utilized_green']; ?> sec</div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
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

