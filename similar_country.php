```php
<?php
include 'DBconnect.php';

$selected_country = "";
$similar_countries = array();

if (isset($_POST['country'])) {
    $selected_country = $_POST['country'];

    // Get selected country
    $sql = "SELECT * FROM country_info WHERE country_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selected_country);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $selected = $result->fetch_assoc();

        // Get minimum and maximum values for normalization
        $range_sql = "SELECT
                        MIN(population) AS min_population,
                        MAX(population) AS max_population,
                        MIN(gdp) AS min_gdp,
                        MAX(gdp) AS max_gdp,
                        MIN(life_expectancy) AS min_life,
                        MAX(life_expectancy) AS max_life,
                        MIN(literacy_rate) AS min_literacy,
                        MAX(literacy_rate) AS max_literacy,
                        MIN(co2_emission) AS min_co2,
                        MAX(co2_emission) AS max_co2
                      FROM country_info";

        $range_result = $conn->query($range_sql);
        $ranges = $range_result->fetch_assoc();

        // Get all other countries
        $all_sql = "SELECT * FROM country_info WHERE country_name != ?";
        $all_stmt = $conn->prepare($all_sql);
        $all_stmt->bind_param("s", $selected_country);
        $all_stmt->execute();
        $all_result = $all_stmt->get_result();

        while ($country = $all_result->fetch_assoc()) {

            /*
             * Calculate similarity for each indicator.
             * The result is between 0% and 100%.
             */

            // Population similarity
            if ($ranges['max_population'] != $ranges['min_population']) {
                $population_similarity =
                    (1 - abs($selected['population'] - $country['population']) /
                    ($ranges['max_population'] - $ranges['min_population'])) * 100;
            } else {
                $population_similarity = 100;
            }

            // GDP similarity
            if ($ranges['max_gdp'] != $ranges['min_gdp']) {
                $gdp_similarity =
                    (1 - abs($selected['gdp'] - $country['gdp']) /
                    ($ranges['max_gdp'] - $ranges['min_gdp'])) * 100;
            } else {
                $gdp_similarity = 100;
            }

            // Life expectancy similarity
            if ($ranges['max_life'] != $ranges['min_life']) {
                $life_similarity =
                    (1 - abs($selected['life_expectancy'] - $country['life_expectancy']) /
                    ($ranges['max_life'] - $ranges['min_life'])) * 100;
            } else {
                $life_similarity = 100;
            }

            // Literacy similarity
            if ($ranges['max_literacy'] != $ranges['min_literacy']) {
                $literacy_similarity =
                    (1 - abs($selected['literacy_rate'] - $country['literacy_rate']) /
                    ($ranges['max_literacy'] - $ranges['min_literacy'])) * 100;
            } else {
                $literacy_similarity = 100;
            }

            // CO2 similarity
            if ($ranges['max_co2'] != $ranges['min_co2']) {
                $co2_similarity =
                    (1 - abs($selected['co2_emission'] - $country['co2_emission']) /
                    ($ranges['max_co2'] - $ranges['min_co2'])) * 100;
            } else {
                $co2_similarity = 100;
            }

            // Keep similarity values between 0 and 100
            if ($population_similarity < 0) $population_similarity = 0;
            if ($population_similarity > 100) $population_similarity = 100;

            if ($gdp_similarity < 0) $gdp_similarity = 0;
            if ($gdp_similarity > 100) $gdp_similarity = 100;

            if ($life_similarity < 0) $life_similarity = 0;
            if ($life_similarity > 100) $life_similarity = 100;

            if ($literacy_similarity < 0) $literacy_similarity = 0;
            if ($literacy_similarity > 100) $literacy_similarity = 100;

            if ($co2_similarity < 0) $co2_similarity = 0;
            if ($co2_similarity > 100) $co2_similarity = 100;

            // Overall similarity
            $overall_similarity =
                ($population_similarity +
                 $gdp_similarity +
                 $life_similarity +
                 $literacy_similarity +
                 $co2_similarity) / 5;

            /*
             * Indicators that are considered similar.
             * 70% or more = Similar
             */
            $basis = array();

            if ($population_similarity >= 70) {
                $basis[] = "✓ Population";
            }

            if ($gdp_similarity >= 70) {
                $basis[] = "✓ GDP";
            }

            if ($life_similarity >= 70) {
                $basis[] = "✓ Life Expectancy";
            }

            if ($literacy_similarity >= 70) {
                $basis[] = "✓ Literacy Rate";
            }

            if ($co2_similarity >= 70) {
                $basis[] = "✓ CO₂ Emission";
            }

            if (count($basis) == 0) {
                $basis_text = "No strong matching indicator";
            } else {
                $basis_text = implode(" &nbsp; ", $basis);
            }

            $similar_countries[] = array(
                "country_name" => $country['country_name'],
                "continent" => $country['continent'],
                "region" => $country['region'],
                "similarity" => $overall_similarity,
                "basis" => $basis_text
            );
        }

        // Sort countries from highest similarity to lowest similarity
        for ($i = 0; $i < count($similar_countries); $i++) {
            for ($j = $i + 1; $j < count($similar_countries); $j++) {

                if ($similar_countries[$j]['similarity'] >
                    $similar_countries[$i]['similarity']) {

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

    <style>

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #e9eef2;
            color: #333333;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
        }

        .title {
            text-align: center;
            color: #3b82b5;
            margin-bottom: 10px;
        }

        .description {
            text-align: center;
            color: #666666;
            margin-bottom: 30px;
        }

        .search-box {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            text-align: center;
            margin-bottom: 30px;
        }

        select {
            width: 300px;
            padding: 12px;
            border: 1px solid #b8c7d1;
            border-radius: 6px;
            font-size: 15px;
            background-color: #f7f9fa;
        }

        button {
            padding: 12px 22px;
            margin-left: 10px;
            border: none;
            border-radius: 6px;
            background-color: #5b9bd5;
            color: white;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background-color: #417cae;
        }

        .selected-country {
            background-color: #dcebf5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            color: #34566e;
        }

        .table-box {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #5b9bd5;
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 13px;
            border-bottom: 1px solid #d8e0e5;
        }

        tr:nth-child(even) {
            background-color: #f3f6f8;
        }

        tr:hover {
            background-color: #e5f1f8;
        }

        .score {
            font-weight: bold;
            color: #3578a8;
        }

        .basis {
            color: #456b80;
            font-size: 14px;
            line-height: 1.8;
        }

        .no-result {
            text-align: center;
            padding: 30px;
            color: #777777;
        }

    </style>

</head>

<body>

<div class="container">

    <h1 class="title">Similar Country Finder</h1>

    <p class="description">
        Find countries that are similar based on population, GDP,
        life expectancy, literacy rate and CO₂ emissions.
    </p>

    <div class="search-box">

        <form method="POST">

            <select name="country" required>

                <option value="">Select a country</option>

                <?php

                $country_sql = "SELECT DISTINCT country_name
                                FROM country_info
                                ORDER BY country_name";

                $country_result = $conn->query($country_sql);

                while ($row = $country_result->fetch_assoc()) {

                    $country_name = $row['country_name'];

                    if ($country_name == $selected_country) {
                        echo "<option value=\"" .
                             htmlspecialchars($country_name) .
                             "\" selected>" .
                             htmlspecialchars($country_name) .
                             "</option>";
                    } else {
                        echo "<option value=\"" .
                             htmlspecialchars($country_name) .
                             "\">" .
                             htmlspecialchars($country_name) .
                             "</option>";
                    }
                }

                ?>

            </select>

            <button type="submit">
                Find Similar Countries
            </button>

        </form>

    </div>

    <?php if ($selected_country != "") { ?>

        <div class="selected-country">

            <strong>Selected Country:</strong>
            <?php echo htmlspecialchars($selected_country); ?>

        </div>

        <div class="table-box">

            <?php if (count($similar_countries) > 0) { ?>

                <table>

                    <tr>

                        <th>Country</th>

                        <th>Similarity Score</th>

                        <th>Similar Based On</th>

                        <th>Continent</th>

                        <th>Region</th>

                    </tr>

                    <?php

                    foreach ($similar_countries as $country) {

                        echo "<tr>";

                        echo "<td>" .
                             htmlspecialchars($country['country_name']) .
                             "</td>";

                        echo "<td class='score'>" .
                             number_format($country['similarity'], 1) .
                             "%" .
                             "</td>";

                        echo "<td class='basis'>" .
                             $country['basis'] .
                             "</td>";

                        echo "<td>" .
                             htmlspecialchars($country['continent']) .
                             "</td>";

                        echo "<td>" .
                             htmlspecialchars($country['region']) .
                             "</td>";

                        echo "</tr>";
                    }

                    ?>

                </table>

            <?php } else { ?>

                <div class="no-result">
                    No similar countries found.
                </div>

            <?php } ?>

        </div>

    <?php } ?>

</div>

</body>
</html>
```
