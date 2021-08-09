<?php
session_start();
date_default_timezone_set("Asia/Calcutta");

	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
		header("location: login_reader.php");
		exit;
	}
	require 'connect_db.php';
	$userID = $_SESSION["id"];
	
	if(($_SERVER["REQUEST_METHOD"]=="POST") && isset($_POST["cancel_reserve"])) {
		$bookID = $_POST["cancel_reserve"];
		$action = "Cancel Reservation";
		$date1 =  date("Y-m-d");
		$time1 = date("h:i:s");
		$prev_status = "";
		
		//queries 
		//query to check status
			$status_Que = "SELECT availability FROM book WHERE book_ID='".$bookID."'";
			$result = $link->query($status_Que);
			$row = mysqli_num_rows($result); 
			if($row==1) {
				while($data = $result->fetch_assoc()) {
					$prev_status = $data['availability'];
				}
			} else {
				echo "Invalid Book ID";
				header("Location: homeuser.php");
				exit;
			}
			
		//to delete row from RESERVE
		$delete_reserveQue = "DELETE FROM reserved WHERE book_ID='".$bookID."' AND reader_ID='".$userID."'";
		
		//to change availability in BOOK back to 'borrowed' or 'available'
		if($prev_status == 'Reserved') {
			$update_que = "UPDATE book SET availability = 'Borrowed' WHERE book_ID='".$bookID."' AND availability = 'Reserved'";
		} else if($prev_status == 'Available_Reserved') {
			$update_que = "UPDATE book SET availability = 'Available' WHERE book_ID='".$bookID."' AND availability = 'Available_Reserved'";
		}
		
		//to insert into REPORT
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
		
		//deleting from RESERVED table
		if ($link->query($delete_reserveQue) === TRUE) {
			//echo "Deletion successfull";
			//we do nothing here
		} else {
				echo "Error: " . $delete_reserveQue . "<br>" . $link->error;
		}
		
		echo "<script>alert('Reservation cancelled successfully');</script>";
	}
	
	if(($_SERVER["REQUEST_METHOD"]=="POST") && isset($_POST["issue_reserve"])) {
		$bookID = $_POST["issue_reserve"];
		$action = "Borrow from reserved";
		$date1 =  date("Y-m-d");
		$date_temp = strtotime(date('Y-m-d'));
		$due_date = date('Y-m-d',strtotime('+15 days',$date_temp));
		$time1 = date("h:i:s");
		$prev_status = "";
		
		//issuing book code here
		//insert into REPORT table
		$insert_reportQue = "INSERT INTO report(reader_ID, book_ID, action, date, time) VALUES('".$userID."','". $bookID."','".$action."','".$date1."','".$time1."')";
									
		//update 'Reserved' to 'Borrowed'
		$updateQue = "UPDATE book SET availability='Borrowed' where book_ID='".$bookID."'";
									
		//insert into BORROWED table
		$insert_borrowQue = "INSERT INTO borrowed VALUES('".$userID."','".$bookID."','".$date1."','".$due_date."')";
									
		//delete from RESERVE table
		$delete_reserveQue = "DELETE FROM reserved WHERE book_ID='".$bookID."' AND reader_ID='".$userID."'";
									
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
			
		echo "<script>alert('Book issued successfully. You had reserved it earlier');document.location='homeuser.php'</script>";
	}
	
	$reserve_displayQue = "SELECT r.book_ID bookID, b.title title, b.author author, b.availability availability, r.reserve_date reserve_date, r.reserve_time reserve_time FROM reserved r, book b WHERE r.reader_ID='".$userID."' AND r.book_ID=b.book_ID";
	
	$result = $link->query($reserve_displayQue);
	
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
					<a href="#" class="nav-link bg-dark text-dark" id="highlight"><i class="fas fa-book-reader mr-3 text-primary fa-fw"></i>reserved books</a>
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
			<h1>My Reservation</h1><br/>
			<?php
			if($result == True) {
				$row_num = mysqli_num_rows($result);
				
				if($row_num>0) {
					//display in html table
					?>
						<table class="table table-striped table-bordered">
							<thead class="thead-dark">
							<tr>
							<th> Book ID </th>
							<th> Title </th>
							<th> Author </th>
							<th> Date Of Reservation </th>
							<th> Time Of Reservation </th>
							<th></th>
							</tr></thead>
							<?php
								while($row = $result->fetch_assoc())
								{
								?>	<tr>
									<td><?php echo $row['bookID']?></td>
									<td><?php echo $row['title']?></td>
									<td><?php echo $row['author']?></td>
									<td><?php echo $row['reserve_date']?></td>
									<td><?php echo $row['reserve_time']?></td>	
									<td>
									<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
									
									<?php 
										// if the book was reserved, and is now available, we give a issue option
										if($row['availability'] == 'Available_Reserved') 
										{
										?>
											<button name="issue_reserve" class="btn btn-success" style="margin-bottom: 10px;" value="<?php echo $row['bookID'];?>" type="submit"> Issue </button>
										<?php
											
										}
									
									?>
									
									<button name="cancel_reserve" class="btn btn-primary" value="<?php echo $row['bookID'];?>" type="submit"> Cancel </button>
									</form>
									</td>
									</tr>
								<?php
								}
							?>
						</table>
					<?php
				} else {
					echo "No reservations";
				}
			} else {
				echo "No reservations.";
			}
		?>
		</div>
</body>
</html>