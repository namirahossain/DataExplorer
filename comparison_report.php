<?php

session_start();

require_once 'dbconnect.php';
require_once 'report_helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_POST['country1']) || !isset($_POST['country2'])) {
    die("Countries were not selected.");
}

$c1 = $_POST['country1'];
$c2 = $_POST['country2'];

$user_id = $_SESSION['user_id'];

// Get the country names
$sql = "
    SELECT country_code, country_name
    FROM country_info
    WHERE country_code IN (?, ?)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $c1, $c2);
$stmt->execute();

$result = $stmt->get_result();

$country_names = [];

while ($row = $result->fetch_assoc()) {
    $country_names[$row['country_code']] = $row['country_name'];
}

$stmt->close();

if (!isset($country_names[$c1]) || !isset($country_names[$c2])) {
    die("Country not found.");
}

$country1_name = $country_names[$c1];
$country2_name = $country_names[$c2];

$title = $country1_name . " vs " . $country2_name . " Comparison";

// Create report
$report_id = create_report(
    $conn,
    $user_id,
    "Country Comparison",
    $title
);

if ($report_id === false) {
    die("Failed to generate report.");
}

// Go to the report page
header("Location: report.php?id=" . urlencode($report_id));
exit();

?>