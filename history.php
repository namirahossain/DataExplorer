<?php

session_start();
require_once 'dbconnect.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// ================= CREATE HISTORY TABLE IF NOT EXISTS =================

$conn->query("
    CREATE TABLE IF NOT EXISTS history (
        history_id INT AUTO_INCREMENT PRIMARY KEY,
        time DATETIME DEFAULT CURRENT_TIMESTAMP,
        user_id VARCHAR(100) NOT NULL,
        report_id VARCHAR(100) DEFAULT NULL
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS report (
        report_id VARCHAR(100) PRIMARY KEY,
        report_type VARCHAR(100),
        report_title VARCHAR(255),
        user_id VARCHAR(100),
        country_comparison_id VARCHAR(100) DEFAULT NULL,
        country_ranking_id VARCHAR(100) DEFAULT NULL,
        similar_country_finder_id VARCHAR(100) DEFAULT NULL
    )
");

// ================= HANDLE CLEAR HISTORY =================

if (isset($_POST['clear_history'])) {

    $sql = "DELETE FROM history WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->close();
}

// ================= FETCH HISTORY =================

$sql = "
    SELECT h.history_id, h.time, h.report_id, r.report_title, r.report_type
    FROM history h
    LEFT JOIN report r ON h.report_id = r.report_id
    WHERE h.user_id = ?
    ORDER BY h.time DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}
$stmt->close();

// ================= COUNTS =================

$total = count($history);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>History - Data Explorer</title>

    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }


        /* ================= NAVBAR ================= */

        .navbar {

            height: 78px;

            background: #82a7b1;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 24px;

            color: white;
        }


        .logo {

            font-size: 32px;

            font-weight: bold;
        }


        .nav-links {

            display: flex;

            gap: 35px;
        }


        .nav-links a {

            color: white;

            text-decoration: none;

            font-size: 18px;

            font-weight: bold;
        }


        /* ================= MAIN ================= */

        .container {

            width: 90%;

            max-width: 1100px;

            margin: 40px auto;
        }


        .title {

            text-align: center;

            color: #5f8995;

            font-size: 36px;

            margin-bottom: 10px;
        }


        .subtitle {

            text-align: center;

            color: #777;

            font-size: 16px;

            margin-bottom: 30px;
        }


        /* ================= CONTROLS ================= */

        .controls {

            background: white;

            padding: 20px;

            border-radius: 10px;

            box-shadow: 0 2px 8px rgba(0,0,0,0.1);

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 30px;
        }


        .controls p {

            margin: 0;

            color: #5f8995;

            font-size: 16px;

            font-weight: bold;
        }


        button {

            padding: 10px 20px;

            background: #82a7b1;

            color: white;

            border: none;

            border-radius: 6px;

            font-size: 14px;

            cursor: pointer;
        }


        button:hover {

            background: #668f9a;
        }


        .btn-clear {

            background: #c55a5a;
        }


        .btn-clear:hover {

            background: #a94444;
        }


        /* ================= TABLE ================= */

        .history-container {

            background: white;

            padding: 30px;

            border-radius: 10px;

            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }


        table {

            width: 100%;

            border-collapse: collapse;

            margin-top: 10px;
        }


        th {

            background-color: #6fa8c5;

            color: white;

            padding: 12px;

            text-align: center;
        }


        td {

            padding: 11px;

            text-align: center;

            border-bottom: 1px solid #d4dce1;
        }


        tr:nth-child(even) {

            background-color: #f1f4f6;
        }


        tr:hover {

            background-color: #dcebf2;
        }


        .no-data {

            text-align: center;

            color: #777;

            padding: 40px 0;

            font-size: 16px;
        }


        .badge {

            display: inline-block;

            padding: 4px 10px;

            background: #e0eef3;

            color: #5f8995;

            border-radius: 12px;

            font-size: 12px;

            font-weight: bold;
        }


        /* ================= RESPONSIVE ================= */

        @media(max-width: 800px) {

            .controls {
                flex-direction: column;
                gap: 15px;
            }

            .logo {
                font-size: 22px;
            }

            .nav-links {
                gap: 12px;
            }

            .nav-links a {
                font-size: 14px;
            }

            table {
                font-size: 13px;
            }
        }

    </style>

</head>


<body>


<!-- ================= NAVBAR ================= -->

<nav class="navbar">

    <div class="logo">
        Data Explorer
    </div>


    <div class="nav-links">

        <a href="home.php">
            Home
        </a>

        <a href="show_students.php">
            Country Info
        </a>

        <a href="country_comparison.php">
            Comparison
        </a>

        <a href="evolution.php">
            Evolution
        </a>

        <a href="history.php">
            History
        </a>

        <a href="profile.php">
            Profile
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</nav>



<!-- ================= MAIN ================= -->

<div class="container">


    <h1 class="title">
        User History
    </h1>

    <p class="subtitle">
        History for <?= htmlspecialchars($user_name) ?> (ID: <?= htmlspecialchars($user_id) ?>)
    </p>


    <!-- ================= CONTROLS ================= -->

    <div class="controls">

        <p>
            Total Records: <?= $total ?>
        </p>


        <form method="POST" onsubmit="return confirm('Are you sure you want to clear all history?');">

            <button type="submit" name="clear_history" class="btn-clear">
                Clear History
            </button>

        </form>

    </div>


    <!-- ================= TABLE ================= -->

    <div class="history-container">

        <?php if ($total > 0): ?>

            <table>

                <tr>
                    <th>History ID</th>
                    <th>Time</th>
                    <th>Report ID</th>
                    <th>Report Title</th>
                    <th>Report Type</th>
                </tr>

                <?php foreach ($history as $row): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($row['history_id']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['time']) ?>
                        </td>

                        <td>
                            <?= $row['report_id'] ? htmlspecialchars($row['report_id']) : '<span class="badge">N/A</span>' ?>
                        </td>

                        <td>
                            <?= $row['report_title'] ? htmlspecialchars($row['report_title']) : '<span class="badge">No Report</span>' ?>
                        </td>

                        <td>
                            <?= $row['report_type'] ? htmlspecialchars($row['report_type']) : '-' ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        <?php else: ?>

            <div class="no-data">
                No history records found. Your activity will appear here when you generate reports, comparisons or rankings.
            </div>

        <?php endif; ?>

    </div>


</div>


</body>

</html>
