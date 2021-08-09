<?php
session_start();

date_default_timezone_set("Asia/Calcutta");

	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin_staff"]) || $_SESSION["loggedin_staff"] !== true) {
		header("location: login_staff.php");
		exit;
	}
	
	require 'connect_db.php'; //for database connectivity
	
	//declaring variables
	$title_err = $author_err = $publisher_err = $edition_err = $price_err = $category_err = "";
	$title = $author = $publisher = $edition = $edition = $category = $price = "";
	
	if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["new_book"])) {
		
		//checking if all all fields are filled and validated
		if(empty(trim($_POST["title_txt"]))) {
			$title_err = "Please enter a book title";
		} else {
			$title = trim($_POST['title_txt']);
			if (!preg_match ('/^[\w ,!?()]+$/', $title) ) {  
				$title_err = "Title - Only alphabets and whitespace are allowed.";  
			}
		}
		
		if(empty(trim($_POST["author_txt"]))) {
			$author_err = "Please enter an author name";
		} else {
			$author = trim($_POST['author_txt']);
			if (!preg_match ('/^[\w ,!?()]+$/', $author) ) {  
				$author_err = "Author - Only alphabets and whitespace are allowed.";  
			}
		}
		
		if(empty(trim($_POST["publisher_txt"]))) {
			$publisher_err = "Please enter a publisher name";
		} else {
			$publisher = trim($_POST['publisher_txt']);
			if (!preg_match ('/^[\w ,!?()]+$/', $publisher) ) {  
				$publisher_err = "Publisher - Only alphabets and whitespace are allowed.";  
			}
		}
		
		if(empty(trim($_POST["edition_txt"]))) {
			$edition_err = "Please enter an edition";
		} else {
			$edition = trim($_POST['edition_txt']);
			if (!preg_match ('/^[\w ,!?()]+$/', $edition) ) {  
				$edition_err = "Edition - Invalid format.";  
			}
		}
		
		if(empty(trim($_POST["price_txt"]))) {
			$price_err = "Please enter a price";
		} else {
			$price = trim($_POST['price_txt']);
			if (!preg_match ('/^[0-9]+(\.[0-9]{2})?$/', $price) ) {  
				$price_err = "Price - Invalid format.";  
			}
		}
		
		if(empty(trim($_POST["category_txt"]))) {
			$category_err = "Please enter a category";
		} else {
			$category = trim($_POST['category_txt']);
			if (!preg_match ('/^[\w ,!?()]+$/', $category) ) {  
				$category_err = "Category - Only alphabets and whitespace are allowed.";  
			}
		}
		
		if((empty($title_err)) && (empty($author_err)) && (empty($publisher_err)) && (empty($edition_err)) && (empty($price_err)) && (empty($category_err))) {
			//all fields are valid
			$staffID = $_SESSION["id"];
			$action = "New Book Addition";
			$date1 =  date("Y-m-d");
			$date_temp = strtotime(date('Y-m-d'));
			$due_date = date('Y-m-d',strtotime('+15 days',$date_temp));
			$time1 = date("h:i:s");
			
			//we need to get the book_ID of the last book in the BOOK tabel, we need it for the report table
			$get_lastID_Que = "SELECT max(book_ID) last_ID FROM book";
			
			$result = $link->query($get_lastID_Que);
			$row_num = mysqli_num_rows($result);
			
			if($row_num == 1) {
				while($data = $result->fetch_assoc()) {
					$book_ID = intval($data['last_ID']);
					//echo "Last book_ID was ".$book_ID."";
				}
			} else {
				echo "Some error occurred";
			}
			
			$book_ID = $book_ID + 1;
			
			//queries
			//insert into BOOK table
			$insert_bookQue = "INSERT INTO book(title, author, publisher, edition, price, category, staff_ID, availability) VALUES('".$title."', '".$author."', '".$publisher."', '".$edition."', ".$price.", '".$category."', '".$staffID."', 'Available')";
			
			//insert into REPORT table
			$insert_reportQue = "INSERT INTO report(book_ID, action, date, time, staff_ID) VALUES(".$book_ID.", '".$action."', '".$date1."', '".$time1."', '".$staffID."')";
			
			//executing queries
			if ($link->query($insert_bookQue) === TRUE) {
				//echo "New record created successfully";
				//we do nothing here
			} else {
			echo "Error: " . $insert_bookQue . "<br>" . $link->error;
			}
			
			//inserting into READER table
			if ($link->query($insert_reportQue) === TRUE) {
				//echo "New record created successfully";
				//we do nothing here
			} else {
			echo "Error: " . $insert_reportQue . "<br>" . $link->error;
			}
			echo "<script>alert('Book added successfully.');document.location='welcome_staff.php';</script>";
			
		} else {
			echo "<script>alert('Fields are not formatted properly.');</script>";
		}
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
					<p class="font-weight-normal text-muted mb-0">ADMIN</p>
					</div>
				</div>
			</div>
			
			<p class="text-gray font-weight-bold text-uppercase px-3 small pb-4 mb-0">Dashboard</p>
			<ul class="nav flex-column bg-white mb-0">
				<li class="nav-item">
					<a href="welcome_staff.php" class="nav-link bg-dark text-light"><i class="fa fa-th-large mr-3 text-primary fa-fw"></i>home</a>
				</li>
				<li class="nav-item">
					<a href="staffsearch.php" class="nav-link bg-dark text-light"><i class="fas fa-search mr-3 text-primary fa-fw"></i>search book</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link active bg-light text-dark"  id="highlight"><i class="fas fa-plus-square mr-3 text-primary fa-fw"></i>add book</a>
				</li>
				<li class="nav-item">
					<a href="bookcatalogue.php" class="nav-link bg-dark text-light"><i class="fas fa-book-reader mr-3 text-primary fa-fw"></i>Book Catalogue</a>
				</li>
				<li class="nav-item">
					<a href="staffborrow.php" class="nav-link bg-dark text-light"><i class="fas fa-book-open mr-3 text-primary fa-fw"></i>issue book</a>
				</li>
				<li class="nav-item">
					<a href="outstandingpage.php" class="nav-link bg-dark text-light"><i class="fas fa-book mr-3 text-primary fa-fw"></i>outstanding books</a>
				</li>
				<li class="nav-item">
					<a href="newUserReg.php" class="nav-link bg-dark text-light"><i class="fas fa-user-plus mr-3 text-primary fa-fw"></i>register new user</a>
				</li>
			</ul>
			
			<p class="text-gray font-weight-bold text-uppercase px-3 small py-4 mb-0">Charts</p>
			<ul class="nav flex-column bg-white mb-0">
				<li class="nav-item">
					<a href="#" class="nav-link bg-dark text-light"><i class="fas fa-clipboard mr-3 text-primary fa-fw"></i>report</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link bg-dark text-light"><i class="fas fa-chart-bar mr-3 text-primary fa-fw"></i>statistics</a>
				</li>
			</ul>
			
			<p class="text-gray font-weight-bold text-uppercase px-3 small py-4 mb-0"></p>
			<ul class="nav flex-column bg-white mb-0">
				<li class="nav-item">
					<a href="logout_staff.php" class="nav-link bg-dark text-light"><i class="fas fa-sign-out-alt mr-3 text-primary fa-fw"></i>logout</a>
				</li>
			</ul>
		
		</div>
		
		<div class="page-content p-5" id="content">
			<h1>Add New Book</h1></br>
			<form action="" method="post">
				<div class="row mb-3">
				<label class="col-sm-2 col-form-label">Book Title : </label>
				<div class="col-sm-5"><input type="text" class="form-control" name="title_txt"/></div>
				<span class="invalid-feedback"><?php echo $title_err; ?></span>
				</div>
				<div class="row mb-3">
				<label class="col-sm-2 col-form-label">Author :</label>
				<div class="col-sm-5"><input type="text" class="form-control" name="author_txt"/></div>
				<span class="invalid-feedback"><?php echo $author_err; ?></span>
				</div>
				<div class="row mb-3">
				<label class="col-sm-2 col-form-label">Publisher :</label>
				<div class="col-sm-5"><input type="text" class="form-control" name="publisher_txt"/></div>
				<span class="invalid-feedback"><?php echo $publisher_err; ?></span>
				</div>
				<div class="row mb-3">
				<label class="col-sm-2 col-form-label"> Edition :</label>
				<div class="col-sm-5"><input type="text" class="form-control" name="edition_txt"/></div>
				<span class="invalid-feedback"><?php echo $edition_err; ?></span>
				</div>
				<div class="row mb-3">
				<label class="col-sm-2 col-form-label"> Price : </label>
				<div class="col-sm-5"><input type="number" class="form-control" name="price_txt"/></div>
				<span class="invalid-feedback"><?php echo $price_err; ?></span>
				</div>
				<div class="row mb-3">
				<label class="col-sm-2 col-form-label"> Category :</label>
				<div class="col-sm-5"><input type="text" class="form-control" name="category_txt"/></div>
				<span class="invalid-feedback"><?php echo $category_err; ?></span>
				</div>
				<br/>
				<input type="submit" class="btn btn-primary" name="new_book" value="Add Book"/>
				</form>
			
		</div>
</body>
</html>