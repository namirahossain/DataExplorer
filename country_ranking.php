<?php

include 'DBconnect.php';

$ranking_type = "gdp";

if (isset($_POST['ranking_type'])) {
    $ranking_type = $_POST['ranking_type'];
}

if ($ranking_type == "gdp") {
    $sql = "SELECT country_name, gdp FROM country_info ORDER BY gdp DESC";
    $column_name = "GDP";
}

else if ($ranking_type == "population") {
    $sql = "SELECT country_name, population FROM country_info ORDER BY population DESC";
    $column_name = "Population";
}

else if ($ranking_type == "literacy_rate") {
    $sql = "SELECT country_name, literacy_rate FROM country_info ORDER BY literacy_rate DESC";
    $column_name = "Literacy Rate";
}

else if ($ranking_type == "life_expectancy") {
    $sql = "SELECT country_name, life_expectancy FROM country_info ORDER BY life_expectancy DESC";
    $column_name = "Life Expectancy";
}

else if ($ranking_type == "co2_emission") {
    $sql = "SELECT country_name, co2_emission FROM country_info ORDER BY co2_emission DESC";
    $column_name = "CO2 Emission";
}

$result = $conn->query($sql);

?>

<!DOCTYPE html>

<html>

<head>

    <title>Country Rankings</title>

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #e9eef2;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.15);
        }

        h1 {
            text-align: center;
            color: #4f86a6;
        }

        .form-box {
            text-align: center;
            margin-bottom: 30px;
        }

        select {
            padding: 10px;
            width: 250px;
            border: 1px solid #9bb7c7;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            padding: 10px 20px;
            margin-left: 10px;
            background-color: #6fa8c5;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
        }

        button:hover {
            background-color: #568da9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #6fa8c5;
            color: white;
            padding: 12px;
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

        .rank {
            font-weight: bold;
            color: #4f86a6;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Country Rankings</h1>

    <div class="form-box">

        <form method="POST">

            <label>Rank countries based on: </label>

            <select name="ranking_type">

                <option value="gdp"
                <?php if ($ranking_type == "gdp") echo "selected"; ?>>
                    GDP
                </option>

                <option value="population"
                <?php if ($ranking_type == "population") echo "selected"; ?>>
                    Population
                </option>

                <option value="literacy_rate"
                <?php if ($ranking_type == "literacy_rate") echo "selected"; ?>>
                    Literacy Rate
                </option>

                <option value="life_expectancy"
                <?php if ($ranking_type == "life_expectancy") echo "selected"; ?>>
                    Life Expectancy
                </option>

                <option value="co2_emission"
                <?php if ($ranking_type == "co2_emission") echo "selected"; ?>>
                    CO2 Emission
                </option>

            </select>

            <button type="submit">Rank Countries</button>

        </form>

    </div>


    <table>

        <tr>

            <th>Rank</th>

            <th>Country</th>

            <th><?php echo $column_name; ?></th>

        </tr>


        <?php

        $rank = 1;

        while ($row = $result->fetch_assoc()) {

        ?>

            <tr>

                <td class="rank">
                    <?php echo $rank; ?>
                </td>

                <td>
                    <?php echo $row['country_name']; ?>
                </td>

                <td>
                    <?php

                    if ($ranking_type == "gdp") {
                        echo $row['gdp'];
                    }

                    else if ($ranking_type == "population") {
                        echo $row['population'];
                    }

                    else if ($ranking_type == "literacy_rate") {
                        echo $row['literacy_rate'] . "%";
                    }

                    else if ($ranking_type == "life_expectancy") {
                        echo $row['life_expectancy'];
                    }

                    else if ($ranking_type == "co2_emission") {
                        echo $row['co2_emission'];
                    }

                    ?>
                </td>

            </tr>

        <?php

            $rank++;

        }

        ?>

    </table>
<form method="POST" action="report.php" style="text-align:center; margin-top:30px;">

        <input type="hidden"
               name="report_type"
               value="Country Ranking">

        <input type="hidden"
               name="ranking_type"
               value="<?php echo htmlspecialchars($ranking_type); ?>">

        <button type="submit"
                name="generate_report">

            Generate Report

        </button>

    </form>
</div>

</body>

</html>