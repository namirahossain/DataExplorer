<?php
session_start();
require_once 'history_helper.php';
?>
<html lang="en">
  <head>
      <meta charset="utf-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <meta name="description" content="About the site"/>
      <meta name="author" content="Author name"/>
      <title> THE TITLE </title>
    
      <!-- core CSS -->
      <link href="css/bootstrap.min.css" rel="stylesheet"/>
      <link href="css/font-awesome.min.css" rel="stylesheet"/>
      <link href="css/animate.min.css" rel="stylesheet"/>
      <link href="css/main.css" rel="stylesheet"/> 
  </head>

  <body> 
    <!-- following section is used for creating the menubar in the webpage -->
	<section id="header" style="background-color: #7b9da6;">
		<div class="row">  
			<div class="col-md-2" style="font-size: 30px;color:#fcfeff;"> Data Explorer </div>
			<div class="col-md-10" style="text-align: right"> 
				<a href="home.php" style="color: #fcfeff;"> Home </a> 
				<a href="show_students.php" style="margin-left: 20px; color: #fcfeff;"> Country Info </a> 
				<a href="country_comparison.php" style="margin-left: 20px; color: #fcfeff;"> Country Comparison </a> 
				<a href="evolution.php" style="margin-left: 20px; color: #fcfeff;"> Data Evolution </a> 
				<a href="history.php" style="margin-left: 20px; color: #fcfeff;"> History </a> 
				<a href="profile.php" style="margin-left: 20px; color: #fcfeff;"> Profile </a> 
				<a href="logout.php" style="margin-left: 20px; color: #fcfeff;"> Logout </a> 
			</div>
		</div>
	</section>
	
	<section id = "section1" style="background-image: none; background-color: #fcfeff;">
		<div class="title" style="color: #7b9da6;"> Country Comparison </div>
		
		<form action="country_comparison.php" class="form_design" method="post">
			<span style="color: #7b9da6;"> Select First Country: </span> 
			<select name="country1" style="width: 100%; padding: 12px 20px; margin: 8px 0; box-sizing: border-box; border-radius: 15px;">
				<option value="">-- Select Country --</option>
				<?php 
				require_once("dbconnect.php");
				$sql = "SELECT country_code, country_name FROM country_info ORDER BY country_name ASC";
				$result = mysqli_query($conn, $sql);
				if(mysqli_num_rows($result) > 0){
					while($row = mysqli_fetch_array($result)){
				?>
				<option value="<?php echo $row[0]; ?>"> <?php echo $row[1]; ?> (<?php echo $row[0]; ?>) </option>
				<?php 
					}
				}
				?>
			</select> <br/>
			<span style="color: #7b9da6;"> Select Second Country: </span> 
			<select name="country2" style="width: 100%; padding: 12px 20px; margin: 8px 0; box-sizing: border-box; border-radius: 15px;">
				<option value="">-- Select Country --</option>
				<?php 
				$sql2 = "SELECT country_code, country_name FROM country_info ORDER BY country_name ASC";
				$result2 = mysqli_query($conn, $sql2);
				if(mysqli_num_rows($result2) > 0){
					while($row2 = mysqli_fetch_array($result2)){
				?>
				<option value="<?php echo $row2[0]; ?>"> <?php echo $row2[1]; ?> (<?php echo $row2[0]; ?>) </option>
				<?php 
					}
				}
				?>
			</select> <br/> <br/>
			<input type="submit" value="Compare" style="background-color: #7b9da6; color: #fcfeff;">
		</form>

		<?php 
		// we need to check if the input in the form textfields are not empty
		if(isset($_POST['country1']) && isset($_POST['country2'])){
			$c1 = $_POST['country1'];
			$c2 = $_POST['country2'];
			if($c1 != "" && $c2 != ""){
				if($c1 == $c2){
					echo "<div style='text-align:center; color:red; margin-bottom:20px;'> Please select two different countries </div>";
				}
				else{
					// write the query to fetch country data
					$sql3 = "SELECT * FROM country_info WHERE country_code = '$c1'";
					$result3 = mysqli_query($conn, $sql3);
					$row3 = mysqli_fetch_array($result3);
					
					$sql4 = "SELECT * FROM country_info WHERE country_code = '$c2'";
					$result4 = mysqli_query($conn, $sql4);
					$row4 = mysqli_fetch_array($result4);
					
					if($row3 && $row4){
					//here we have to write some HTML code, so we will close php tag
		?>
		<div style="margin-left:10%; margin-right:10%; margin-top:20px; margin-bottom:50px;text-align:center;background:#66b3ff;">
			<div class="row country-row" style="padding:5px; background-color: #c7ccd1; font-weight: bold;"> 
				<div class="col-md-4">Indicator</div>
     			<div class="col-md-4"><?php echo $row3[2]; ?> (<?php echo $row3[1]; ?>)</div>
     			<div class="col-md-4"><?php echo $row4[2]; ?> (<?php echo $row4[1]; ?>)</div>
			</div>
			<div class="row" style="padding:5px;"> 
				<div class="col-md-4">Year</div>
				<div class="col-md-4"><?php echo $row3[0]; ?></div>
				<div class="col-md-4"><?php echo $row4[0]; ?></div>
			</div>
			<div class="row" style="padding:5px; background-color: #e6f0ff;"> 
				<div class="col-md-4">Capital</div>
				<div class="col-md-4"><?php echo $row3[3]; ?></div>
				<div class="col-md-4"><?php echo $row4[3]; ?></div>
			</div>
			<div class="row" style="padding:5px;"> 
				<div class="col-md-4">Continent</div>
				<div class="col-md-4"><?php echo $row3[4]; ?></div>
				<div class="col-md-4"><?php echo $row4[4]; ?></div>
			</div>
			<div class="row" style="padding:5px; background-color: #e6f0ff;"> 
				<div class="col-md-4">Region</div>
				<div class="col-md-4"><?php echo $row3[5]; ?></div>
				<div class="col-md-4"><?php echo $row4[5]; ?></div>
			</div>
			<div class="row" style="padding:5px;"> 
				<div class="col-md-4">Population</div>
				<div class="col-md-4"><?php echo $row3[6]; ?></div>
				<div class="col-md-4"><?php echo $row4[6]; ?></div>
			</div>
			<div class="row" style="padding:5px; background-color: #e6f0ff;"> 
				<div class="col-md-4">GDP</div>
				<div class="col-md-4"><?php echo $row3[7]; ?></div>
				<div class="col-md-4"><?php echo $row4[7]; ?></div>
			</div>
			<div class="row" style="padding:5px;"> 
				<div class="col-md-4">Life Expectancy</div>
				<div class="col-md-4"><?php echo $row3[8]; ?></div>
				<div class="col-md-4"><?php echo $row4[8]; ?></div>
			</div>
			<div class="row" style="padding:5px; background-color: #e6f0ff;"> 
				<div class="col-md-4">Literacy Rate</div>
				<div class="col-md-4"><?php echo $row3[9]; ?></div>
				<div class="col-md-4"><?php echo $row4[9]; ?></div>
			</div>
			<div class="row" style="padding:5px;"> 
				<div class="col-md-4">CO2 Emission</div>
				<div class="col-md-4"><?php echo $row3[10]; ?></div>
				<div class="col-md-4"><?php echo $row4[10]; ?></div>
			</div>
		</div>
		<?php 
					// log history for comparison view
					if (isset($_SESSION['user_id'])) {
						require_once("dbconnect.php");
						log_history($conn, $_SESSION['user_id'], null);
					}
					}
					else{
						echo "<div style='text-align:center; color:red; margin-bottom:20px;'> Country not found </div>";
					}
				}
			}
			else{
				echo "<div style='text-align:center; color:red; margin-bottom:20px;'> Please select both countries </div>";
			}
		}
		?>
		
	</section>

	
	<!----- Footer ----->
	<section id="footer" style="background-color: #7b9da6;"> 
	
	</section>
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/wow.min.js"></script>
  </body> 
</html>
