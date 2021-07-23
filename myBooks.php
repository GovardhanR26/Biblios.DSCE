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
	<title> My Books </title>
	</head>
	<body>
	<?php
	if($result == True) {
		$row_num = mysqli_num_rows($result);
		
		if($row_num > 0) {
			//display in html table
			?>
				<table border="1" cellpadding="10">
					<tr>
						<th> Book ID </th>
						<th> Title </th>
						<th> Author </th>
						<th> Date of Issue </th>
						<th colspan="2"> Due Date </th>
					</tr>
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
							<button type="submit" value="<?php echo $row['book_ID']?>" name="return_button"> Return </button>
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
	<p>
	<a href="welcome_reader.php">Back</a>
	</p>
	</body>
	</html>