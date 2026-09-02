<?php
session_start();
require_once 'dbconnect.php';
require_once 'history_helper.php';

header('Content-Type: application/json');


// ==========================================
// GET INPUT
// ==========================================

$country_code = $_GET['country_code'] ?? '';

$attribute = $_GET['attribute'] ?? '';

$from_year = isset($_GET['from_year'])
    ? intval($_GET['from_year'])
    : 0;

$to_year = isset($_GET['to_year'])
    ? intval($_GET['to_year'])
    : 0;


// ==========================================
// ALLOWED ATTRIBUTES
// ==========================================

$allowed_attributes = [

    'population' => 'Population',

    'gdp' => 'GDP',

    'life_expectancy' => 'Life Expectancy',

    'literacy_rate' => 'Literacy Rate',

    'co2_emission' => 'CO₂ Emissions'

];


// ==========================================
// VALIDATION
// ==========================================

if ($country_code === '') {

    echo json_encode([
        'error' => 'Please select a country.'
    ]);

    exit;
}


if (!array_key_exists($attribute, $allowed_attributes)) {

    echo json_encode([
        'error' => 'Invalid attribute.'
    ]);

    exit;
}


if ($from_year <= 0 || $to_year <= 0) {

    echo json_encode([
        'error' => 'Invalid year range.'
    ]);

    exit;
}


if ($from_year > $to_year) {

    echo json_encode([
        'error' => 'From Year cannot be greater than To Year.'
    ]);

    exit;
}


// ==========================================
// GET COUNTRY NAME
// ==========================================

$countrySql = "
    SELECT DISTINCT country_name
    FROM country_info
    WHERE country_code = ?
    LIMIT 1
";

$countryStmt = $conn->prepare($countrySql);

$countryStmt->bind_param(
    "s",
    $country_code
);

$countryStmt->execute();

$countryResult = $countryStmt->get_result();

$countryRow = $countryResult->fetch_assoc();


if (!$countryRow) {

    echo json_encode([
        'error' => 'Country not found.'
    ]);

    exit;
}


$countryName = $countryRow['country_name'];


// ==========================================
// GET HISTORICAL DATA
// ==========================================

$sql = "
    SELECT year, `$attribute`
    FROM country_info
    WHERE country_code = ?
    AND year BETWEEN ? AND ?
    ORDER BY year ASC
";


$stmt = $conn->prepare($sql);


$stmt->bind_param(
    "sii",
    $country_code,
    $from_year,
    $to_year
);


$stmt->execute();


$result = $stmt->get_result();


// ==========================================
// PREPARE DATA FOR CHART
// ==========================================

$years = [];

$values = [];


while ($row = $result->fetch_assoc()) {

    $years[] = (int)$row['year'];


    if ($row[$attribute] === null) {

        $values[] = null;

    } else {

        $values[] = (float)$row[$attribute];

    }

}


// ==========================================
// LOG HISTORY
// ==========================================

if (isset($_SESSION['user_id'])) {
    log_history($conn, $_SESSION['user_id'], null);
}

// ==========================================
// RETURN JSON
// ==========================================

echo json_encode([

    'country' => $countryName,

    'label' => $allowed_attributes[$attribute],

    'years' => $years,

    'values' => $values

]);

?>