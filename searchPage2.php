<?php
session_start();
date_default_timezone_set("Asia/Calcutta");

	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
		header("location: login_reader.php");
		exit;
	}
	require 'connect_db.php';
	
	//variable to make sure that the page is regenerated
	$_SESSION["search_word"] = "";
	
	if(($_SERVER["REQUEST_METHOD"]=="POST") && (isset($_POST["book1"]))) {
		$issueID = $_POST["book1"];
		echo "You want ".$issueID." ?";
		$_SESSION["regen_page"] = 1;
		
		//code paste
		
			$readerID = $_SESSION["id"];
			$book_ID = $issueID;
			$action = "Borrow";
			$date1 =  date("Y-m-d");
			$time1 = date("h:i:s");
			$date_temp = strtotime(date('Y-m-d'));
			$due_date = date('Y-m-d',strtotime('+15 days',$date_temp));
			
			// we should check if the book is available first. if not show message.
			$checkQue = "SELECT status FROM book where book_ID = ?";
			if($stmt = mysqli_prepare($link, $checkQue)){
				
				mysqli_stmt_bind_param($stmt, "s", $param_bookID);
				$param_bookID = $book_ID;
				
				if(mysqli_stmt_execute($stmt)){
					
					mysqli_stmt_store_result($stmt);
					
					if(mysqli_stmt_num_rows($stmt) == 1) {
						
						mysqli_stmt_bind_result($stmt, $status);
						
						if(mysqli_stmt_fetch($stmt)){
							
							if($status == 'Available') {
								//insert into REPORT table
								$insert_reportQue = "INSERT INTO report(reader_ID, book_ID, action, date, time) VALUES('".$readerID."','". $book_ID."','".$action."','".$date1."','".$time1."')";
								
								//update 'Available' to 'Borrowed'
								$updateQue = "UPDATE book SET status='Borrowed' where book_ID='".$book_ID."'";
								
								//insert into BORROWED table
								$insert_borrowQue = "INSERT INTO borrowed VALUES('".$readerID."','".$book_ID."','".$date1."','".$due_date."')";
								
								//select email_id of reader
								//$email_Que = "SELECT ";
								
								//execute queries
								//inserting into BORROWED table
								if ($link->query($insert_borrowQue) === TRUE) {
									//echo "New record created successfully";
									//we do nothing here
								} else {
									echo "Error: " . $insert_reserveQue . "<br>" . $link->error;
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
								echo "<script>alert('Book issued successfully');document.location='welcome_reader.php'</script>";
								//javascript alert code here
								//header("Location: welcome_reader.php");
								//exit;
							} else if($status == 'Borrowed') {
								//reserving code here
								$action = "Reserve";
								
								//insert into REPORT table
								$insert_reportQue = "INSERT INTO report(userID, bookID, action, date, time) VALUES('".$userID."','". $bookID."','".$action."','".$date1."','".$time1."')";
								
								//update book status to 'Reserved'
								$updateQue = "UPDATE book SET status='Reserved' where bookID='".$bookID."'";
								
								//insert into RESERVE table
								$insert_reserveQue = "INSERT INTO reserve VALUES('".$userID."','".$bookID."','".$date1."','".$time1."')";
								
								//execute queries
								//inserting into BORROWED table
								if ($link->query($insert_reserveQue) === TRUE) {
									//echo "New record created successfully";
									//we do nothing here
								} else {
									echo "Error: " . $insert_reserveQue . "<br>" . $link->error;
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
								echo "<script>alert('Book reserved successfully');document.location='welcome_reader.php'</script>";
								
							} else {
								//if book is already reserved
								echo "Book is temporarily unavailable<br/>";
								$issue_err = "Book is temporarily unavailable";
							}
						}
					} else {
						echo "Book not found <br/>";
						$issue_err = "Book not found";
					}
				}
			}
		
		//pasted code end
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>Search page in php</title>
		<style>
			table {
				margin: 0 auto;
				font-size: large;
				border: 1px solid black;
				white-space: nowrap;
			}

			h1 {
				text-align: center;
				color: #006600;
				font-size: xx-large;
				font-family: 'Gill Sans', 'Gill Sans MT',
				' Calibri', 'Trebuchet MS', 'sans-serif';
			}

			td {
				background-color: #E4F5D4;
				border: 1px solid black;
			}

			th, td {
				font-weight: bold;
				border: 1px solid black;
				padding: 20px;
				text-align: center;
			}
			td {
				font-weight: lighter;
			}
		</style>
	</head>

	<body>

	<form method="post" id="frm" name="frm" action="">
	<label>Enter keyword : 
	<input type="text" name="searchBut" id="searchBut"/>
	</label>
	<input type="submit" name="Submit" value="Search"/>
	</form>

	<?php

	if(($_SERVER["REQUEST_METHOD"]=="POST") && (isset($_POST['Submit'])))
	{	
		//$_SESSION["search_word"] = $_REQUEST[searchBut];
		if(!empty($_POST['searchBut'])) {
			$logQu = "SELECT * FROM book WHERE title LIKE '%".$_POST['searchBut']."%' OR author LIKE '%".$_POST['searchBut']."%'";
			
			$logQu2 = "SELECT * FROM book b WHERE b.title LIKE '%".$_POST['searchBut']."%' OR b.author LIKE '%".$_POST['searchBut']."%' WHERE NOT EXISTS (SELECT * FROM reserve r WHERE (b.title like '%".$_POST['searchBut']."%' OR b.author like '%".$_POST['searchBut']."%') AND r.userID='".$_SESSION["id"]."' AND r.bookID=b.bookID)";
			
			$logQu3 = "SELECT * FROM book b, reserve r WHERE b.title LIKE '%".$_POST['searchBut']."%' OR b.author LIKE '%".$_POST['searchBut']."%' LEFT JOIN book as b2 WHERE (b2.title like '%".$_POST['searchBut']."%' OR b2.author like '%".$_POST['searchBut']."%') AND r.userID='".$_SESSION["id"]."' AND r.bookID=b2.bookID";
			
			$logQu4 = "SELECT * FROM book WHERE title LIKE '%".$_POST['searchBut']."%' OR author LIKE '%".$_POST['searchBut']."%' WHERE bookID NOT IN (SELECT b.bookID from book b, reserve r WHERE r.bookID=b.bookID AND r.userID='".$_SESSION["id"]."' AND (b.title LIKE '%".$_POST['searchBut']."%' OR b.author LIKE '%".$_POST['searchBut']."%'))";
			
			$newQue = "SELECT * FROM book b, reserve r WHERE (b.title like '%".$_POST['searchBut']."%' OR b.author like '%".$_POST['searchBut']."%') AND r.userID='".$_SESSION["id"]."' AND r.bookID=b.bookID";
			
			$result = $link->query($logQu);	
			
			$result2 = $link->query($newQue);
			
			//if(result == True)
			$row = mysqli_num_rows($result); 
			
			if($row>=1) {
	?>
	<br/>
	<br/>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	<table width="500" border="1">
	  <tr>
			<td>Book ID</td>
			<td>Title</td>
			<td>Author</td>
			<td colspan="2">Availability</td>
	  </tr>
	  <?php // LOOP TILL END OF DATA
					if($result2 == True) {
						$row2 = mysqli_num_rows($result2);
						if($row2>=1) {
							while($rows2=$result2->fetch_assoc())
							{
				?>	
							<td><?php echo $rows2['bookID'];?></td>
							<td><?php echo $rows2['title'];?></td>
							<td><?php echo $rows2['author'];?></td>
							<td><?php echo $rows2['status'];?></td>
							<td><form action="" method="post">
							<button name="res_issue" value="<?php echo $rows2['bookID']?>" type="submit" <?php if($rows2['status']!='Available_Reserved') {?> disabled <?php } ?>>
							<?php
								if($rows2['status']=='Available_Reserved') {
									echo "Issue";
								} else if($rows2['status']=='Reserved') {
									echo "Unavailable";
								}
							?>
							</button>
							</form>
							</td>
				<?php
							}
						}
					}
					while($rows=$result->fetch_assoc())
					{
				?>
				<tr>
					<!--FETCHING DATA FROM EACH
						ROW OF EVERY COLUMN-->
					<td><?php echo $rows['bookID'];?></td>
					<td><?php echo $rows['title'];?></td>
					<td><?php echo $rows['author'];?></td>
					<td><?php echo $rows['status'];?></td>
					<!--td><input type="radio" name="books" value="<?php //echo $rows['bookID'];?>"/></td-->
					<td><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<button name="book1" value="<?php echo $rows['bookID'];?>" type="submit" <?php if(($rows['status']== 'Available_Reserved')||($rows['status']== 'Reserved')){?> disabled <?php }?>>
											<?php //this is to display "Available" or "Unavailable"
												if($rows['status']=='Available') {
													echo "Issue";
												}
												else if($rows['status']=='Borrowed'){
													echo "Reserve";
												}
												else {
													//check if this user had reserved the book
													//$check_reserveQue = "SELECT * FROM reserve WHERE bookID='".$rows['bookID']."' AND userID='".$_SESSION["id"]."'";
													//$result = $link->query($check_reserveQue);
								
													//if ($result->num_rows > 0) {
													//	echo "Issue";
													//} else {								
														echo "Unavailable";
													//}
												}
											?>
				</button></form></td>
				</tr>
				<?php
					}
				?>
	</table>
	<!--input type="Submit" name="books_submit" value="Issue"/-->
	<?php
			} else {
	?>
		<p>No Results found</p> 
	<?php
			}
		} else {
			?>
			<p>Please enter text to search</p>
	<?php
		}
	}
	/*if(isset($_POST["books_submit"])) {
		if(isset($_POST["books"])) {
			$bookid_value = $_POST["books"];
			echo "<br/>Book ID is ".$bookid_value."<br/>";
		} else {
			echo "<br/>No book is selected<br/>";
		}
	}*/
	?>
	<p>
		<a href="welcome_reader.php">Back</a>
	</p>
	</body>

</html>