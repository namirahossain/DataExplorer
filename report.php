<?php

session_start();

require_once 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Make sure the button sent both countries
if (!isset($_POST['country1']) || !isset($_POST['country2'])) {
    die("No countries were selected.");
}

$c1 = $_POST['country1'];
$c2 = $_POST['country2'];

$user_id = $_SESSION['user_id'];

// Get first country
$sql1 = "SELECT * FROM country_info WHERE country_code = ?";
$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param("s", $c1);
$stmt1->execute();
$result1 = $stmt1->get_result();
$row1 = $result1->fetch_array(MYSQLI_BOTH);
$stmt1->close();

// Get second country
$sql2 = "SELECT * FROM country_info WHERE country_code = ?";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("s", $c2);
$stmt2->execute();
$result2 = $stmt2->get_result();
$row2 = $result2->fetch_array(MYSQLI_BOTH);
$stmt2->close();

if (!$row1 || !$row2) {
    die("Country information could not be found.");
}


// Create report ID
$report_id = 'REP_' . uniqid();

$report_title = $row1[2] . " vs " . $row2[2] . " Comparison";

// Save report in report table
$sql_report = "
    INSERT INTO report
    (
        report_id,
        report_type,
        report_title,
        user_id
    )
    VALUES (?, ?, ?, ?)
";

$stmt_report = $conn->prepare($sql_report);

$report_type = "Country Comparison";

$stmt_report->bind_param(
    "ssss",
    $report_id,
    $report_type,
    $report_title,
    $user_id
);

$stmt_report->execute();
$stmt_report->close();


// Save report in history
$sql_history = "
    INSERT INTO history
    (user_id, report_id)
    VALUES (?, ?)
";

$stmt_history = $conn->prepare($sql_history);

$stmt_history->bind_param(
    "ss",
    $user_id,
    $report_id
);

$stmt_history->execute();
$stmt_history->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Country Comparison Report</title>

    <link href="css/bootstrap.min.css" rel="stylesheet"/>
    <link href="css/font-awesome.min.css" rel="stylesheet"/>
    <link href="css/main.css" rel="stylesheet"/>

</head>

<body>

<section id="header" style="background-color: #7b9da6;">

    <div class="row">

        <div class="col-md-2"
             style="font-size: 30px;color:#fcfeff;">

            Data Explorer

        </div>

        <div class="col-md-10"
             style="text-align: right">

            <a href="home.php"
               style="color:#fcfeff;">
                Home
            </a>

            <a href="show_students.php"
               style="margin-left:20px;color:#fcfeff;">
                Country Info
            </a>

            <a href="country_comparison.php"
               style="margin-left:20px;color:#fcfeff;">
                Country Comparison
            </a>

            <a href="evolution.php"
               style="margin-left:20px;color:#fcfeff;">
                Data Evolution
            </a>

            <a href="history.php"
               style="margin-left:20px;color:#fcfeff;">
                History
            </a>

            <a href="profile.php"
               style="margin-left:20px;color:#fcfeff;">
                Profile
            </a>

            <a href="logout.php"
               style="margin-left:20px;color:#fcfeff;">
                Logout
            </a>

        </div>

    </div>

</section>


<section style="background-color:#fcfeff; padding:30px;">

    <div class="title"
         style="color:#7b9da6; text-align:center;">

        Country Comparison Report

    </div>


    <div style="
        margin-left:10%;
        margin-right:10%;
        margin-top:30px;
        margin-bottom:50px;
        text-align:center;
        background:#66b3ff;
    ">

        <!-- Header -->

        <div class="row"
             style="padding:10px;
                    background-color:#c7ccd1;
                    font-weight:bold;">

            <div class="col-md-4">
                Indicator
            </div>

            <div class="col-md-4">
                <?php echo $row1[2]; ?>
                (<?php echo $row1[1]; ?>)
            </div>

            <div class="col-md-4">
                <?php echo $row2[2]; ?>
                (<?php echo $row2[1]; ?>)
            </div>

        </div>


        <!-- Year -->

        <div class="row" style="padding:5px;">

            <div class="col-md-4">
                Year
            </div>

            <div class="col-md-4">
                <?php echo $row1[0]; ?>
            </div>

            <div class="col-md-4">
                <?php echo $row2[0]; ?>
            </div>

        </div>


        <!-- Capital -->

        <div class="row"
             style="padding:5px;background-color:#e6f0ff;">

            <div class="col-md-4">
                Capital
            </div>

            <div class="col-md-4">
                <?php echo $row1[3]; ?>
            </div>

            <div class="col-md-4">
                <?php echo $row2[3]; ?>
            </div>

        </div>


        <!-- Continent -->

        <div class="row" style="padding:5px;">

            <div class="col-md-4">
                Continent
            </div>

            <div class="col-md-4">
                <?php echo $row1[4]; ?>
            </div>

            <div class="col-md-4">
                <?php echo $row2[4]; ?>
            </div>

        </div>


        <!-- Region -->

        <div class="row"
             style="padding:5px;background-color:#e6f0ff;">

            <div class="col-md-4">
                Region
            </div>

            <div class="col-md-4">
                <?php echo $row1[5]; ?>
            </div>

            <div class="col-md-4">
                <?php echo $row2[5]; ?>
            </div>

        </div>


        <!-- Population -->

        <div class="row" style="padding:5px;">

            <div class="col-md-4">
                Population
            </div>

            <div class="col-md-4">
                <?php echo $row1[6]; ?>
            </div>

            <div class="col-md-4">
                <?php echo $row2[6]; ?>
            </div>

        </div>


        <!-- GDP -->

        <div class="row"
             style="padding:5px;background-color:#e6f0ff;">

            <div class="col-md-4">
                GDP
            </div>

            <div class="col-md-4">
                <?php echo $row1[7]; ?>
            </div>

            <div class="col-md-4">
                <?php echo $row2[7]; ?>
            </div>

        </div>


        <!-- Life Expectancy -->

        <div class="row" style="padding:5px;">

            <div class="col-md-4">
                Life Expectancy
            </div>

            <div class="col-md-4">
                <?php echo $row1[8]; ?>
            </div>

            <div class="col-md-4">
                <?php echo $row2[8]; ?>
            </div>

        </div>


        <!-- Literacy Rate -->

        <div class="row"
             style="padding:5px;background-color:#e6f0ff;">

            <div class="col-md-4">
                Literacy Rate
            </div>

            <div class="col-md-4">
                <?php echo $row1[9]; ?>
            </div>

            <div class="col-md-4">
                <?php echo $row2[9]; ?>
            </div>

        </div>


        <!-- CO2 -->

        <div class="row" style="padding:5px;">

            <div class="col-md-4">
                CO2 Emission
            </div>

            <div class="col-md-4">
                <?php echo $row1[10]; ?>
            </div>

            <div class="col-md-4">
                <?php echo $row2[10]; ?>
            </div>

        </div>

    </div>


    <div style="text-align:center; margin-bottom:30px;">

        <button onclick="window.print()"
                style="
                background-color:#7b9da6;
                color:#fcfeff;
                padding:10px 25px;
                border:none;
                border-radius:5px;
                ">

            Print Report

        </button>

    </div>

</section>


<section id="footer"
         style="background-color:#7b9da6;">
</section>


<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>

</html>