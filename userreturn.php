<?php
	
session_start();	
date_default_timezone_set("Asia/Calcutta");

	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
		header("location: logintest.php");
		exit;
	}
	
	require 'connect_db.php';
	$userID = $_SESSION["id"];
	
	if(($_SERVER["REQUEST_METHOD"]=="POST") && isset($_POST["return_button"])) {
		$bookID = $_POST["return_button"];
		$action = "Return";
		$date1 =  date("Y-m-d");
		$time1 = date("h:i:s");
		$prev_status = "";
		
		//queries 
		//query to check status
			$status_Que = "SELECT availability FROM book WHERE book_ID=".$bookID."";
			$result = $link->query($status_Que);
			if($result) {
				$row_num = mysqli_num_rows($result);

				if($row_num == 1) {
					while($data = $result->fetch_assoc()) {
						$prev_status = $data['availability'];
					}
				}
			} else {
				echo "Invalid Book ID";
				//header("Location: returnPage.php");
				exit;
			}
			
		//to delete row from BORROWED
		$delete_borrowedQue = "DELETE FROM borrowed WHERE book_ID='".$bookID."' AND reader_ID='".$userID."'";
		
		//query to update BOOK table. To change status as per the requirement
		if($prev_status == 'Borrowed') {
			$update_que = "UPDATE book SET availability = 'Available' WHERE book_ID='".$bookID."' AND availability = 'Borrowed'";
		} else if($prev_status == 'Reserved') {
			$update_que = "UPDATE book SET availability = 'Available_Reserved' WHERE book_ID='".$bookID."' AND availability = 'Reserved'";
		}
		
		//query to insert row into REPORT
		$insert_reportQue = "INSERT INTO report(reader_ID, book_ID, action, date, time) VALUES('".$userID."','". $bookID."','".$action."','".$date1."','".$time1."')";
			
		//queries execution
		//inserting into REPORT table
		if ($link->query($insert_reportQue) === TRUE) {
			//echo "New record created successfully";
			//we do nothing here
		} else {
			echo "Error: " . $insert_reportQue . "<br>" . $link->error;
		}
			
		//updating the BOOK table
		if ($link->query($update_que) === TRUE) {
			//echo "Update successfull";
			//we do nothing here
		} else {
			echo "Error: " . $update_que . "<br>" . $link->error;
		}
			
		//deleting from BORROWED table
		if ($link->query($delete_borrowedQue) === TRUE) {
			//echo "Deletion successfull";
			//we do nothing here
		} else {
			echo "Error: " . $delete_borrowedQue . "<br>" . $link->error;
		}
		
		echo "<script>alert('Book returned successfully');</script>";
	}
	
	$display_booksQue = "SELECT b.book_ID book_ID, b.title title, b.author author, bor.reserve_date issue_date, bor.due_date due_date FROM book b, borrowed bor WHERE b.book_ID=bor.book_ID AND bor.reader_ID = '".$userID."'";
	
	$result = $link->query($display_booksQue);	
	?>

<html>
<head>
<title>Biblio@DSCE</title>
	
	<script src="https://kit.fontawesome.com/5d3eee0a99.js" crossorigin="anonymous"></script>
	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	
	
	<link rel="stylesheet" type="text/css" href="homeu.css">
	<style>
		.page-content td{
			vertical-align:middle;
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
					<p class="font-weight-normal text-muted mb-0">STUDENT</p>
					</div>
				</div>
			</div>
			
			<p class="text-gray font-weight-bold text-uppercase px-3 small pb-4 mb-0">Dashboard</p>
			<ul class="nav flex-column bg-white mb-0">
				<li class="nav-item">
					<a href="homeuser.php" class="nav-link active bg-dark text-light"><i class="fa fa-th-large mr-3 text-primary fa-fw"></i>home</a>
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
					<a href="#" class="nav-link bg-dark text-dark" id="highlight"><i class="fas fa-book mr-3 text-primary fa-fw"></i>return book</a>
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
			<h1>Return Book</h1><br/>
			<?php
	if($result == True) {
		$row_num = mysqli_num_rows($result);
		
		if($row_num > 0) {
			//display in html table
			?>
				<table class="table table-striped table-bordered">
					<thead class="thead-dark"><tr>
						<th> Book ID </th>
						<th> Title </th>
						<th> Author </th>
						<th> Date of Issue </th>
						<th colspan="2"> Due Date </th>
					</tr></thead>
					<?php while($row = $result->fetch_assoc())
							{
					?>
						<tr>
							<td><?php echo $row['book_ID']?></td>
							<td><?php echo $row['title']?></td>
							<td><?php echo $row['author']?></td>
							<td><?php echo $row['issue_date']?></td>
							<td><?php echo $row['due_date']?></td>
							<td><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
							<button type="submit" class="btn btn-primary" value="<?php echo $row['book_ID']?>" name="return_button"> Return </button>
							</form>
							</td>
						</tr>
					<?php
							}					
					?>
				</table>
			<?php
		} else {
			echo "You have no books borrowed currently.";
		}
	} else {
		echo "You have no books borrowed currently";
	}
?>
		</div>
</body>
</html>