<?php
// Initialize the session
session_start();
date_default_timezone_set("Asia/Calcutta"); 

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin_staff"]) || $_SESSION["loggedin_staff"] !== true){
    header("location: login_staff.php");
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
		function issueClicked() {
			window.location = "issuePage_staff.php";
		}
		function returnClicked() {
			window.location = "returnPage_staff.php";
		}
		function searchEditClicked() {
			window.location = "searchPage_editted_staff.php";
		}
		function newUserClicked() {
			window.location = "newUserReg.php";
		}
		function outstandingClicked() {
			window.location = "outstandingPage.php";
		}
		function newbookClicked() {
			window.location = "newBook.php";
		}
		function bookCatClicked() {
			window.location = "bookCatalogue.php";
		}
	</script>
</head>
<body style="background-color:green">
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. You are a staff.</h1>
	<center>
	<input type="button" name="issue" onclick="issueClicked()" value="Issue"></input>
	<br/><br/>
	<input type="button" name="search" onclick="searchEditClicked()" value="Search Page Editted"></input>
	<br/><br/>
	<input type="button" name="return_book" onclick="returnClicked()" value="Return"></input>
	<br/><br/>
	<input type="button" name="outstanding_books" onclick="outstandingClicked()" value="Outstanding Books"></input>
	<br/><br/>
	<input type="button" name="new_book" onclick="newbookClicked()" value="New Book"></input>
	<br/><br/>
	<input type="button" onclick="newUserClicked()" value="New User"></input>
	<br/><br/>
	<input type="button" onclick="bookCatClicked()" value="Book Catalogue"></input>
	</center>
	<br/>
	<br/>
    <p>
        <!--We don't need this right now <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a><br/>-->
        <a href="logout_staff.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
    </p>
</body>
</html>