<?php

session_start();

require_once 'DBconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];


// =====================================================
// HELPER FUNCTION
// =====================================================

function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}


// =====================================================
// 1. VIEW AN EXISTING SAVED REPORT
// =====================================================

if (isset($_GET['id'])) {

    $report_id = $_GET['id'];

    // Get the report, but only if it belongs to this user
    $sql = "
        SELECT *
        FROM report
        WHERE report_id = ?
        AND user_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $report_id, $user_id);
    $stmt->execute();

    $report_result = $stmt->get_result();
    $saved_report = $report_result->fetch_assoc();

    $stmt->close();

    if (!$saved_report) {
        die("Report not found.");
    }


    // =================================================
    // SAVED COUNTRY COMPARISON REPORT
    // =================================================

    if ($saved_report['report_type'] == "Country Comparison") {

        /*
         * Title was saved like:
         *
         * Bangladesh vs India Comparison
         */

        $title = $saved_report['report_title'];

        $title_without_comparison = str_replace(
            " Comparison",
            "",
            $title
        );

        $countries = explode(
            " vs ",
            $title_without_comparison,
            2
        );

        if (count($countries) != 2) {
            die("Could not identify the countries in this report.");
        }

        $country1_name = trim($countries[0]);
        $country2_name = trim($countries[1]);


        // Get first country
        $sql1 = "
            SELECT *
            FROM country_info
            WHERE country_name = ?
            LIMIT 1
        ";

        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("s", $country1_name);
        $stmt1->execute();

        $result1 = $stmt1->get_result();
        $row1 = $result1->fetch_assoc();

        $stmt1->close();


        // Get second country
        $sql2 = "
            SELECT *
            FROM country_info
            WHERE country_name = ?
            LIMIT 1
        ";

        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("s", $country2_name);
        $stmt2->execute();

        $result2 = $stmt2->get_result();
        $row2 = $result2->fetch_assoc();

        $stmt2->close();


        if (!$row1 || !$row2) {
            die("Country information could not be found.");
        }

        ?>


        <!DOCTYPE html>

        <html lang="en">

        <head>

            <meta charset="utf-8">

            <meta name="viewport"
                  content="width=device-width, initial-scale=1.0">

            <title>Country Comparison Report</title>

            <style>

                * {
                    box-sizing: border-box;
                }

                body {
                    margin: 0;
                    font-family: Arial, sans-serif;
                    background-color: #e9eef2;
                    color: #333;
                }

                .report-container {
                    width: 85%;
                    max-width: 1000px;
                    margin: 40px auto;
                    background-color: white;
                    padding: 50px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
                }

                .report-header {
                    text-align: center;
                    border-bottom: 3px solid #7b9da6;
                    padding-bottom: 25px;
                    margin-bottom: 30px;
                }

                .report-header h1 {
                    color: #4f86a6;
                    margin-bottom: 10px;
                    font-size: 32px;
                    letter-spacing: 1px;
                }

                .report-header h2 {
                    color: #555;
                    font-size: 22px;
                    font-weight: normal;
                    margin-bottom: 20px;
                }

                .report-info {
                    width: 100%;
                    background-color: #f1f4f6;
                    padding: 15px;
                    border-left: 4px solid #7b9da6;
                    margin-bottom: 35px;
                }

                .report-info p {
                    margin: 6px 0;
                }

                .section-title {
                    color: #4f86a6;
                    border-bottom: 2px solid #d5e0e5;
                    padding-bottom: 8px;
                    margin-top: 30px;
                    margin-bottom: 15px;
                    font-size: 21px;
                }

                .overview {
                    line-height: 1.7;
                    text-align: justify;
                }

                .comparison-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                .comparison-table th {
                    background-color: #6fa8c5;
                    color: white;
                    padding: 13px;
                    text-align: center;
                }

                .comparison-table td {
                    padding: 12px;
                    border-bottom: 1px solid #d4dce1;
                    text-align: center;
                }

                .comparison-table tr:nth-child(even) {
                    background-color: #f1f4f6;
                }

                .comparison-table td:first-child {
                    font-weight: bold;
                    text-align: left;
                }

                .observations {
                    line-height: 1.8;
                }

                .observations li {
                    margin-bottom: 8px;
                }

                .conclusion {
                    background-color: #f1f4f6;
                    padding: 20px;
                    line-height: 1.7;
                    text-align: justify;
                    border-left: 4px solid #7b9da6;
                }

                .report-footer {
                    margin-top: 45px;
                    padding-top: 15px;
                    border-top: 1px solid #ddd;
                    text-align: center;
                    color: #777;
                    font-size: 13px;
                }

                .button-area {
                    text-align: center;
                    margin: 30px auto;
                }

                button {
                    background-color: #7b9da6;
                    color: white;
                    padding: 11px 25px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 15px;
                }

                button:hover {
                    background-color: #5f8995;
                }

                @media print {

                    body {
                        background-color: white;
                    }

                    .report-container {
                        width: 100%;
                        max-width: none;
                        margin: 0;
                        padding: 20px;
                        box-shadow: none;
                    }

                    .button-area {
                        display: none;
                    }

                }

            </style>

        </head>


        <body>


        <div class="report-container">


            <!-- REPORT HEADER -->

            <div class="report-header">

                <h1>
                    COUNTRY COMPARISON REPORT
                </h1>

                <h2>
                    <?php echo e($country1_name); ?>
                    vs
                    <?php echo e($country2_name); ?>
                </h2>

            </div>


            <!-- REPORT INFORMATION -->

            <div class="report-info">

                <p>
                    <strong>Report ID:</strong>
                    <?php echo e($saved_report['report_id']); ?>
                </p>

                <p>
                    <strong>Report Type:</strong>
                    Country Comparison
                </p>

                <p>
                    <strong>Country 1:</strong>
                    <?php echo e($country1_name); ?>
                </p>

                <p>
                    <strong>Country 2:</strong>
                    <?php echo e($country2_name); ?>
                </p>

            </div>


            <!-- INTRODUCTION -->

            <h3 class="section-title">
                1. Overview
            </h3>

            <p class="overview">

                This report provides a comparative overview of
                <?php echo e($country1_name); ?>
                and
                <?php echo e($country2_name); ?>.

                The comparison is based on demographic, economic,
                educational and environmental indicators available
                in the Data Explorer database.

                The purpose of this report is to present the
                available information in a structured format and
                highlight the differences between the two countries.

            </p>


            <!-- COUNTRY INFORMATION -->

            <h3 class="section-title">
                2. Country Information
            </h3>


            <table class="comparison-table">

                <tr>

                    <th>
                        Indicator
                    </th>

                    <th>
                        <?php echo e($country1_name); ?>
                        (<?php echo e($row1['country_code']); ?>)
                    </th>

                    <th>
                        <?php echo e($country2_name); ?>
                        (<?php echo e($row2['country_code']); ?>)
                    </th>

                </tr>


                <tr>

                    <td>
                        Year
                    </td>

                    <td>
                        <?php echo e($row1['year']); ?>
                    </td>

                    <td>
                        <?php echo e($row2['year']); ?>
                    </td>

                </tr>


                <tr>

                    <td>
                        Capital
                    </td>

                    <td>
                        <?php echo e($row1['capital']); ?>
                    </td>

                    <td>
                        <?php echo e($row2['capital']); ?>
                    </td>

                </tr>


                <tr>

                    <td>
                        Continent
                    </td>

                    <td>
                        <?php echo e($row1['continent']); ?>
                    </td>

                    <td>
                        <?php echo e($row2['continent']); ?>
                    </td>

                </tr>


                <tr>

                    <td>
                        Region
                    </td>

                    <td>
                        <?php echo e($row1['region']); ?>
                    </td>

                    <td>
                        <?php echo e($row2['region']); ?>
                    </td>

                </tr>


                <tr>

                    <td>
                        Population
                    </td>

                    <td>
                        <?php echo e($row1['population']); ?>
                    </td>

                    <td>
                        <?php echo e($row2['population']); ?>
                    </td>

                </tr>


                <tr>

                    <td>
                        GDP
                    </td>

                    <td>
                        <?php echo e($row1['gdp']); ?>
                    </td>

                    <td>
                        <?php echo e($row2['gdp']); ?>
                    </td>

                </tr>


                <tr>

                    <td>
                        Life Expectancy
                    </td>

                    <td>
                        <?php echo e($row1['life_expectancy']); ?>
                    </td>

                    <td>
                        <?php echo e($row2['life_expectancy']); ?>
                    </td>

                </tr>


                <tr>

                    <td>
                        Literacy Rate
                    </td>

                    <td>
                        <?php echo e($row1['literacy_rate']); ?>%
                    </td>

                    <td>
                        <?php echo e($row2['literacy_rate']); ?>%
                    </td>

                </tr>


                <tr>

                    <td>
                        CO2 Emission
                    </td>

                    <td>
                        <?php echo e($row1['co2_emission']); ?>
                    </td>

                    <td>
                        <?php echo e($row2['co2_emission']); ?>
                    </td>

                </tr>

            </table>


            <!-- OBSERVATIONS -->

            <h3 class="section-title">
                3. Key Observations
            </h3>

            <ul class="observations">

                <?php

                if ($row1['population'] > $row2['population']) {

                    echo "<li>" .
                         e($country1_name) .
                         " has a larger population than " .
                         e($country2_name) .
                         " based on the available data.</li>";

                } else {

                    echo "<li>" .
                         e($country2_name) .
                         " has a larger population than " .
                         e($country1_name) .
                         " based on the available data.</li>";

                }


                if ($row1['gdp'] > $row2['gdp']) {

                    echo "<li>" .
                         e($country1_name) .
                         " has a higher GDP than " .
                         e($country2_name) .
                         " in the available dataset.</li>";

                } else {

                    echo "<li>" .
                         e($country2_name) .
                         " has a higher GDP than " .
                         e($country1_name) .
                         " in the available dataset.</li>";

                }


                if ($row1['literacy_rate'] > $row2['literacy_rate']) {

                    echo "<li>" .
                         e($country1_name) .
                         " has a higher literacy rate.</li>";

                } else {

                    echo "<li>" .
                         e($country2_name) .
                         " has a higher literacy rate.</li>";

                }


                if ($row1['life_expectancy'] > $row2['life_expectancy']) {

                    echo "<li>" .
                         e($country1_name) .
                         " has a higher life expectancy.</li>";

                } else {

                    echo "<li>" .
                         e($country2_name) .
                         " has a higher life expectancy.</li>";

                }

                ?>

            </ul>


            <!-- CONCLUSION -->

            <h3 class="section-title">
                4. Conclusion
            </h3>

            <div class="conclusion">

                The comparison demonstrates that
                <?php echo e($country1_name); ?>
                and
                <?php echo e($country2_name); ?>
                differ across several demographic, economic,
                educational and environmental indicators.

                The information presented in this report can be
                used to understand the relative characteristics
                of the two countries based on the data stored in
                the Data Explorer database.

            </div>


            <!-- FOOTER -->

            <div class="report-footer">

                Data Explorer<br>

                Country Comparison Report

            </div>


        </div>


        <div class="button-area">

            <button onclick="window.print()">
                Print Report
            </button>

        </div>


        </body>

        </html>


        <?php

        exit();

    }


    // =================================================
    // SAVED DATA EVOLUTION REPORT
    // =================================================

    else if ($saved_report['report_type'] == "Data Evolution") {

        $parts = explode("|", $saved_report['report_title']);

        if (count($parts) != 5) {
            die("Invalid Data Evolution report.");
        }

        $country_code = $parts[1];
        $attribute = $parts[2];
        $from_year = $parts[3];
        $to_year = $parts[4];


        // Get country name

        $sql_country = "
            SELECT country_name
            FROM country_info
            WHERE country_code = ?
            LIMIT 1
        ";

        $stmt_country = $conn->prepare($sql_country);

        $stmt_country->bind_param(
            "s",
            $country_code
        );

        $stmt_country->execute();

        $country_result = $stmt_country->get_result();

        $country_row = $country_result->fetch_assoc();

        $stmt_country->close();


        if (!$country_row) {
            die("Country not found.");
        }

        $country_name = $country_row['country_name'];


        // Attribute name

        if ($attribute == "population") {

            $column_name = "Population";

        }

        else if ($attribute == "gdp") {

            $column_name = "GDP";

        }

        else if ($attribute == "life_expectancy") {

            $column_name = "Life Expectancy";

        }

        else if ($attribute == "literacy_rate") {

            $column_name = "Literacy Rate";

        }

        else if ($attribute == "co2_emission") {

            $column_name = "CO2 Emission";

        }

        else {

            die("Invalid attribute.");

        }


        // Allowed columns

        $allowed_columns = [
            "population",
            "gdp",
            "life_expectancy",
            "literacy_rate",
            "co2_emission"
        ];

        if (!in_array($attribute, $allowed_columns)) {
            die("Invalid attribute.");
        }


        // Get data

        $sql_data = "
            SELECT year, $attribute
            FROM country_info
            WHERE country_code = ?
            AND year BETWEEN ? AND ?
            ORDER BY year ASC
        ";

        $stmt_data = $conn->prepare($sql_data);

        $stmt_data->bind_param(
            "sii",
            $country_code,
            $from_year,
            $to_year
        );

        $stmt_data->execute();

        $data_result = $stmt_data->get_result();


        $years_data = [];
        $values_data = [];


        while ($row = $data_result->fetch_assoc()) {

            $years_data[] = $row['year'];
            $values_data[] = $row[$attribute];

        }


        $stmt_data->close();


        if (count($years_data) == 0) {
            die("No data available for this period.");
        }


        ?>


        <!DOCTYPE html>

        <html lang="en">

        <head>

            <meta charset="UTF-8">

            <meta name="viewport"
                  content="width=device-width, initial-scale=1.0">

            <title>Data Evolution Report</title>


            <style>

                * {
                    box-sizing: border-box;
                }

                body {
                    margin: 0;
                    font-family: Arial, sans-serif;
                    background-color: #e9eef2;
                    color: #333;
                }

                .report-container {
                    width: 85%;
                    max-width: 1000px;
                    margin: 40px auto;
                    background-color: white;
                    padding: 50px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
                }

                .report-header {
                    text-align: center;
                    border-bottom: 3px solid #7b9da6;
                    padding-bottom: 25px;
                    margin-bottom: 30px;
                }

                .report-header h1 {
                    color: #4f86a6;
                    font-size: 32px;
                    letter-spacing: 1px;
                    margin-bottom: 10px;
                }

                .report-header h2 {
                    color: #555;
                    font-size: 22px;
                    font-weight: normal;
                    margin-bottom: 10px;
                }

                .report-info {
                    background-color: #f1f4f6;
                    padding: 15px;
                    border-left: 4px solid #7b9da6;
                    margin-bottom: 35px;
                }

                .report-info p {
                    margin: 6px 0;
                }

                .section-title {
                    color: #4f86a6;
                    border-bottom: 2px solid #d5e0e5;
                    padding-bottom: 8px;
                    margin-top: 30px;
                    margin-bottom: 15px;
                    font-size: 21px;
                }

                .overview {
                    line-height: 1.7;
                    text-align: justify;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                th {
                    background-color: #6fa8c5;
                    color: white;
                    padding: 13px;
                }

                td {
                    padding: 12px;
                    text-align: center;
                    border-bottom: 1px solid #d4dce1;
                }

                tr:nth-child(even) {
                    background-color: #f1f4f6;
                }

                .summary-box {
                    background-color: #f1f4f6;
                    padding: 20px;
                    line-height: 1.8;
                    border-left: 4px solid #7b9da6;
                }

                .conclusion {
                    background-color: #f1f4f6;
                    padding: 20px;
                    line-height: 1.7;
                    text-align: justify;
                    border-left: 4px solid #7b9da6;
                }

                .report-footer {
                    margin-top: 45px;
                    padding-top: 15px;
                    border-top: 1px solid #ddd;
                    text-align: center;
                    color: #777;
                    font-size: 13px;
                }

                .button-area {
                    text-align: center;
                    margin: 30px auto;
                }

                button {
                    padding: 11px 25px;
                    background-color: #7b9da6;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 15px;
                }

                button:hover {
                    background-color: #5f8995;
                }

                @media print {

                    body {
                        background-color: white;
                    }

                    .report-container {
                        width: 100%;
                        max-width: none;
                        margin: 0;
                        padding: 20px;
                        box-shadow: none;
                    }

                    .button-area {
                        display: none;
                    }

                }

            </style>

        </head>


        <body>


        <div class="report-container">


            <!-- HEADER -->

            <div class="report-header">

                <h1>
                    DATA EVOLUTION REPORT
                </h1>

                <h2>

                    <?php echo e($country_name); ?>

                    -

                    <?php echo e($column_name); ?>

                </h2>

            </div>


            <!-- REPORT INFORMATION -->

            <div class="report-info">

                <p>
                    <strong>Report ID:</strong>
                    <?php echo e($saved_report['report_id']); ?>
                </p>

                <p>
                    <strong>Report Type:</strong>
                    Data Evolution
                </p>

                <p>
                    <strong>Country:</strong>
                    <?php echo e($country_name); ?>
                </p>

                <p>
                    <strong>Indicator:</strong>
                    <?php echo e($column_name); ?>
                </p>

                <p>
                    <strong>Period:</strong>
                    <?php echo e($from_year); ?>
                    -
                    <?php echo e($to_year); ?>
                </p>

            </div>


            <!-- OVERVIEW -->

            <h3 class="section-title">
                1. Overview
            </h3>

            <p class="overview">

                This report presents the historical development of
                <?php echo e($column_name); ?>
                in
                <?php echo e($country_name); ?>
                between
                <?php echo e($from_year); ?>
                and
                <?php echo e($to_year); ?>.

                The purpose of this report is to show how the
                selected indicator changed over the specified
                period using data stored in the Data Explorer
                database.

            </p>


            <!-- DATA TABLE -->

            <h3 class="section-title">
                2. Historical Data
            </h3>


            <table>

                <tr>

                    <th>
                        Year
                    </th>

                    <th>
                        <?php echo e($column_name); ?>
                    </th>

                </tr>


                <?php

                for ($i = 0; $i < count($years_data); $i++) {

                ?>

                    <tr>

                        <td>
                            <?php echo e($years_data[$i]); ?>
                        </td>

                        <td>

                            <?php

                            echo e($values_data[$i]);

                            if ($attribute == "literacy_rate") {
                                echo "%";
                            }

                            ?>

                        </td>

                    </tr>

                <?php

                }

                ?>

            </table>


            <!-- TREND SUMMARY -->

            <h3 class="section-title">
                3. Trend Summary
            </h3>


            <?php

            $first_value = $values_data[0];

            $last_value = $values_data[
                count($values_data) - 1
            ];

            $difference = $last_value - $first_value;


            if ($first_value != 0) {

                $percentage_change =
                    ($difference / $first_value) * 100;

            }

            else {

                $percentage_change = 0;

            }


            if ($difference > 0) {

                $trend = "increased";

            }

            else if ($difference < 0) {

                $trend = "decreased";

            }

            else {

                $trend = "remained unchanged";

            }

            ?>


            <div class="summary-box">

                The <?php echo e($column_name); ?>
                of
                <?php echo e($country_name); ?>
                <strong><?php echo $trend; ?></strong>
                from
                <?php echo e($first_value); ?>
                in
                <?php echo e($years_data[0]); ?>
                to
                <?php echo e($last_value); ?>
                in
                <?php echo e($years_data[count($years_data) - 1]); ?>.

                <br><br>

                The overall change during the selected period was
                approximately
                <strong>
                    <?php echo number_format(
                        abs($percentage_change),
                        2
                    ); ?>%
                </strong>.

            </div>


            <!-- CONCLUSION -->

            <h3 class="section-title">
                4. Conclusion
            </h3>

            <div class="conclusion">

                The historical data provides an overview of how
                <?php echo e($column_name); ?>
                changed in
                <?php echo e($country_name); ?>
                during the selected period.

                This report allows users to examine changes over
                time and identify the overall direction of the
                selected indicator.

            </div>


            <!-- FOOTER -->

            <div class="report-footer">

                Data Explorer<br>

                Data Evolution Report

            </div>


        </div>


        <div class="button-area">

            <button onclick="window.print()">
                Print Report
            </button>

        </div>


        </body>

        </html>


        <?php

        exit();

    }


    // =================================================
    // SAVED COUNTRY RANKING REPORT
    // =================================================

    else if ($saved_report['report_type'] == "Country Ranking") {

        $title = $saved_report['report_title'];

        $ranking_type = str_replace(
            "Country Ranking by ",
            "",
            $title
        );


        if ($ranking_type == "GDP") {

            $sql = "
                SELECT country_name, gdp
                FROM country_info
                ORDER BY gdp DESC
            ";

            $column_name = "GDP";
            $database_column = "gdp";

        }

        else if ($ranking_type == "Population") {

            $sql = "
                SELECT country_name, population
                FROM country_info
                ORDER BY population DESC
            ";

            $column_name = "Population";
            $database_column = "population";

        }

        else if ($ranking_type == "Literacy Rate") {

            $sql = "
                SELECT country_name, literacy_rate
                FROM country_info
                ORDER BY literacy_rate DESC
            ";

            $column_name = "Literacy Rate";
            $database_column = "literacy_rate";

        }

        else if ($ranking_type == "Life Expectancy") {

            $sql = "
                SELECT country_name, life_expectancy
                FROM country_info
                ORDER BY life_expectancy DESC
            ";

            $column_name = "Life Expectancy";
            $database_column = "life_expectancy";

        }

        else if ($ranking_type == "CO2 Emission") {

            $sql = "
                SELECT country_name, co2_emission
                FROM country_info
                ORDER BY co2_emission DESC
            ";

            $column_name = "CO2 Emission";
            $database_column = "co2_emission";

        }

        else {

            die("Invalid ranking report.");

        }


        $result = $conn->query($sql);

        ?>


        <!DOCTYPE html>

        <html lang="en">

        <head>

            <meta charset="utf-8">

            <meta name="viewport"
                  content="width=device-width, initial-scale=1.0">

            <title>Country Ranking Report</title>


            <style>

                * {
                    box-sizing: border-box;
                }

                body {
                    margin: 0;
                    font-family: Arial, sans-serif;
                    background-color: #e9eef2;
                    color: #333;
                }

                .report-container {
                    width: 85%;
                    max-width: 1000px;
                    margin: 40px auto;
                    background-color: white;
                    padding: 50px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
                }

                .report-header {
                    text-align: center;
                    border-bottom: 3px solid #7b9da6;
                    padding-bottom: 25px;
                    margin-bottom: 30px;
                }

                .report-header h1 {
                    color: #4f86a6;
                    font-size: 32px;
                    letter-spacing: 1px;
                    margin-bottom: 10px;
                }

                .report-header h2 {
                    color: #555;
                    font-size: 22px;
                    font-weight: normal;
                }

                .report-info {
                    background-color: #f1f4f6;
                    padding: 15px;
                    border-left: 4px solid #7b9da6;
                    margin-bottom: 35px;
                }

                .report-info p {
                    margin: 6px 0;
                }

                .section-title {
                    color: #4f86a6;
                    border-bottom: 2px solid #d5e0e5;
                    padding-bottom: 8px;
                    margin-top: 30px;
                    margin-bottom: 15px;
                    font-size: 21px;
                }

                .overview {
                    line-height: 1.7;
                    text-align: justify;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                th {
                    background-color: #6fa8c5;
                    color: white;
                    padding: 13px;
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

                .rank {
                    font-weight: bold;
                    color: #4f86a6;
                }

                .top-rank {
                    font-weight: bold;
                }

                .summary-box {
                    background-color: #f1f4f6;
                    padding: 20px;
                    line-height: 1.8;
                    border-left: 4px solid #7b9da6;
                }

                .conclusion {
                    background-color: #f1f4f6;
                    padding: 20px;
                    line-height: 1.7;
                    text-align: justify;
                    border-left: 4px solid #7b9da6;
                }

                .report-footer {
                    margin-top: 45px;
                    padding-top: 15px;
                    border-top: 1px solid #ddd;
                    text-align: center;
                    color: #777;
                    font-size: 13px;
                }

                .button-area {
                    text-align: center;
                    margin: 30px auto;
                }

                button {
                    padding: 11px 25px;
                    background-color: #7b9da6;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 15px;
                }

                button:hover {
                    background-color: #5f8995;
                }

                @media print {

                    body {
                        background-color: white;
                    }

                    .report-container {
                        width: 100%;
                        max-width: none;
                        margin: 0;
                        padding: 20px;
                        box-shadow: none;
                    }

                    .button-area {
                        display: none;
                    }

                }

            </style>

        </head>


        <body>


        <div class="report-container">


            <!-- HEADER -->

            <div class="report-header">

                <h1>
                    COUNTRY RANKING REPORT
                </h1>

                <h2>
                    Ranking by <?php echo e($column_name); ?>
                </h2>

            </div>


            <!-- REPORT INFORMATION -->

            <div class="report-info">

                <p>
                    <strong>Report ID:</strong>
                    <?php echo e($saved_report['report_id']); ?>
                </p>

                <p>
                    <strong>Report Type:</strong>
                    Country Ranking
                </p>

                <p>
                    <strong>Ranking Indicator:</strong>
                    <?php echo e($column_name); ?>
                </p>

                <p>
                    <strong>Ranking Order:</strong>
                    Highest to Lowest
                </p>

            </div>


            <!-- OVERVIEW -->

            <h3 class="section-title">
                1. Overview
            </h3>

            <p class="overview">

                This report ranks the countries available in the
                Data Explorer database according to their
                <?php echo e($column_name); ?>.

                The countries are arranged from the highest value
                to the lowest value for the selected indicator.

                This ranking provides a simple way to compare the
                relative performance or value of countries according
                to the selected indicator.

            </p>


            <!-- RANKING TABLE -->

            <h3 class="section-title">
                2. Country Ranking
            </h3>


            <table>

                <tr>

                    <th>
                        Rank
                    </th>

                    <th>
                        Country
                    </th>

                    <th>
                        <?php echo e($column_name); ?>
                    </th>

                </tr>


                <?php

                $rank = 1;

                $top_country = "";
                $top_value = "";

                while ($row = $result->fetch_assoc()) {

                    if ($rank == 1) {

                        $top_country =
                            $row['country_name'];

                        $top_value =
                            $row[$database_column];

                    }

                ?>

                    <tr>

                        <td class="rank">

                            <?php echo $rank; ?>

                        </td>


                        <td class="<?php

                            if ($rank <= 3) {
                                echo 'top-rank';
                            }

                        ?>">

                            <?php

                            echo e(
                                $row['country_name']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo e(
                                $row[$database_column]
                            );

                            if ($ranking_type == "Literacy Rate") {
                                echo "%";
                            }

                            ?>

                        </td>

                    </tr>

                <?php

                    $rank++;

                }

                ?>


            </table>


            <!-- SUMMARY -->

            <h3 class="section-title">
                3. Ranking Summary
            </h3>


            <div class="summary-box">

                Based on the available data, the country ranked
                first for
                <strong><?php echo e($column_name); ?></strong>
                is
                <strong><?php echo e($top_country); ?></strong>.

                Its recorded value for the selected indicator is
                <strong><?php echo e($top_value); ?></strong>.

                The complete ranking is presented in the table
                above, with countries arranged from the highest
                value to the lowest value.

            </div>


            <!-- CONCLUSION -->

            <h3 class="section-title">
                4. Conclusion
            </h3>

            <div class="conclusion">

                The country ranking provides a clear comparison
                of countries based on
                <?php echo e($column_name); ?>.

                By arranging the available countries in descending
                order, the report makes it easier to identify the
                countries with the highest and lowest recorded
                values in the selected indicator.

            </div>


            <!-- FOOTER -->

            <div class="report-footer">

                Data Explorer<br>

                Country Ranking Report

            </div>


        </div>


        <div class="button-area">

            <button onclick="window.print()">
                Print Report
            </button>

        </div>


        </body>

        </html>


        <?php

        exit();

    }


    else {

        die("Unknown report type.");

    }

}


// =====================================================
// 2. GENERATE A NEW DATA EVOLUTION REPORT
// =====================================================

if (
    isset($_POST['generate_report']) &&
    isset($_POST['report_type']) &&
    $_POST['report_type'] == "Data Evolution"
) {

    $country_code = $_POST['country_code'];
    $attribute = $_POST['attribute'];
    $from_year = $_POST['from_year'];
    $to_year = $_POST['to_year'];


    // Get country name

    $sql_country = "
        SELECT country_name
        FROM country_info
        WHERE country_code = ?
        LIMIT 1
    ";

    $stmt_country = $conn->prepare($sql_country);

    $stmt_country->bind_param(
        "s",
        $country_code
    );

    $stmt_country->execute();

    $country_result = $stmt_country->get_result();

    $country_row = $country_result->fetch_assoc();

    $stmt_country->close();


    if (!$country_row) {
        die("Country not found.");
    }


    $country_name = $country_row['country_name'];


    // Attribute name

    if ($attribute == "population") {

        $column_name = "Population";

    }

    else if ($attribute == "gdp") {

        $column_name = "GDP";

    }

    else if ($attribute == "life_expectancy") {

        $column_name = "Life Expectancy";

    }

    else if ($attribute == "literacy_rate") {

        $column_name = "Literacy Rate";

    }

    else if ($attribute == "co2_emission") {

        $column_name = "CO2 Emission";

    }

    else {

        die("Invalid attribute.");

    }


    $allowed_columns = [
        "population",
        "gdp",
        "life_expectancy",
        "literacy_rate",
        "co2_emission"
    ];

    if (!in_array($attribute, $allowed_columns)) {
        die("Invalid attribute.");
    }


    // Get data

    $sql_data = "
        SELECT year, $attribute
        FROM country_info
        WHERE country_code = ?
        AND year BETWEEN ? AND ?
        ORDER BY year ASC
    ";

    $stmt_data = $conn->prepare($sql_data);

    $stmt_data->bind_param(
        "sii",
        $country_code,
        $from_year,
        $to_year
    );

    $stmt_data->execute();

    $data_result = $stmt_data->get_result();


    $years_data = [];
    $values_data = [];


    while ($row = $data_result->fetch_assoc()) {

        $years_data[] = $row['year'];
        $values_data[] = $row[$attribute];

    }


    $stmt_data->close();


    if (count($years_data) == 0) {
        die("No data available for this period.");
    }


    // Create report ID

    $report_id = 'REP_' . uniqid();

    $report_type = "Data Evolution";


    $report_title =
        "EVOLUTION|" .
        $country_code . "|" .
        $attribute . "|" .
        $from_year . "|" .
        $to_year;


    // Save report

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

    $stmt_report->bind_param(
        "ssss",
        $report_id,
        $report_type,
        $report_title,
        $user_id
    );

    $stmt_report->execute();

    $stmt_report->close();


    // Save history

    $sql_history = "
        INSERT INTO history
        (
            user_id,
            report_id
        )
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


    // Calculate trend

    $first_value = $values_data[0];

    $last_value =
        $values_data[count($values_data) - 1];

    $difference =
        $last_value - $first_value;


    if ($first_value != 0) {

        $percentage_change =
            ($difference / $first_value) * 100;

    }

    else {

        $percentage_change = 0;

    }


    if ($difference > 0) {

        $trend = "increased";

    }

    else if ($difference < 0) {

        $trend = "decreased";

    }

    else {

        $trend = "remained unchanged";

    }

    ?>


    <!DOCTYPE html>

    <html lang="en">

    <head>

        <meta charset="UTF-8">

        <meta name="viewport"
              content="width=device-width, initial-scale=1.0">

        <title>Data Evolution Report</title>


        <style>

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: Arial, sans-serif;
                background-color: #e9eef2;
                color: #333;
            }

            .report-container {
                width: 85%;
                max-width: 1000px;
                margin: 40px auto;
                background-color: white;
                padding: 50px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            }

            .report-header {
                text-align: center;
                border-bottom: 3px solid #7b9da6;
                padding-bottom: 25px;
                margin-bottom: 30px;
            }

            .report-header h1 {
                color: #4f86a6;
                font-size: 32px;
                letter-spacing: 1px;
                margin-bottom: 10px;
            }

            .report-header h2 {
                color: #555;
                font-size: 22px;
                font-weight: normal;
            }

            .report-info {
                background-color: #f1f4f6;
                padding: 15px;
                border-left: 4px solid #7b9da6;
                margin-bottom: 35px;
            }

            .report-info p {
                margin: 6px 0;
            }

            .section-title {
                color: #4f86a6;
                border-bottom: 2px solid #d5e0e5;
                padding-bottom: 8px;
                margin-top: 30px;
                margin-bottom: 15px;
                font-size: 21px;
            }

            .overview {
                line-height: 1.7;
                text-align: justify;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th {
                background-color: #6fa8c5;
                color: white;
                padding: 13px;
            }

            td {
                padding: 12px;
                text-align: center;
                border-bottom: 1px solid #d4dce1;
            }

            tr:nth-child(even) {
                background-color: #f1f4f6;
            }

            .summary-box {
                background-color: #f1f4f6;
                padding: 20px;
                line-height: 1.8;
                border-left: 4px solid #7b9da6;
            }

            .conclusion {
                background-color: #f1f4f6;
                padding: 20px;
                line-height: 1.7;
                text-align: justify;
                border-left: 4px solid #7b9da6;
            }

            .report-footer {
                margin-top: 45px;
                padding-top: 15px;
                border-top: 1px solid #ddd;
                text-align: center;
                color: #777;
                font-size: 13px;
            }

            .button-area {
                text-align: center;
                margin: 30px auto;
            }

            button {
                padding: 11px 25px;
                background-color: #7b9da6;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 15px;
            }

            button:hover {
                background-color: #5f8995;
            }

            @media print {

                body {
                    background-color: white;
                }

                .report-container {
                    width: 100%;
                    max-width: none;
                    margin: 0;
                    padding: 20px;
                    box-shadow: none;
                }

                .button-area {
                    display: none;
                }

            }

        </style>

    </head>


    <body>


    <div class="report-container">


        <!-- HEADER -->

        <div class="report-header">

            <h1>
                DATA EVOLUTION REPORT
            </h1>

            <h2>

                <?php echo e($country_name); ?>

                -

                <?php echo e($column_name); ?>

            </h2>

        </div>


        <!-- REPORT INFORMATION -->

        <div class="report-info">

            <p>
                <strong>Report ID:</strong>
                <?php echo e($report_id); ?>
            </p>

            <p>
                <strong>Report Type:</strong>
                Data Evolution
            </p>

            <p>
                <strong>Country:</strong>
                <?php echo e($country_name); ?>
            </p>

            <p>
                <strong>Indicator:</strong>
                <?php echo e($column_name); ?>
            </p>

            <p>
                <strong>Period:</strong>
                <?php echo e($from_year); ?>
                -
                <?php echo e($to_year); ?>
            </p>

        </div>


        <!-- OVERVIEW -->

        <h3 class="section-title">
            1. Overview
        </h3>

        <p class="overview">

            This report examines the evolution of
            <?php echo e($column_name); ?>
            in
            <?php echo e($country_name); ?>
            between
            <?php echo e($from_year); ?>
            and
            <?php echo e($to_year); ?>.

            The report presents the available yearly data and
            summarizes the overall change in the selected indicator
            during the specified period.

        </p>


        <!-- DATA -->

        <h3 class="section-title">
            2. Historical Data
        </h3>


        <table>

            <tr>

                <th>
                    Year
                </th>

                <th>
                    <?php echo e($column_name); ?>
                </th>

            </tr>


            <?php

            for ($i = 0;
                 $i < count($years_data);
                 $i++) {

            ?>

                <tr>

                    <td>
                        <?php echo e($years_data[$i]); ?>
                    </td>

                    <td>

                        <?php

                        echo e($values_data[$i]);

                        if ($attribute == "literacy_rate") {
                            echo "%";
                        }

                        ?>

                    </td>

                </tr>

            <?php

            }

            ?>

        </table>


        <!-- TREND -->

        <h3 class="section-title">
            3. Trend Analysis
        </h3>


        <div class="summary-box">

            During the selected period, the
            <strong><?php echo e($column_name); ?></strong>
            of
            <strong><?php echo e($country_name); ?></strong>
            <strong><?php echo $trend; ?></strong>
            from
            <strong><?php echo e($first_value); ?></strong>
            in
            <strong><?php echo e($years_data[0]); ?></strong>
            to
            <strong><?php echo e($last_value); ?></strong>
            in
            <strong>
                <?php
                echo e(
                    $years_data[count($years_data) - 1]
                );
                ?>
            </strong>.

            <br><br>

            The overall percentage change was approximately
            <strong>
                <?php
                echo number_format(
                    abs($percentage_change),
                    2
                );
                ?>%
            </strong>.

        </div>


        <!-- CONCLUSION -->

        <h3 class="section-title">
            4. Conclusion
        </h3>

        <div class="conclusion">

            The historical data provides an overview of the
            changes in
            <?php echo e($column_name); ?>
            for
            <?php echo e($country_name); ?>
            during the selected period.

            This report can be used to identify the overall
            direction of the selected indicator and examine
            changes between individual years.

        </div>


        <!-- FOOTER -->

        <div class="report-footer">

            Data Explorer<br>

            Data Evolution Report

        </div>


    </div>


    <div class="button-area">

        <button onclick="window.print()">
            Print Report
        </button>

    </div>


    </body>

    </html>


    <?php

    exit();

}


// =====================================================
// 3. GENERATE A NEW COUNTRY RANKING REPORT
// =====================================================

if (
    isset($_POST['generate_report']) &&
    isset($_POST['report_type']) &&
    $_POST['report_type'] == "Country Ranking"
) {

    $ranking_type = $_POST['ranking_type'];


    if ($ranking_type == "gdp") {

        $sql = "
            SELECT country_name, gdp
            FROM country_info
            ORDER BY gdp DESC
        ";

        $column_name = "GDP";
        $database_column = "gdp";

    }

    else if ($ranking_type == "population") {

        $sql = "
            SELECT country_name, population
            FROM country_info
            ORDER BY population DESC
        ";

        $column_name = "Population";
        $database_column = "population";

    }

    else if ($ranking_type == "literacy_rate") {

        $sql = "
            SELECT country_name, literacy_rate
            FROM country_info
            ORDER BY literacy_rate DESC
        ";

        $column_name = "Literacy Rate";
        $database_column = "literacy_rate";

    }

    else if ($ranking_type == "life_expectancy") {

        $sql = "
            SELECT country_name, life_expectancy
            FROM country_info
            ORDER BY life_expectancy DESC
        ";

        $column_name = "Life Expectancy";
        $database_column = "life_expectancy";

    }

    else if ($ranking_type == "co2_emission") {

        $sql = "
            SELECT country_name, co2_emission
            FROM country_info
            ORDER BY co2_emission DESC
        ";

        $column_name = "CO2 Emission";
        $database_column = "co2_emission";

    }

    else {

        die("Invalid ranking type.");

    }


    $result = $conn->query($sql);


    // Create report ID

    $report_id = 'REP_' . uniqid();

    $report_type = "Country Ranking";

    $report_title =
        "Country Ranking by " . $column_name;


    // Save report

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

    $stmt_report->bind_param(
        "ssss",
        $report_id,
        $report_type,
        $report_title,
        $user_id
    );

    $stmt_report->execute();

    $stmt_report->close();


    // Save history

    $sql_history = "
        INSERT INTO history
        (
            user_id,
            report_id
        )
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

        <meta charset="utf-8">

        <meta name="viewport"
              content="width=device-width, initial-scale=1.0">

        <title>Country Ranking Report</title>


        <style>

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: Arial, sans-serif;
                background-color: #e9eef2;
                color: #333;
            }

            .report-container {
                width: 85%;
                max-width: 1000px;
                margin: 40px auto;
                background-color: white;
                padding: 50px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            }

            .report-header {
                text-align: center;
                border-bottom: 3px solid #7b9da6;
                padding-bottom: 25px;
                margin-bottom: 30px;
            }

            .report-header h1 {
                color: #4f86a6;
                font-size: 32px;
                letter-spacing: 1px;
                margin-bottom: 10px;
            }

            .report-header h2 {
                color: #555;
                font-size: 22px;
                font-weight: normal;
            }

            .report-info {
                background-color: #f1f4f6;
                padding: 15px;
                border-left: 4px solid #7b9da6;
                margin-bottom: 35px;
            }

            .report-info p {
                margin: 6px 0;
            }

            .section-title {
                color: #4f86a6;
                border-bottom: 2px solid #d5e0e5;
                padding-bottom: 8px;
                margin-top: 30px;
                margin-bottom: 15px;
                font-size: 21px;
            }

            .overview {
                line-height: 1.7;
                text-align: justify;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th {
                background-color: #6fa8c5;
                color: white;
                padding: 13px;
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

            .rank {
                font-weight: bold;
                color: #4f86a6;
            }

            .top-rank {
                font-weight: bold;
            }

            .summary-box {
                background-color: #f1f4f6;
                padding: 20px;
                line-height: 1.8;
                border-left: 4px solid #7b9da6;
            }

            .conclusion {
                background-color: #f1f4f6;
                padding: 20px;
                line-height: 1.7;
                text-align: justify;
                border-left: 4px solid #7b9da6;
            }

            .report-footer {
                margin-top: 45px;
                padding-top: 15px;
                border-top: 1px solid #ddd;
                text-align: center;
                color: #777;
                font-size: 13px;
            }

            .button-area {
                text-align: center;
                margin: 30px auto;
            }

            button {
                padding: 11px 25px;
                background-color: #7b9da6;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 15px;
            }

            button:hover {
                background-color: #5f8995;
            }

            @media print {

                body {
                    background-color: white;
                }

                .report-container {
                    width: 100%;
                    max-width: none;
                    margin: 0;
                    padding: 20px;
                    box-shadow: none;
                }

                .button-area {
                    display: none;
                }

            }

        </style>

    </head>


    <body>


    <div class="report-container">


        <!-- HEADER -->

        <div class="report-header">

            <h1>
                COUNTRY RANKING REPORT
            </h1>

            <h2>
                Ranking by <?php echo e($column_name); ?>
            </h2>

        </div>


        <!-- REPORT INFORMATION -->

        <div class="report-info">

            <p>
                <strong>Report ID:</strong>
                <?php echo e($report_id); ?>
            </p>

            <p>
                <strong>Report Type:</strong>
                Country Ranking
            </p>

            <p>
                <strong>Ranking Indicator:</strong>
                <?php echo e($column_name); ?>
            </p>

            <p>
                <strong>Ranking Order:</strong>
                Highest to Lowest
            </p>

        </div>


        <!-- OVERVIEW -->

        <h3 class="section-title">
            1. Overview
        </h3>

        <p class="overview">

            This report ranks the countries available in the
            Data Explorer database according to their
            <?php echo e($column_name); ?>.

            The countries are arranged from the highest value
            to the lowest value.

            This ranking allows users to easily compare countries
            according to the selected demographic, economic,
            educational or environmental indicator.

        </p>


        <!-- RANKING -->

        <h3 class="section-title">
            2. Country Ranking
        </h3>


        <table>

            <tr>

                <th>
                    Rank
                </th>

                <th>
                    Country
                </th>

                <th>
                    <?php echo e($column_name); ?>
                </th>

            </tr>


            <?php

            $rank = 1;

            $top_country = "";
            $top_value = "";

            while ($row = $result->fetch_assoc()) {

                if ($rank == 1) {

                    $top_country =
                        $row['country_name'];

                    $top_value =
                        $row[$database_column];

                }

            ?>

                <tr>

                    <td class="rank">

                        <?php echo $rank; ?>

                    </td>


                    <td class="<?php

                        if ($rank <= 3) {
                            echo 'top-rank';
                        }

                    ?>">

                        <?php

                        echo e(
                            $row['country_name']
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo e(
                            $row[$database_column]
                        );

                        if ($ranking_type == "literacy_rate") {
                            echo "%";
                        }

                        ?>

                    </td>

                </tr>

            <?php

                $rank++;

            }

            ?>

        </table>


        <!-- SUMMARY -->

        <h3 class="section-title">
            3. Ranking Summary
        </h3>


        <div class="summary-box">

            According to the available data, the country ranked
            first for
            <strong><?php echo e($column_name); ?></strong>
            is
            <strong><?php echo e($top_country); ?></strong>.

            Its recorded value is
            <strong><?php echo e($top_value); ?></strong>.

            The complete ranking above presents all available
            countries in descending order according to the
            selected indicator.

        </div>


        <!-- CONCLUSION -->

        <h3 class="section-title">
            4. Conclusion
        </h3>

        <div class="conclusion">

            The country ranking provides a structured comparison
            of countries based on
            <?php echo e($column_name); ?>.

            The ranking makes it easier to identify countries
            with relatively higher and lower values of the
            selected indicator and provides a useful summary
            of the information stored in the Data Explorer
            database.

        </div>


        <!-- FOOTER -->

        <div class="report-footer">

            Data Explorer<br>

            Country Ranking Report

        </div>


    </div>


    <div class="button-area">

        <button onclick="window.print()">
            Print Report
        </button>

    </div>


    </body>

    </html>


    <?php

    exit();

}


// =====================================================
// 4. SHOW USER'S SAVED REPORTS
// =====================================================

$sql_reports = "
    SELECT report_id, report_type, report_title
    FROM report
    WHERE user_id = ?
    ORDER BY report_id DESC
";

$stmt_reports = $conn->prepare($sql_reports);

$stmt_reports->bind_param(
    "s",
    $user_id
);

$stmt_reports->execute();

$reports_result = $stmt_reports->get_result();

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Reports</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/main.css" rel="stylesheet">


    <style>

        body {
            background-color: #e9eef2;
        }

        .reports-container {
            width: 80%;
            margin: 50px auto;
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .reports-title {
            text-align: center;
            color: #4f86a6;
            margin-bottom: 30px;
        }

        .reports-table {
            width: 100%;
            border-collapse: collapse;
        }

        .reports-table th {
            background-color: #6fa8c5;
            color: white;
            padding: 13px;
            text-align: center;
        }

        .reports-table td {
            padding: 13px;
            text-align: center;
            border-bottom: 1px solid #d4dce1;
        }

        .reports-table tr:nth-child(even) {
            background-color: #f1f4f6;
        }

        .view-button {
            display: inline-block;
            padding: 7px 15px;
            background-color: #7b9da6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .view-button:hover {
            background-color: #5f8995;
            color: white;
            text-decoration: none;
        }

        .empty-message {
            text-align: center;
            color: #666;
            padding: 30px;
        }

    </style>

</head>


<body>


<!-- HEADER -->

<section id="header"
         style="background-color:#7b9da6;">

    <div class="row">

        <div class="col-md-2"
             style="font-size:30px;color:#fcfeff;">

            Data Explorer

        </div>


        <div class="col-md-10"
             style="text-align:right;">

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

            <a href="report.php"
               style="margin-left:20px;color:#fcfeff;">
                Report
            </a>

            <a href="logout.php"
               style="margin-left:20px;color:#fcfeff;">
                Logout
            </a>

        </div>

    </div>

</section>


<!-- SAVED REPORTS -->

<div class="reports-container">


    <h1 class="reports-title">
        My Reports
    </h1>


    <?php if ($reports_result->num_rows == 0) { ?>

        <p class="empty-message">

            You have not generated any reports yet.

        </p>

    <?php } else { ?>


        <table class="reports-table">

            <tr>

                <th>
                    Report Title
                </th>

                <th>
                    Report Type
                </th>

                <th>
                    Action
                </th>

            </tr>


            <?php while ($report = $reports_result->fetch_assoc()) { ?>

                <tr>


                    <td>

                        <?php

                        /*
                         * Make Data Evolution titles readable.
                         *
                         * Instead of showing:
                         *
                         * EVOLUTION|BD|population|2020|2024
                         *
                         * show:
                         *
                         * Population Evolution Report
                         */

                        if (
                            $report['report_type']
                            == "Data Evolution"
                        ) {

                            $parts = explode(
                                "|",
                                $report['report_title']
                            );

                            if (count($parts) == 5) {

                                $attribute_name =
                                    $parts[2];

                                if (
                                    $attribute_name
                                    == "population"
                                ) {
                                    $display_attribute =
                                        "Population";
                                }

                                else if (
                                    $attribute_name
                                    == "gdp"
                                ) {
                                    $display_attribute =
                                        "GDP";
                                }

                                else if (
                                    $attribute_name
                                    == "life_expectancy"
                                ) {
                                    $display_attribute =
                                        "Life Expectancy";
                                }

                                else if (
                                    $attribute_name
                                    == "literacy_rate"
                                ) {
                                    $display_attribute =
                                        "Literacy Rate";
                                }

                                else if (
                                    $attribute_name
                                    == "co2_emission"
                                ) {
                                    $display_attribute =
                                        "CO2 Emission";
                                }

                                else {
                                    $display_attribute =
                                        $attribute_name;
                                }


                                echo e(
                                    $display_attribute .
                                    " Evolution Report (" .
                                    $parts[3] .
                                    " - " .
                                    $parts[4] .
                                    ")"
                                );

                            }

                            else {

                                echo e(
                                    $report['report_title']
                                );

                            }

                        }

                        else {

                            echo e(
                                $report['report_title']
                            );

                        }

                        ?>

                    </td>


                    <td>

                        <?php

                        echo e(
                            $report['report_type']
                        );

                        ?>

                    </td>


                    <td>

                        <a class="view-button"
                           href="report.php?id=<?php
                               echo urlencode(
                                   $report['report_id']
                               );
                           ?>">

                            View Report

                        </a>

                    </td>


                </tr>

            <?php } ?>


        </table>


    <?php } ?>


</div>


</body>

</html>