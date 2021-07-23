<?php
// Initialize the session
session_start();
date_default_timezone_set("Asia/Calcutta"); 

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login_reader.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style>
	<script>
		//function searchClicked() {
		//	window.location = "searchPage2.php";
		//}
		function issueClicked() {
			window.location = "issuePage.php";
		}
		function returnClicked() {
			window.location = "returnPage.php";
		}
		function reserveClicked() {
			window.location = "showReserve.php";
		}
		function viewClicked() {
			window.location = "myBooks.php";
		}
		function searchEditClicked() {
			window.location = "searchPage_editted.php";
		}
		function newUserClicked() {
			window.location = "newUserReg.php";
		}
	</script>
</head>
<body>
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
	<center>
	<input type="button" name="issue" onclick="issueClicked()" value="Issue"></input>
	<br/><br/>
	<!--seachPage2 is not working as expected.<input type="button" name="search" onclick="searchClicked()" value="Search"></input>
	<br/><br/>-->
	<input type="button" name="search" onclick="searchEditClicked()" value="Search Page Editted"></input>
	<br/><br/>
	<input type="button" name="return_book" onclick="returnClicked()" value="Return"></input>
	<br/><br/>
	<input type="button" name="view_book" onclick="viewClicked()" value="My Books"></input>
	<br/><br/>
	<input type="button" name="reserve_show" onclick="reserveClicked()" value="My Reservations"></input>
	<br/><br/>
	<!--<input type="button" onclick="newUserClicked()" value="New User"></input>-->
	<center>
	<br/>
	<br/>
    <p>
        <!--We don't need this right now <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a><br/>-->
        <a href="logout_reader.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
    </p>
</body>
</html>