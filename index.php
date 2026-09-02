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
			
		</div>
	</section>
	
	<section id = "section1" style="background-image: url('https://i.ytimg.com/vi/AM6w_tUlIn4/maxresdefault.jpg'); background-size:cover; background-position: center;">
		<div class="title" style="color: #7b9da6;"> SIGN IN </div>
		
		<form action="signin.php" class="form_design" method="post">
			<span style="color: #7b9da6;"> Username: </span> <input type="text" name="fname"> <br/>
			<span style="color: #7b9da6;"> Password: </span> <input type="password" name="pass"> <br/> <br/>
			<input type="submit" value="Sign In" style="background-color: #7b9da6; color: #fcfeff;">
			<br><br>
			<span style="color: #7b9da6;">Not a user yet?</span>
			<a href="signup.php" style="color: #7b9da6;">Sign Up</a>
		</form>
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

