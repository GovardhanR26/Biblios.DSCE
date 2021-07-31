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
			$author_err = "Please enter a first name";
		} else {
			$author = trim($_POST['author_txt']);
			if (!preg_match ('/^[\w ,!?()]+$/', $author) ) {  
				$author_err = "Author - Only alphabets and whitespace are allowed.";  
			}
		}
		
		if(empty(trim($_POST["publisher_txt"]))) {
			$publisher_err = "Please enter a first name";
		} else {
			$publisher = trim($_POST['publisher_txt']);
			if (!preg_match ('/^[\w ,!?()]+$/', $publisher) ) {  
				$publisher_err = "Publisher - Only alphabets and whitespace are allowed.";  
			}
		}
		
		if(empty(trim($_POST["edition_txt"]))) {
			$edition_err = "Please enter a first name";
		} else {
			$edition = trim($_POST['edition_txt']);
			if (!preg_match ('/^[\w ,!?()]+$/', $edition) ) {  
				$edition_err = "Edition - Invalid format.";  
			}
		}
		
		if(empty(trim($_POST["price_txt"]))) {
			$price_err = "Please enter a first name";
		} else {
			$price = trim($_POST['price_txt']);
			if (!preg_match ('/^[0-9]+(\.[0-9]{2})?$/', $price) ) {  
				$price_err = "Price - Invalid format.";  
			}
		}
		
		if(empty(trim($_POST["category_txt"]))) {
			$category_err = "Please enter a first name";
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
<html>
<head>
	<title> New Book </title>
</head>
<body>
	<form action="" method="post">
	<label> Book Title : 
	<input type="text" name="title_txt"/>
	<span class="invalid-feedback"><?php echo $title_err; ?></span>
	</label><br/>
	<label> Author : 
	<input type="text" name="author_txt"/>
	<span class="invalid-feedback"><?php echo $author_err; ?></span>
	</label><br/>
	<label> Publisher : 
	<input type="text" name="publisher_txt"/>
	<span class="invalid-feedback"><?php echo $publisher_err; ?></span>
	</label><br/>
	<label> Edition :
	<input type="text" name="edition_txt"/>
	<span class="invalid-feedback"><?php echo $edition_err; ?></span>
	</label><br/>
	<label> Price : 
	<input type="number" name="price_txt"/>
	<span class="invalid-feedback"><?php echo $price_err; ?></span>
	</label><br/>
	<label> Category :
	<input type="text" name="category_txt"/>
	<span class="invalid-feedback"><?php echo $category_err; ?></span>
	</label><br/>
	<br/>
	<input type="submit" name="new_book" value="Add Book"/>
	</form>
	<p>
		<a href="welcome_staff.php">Back</a>
	</p>