<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin_staff"]) || $_SESSION["loggedin_staff"] !== true){
    header("location: login_reader.php");
    exit;
}
	$issue_err = "";
	require 'connect_db.php';	
	
	$issueID = $reader_ID = "";
	
	if($_SERVER["REQUEST_METHOD"]=="POST") {
		$issueID = test_input($_POST["issueBookID"]);
		$reader_ID = test_input($_POST["issueReaderID"]);
		
		if($issueID==NULL) {
			echo "<br>Please fill in all the fields<br>";
		}
		else {
			require 'connect_db.php';
			$staffID = $_SESSION["id"];
			$bookID = $issueID;
			$action = "Borrow";
			$date1 =  date("Y-m-d");
			$time1 = date("h:i:s");
			$date_temp = strtotime(date('Y-m-d'));
			$due_date = date('Y-m-d',strtotime('+15 days',$date_temp));
			
			// we should check if the book is available first. if not show message.
			$checkQue = "SELECT availability FROM book where book_ID = ?";
			if($stmt = mysqli_prepare($link, $checkQue)){
				
				mysqli_stmt_bind_param($stmt, "s", $param_bookID);
				$param_bookID = $bookID;
				
				if(mysqli_stmt_execute($stmt)){
					
					mysqli_stmt_store_result($stmt);
					
					if(mysqli_stmt_num_rows($stmt) == 1) {
						
						mysqli_stmt_bind_result($stmt, $status);
						
						if(mysqli_stmt_fetch($stmt)){
							
							if($status == 'Available') {
								//insert into REPORT table
								$insert_reportQue = "INSERT INTO report(reader_ID, book_ID, action, date, time, staff_ID) VALUES('".$reader_ID."','". $bookID."','".$action."','".$date1."','".$time1."','".$staffID."')";
								
								//update 'Available' to 'Borrowed'
								$updateQue = "UPDATE book SET availability='Borrowed' where book_ID='".$bookID."'";
								
								//insert into BORROWED table
								$insert_borrowQue = "INSERT INTO borrowed VALUES('".$reader_ID."','".$bookID."','".$date1."','".$due_date."')";
								
								//select email_id of reader
								$email_Que = "SELECT ";
								
								//execute queries
								//inserting into BORROWED table
								if ($link->query($insert_borrowQue) === TRUE) {
									//echo "New record created successfully";
									//we do nothing here
								} else {
									echo "Error: " . $insert_borrowQue . "<br>" . $link->error;
								}
								
								//inserting into REPORT table
								if ($link->query($insert_reportQue) === TRUE) {
									//echo "New record created successfully";
									//we do nothing here
								} else {
									echo "Error: " . $insert_reportQue . "<br>" . $link->error;
								}
								
								//updating the BOOK table
								if ($link->query($updateQue) === TRUE) {
									//echo "New record created successfully";
									//we do nothing here
								} else {
									echo "Error: " . $updateQue . "<br>" . $link->error;
								}
								echo "<script>alert('Book issued successfully');document.location='welcome_staff.php'</script>";
								
							} else if($status == 'Available_Reserved') {
								//check if this is the user who reserved the book
								$check_reserveQue = "SELECT * FROM reserved WHERE bookID='".$bookID."' AND reader_ID='".$reader_ID."'";
								
								$result = $link->query($check_reserveQue);
								
								if ($result->num_rows > 0) {
									// this user did reserve the book. we need to issue the book to him
									//insert into REPORT table
									$insert_reportQue = "INSERT INTO report(reader_ID, book_ID, action, date, time, staff_ID) VALUES('".$reader_ID."','". $bookID."','".$action."','".$date1."','".$time1."','".$staffID."')";
									
									//update 'Reserved' to 'Borrowed'
									$updateQue = "UPDATE book SET availability='Borrowed' where book_ID='".$bookID."'";
									
									//insert into BORROWED table
									$insert_borrowQue = "INSERT INTO borrowed VALUES('".$reader_ID."','".$bookID."','".$date1."','".$due_date."')";
									
									//delete from RESERVE table
									$delete_reserveQue = "DELETE FROM reserved WHERE bookID='".$bookID."' AND reader_ID='".$reader_ID."'";
									
									//select email_id of reader
									$email_Que = "SELECT ";
									
									//execute queries
									//inserting into BORROWED table
									if ($link->query($insert_borrowQue) === TRUE) {
										//echo "New record created successfully";
										//we do nothing here
									} else {
										echo "Error: " . $insert_borrowQue . "<br>" . $link->error;
									}
									
									//inserting into REPORT table
									if ($link->query($insert_reportQue) === TRUE) {
										//echo "New record created successfully";
										//we do nothing here
									} else {
										echo "Error: " . $insert_reportQue . "<br>" . $link->error;
									}
									
									//updating the BOOK table
									if ($link->query($updateQue) === TRUE) {
										//echo "New record created successfully";
										//we do nothing here
									} else {
										echo "Error: " . $updateQue . "<br>" . $link->error;
									}
									
									//deleting from RESERVE table 
									if ($link->query($delete_reserveQue) === TRUE) {
										//echo "New record created successfully";
										//we do nothing here
									} else {
										echo "Error: " . $delete_reserveQue . "<br>" . $link->error;
									}
									
									echo "<script>alert('Book issued successfully. This reader had reserved it earlier');document.location='welcome_staff.php'</script>";
									
								} else {
									//this user did not reserve the book
									echo "<script>alert('Book is temporarily unavailable');</script>";
									$issue_err = "Book is temporarily unavailable";								  
								}
							}	else {
								echo "<script>alert('Book is temporarily unavailable');</script>";
								$issue_err = "Book is temporarily unavailable";
							}
						}
					} else {
						echo "<script>alert('Book not found');</script>";
						$issue_err = "Book not found";
					}
				}
			}
		}
	}
	
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
?>


<html>
<head>
<title>Biblio@DSCE</title>
	
	<script src="https://kit.fontawesome.com/5d3eee0a99.js" crossorigin="anonymous"></script>
	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	
	
	<link rel="stylesheet" type="text/css" href="homeu.css">
	<style type="text/css">
		.page-content .partitiona{
			width:50%;
			float:left;
			padding: 10px 30px;
			/* border-right: 1px solid gray; */
		}
		.page-content .partitionb{
			width:50%;
			float:right;
			padding: 10px 30px;
		}
	</style>
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
					<a href="welcome_staff.php" class="nav-link active bg-dark text-light"><i class="fa fa-th-large mr-3 text-primary fa-fw"></i>home</a>
				</li>
				<li class="nav-item">
					<a href="staffsearch.php" class="nav-link bg-dark text-light"><i class="fas fa-search mr-3 text-primary fa-fw"></i>search book</a>
				</li>
				<li class="nav-item">
					<a href="newbook.php" class="nav-link bg-dark text-light"><i class="fas fa-plus-square mr-3 text-primary fa-fw"></i>add book</a>
				</li>
				<li class="nav-item">
					<a href="bookcatalogue.php" class="nav-link bg-dark text-light"><i class="fas fa-book-reader mr-3 text-primary fa-fw"></i>Book Catalogue</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link bg-dark text-dark"  id="highlight"><i class="fas fa-book-open mr-3 text-primary fa-fw"></i>issue book</a>
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
		<h1>Issue Book</h1><br/>
			<div class="partition">
			<?php 
				if(!empty($login_err)){
					echo '' . $issue_err . '<br/>';
				}
			?>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="mb-3">
				<label for="exampleFormControlInput1" class="form-label">Book ID</label>
				<input type="number" class="form-control" id="exampleFormControlInput1" placeholder="Enter Book ID" id="issueID" name="issueBookID" required>
				<br/><label for="exampleFormControlInput1" class="form-label">Reader ID</label>
				<input type="number" class="form-control" id="exampleFormControlInput1" placeholder="Enter Reader ID" id="readerID" name="issueReaderID" required>
				</div>
				<button type="submit" class="btn btn-primary" name="issueSubmit" value="Issue">Issue Book</button>
			</form>
			</div>
		</div>
</body>
</html>