<?php

session_start();
require_once 'dbconnect.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

// ================= HANDLE UPDATE =================

if (isset($_POST['update_profile'])) {

    $new_country = trim($_POST['user_country'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');

    if ($new_country === '') {
        $message = "Country cannot be empty.";
        $message_type = "error";
    } else {

        if ($new_password !== '') {
            $sql = "UPDATE user_profile SET user_country = ?, password = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $new_country, $new_password, $user_id);
        } else {
            $sql = "UPDATE user_profile SET user_country = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $new_country, $user_id);
        }

        if ($stmt->execute()) {
            $_SESSION['user_country'] = $new_country;
            $message = "Profile updated successfully.";
            $message_type = "success";
        } else {
            $message = "Failed to update profile.";
            $message_type = "error";
        }

        $stmt->close();
    }
}

// ================= FETCH USER PROFILE =================

$sql = "SELECT user_id, user_name, user_country FROM user_profile WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// ================= FETCH STATS =================

$history_count = 0;
$report_count = 0;

// check if history table exists
$check = $conn->query("SHOW TABLES LIKE 'history'");
if ($check && $check->num_rows > 0) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM history WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $history_count = $r['cnt'] ?? 0;
    $stmt->close();
}

$check2 = $conn->query("SHOW TABLES LIKE 'report'");
if ($check2 && $check2->num_rows > 0) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM report WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $report_count = $r['cnt'] ?? 0;
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Profile - Data Explorer</title>

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

            max-width: 1000px;

            margin: 40px auto;
        }


        .title {

            text-align: center;

            color: #5f8995;

            font-size: 36px;

            margin-bottom: 30px;
        }


        /* ================= CARD ================= */

        .profile-card {

            background: white;

            padding: 30px;

            border-radius: 10px;

            box-shadow: 0 2px 8px rgba(0,0,0,0.1);

            margin-bottom: 30px;
        }


        .profile-header {

            text-align: center;

            margin-bottom: 25px;
        }


        .avatar {

            width: 90px;

            height: 90px;

            background: #82a7b1;

            color: white;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 38px;

            font-weight: bold;

            margin: 0 auto 15px;
        }


        .profile-header h2 {

            margin: 0;

            color: #5f8995;

            font-size: 26px;
        }


        .profile-header p {

            margin: 5px 0 0;

            color: #777;

            font-size: 15px;
        }


        .info-grid {

            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 20px;

            margin-top: 25px;
        }


        .info-item {

            background: #fafafa;

            padding: 15px;

            border: 1px solid #ddd;

            border-radius: 8px;
        }


        .info-item label {

            display: block;

            font-size: 13px;

            color: #777;

            margin-bottom: 5px;

            font-weight: bold;
        }


        .info-item span {

            color: #5f8995;

            font-size: 17px;

            font-weight: bold;
        }


        /* ================= STATS ================= */

        .stats-grid {

            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 20px;

            margin-top: 25px;
        }


        .stat-card {

            text-align: center;

            padding: 20px;

            border: 1px solid #ddd;

            border-radius: 8px;

            background: #fafafa;
        }


        .stat-card h3 {

            margin: 0 0 10px;

            color: #777;

            font-size: 15px;
        }


        .stat-card p {

            margin: 0;

            color: #5f8995;

            font-size: 28px;

            font-weight: bold;
        }


        /* ================= FORM ================= */

        .form-card {

            background: white;

            padding: 30px;

            border-radius: 10px;

            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }


        .form-title {

            text-align: center;

            color: #5f8995;

            font-size: 22px;

            margin-bottom: 25px;
        }


        .form-group {

            margin-bottom: 18px;
        }


        .form-group label {

            display: block;

            margin-bottom: 8px;

            font-weight: bold;

            color: #5f8995;
        }


        .form-group input {

            width: 100%;

            padding: 12px;

            border: 1px solid #aaa;

            border-radius: 6px;

            font-size: 16px;

            background: white;
        }


        button {

            padding: 12px 25px;

            background: #82a7b1;

            color: white;

            border: none;

            border-radius: 6px;

            font-size: 16px;

            cursor: pointer;

            width: 100%;
        }


        button:hover {

            background: #668f9a;
        }


        .message {

            text-align: center;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;
        }


        .message.success {

            background: #e6f4ea;

            color: #2e7d32;

            border: 1px solid #a5d6a7;
        }


        .message.error {

            background: #fdecea;

            color: #c62828;

            border: 1px solid #ef9a9a;
        }


        /* ================= RESPONSIVE ================= */

        @media(max-width: 800px) {

            .info-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
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
        User Profile
    </h1>


    <?php if ($message !== ""): ?>

        <div class="message <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <!-- ================= PROFILE CARD ================= -->

    <div class="profile-card">

        <div class="profile-header">

            <div class="avatar">
                <?= strtoupper(substr($user['user_name'], 0, 1)) ?>
            </div>

            <h2>
                <?= htmlspecialchars($user['user_name']) ?>
            </h2>

            <p>
                User ID: <?= htmlspecialchars($user['user_id']) ?>
            </p>

        </div>


        <div class="info-grid">

            <div class="info-item">
                <label>User Name</label>
                <span><?= htmlspecialchars($user['user_name']) ?></span>
            </div>

            <div class="info-item">
                <label>User ID</label>
                <span><?= htmlspecialchars($user['user_id']) ?></span>
            </div>

            <div class="info-item">
                <label>Country</label>
                <span><?= htmlspecialchars($user['user_country'] ?: 'Not set') ?></span>
            </div>

            <div class="info-item">
                <label>Member Since</label>
                <span>Data Explorer User</span>
            </div>

        </div>


        <div class="stats-grid">

            <div class="stat-card">
                <h3>History Records</h3>
                <p><?= $history_count ?></p>
            </div>

            <div class="stat-card">
                <h3>Reports Generated</h3>
                <p><?= $report_count ?></p>
            </div>

        </div>

    </div>


    <!-- ================= EDIT FORM ================= -->

    <div class="form-card">

        <h2 class="form-title">
            Update Profile
        </h2>


        <form method="POST">

            <div class="form-group">
                <label for="user_country">
                    Country
                </label>
                <input type="text" id="user_country" name="user_country" value="<?= htmlspecialchars($user['user_country']) ?>" placeholder="Enter your country">
            </div>

            <div class="form-group">
                <label for="new_password">
                    New Password (leave blank to keep current)
                </label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password">
            </div>

            <button type="submit" name="update_profile">
                Update Profile
            </button>

        </form>

    </div>


</div>


</body>

</html>
