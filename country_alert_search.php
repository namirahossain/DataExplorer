<?php

require_once("dbconnect.php");


// Get all countries for the dropdown
$sql = "SELECT DISTINCT country_code, country_name
        FROM country_info
        ORDER BY country_name ASC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Country Alerts - Data Explorer</title>


    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #e5e7eb;
            color: #263238;
        }


        /* Header */

        .header {
            background-color: #7b9da6;
            padding: 20px 40px;
            color: white;
        }


        .header-title {
            font-size: 30px;
            font-weight: bold;
        }


        .header-subtitle {
            margin-top: 5px;
            color: #e8f7fb;
        }


        /* Main area */

        .container {
            width: 80%;
            max-width: 900px;
            margin: 60px auto;
        }


        /* Search box */

        .search-box {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;

            box-shadow:
                0 4px 12px rgba(0,0,0,0.10);
        }


        .search-box h1 {
            margin-top: 0;
            color: #37474f;
        }


        .search-box p {
            color: #607d8b;
            margin-bottom: 30px;
        }


        /* Dropdown */

        select {
            width: 300px;
            padding: 13px;

            border: 1px solid #b0bec5;
            border-radius: 7px;

            background-color: white;

            font-size: 15px;
            color: #37474f;
        }


        /* Search button */

        .search-button {
            margin-left: 10px;

            padding: 13px 22px;

            border: none;
            border-radius: 7px;

            background-color: #9bd7ef;
            color: #263238;

            font-size: 15px;
            font-weight: bold;

            cursor: pointer;
        }


        .search-button:hover {
            background-color: #7fc9e7;
        }


        /* Alert information */

        .info-box {
            margin-top: 30px;

            padding: 20px;

            background-color: #f8fafb;

            border-left: 5px solid #9bd7ef;

            border-radius: 7px;

            color: #546e7a;

            text-align: left;
        }


        .info-box strong {
            color: #37474f;
        }


        /* Footer */

        .footer {
            text-align: center;

            margin-top: 60px;

            padding: 20px;

            color: #607d8b;
        }

    </style>

</head>


<body>


<!-- HEADER -->

<div class="header">

    <div class="header-title">
        Data Explorer
    </div>

    <div class="header-subtitle">
        Country Alert System
    </div>

</div>



<!-- MAIN CONTENT -->

<div class="container">


    <div class="search-box">


        <h1>
            🔔 Country Alerts
        </h1>


        <p>
            Select a country to view its current indicator alerts.
        </p>



        <form
            action="alerts.php"
            method="get"
        >


            <select
                name="country_code"
                required
            >


                <option value="">
                    -- Select a Country --
                </option>


                <?php

                while (
                    $row =
                    mysqli_fetch_assoc($result)
                ) {

                ?>


                    <option
                        value="<?php
                            echo htmlspecialchars(
                                $row['country_code']
                            );
                        ?>"
                    >

                        <?php

                        echo htmlspecialchars(
                            $row['country_name']
                        );

                        ?>

                    </option>


                <?php

                }

                ?>


            </select>



            <button
                type="submit"
                class="search-button"
            >

                🔔 View Alerts

            </button>


        </form>



        <div class="info-box">

            <strong>How it works</strong>

            <br><br>

            1. Select a country from the list.

            <br>

            2. Click <strong>View Alerts</strong>.

            <br>

            3. The system will show the current
            indicator status for that country.

            <br><br>

            The alert levels are:

            <br><br>

            <strong>LOW</strong> — bottom 25%

            <br>

            <strong>NORMAL</strong> — middle 50%

            <br>

            <strong>HIGH</strong> — top 25%

        </div>


    </div>


</div>



<!-- FOOTER -->

<div class="footer">

    Data Explorer © 2025

</div>


</body>

</html>