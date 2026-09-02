<?php
session_start();
require_once 'dbconnect.php';


// Get all countries
$sql = "
    SELECT DISTINCT country_code, country_name
    FROM country_info
    ORDER BY country_name
";

$result = $conn->query($sql);


// Get available years
$yearSql = "
    SELECT DISTINCT year
    FROM country_info
    ORDER BY year ASC
";

$yearResult = $conn->query($yearSql);


$years = [];

while ($row = $yearResult->fetch_assoc()) {

    $years[] = $row['year'];

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Evolution - Data Explorer</title>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


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

            font-size: 20px;

            font-weight: bold;
        }


        /* ================= MAIN ================= */

        .container {

            width: 90%;

            max-width: 1200px;

            margin: 40px auto;
        }


        .title {

            text-align: center;

            color: #5f8995;

            font-size: 36px;

            margin-bottom: 30px;
        }


        /* ================= CONTROLS ================= */

        .controls {

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow: 0 2px 8px rgba(0,0,0,0.1);

            display: flex;

            gap: 20px;

            align-items: end;
        }


        .control-group {

            flex: 1;
        }


        .control-group label {

            display: block;

            margin-bottom: 8px;

            font-weight: bold;

            color: #5f8995;
        }


        select {

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
        }


        button:hover {

            background: #668f9a;
        }


        /* ================= CHART ================= */

        .chart-container {

            margin-top: 35px;

            background: white;

            padding: 30px;

            border-radius: 10px;

            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }


        .chart-title {

            text-align: center;

            color: #5f8995;

            font-size: 25px;

            margin-bottom: 25px;
        }


        #message {

            text-align: center;

            color: #777;

            margin: 20px;
        }


        canvas {

            max-height: 500px;
        }


        /* ================= SUMMARY ================= */

        .summary {

            margin-top: 35px;

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }


        .summary-title {

            text-align: center;

            color: #5f8995;

            font-size: 24px;

            margin-bottom: 25px;
        }


        .summary-grid {

            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 20px;
        }


        .summary-card {

            text-align: center;

            padding: 20px;

            border: 1px solid #ddd;

            border-radius: 8px;

            background: #fafafa;
        }


        .summary-card h3 {

            margin: 0 0 10px;

            color: #777;

            font-size: 16px;
        }


        .summary-card p {

            margin: 0;

            color: #5f8995;

            font-size: 25px;

            font-weight: bold;
        }


        /* ================= RESPONSIVE ================= */

        @media(max-width: 800px) {

            .controls {

                flex-direction: column;

                align-items: stretch;
            }


            .summary-grid {

                grid-template-columns: 1fr;
            }


            .logo {

                font-size: 24px;
            }


            .nav-links {

                gap: 10px;
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
<!-- GENERATE REPORT BUTTON -->

<div id="reportButton"
     style="text-align:center; margin-top:30px; display:none;">

    <form method="POST" action="report.php">

        <input type="hidden"
               name="report_type"
               value="Data Evolution">

        <input type="hidden"
               name="country_code"
               id="reportCountry">

        <input type="hidden"
               name="attribute"
               id="reportAttribute">

        <input type="hidden"
               name="from_year"
               id="reportFromYear">

        <input type="hidden"
               name="to_year"
               id="reportToYear">

        <button type="submit"
                name="generate_report">

            Generate Report

        </button>

    </form>

</div>


<!-- ================= MAIN ================= -->

<div class="container">


    <h1 class="title">
        Data Evolution
    </h1>



    <!-- ================= CONTROLS ================= -->

    <div class="controls">


        <!-- COUNTRY -->

        <div class="control-group">

            <label for="country">
                Select Country
            </label>


            <select id="country">

                <option value="">
                    -- Select Country --
                </option>


                <?php while ($row = $result->fetch_assoc()): ?>

                    <option value="<?= htmlspecialchars($row['country_code']) ?>">
    <?= htmlspecialchars($row['country_name']) ?>
</option>

                <?php endwhile; ?>

            </select>

        </div>



        <!-- ATTRIBUTE -->

        <div class="control-group">

            <label for="attribute">
                Select Attribute
            </label>


            <select id="attribute">

    <option value="population">
        Population
    </option>

    <option value="gdp">
        GDP
    </option>

    <option value="life_expectancy">
        Life Expectancy
    </option>

    <option value="literacy_rate">
        Literacy Rate
    </option>

    <option value="co2_emission">
        CO₂ Emissions
    </option>

</select>

        </div>



        <!-- FROM YEAR -->

        <div class="control-group">

            <label for="fromYear">
                From Year
            </label>


            <select id="fromYear">

                <option value="">
                    From
                </option>


                <?php foreach ($years as $year): ?>

                    <option value="<?= $year ?>">
                        <?= $year ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>



        <!-- TO YEAR -->

        <div class="control-group">

            <label for="toYear">
                To Year
            </label>


            <select id="toYear">

                <option value="">
                    To
                </option>


                <?php foreach ($years as $year): ?>

                    <option value="<?= $year ?>">
                        <?= $year ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>



        <!-- BUTTON -->

        <button onclick="loadEvolution()">

            Show Graph

        </button>


    </div>



    <!-- ================= GRAPH ================= -->

    <div class="chart-container">


        <h2
            id="chartTitle"
            class="chart-title"
            style="display:none;"
        >
        </h2>


        <div id="message">

            Select a country, attribute and year range to view its evolution.

        </div>


        <canvas id="evolutionChart"></canvas>


    </div>



    <!-- ================= SUMMARY ================= -->

    <div
        id="summary"
        class="summary"
        style="display:none;"
    >


        <h2 class="summary-title">

            Indicator Summary

        </h2>


        <div class="summary-grid">


            <!-- START -->

            <div class="summary-card">

                <h3>
                    Starting Value
                </h3>

                <p id="startingValue">
                    -
                </p>

            </div>



            <!-- LATEST -->

            <div class="summary-card">

                <h3>
                    Latest Value
                </h3>

                <p id="latestValue">
                    -
                </p>

            </div>



            <!-- CHANGE -->

            <div class="summary-card">

                <h3>
                    Change
                </h3>

                <p id="percentageChange">
                    -
                </p>

            </div>


        </div>

    </div>


</div>



<script>


let evolutionChart = null;



function loadEvolution() {


    const country =
        document.getElementById("country").value;


    const attribute =
        document.getElementById("attribute").value;


    const fromYear =
        document.getElementById("fromYear").value;


    const toYear =
        document.getElementById("toYear").value;
		
		document.getElementById("reportCountry").value = country;

		document.getElementById("reportAttribute").value = attribute;

		document.getElementById("reportFromYear").value = fromYear;

		document.getElementById("reportToYear").value = toYear;



    /* ================= VALIDATION ================= */


    if (!country) {

        alert("Please select a country.");

        return;

    }


    if (!fromYear || !toYear) {

        alert("Please select both From Year and To Year.");

        return;

    }


    if (parseInt(fromYear) > parseInt(toYear)) {

        alert("From Year cannot be greater than To Year.");

        return;

    }



    /* ================= REQUEST ================= */


    fetch(

        "evolution_data.php" +

        "?country_code=" +
        encodeURIComponent(country) +

        "&attribute=" +
        encodeURIComponent(attribute) +

        "&from_year=" +
        encodeURIComponent(fromYear) +

        "&to_year=" +
        encodeURIComponent(toYear)

    )


    .then(response => response.json())


    .then(data => {


        if (data.error) {

            alert(data.error);

            return;

        }



        /* ================= NO DATA ================= */


        if (data.years.length === 0) {

            document.getElementById("message").innerText =
                "No data available for the selected period.";

            document.getElementById("summary").style.display = "none";

            return;

        }



        /* ================= TITLE ================= */


        document.getElementById("message").style.display = "none";


        const chartTitle =
            document.getElementById("chartTitle");


        chartTitle.style.display = "block";


        chartTitle.innerText =
            data.country + " — " + data.label + " Evolution";



        /* ================= DESTROY OLD CHART ================= */


        if (evolutionChart !== null) {

            evolutionChart.destroy();

        }



        /* ================= CREATE CHART ================= */


        const ctx =
            document
                .getElementById("evolutionChart")
                .getContext("2d");


        evolutionChart = new Chart(ctx, {

            type: "line",


            data: {

                labels: data.years,


                datasets: [{

                    label: data.label,

                    data: data.values,

                    borderWidth: 3,

                    tension: 0.3,

                    fill: false,

                    pointRadius: 4,

                    pointHoverRadius: 7

                }]

            },


            options: {

                responsive: true,

                maintainAspectRatio: true,


                scales: {

                    x: {

                        title: {

                            display: true,

                            text: "Year"

                        }

                    },


                    y: {

                        title: {

                            display: true,

                            text: data.label

                        },

                        beginAtZero: false

                    }

                },


                plugins: {

                    legend: {

                        display: true

                    },


                    tooltip: {

                        enabled: true

                    }

                }

            }

        });



        /* ================= SUMMARY ================= */


        const values = data.values.filter(
            value => value !== null
        );


        if (values.length > 0) {


            const starting =
                parseFloat(values[0]);


            const latest =
                parseFloat(values[values.length - 1]);


            let percentage = 0;


            if (starting !== 0) {

                percentage =
                    ((latest - starting) / starting) * 100;

            }



            document.getElementById("startingValue").innerText =
                formatValue(starting, attribute);


            document.getElementById("latestValue").innerText =
                formatValue(latest, attribute);


            document.getElementById("percentageChange").innerText =
                (percentage >= 0 ? "+" : "") +
                percentage.toFixed(2) +
                "%";


            document.getElementById("summary").style.display =
                "block";
				document.getElementById("reportButton").style.display =
                "block";

        }

    })


    .catch(error => {

        console.error(error);

        alert("Something went wrong while loading the data.");

    });

}



/* ================= FORMAT VALUES ================= */


function formatValue(value, attribute) {


    if (attribute === "gdp") {

        if (value >= 1000000000000) {

            return "$" +
                (value / 1000000000000).toFixed(2) +
                "T";

        }


        if (value >= 1000000000) {

            return "$" +
                (value / 1000000000).toFixed(2) +
                "B";

        }


        if (value >= 1000000) {

            return "$" +
                (value / 1000000).toFixed(2) +
                "M";

        }


        return "$" + value.toLocaleString();

    }



    if (attribute === "population") {

        return value.toLocaleString();

    }



    if (
        attribute === "life_expectancy" ||
        attribute === "literacy_rate"
    ) {

        return value.toFixed(2);

    }



    if (attribute === "co2_emissions") {

        return value.toLocaleString();

    }


    return value.toLocaleString();

}


</script>


</body>

</html>