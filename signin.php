<?php
session_start();

// first of all, we need to connect to the database
require_once('dbconnect.php');

// we need to check if the input in the form textfields are not empty
if(isset($_POST['fname']) && isset($_POST['pass'])){
	// write the query to check if this username and password exists in our database
	$u = $_POST['fname'];
	$p = $_POST['pass'];
	$sql = "SELECT * FROM user_profile WHERE user_name = '$u' AND password = '$p'";
	
	//Execute the query 
	$result = mysqli_query($conn, $sql);
	
	//check if it returns an empty set
	if(mysqli_num_rows($result) !=0 ){

		$row = mysqli_fetch_assoc($result);

		$_SESSION['user_id'] = $row['user_id'];
		$_SESSION['user_name'] = $row['user_name'];
		$_SESSION['user_country'] = $row['user_country'];

		header("Location: home.php");
		exit();
	}
	else{
		// echo "Username or Password is wrong";
		header("Location: index.php");
		exit();
	}
	
}


?>
