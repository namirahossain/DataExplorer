<?php
include 'DBconnect.php';

$selected_country = "";
$similar_countries = array();

if (isset($_POST['country'])) {

    $selected_country = $_POST['country'];

    // Get the selected country's information
    $sql = "SELECT * FROM country_info WHERE country_name = '$selected_country'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $country = $result->fetch_assoc();

        $population = $country['population'];
        $gdp = $country['gdp'];
        $life_expectancy = $country['life_expectancy'];
        $literacy_rate = $country['literacy_rate'];
        $co2_emission = $country['co2_emission'];

        // Get all other countries
        $sql2 = "SELECT * FROM country_info WHERE country_name != '$selected_country'";

        $result2 = $conn->query($sql2);

        while ($row = $result2->fetch_assoc()) {

            // Calculate differences
            $population_difference = abs($population - $row['population']);
            $gdp_difference = abs($gdp - $row['gdp']);
            $life_difference = abs($life_expectancy - $row['life_expectancy']);
            $literacy_difference = abs($literacy_rate - $row['literacy_rate']);
            $co2_difference = abs($co2_emission - $row['co2_emission']);

            // Calculate a simple similarity score
            $score =
                $population_difference +
                $gdp_difference +
                $life_difference +
                $literacy_difference +
                $co2_difference;

            $row['score'] = $score;

            $similar_countries[] = $row;
        }

        // Sort countries by similarity score
        for ($i = 0; $i < count($similar_countries); $i++) {

            for ($j = $i + 1; $j < count($similar_countries); $j++) {

                if ($similar_countries[$j]['score'] < $similar_countries[$i]['score']) {

                    $temp = $similar_countries[$i];

                    $similar_countries[$i] = $similar_countries[$j];

                    $similar_countries[$j] = $temp;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>

<html>

<head>

    <title>Similar Country Finder</title>

</head>

<body>

    <h1>Similar Country Finder</h1>

    <form method="POST">

        <label>Select a country:</label>

        <select name="country">

            <?php

            $sql = "SELECT country_name FROM country_info";

            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {

                echo "<option value='" . $row['country_name'] . "'>";

                echo $row['country_name'];

                echo "</option>";
            }

            ?>

        </select>

        <br><br>

        <button type="submit">Find Similar Countries</button>

    </form>


    <?php

    if (count($similar_countries) > 0) {

        echo "<h2>Countries similar to " . $selected_country . "</h2>";

        echo "<table border='1' cellpadding='10'>";

        echo "<tr>";

        echo "<th>Country</th>";
        echo "<th>Continent</th>";
        echo "<th>Region</th>";
        echo "<th>Population</th>";
        echo "<th>GDP</th>";
        echo "<th>Life Expectancy</th>";
        echo "<th>Literacy Rate</th>";
        echo "<th>CO2 Emission</th>";

        echo "</tr>";

        $limit = 5;

        for ($i = 0; $i < count($similar_countries) && $i < $limit; $i++) {

            echo "<tr>";

            echo "<td>" . $similar_countries[$i]['country_name'] . "</td>";

            echo "<td>" . $similar_countries[$i]['continent'] . "</td>";

            echo "<td>" . $similar_countries[$i]['region'] . "</td>";

            echo "<td>" . $similar_countries[$i]['population'] . "</td>";

            echo "<td>" . $similar_countries[$i]['gdp'] . "</td>";

            echo "<td>" . $similar_countries[$i]['life_expectancy'] . "</td>";

            echo "<td>" . $similar_countries[$i]['literacy_rate'] . "</td>";

            echo "<td>" . $similar_countries[$i]['co2_emission'] . "</td>";

            echo "</tr>";
        }

        echo "</table>";
    }

    ?>

</body>

</html>