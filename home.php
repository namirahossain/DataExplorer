<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
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

     

    <a href="show_students.php" 
       style="margin-left: 20px; color: #fcfeff;"> 
       Country Info 
    </a> 

    <a href="country_comparison.php" 
       style="margin-left: 20px; color: #fcfeff;"> 
       Country Comparison 
    </a> 

    <a href="evolution.php" 
       style="margin-left: 20px; color: #fcfeff;"> 
       Data Evolution 
    </a> 

    <a href="history.php" 
       style="margin-left: 20px; color: #fcfeff;"> 
       History 
    </a> 

    <a href="profile.php" 
       style="margin-left: 20px; color: #fcfeff;"> 
       Profile 
    </a> 

    <a href="report.php" 
       style="margin-left: 20px; color: #fcfeff;"> 
       Report 
    </a> 

    <a href="logout.php" 
       style="margin-left: 20px; color: #fcfeff;"> 
       Logout 
    </a> 

</div>
		</div>
	</section>
	
	<section id = "section1" style="background-image: none; background-color: #fcfeff;">
		<div class="title" style="color: #7b9da6;"> Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> </div>

		<div style="text-align:center; color:#5f8995; font-size:18px; margin-top:20px;">
			Explore country data, comparisons, evolution charts and your history.
		</div>

		<div style="margin: 40px auto; width: 80%; display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">

			<a href="show_students.php" style="background:white; padding:25px; border-radius:10px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.1); color:#5f8995; text-decoration:none;">
				<h3>Country Info</h3>
				<p style="color:#777; font-size:14px;">Browse all country data</p>
			</a>

			<a href="country_comparison.php" style="background:white; padding:25px; border-radius:10px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.1); color:#5f8995; text-decoration:none;">
				<h3>Country Comparison</h3>
				<p style="color:#777; font-size:14px;">Compare two countries</p>
			</a>

			<a href="evolution.php" style="background:white; padding:25px; border-radius:10px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.1); color:#5f8995; text-decoration:none;">
				<h3>Data Evolution</h3>
				<p style="color:#777; font-size:14px;">View indicator over time</p>
			</a>

			<a href="history.php" style="background:white; padding:25px; border-radius:10px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.1); color:#5f8995; text-decoration:none;">
				<h3>History</h3>
				<p style="color:#777; font-size:14px;">Your activity history</p>
			</a>

			<a href="profile.php" style="background:white; padding:25px; border-radius:10px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.1); color:#5f8995; text-decoration:none;">
				<h3>Profile</h3>
				<p style="color:#777; font-size:14px;">View and edit profile</p>
			</a>

			<a href="country_ranking.php" style="background:white; padding:25px; border-radius:10px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.1); color:#5f8995; text-decoration:none;">
				<h3>Country Ranking</h3>
				<p style="color:#777; font-size:14px;">Rank countries by indicator</p>
			</a>

		</div>
		
		
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

