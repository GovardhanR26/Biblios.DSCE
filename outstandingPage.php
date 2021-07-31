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
	<title> Outstanding Books </title>
	</head>
	<body>
	<?php
	if($result == True) {
		$row_num = mysqli_num_rows($result);
		
		if($row_num>0) {
			//display in html table
			?>
				<table border="1" cellpadding="10">
					<tr>
					<th> Title </th>
					<th> Author </th>
					<th> Issued by</th>
					<th> Reader ID </th>
					<th> Issue Date </th>
					<th> Due Date </th>
					<th> Return </th>
					</tr>
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
								<button type="submit" value="<?php echo $row['bookID']?>" name="return_button"> Return </button>
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
	<p>
	<a href="welcome_staff.php">Back</a>
	</p>
	</body>
	</html>