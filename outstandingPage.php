<?php
session_start();
date_default_timezone_set("Asia/Calcutta");

	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin_staff"]) || $_SESSION["loggedin_staff"] !== true) {
		header("location: login_staff.php");
		exit;
	}
	require 'connect_db.php';
	$StaffID = $_SESSION["id"];
	
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
		$delete_borrowedQue = "DELETE FROM borrowed WHERE book_ID='".$bookID."'";
		
		//query to update BOOK table. To change status as per the requirement
		if($prev_status == 'Borrowed') {
			$update_que = "UPDATE book SET availability = 'Available' WHERE book_ID='".$bookID."' AND availability = 'Borrowed'";
		} else if($prev_status == 'Reserved') {
			$update_que = "UPDATE book SET availability = 'Available_Reserved' WHERE book_ID='".$bookID."' AND availability = 'Reserved'";
		}
		
		//query to insert row into REPORT
		$insert_reportQue = "INSERT INTO report(staff_ID, book_ID, action, date, time) VALUES('".$StaffID."','". $bookID."','".$action."','".$date1."','".$time1."')";
			
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
	
	//$reserve_displayQue = "SELECT r.book_ID bookID, b.title title, b.author author, b.availability availability, r.reserve_date reserve_date, r.reserve_time reserve_time FROM reserved r, book b WHERE r.reader_ID='".$userID."' AND r.book_ID=b.book_ID";
	
	$outstand_displayQue = "SELECT b.book_ID bookID, b.title title, b.author author, r.fname name, r.reader_ID readerID, bor.reserve_date issueDate, bor.due_date dueDate from book b, reader r, borrowed bor WHERE bor.book_ID=b.book_ID AND r.reader_ID=bor.reader_ID";
	
	$result = $link->query($outstand_displayQue);
	
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
					<a href="newbook.php" class="nav-link active bg-dark text-light"><i class="fas fa-plus-square mr-3 text-primary fa-fw"></i>add book</a>
				</li>
				<li class="nav-item">
					<a href="bookcatalogue.php" class="nav-link bg-dark text-light"><i class="fas fa-book-reader mr-3 text-primary fa-fw"></i>Book Catalogue</a>
				</li>
				<li class="nav-item">
					<a href="staffborrow.php" class="nav-link bg-dark text-light"><i class="fas fa-book-open mr-3 text-primary fa-fw"></i>issue book</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link bg-dark text-dark"  id="highlight"><i class="fas fa-book mr-3 text-primary fa-fw"></i>outstanding books</a>
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
			<h1>Outstanding Books</h1><br/>
			
			<?php
				if($result == True) {
					$row_num = mysqli_num_rows($result);
					
					if($row_num>0) {
						//display in html table
						?>
							<table class="table table-striped table-bordered">
								<thead class="thead-dark">
								<tr>
								<th> Title </th>
								<th> Author </th>
								<th> Issued by</th>
								<th> Reader ID </th>
								<th> Issue Date </th>
								<th> Due Date </th>
								<th> Return </th>
								</tr></thead>
								<?php
									while($row = $result->fetch_assoc())
									{
									?>	<tr>
										<td><?php echo $row['title']?></td>
										<td><?php echo $row['author']?></td>
										<td><?php echo $row['name']?></td>
										<td><?php echo $row['readerID']?></td>
										<td><?php echo $row['issueDate']?></td>	
										<td><?php echo $row['dueDate']?></td>	
										<td>
											<!-- Return button here -->
											<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
											<button type="submit" class="btn btn-primary" value="<?php echo $row['bookID']?>" name="return_button"> Return </button>
											</form>
										</td>
										<!--<td>
										<form action="<?php //echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
										
										<?php 
											// if the book was reserved, and is now available, we give a issue option
											//if($row['availability'] == 'Available_Reserved') 
											//{
											?>
												<button name="issue_reserve" value="<?php //echo $row['bookID'];?>" type="submit"> Issue </button>
											<?php
												
											//}
										
										?>
										
										<button name="cancel_reserve" value="<?php //echo $row['bookID'];?>" type="submit"> Cancel </button>
										</form>
										</td>-->
										</tr>
									<?php
									}
								?>
							</table>
						<?php
					} else {
						echo "No outstanding books.";
					}
				} else {
					echo "No outstanding books.";
				}
			?>
		
		
		</div>
</body>
</html>