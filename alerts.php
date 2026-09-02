<?php

include 'DBconnect.php';

/*
    AUTOMATIC COUNTRY ALERT SYSTEM

    Classification:
    LOW     = bottom 25%
    NORMAL  = middle 50%
    HIGH    = top 25%

    The comparison is made against countries
    in the same current year.

    There is NO previous-year comparison.
*/


// --------------------------------------------------
// 1. GET COUNTRY CODE
// --------------------------------------------------

if (!isset($_GET['country_code']) || empty($_GET['country_code'])) {
    die("No country selected.");
}

$country_code = $_GET['country_code'];


// --------------------------------------------------
// 2. GET LATEST YEAR FOR THIS COUNTRY
// --------------------------------------------------

$sql = "SELECT *
        FROM country_info
        WHERE country_code = ?
        ORDER BY year DESC
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $country_code);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Country not found.");
}

$country = $result->fetch_assoc();

$year = $country['year'];


// --------------------------------------------------
// 3. GET ALL COUNTRIES FROM THE SAME YEAR
// --------------------------------------------------

$sql_all = "SELECT *
            FROM country_info
            WHERE year = ?";

$stmt_all = $conn->prepare($sql_all);
$stmt_all->bind_param("i", $year);
$stmt_all->execute();

$result_all = $stmt_all->get_result();


// --------------------------------------------------
// 4. STORE ALL INDICATOR VALUES
// --------------------------------------------------

$all_data = array();

while ($row = $result_all->fetch_assoc()) {

    $all_data[] = array(
        "gdp" => $row['gdp'],
        "population" => $row['population'],
        "life_expectancy" => $row['life_expectancy'],
        "literacy_rate" => $row['literacy_rate'],
        "co2_emission" => $row['co2_emission']
    );
}


// --------------------------------------------------
// 5. FUNCTION TO CALCULATE QUARTILE THRESHOLDS
// --------------------------------------------------

function calculateThresholds($data, $field)
{
    $values = array();

    foreach ($data as $row) {

        if (
            isset($row[$field]) &&
            $row[$field] !== '' &&
            $row[$field] !== null &&
            is_numeric($row[$field])
        ) {
            $values[] = floatval($row[$field]);
        }
    }


    // If there is no usable data
    if (count($values) == 0) {
        return array(
            "low" => null,
            "high" => null
        );
    }


    // Manual sorting
    sort($values, SORT_NUMERIC);

    $count = count($values);


    // 25th percentile position
    $low_position = floor(($count - 1) * 0.25);

    // 75th percentile position
    $high_position = floor(($count - 1) * 0.75);


    $low_threshold = $values[$low_position];
    $high_threshold = $values[$high_position];


    return array(
        "low" => $low_threshold,
        "high" => $high_threshold
    );
}


// --------------------------------------------------
// 6. FUNCTION TO DETERMINE STATUS
// --------------------------------------------------

function getStatus($value, $thresholds)
{
    if (
        $value === null ||
        $value === '' ||
        !is_numeric($value) ||
        $thresholds['low'] === null
    ) {
        return "N/A";
    }


    $value = floatval($value);


    if ($value <= $thresholds['low']) {
        return "LOW";
    }

    if ($value >= $thresholds['high']) {
        return "HIGH";
    }

    return "NORMAL";
}


// --------------------------------------------------
// 7. CALCULATE THRESHOLDS FOR EACH INDICATOR
// --------------------------------------------------

$gdp_thresholds =
    calculateThresholds($all_data, "gdp");

$population_thresholds =
    calculateThresholds($all_data, "population");

$life_expectancy_thresholds =
    calculateThresholds($all_data, "life_expectancy");

$literacy_thresholds =
    calculateThresholds($all_data, "literacy_rate");

$co2_thresholds =
    calculateThresholds($all_data, "co2_emission");


// --------------------------------------------------
// 8. GET STATUS FOR SELECTED COUNTRY
// --------------------------------------------------

$gdp_status =
    getStatus($country['gdp'], $gdp_thresholds);

$population_status =
    getStatus($country['population'], $population_thresholds);

$life_status =
    getStatus($country['life_expectancy'], $life_expectancy_thresholds);

$literacy_status =
    getStatus($country['literacy_rate'], $literacy_thresholds);

$co2_status =
    getStatus($country['co2_emission'], $co2_thresholds);


// --------------------------------------------------
// 9. CREATE ALERT DATA
// --------------------------------------------------

$alerts = array();

$alerts[] = array(
    "indicator" => "GDP",
    "value" => $country['gdp'],
    "status" => $gdp_status
);

$alerts[] = array(
    "indicator" => "Population",
    "value" => $country['population'],
    "status" => $population_status
);

$alerts[] = array(
    "indicator" => "Life Expectancy",
    "value" => $country['life_expectancy'],
    "status" => $life_status
);

$alerts[] = array(
    "indicator" => "Literacy Rate",
    "value" => $country['literacy_rate'],
    "status" => $literacy_status
);

$alerts[] = array(
    "indicator" => "CO2 Emission",
    "value" => $country['co2_emission'],
    "status" => $co2_status
);


// --------------------------------------------------
// 10. SAVE ALERTS INTO Alert TABLE
// --------------------------------------------------

foreach ($alerts as $alert) {

    // Skip unavailable values
    if (
        $alert['value'] === null ||
        $alert['value'] === '' ||
        !is_numeric($alert['value'])
    ) {
        continue;
    }


    $indicator = $alert['indicator'];
    $curr_value = floatval($alert['value']);

    $status = $alert['status'];

    $alert_text =
        $indicator . " is " . $status . ".";


    // Check whether this alert already exists
    $check_sql = "SELECT alert_id
                  FROM Alert
                  WHERE indicator = ?
                  AND year = ?
                  AND country_code = ?
                  LIMIT 1";

    $check_stmt = $conn->prepare($check_sql);

    $check_stmt->bind_param(
        "sis",
        $indicator,
        $year,
        $country_code
    );

    $check_stmt->execute();

    $check_result = $check_stmt->get_result();


    // Insert only if it does not already exist
    if ($check_result->num_rows == 0) {

        $insert_sql = "INSERT INTO Alert
                       (
                           indicator,
                           prev_value,
                           curr_value,
                           change_percentage,
                           alert_text,
                           user_id,
                           year,
                           country_code
                       )
                       VALUES
                       (
                           ?,
                           NULL,
                           ?,
                           NULL,
                           ?,
                           NULL,
                           ?,
                           ?
                       )";


        $insert_sql = "INSERT INTO Alert
                       (
                           indicator,
                           prev_value,
                           curr_value,
                           change_percentage,
                           alert_text,
                           user_id,
                           year,
                           country_code
                       )
                       VALUES
                       (?, NULL, ?, NULL, ?, NULL, ?, ?)";

        $insert_stmt = $conn->prepare($insert_sql);

        $insert_stmt->bind_param(
            "sdsis",
            $indicator,
            $curr_value,
            $alert_text,
            $year,
            $country_code
        );

        $insert_stmt->execute();

        $insert_stmt->close();
    }

    $check_stmt->close();
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Country Alerts</title>

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #e5e7eb;
            color: #333;
        }

        .header {
            background: #9bd7ef;
            padding: 25px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            color: #263238;
        }

        .header p {
            margin-top: 8px;
            color: #455a64;
        }

        .container {
            width: 90%;
            max-width: 900px;
            margin: 35px auto;
        }

        .country-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.10);
        }

        .country-card h2 {
            margin-top: 0;
            color: #37474f;
        }

        .year {
            color: #607d8b;
        }

        .alert-card {
            background: white;
            margin-bottom: 15px;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .indicator {
            font-size: 18px;
            font-weight: bold;
            color: #37474f;
        }

        .value {
            font-size: 14px;
            color: #78909c;
            margin-top: 6px;
        }

        .status {
            padding: 9px 18px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
        }

        .high {
            background: #ffcdd2;
            color: #b71c1c;
        }

        .normal {
            background: #fff3cd;
            color: #856404;
        }

        .low {
            background: #cfeef9;
            color: #0277bd;
        }

        .na {
            background: #eeeeee;
            color: #616161;
        }

        .info-box {
            background: #f8fafb;
            border-left: 5px solid #9bd7ef;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

    </style>

</head>


<body>


<div class="header">

    <h1>Country Alert System</h1>

    <p>
        Current-year indicator classification
    </p>

</div>


<div class="container">


    <div class="country-card">

        <h2>
            <?php echo htmlspecialchars($country['country_name']); ?>
        </h2>

        <div class="year">
            Year: <?php echo htmlspecialchars($year); ?>
        </div>

    </div>


    <div class="info-box">

        <strong>How the alerts work:</strong>

        <br><br>

        Each indicator is compared with the other countries
        in the same year.

        <br>

        <strong>LOW</strong> = bottom 25%

        <br>

        <strong>NORMAL</strong> = middle 50%

        <br>

        <strong>HIGH</strong> = top 25%

        <br><br>

        This system does not compare the country with previous years.

    </div>


    <?php foreach ($alerts as $alert): ?>

        <?php

        $status_class = "na";

        if ($alert['status'] == "HIGH") {
            $status_class = "high";
        }
        else if ($alert['status'] == "NORMAL") {
            $status_class = "normal";
        }
        else if ($alert['status'] == "LOW") {
            $status_class = "low";
        }

        ?>


        <div class="alert-card">


            <div>

                <div class="indicator">

                    <?php
                    echo htmlspecialchars($alert['indicator']);
                    ?>

                </div>


                <div class="value">

                    Value:
                    <?php

                    if (
                        $alert['value'] !== null &&
                        $alert['value'] !== '' &&
                        is_numeric($alert['value'])
                    ) {

                        echo number_format(
                            floatval($alert['value']),
                            2
                        );

                    }
                    else {

                        echo "Not available";

                    }

                    ?>

                </div>

            </div>


            <div class="status <?php echo $status_class; ?>">

                <?php
                echo htmlspecialchars($alert['status']);
                ?>

            </div>


        </div>


    <?php endforeach; ?>


</div>


</body>

</html>