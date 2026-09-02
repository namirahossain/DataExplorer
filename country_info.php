<?php
require_once("dbconnect.php");

/* Get selected year */
$selected_year = isset($_GET['year']) ? $_GET['year'] : 'all';

/* Get all available years */
$year_sql = "SELECT DISTINCT year FROM country_info ORDER BY year DESC";
$year_result = mysqli_query($conn, $year_sql);

/* Get country information */
if ($selected_year == 'all') {
    $sql = "SELECT *
            FROM country_info
            ORDER BY country_name ASC, year DESC";
    
    $result = mysqli_query($conn, $sql);
} else {
    $sql = "SELECT *
            FROM country_info
            WHERE year = ?
            ORDER BY country_name ASC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $selected_year);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <meta name="description" content="Country Information"/>
    <meta name="author" content="Data Explorer"/>

    <title>Country Information - Data Explorer</title>

    <link href="css/bootstrap.min.css" rel="stylesheet"/>
    <link href="css/font-awesome.min.css" rel="stylesheet"/>
    <link href="css/animate.min.css" rel="stylesheet"/>
    <link href="css/main.css" rel="stylesheet"/>

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f7f8;
        }

        /* ---------------- NAVBAR ---------------- */

        #header {
            background-color: #7b9da6;
            padding: 18px 25px;
        }

        #header .logo {
            font-size: 30px;
            font-weight: bold;
            color: #ffffff;
        }

        #header .menu {
            text-align: right;
        }

        #header a {
            color: #ffffff;
            text-decoration: none;
            font-size: 17px;
            font-weight: bold;
            margin-left: 22px;
        }

        #header a:hover {
            color: #dcecef;
        }


        /* ---------------- PAGE TITLE ---------------- */

        .page-title {
            background-color: #7b9da6;
            color: white;
            text-align: center;
            font-size: 34px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 20px;
            margin-top: 0;
        }


        /* ---------------- MAIN CONTENT ---------------- */

        .container-box {
            width: 92%;
            margin: 35px auto 60px auto;
        }


        /* ---------------- FILTER AREA ---------------- */

        .filter-box {
            background-color: white;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.12);
            margin-bottom: 25px;
        }

        .filter-box label {
            font-size: 17px;
            font-weight: bold;
            color: #444;
            margin-right: 10px;
        }

        .year-select {
            padding: 9px 15px;
            border: 1px solid #bbb;
            border-radius: 5px;
            font-size: 15px;
            min-width: 150px;
        }

        .view-button {
            background-color: #7b9da6;
            color: white;
            border: none;
            padding: 9px 20px;
            border-radius: 5px;
            margin-left: 8px;
            font-weight: bold;
        }

        .view-button:hover {
            background-color: #668992;
        }


        /* ---------------- TABLE ---------------- */

        .table-box {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 2px 10px rgba(0,0,0,0.12);
            overflow: hidden;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .country-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            min-width: 1300px;
        }

        .country-table thead {
            background-color: #7b9da6;
            color: white;
        }

        .country-table th {
            padding: 16px 12px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            white-space: nowrap;
        }

        .country-table td {
            padding: 14px 12px;
            text-align: center;
            font-size: 14px;
            color: #444;
            border-bottom: 1px solid #e5e5e5;
            white-space: nowrap;
        }

        .country-table tbody tr:nth-child(even) {
            background-color: #f7f9fa;
        }

        .country-table tbody tr:hover {
            background-color: #e8f1f3;
        }

        .country-name {
            font-weight: bold;
            color: #527983;
        }

        .country-code {
            font-weight: bold;
        }


        /* ---------------- NO DATA ---------------- */

        .no-data {
            text-align: center;
            padding: 40px;
            color: #777;
            font-size: 18px;
        }


        /* ---------------- FOOTER ---------------- */

        #footer {
            background-color: #7b9da6;
            height: 50px;
            margin-top: 50px;
        }


        /* ---------------- MOBILE ---------------- */

        @media screen and (max-width: 900px) {

            #header .logo {
                text-align: center;
                margin-bottom: 15px;
            }

            #header .menu {
                text-align: center;
            }

            #header a {
                display: inline-block;
                margin: 5px 8px;
                font-size: 14px;
            }

            .page-title {
                font-size: 26px;
            }

            .container-box {
                width: 95%;
            }

        }

    </style>

</head>


<body>


<!-- ================= NAVIGATION BAR ================= -->

<section id="header">

    <div class="row">

        <div class="col-md-3 logo">
            Data Explorer
        </div>

        <div class="col-md-9 menu">

            <a href="home.php">Home</a>

            <a href="country_info.php">
                Country Info
            </a>

            <a href="country_comparison.php">
                Country Comparison
            </a>

            <a href="evolution.php">
                Data Evolution
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

    </div>

</section>



<!-- ================= PAGE TITLE ================= -->

<div class="page-title">
    Country Information
</div>



<!-- ================= MAIN CONTENT ================= -->

<div class="container-box">


    <!-- YEAR FILTER -->

    <div class="filter-box">

        <form method="GET">

            <label for="year">
                Select Year:
            </label>

            <select name="year" id="year" class="year-select">

                <option value="all"
                    <?php
                    if ($selected_year == 'all') {
                        echo "selected";
                    }
                    ?>>
                    All Years
                </option>


                <?php

                while ($year_row = mysqli_fetch_assoc($year_result)) {

                    ?>

                    <option value="<?php echo $year_row['year']; ?>"
                        <?php

                        if ($selected_year == $year_row['year']) {
                            echo "selected";
                        }

                        ?>>

                        <?php echo $year_row['year']; ?>

                    </option>

                    <?php
                }

                ?>

            </select>


            <button type="submit" class="view-button">
                View Data
            </button>

        </form>

    </div>



    <!-- COUNTRY DATA TABLE -->

    <div class="table-box">

        <div class="table-responsive">

            <table class="country-table">


                <!-- TABLE HEADER -->

                <thead>

                    <tr>

                        <th>Year</th>

                        <th>Country Code</th>

                        <th>Country Name</th>

                        <th>Capital</th>

                        <th>Continent</th>

                        <th>Region</th>

                        <th>Population</th>

                        <th>GDP</th>

                        <th>Life Expectancy</th>

                        <th>Literacy Rate</th>

                        <th>CO2 Emission</th>

                    </tr>

                </thead>



                <!-- TABLE DATA -->

                <tbody>

                <?php

                if (mysqli_num_rows($result) > 0) {

                    while ($row = mysqli_fetch_assoc($result)) {

                ?>

                    <tr>

                        <!-- Year -->
                        <td>
                            <?php echo htmlspecialchars($row['year']); ?>
                        </td>


                        <!-- Country Code -->
                        <td class="country-code">
                            <?php echo htmlspecialchars($row['country_code']); ?>
                        </td>


                        <!-- Country Name -->
                        <td class="country-name">
                            <?php echo htmlspecialchars($row['country_name']); ?>
                        </td>


                        <!-- Capital -->
                        <td>
                            <?php echo htmlspecialchars($row['capital']); ?>
                        </td>


                        <!-- Continent -->
                        <td>
                            <?php echo htmlspecialchars($row['continent']); ?>
                        </td>


                        <!-- Region -->
                        <td>
                            <?php echo htmlspecialchars($row['region']); ?>
                        </td>


                        <!-- Population -->
                        <td>
                            <?php echo number_format($row['population']); ?>
                        </td>


                        <!-- GDP -->
                        <td>
                            <?php echo number_format($row['gdp'], 2); ?>
                        </td>


                        <!-- Life Expectancy -->
                        <td>
                            <?php echo number_format($row['life_expectancy'], 2); ?>
                        </td>


                        <!-- Literacy Rate -->
                        <td>
                            <?php echo number_format($row['literacy_rate'], 2); ?>%
                        </td>


                        <!-- CO2 Emission -->
                        <td>
                            <?php echo number_format($row['co2_emission'], 2); ?>
                        </td>

                    </tr>

                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td colspan="11" class="no-data">

                            No country information available for this year.

                        </td>

                    </tr>

                <?php

                }

                ?>

                </tbody>

            </table>

        </div>

    </div>


</div>



<!-- ================= FOOTER ================= -->

<section id="footer">
</section>



<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.isotope.min.js"></script>
<script src="js/wow.min.js"></script>

</body>

</html>