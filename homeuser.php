<?php
// Initialize the session
session_start();
date_default_timezone_set("Asia/Calcutta"); 

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: logintest.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Biblio@DSCE</title>
	
	<script src="https://kit.fontawesome.com/5d3eee0a99.js" crossorigin="anonymous"></script>
	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	
	
	<link rel="stylesheet" type="text/css" href="homeu.css">
</head>
<body>
		<div class="vertical-nav bg-dark text-light" id="sidebar">
			<div class="py-4 px-3 mb-4 bg-dark text-light">
				<div class="media d-flex align-item-center">
					<img src="avatar1.png" alt="user image" width="80" height="80" class="mr-3 rounded-circle img-thumbnail shadow-sm">
					<div class="media-body">
					<h4 class="mt-3"> <?php echo htmlspecialchars($_SESSION["username"]); ?> </h4>
					<p class="font-weight-normal text-muted mb-0">STUDENT</p>
					</div>
				</div>
			</div>
			
			<p class="text-gray font-weight-bold text-uppercase px-3 small pb-4 mb-0">Dashboard</p>
			<ul class="nav flex-column bg-white mb-0">
				<li class="nav-item">
					<a href="#" class="nav-link active bg-dark text-dark" id="highlight"><i class="fa fa-th-large mr-3 text-primary fa-fw"></i>home</a>
				</li>
				<li class="nav-item">
					<a href="usersearch.php" class="nav-link bg-dark text-light"><i class="fas fa-search mr-3 text-primary fa-fw"></i>search book</a>
				</li>
				<li class="nav-item">
					<a href="usermybooks.php" class="nav-link bg-dark text-light"><i class="fas fa-book-reader mr-3 text-primary fa-fw"></i>reserved books</a>
				</li>
				<li class="nav-item">
					<a href="userborrow.php" class="nav-link bg-dark text-light"><i class="fas fa-book-open mr-3 text-primary fa-fw"></i>borrow book</a>
				</li>
				<li class="nav-item">
					<a href="userreturn.php" class="nav-link bg-dark text-light"><i class="fas fa-book mr-3 text-primary fa-fw"></i>return book</a>
				</li>
			</ul>
			
			<p class="text-gray font-weight-bold text-uppercase px-3 small py-5 mb-0"></p>	
			
			<p class="text-gray font-weight-bold text-uppercase px-3 small py-5 mb-0"></p>
			<ul class="nav flex-column bg-white mb-0">
				<li class="nav-item">
					<a href="#" class="nav-link bg-dark text-light"><i class="fas fa-exchange-alt mr-3 text-primary fa-fw"></i>my profile</a>
				</li>
				<li class="nav-item">
					<a href="logout_reader.php" class="nav-link bg-dark text-light"><i class="fas fa-sign-out-alt mr-3 text-primary fa-fw"></i>logout</a>
				</li>
			</ul>
		
		</div>
		
		<div class="page-content p-5" id="content">
			<h1>Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?> !</h1>
			<p style="text-align:center; margin-top: 150px; margin-left: 150px; width: 800px; font-size: 35px; font-style:italic;">
			“To ask why we need libraries at all, when there is so much information available elsewhere, is about as sensible as asking if roadmaps are necessary now that there are so very many roads.”
			</p>
		</div>
</body>
</html>