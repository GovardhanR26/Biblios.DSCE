<?php
session_start();

date_default_timezone_set("Asia/Calcutta");

	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
		header("location: login_reader.php");
		exit;
	}
	
	require 'connect_db.php';
	
	// checking if any button in the table has been pressed
	if(($_SERVER["REQUEST_METHOD"]=="POST") && (isset($_POST["table_button"]))) {
		$issueID = $_POST["table_button"];
		//echo "You want ".$issueID." ?";
		
		// code paste
		$reader_ID = $_SESSION["id"];
		$book_ID = $issueID;
		$action = "Borrow";
		$date1 =  date("Y-m-d");
		$time1 = date("h:i:s");
		$date_temp = strtotime(date('Y-m-d'));
		$due_date = date('Y-m-d',strtotime('+15 days',$date_temp));
			
		// we should check if the book is available first. if not show message.
		$checkQue = "SELECT availability FROM book where book_ID = ?";
		if($stmt = mysqli_prepare($link, $checkQue)){
				
			mysqli_stmt_bind_param($stmt, "s", $param_bookID);
			$param_bookID = $book_ID;
				
			if(mysqli_stmt_execute($stmt)){
					
				mysqli_stmt_store_result($stmt);
					
				if(mysqli_stmt_num_rows($stmt) == 1) {
						
					mysqli_stmt_bind_result($stmt, $availability);
						
					if(mysqli_stmt_fetch($stmt)){
							
						if($availability == 'Available') {
							//insert into REPORT table
							$insert_reportQue = "INSERT INTO report(reader_ID, book_ID, action, date, time) VALUES('".$reader_ID."','". $book_ID."','".$action."','".$date1."','".$time1."')";
								
							//update 'Available' to 'Borrowed'
							$updateQue = "UPDATE book SET availability='Borrowed' where book_ID='".$book_ID."'";
								
							//insert into BORROWED table
							$insert_borrowQue = "INSERT INTO borrowed VALUES('".$reader_ID."','".$book_ID."','".$date1."','".$due_date."')";
								
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
							echo "<script>alert('Book issued successfully');document.location='homeuser.php'</script>";
							//javascript alert code here
							//header("Location: welcome_reader.php");
							//exit;
						} else if($availability == 'Borrowed') {
							//reserving code here
							$action = "Reserve";
							
							//insert into REPORT table
							$insert_reportQue = "INSERT INTO report(reader_ID, book_ID, action, date, time) VALUES('".$reader_ID."','". $book_ID."','".$action."','".$date1."','".$time1."')";
								
							//update book availability to 'Reserved'
							$updateQue = "UPDATE book SET availability='Reserved' where book_ID='".$book_ID."'";
								
							//insert into RESERVED table
							$insert_reserveQue = "INSERT INTO reserved VALUES('".$reader_ID."','".$book_ID."','".$date1."','".$time1."')";
								
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
							echo "<script>alert('Book reserved successfully');document.location='homeuser.php'</script>";
								
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
					<a href="#" class="nav-link bg-dark text-dark"  id="highlight"><i class="fas fa-search mr-3 text-primary fa-fw"></i>search book</a>
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
		<h1>Search Book</h1><br/>
			<form action="" method="post">
			<div class="input-group">
				<input type="text" id="search_bar"name="search_txt" autocomplete="off" class="form-control" placeholder="Type to search..."/>
				<div class="input-group-append">
				<button class="btn btn-default" name="search_submit" value="Search" type="submit">
				<i class ="fas fa-search"></i>
				</button>
				</div>
				</div><br/>
			</form>
			<?php
		if(($_SERVER["REQUEST_METHOD"]=="POST") && (isset($_POST["search_submit"]))) {
			$search_txt = $_POST["search_txt"];
			// echo "<script>getElementById('search_bar').innerHTML='".$search_txt."';</script>";
			if(!empty($search_txt)) {
				$searchQue = "SELECT * FROM book WHERE title LIKE '%".$search_txt."%' OR author LIKE '%".$search_txt."%'";
			
				$result = $link->query($searchQue);
			
				if($result == True) {
					$row_num = mysqli_num_rows($result);
					if($row_num>0) {
						//display in html table
						?>
						<table class="table table-striped table-bordered">
						<thead class="thead-dark"><tr>
							<th scope=""> Book ID </th>
							<th scope="col"> Title </th>
							<th scope="col"> Author </th>
							<th scope="col" colspan="2"> Availability </th>
						</tr></thead>
						<?php
						while($row=$result->fetch_assoc()) 
						{
						?>
							<tbody><tr>
								<td><?php echo $row['book_ID']?></td>
								<td><?php echo $row['title']?></td>
								<td><?php echo $row['author']?></td>
								<td><?php echo $row['availability']?></td>
								<td><form action="" method="post">
								<button name="table_button" type="submit" class="btn btn-primary" value="<?php echo $row['book_ID']; ?>" <?php if(($row['availability']!='Available')&&($row['availability']!='Borrowed')) {?> disabled <?php } ?>>
								<?php 
									if($row['availability']=='Available') {
										echo "Issue";
									} else if($row['availability']=='Borrowed') {
										echo "Reserve";
									} else if(($row['availability']=='Available_Reserved')|| ($row['availability']=='Reserved')) {
										echo "Reserved";
									} else {
										echo "Unavailable";
									}
								?> 
								</button>
								</form>
								</td>
							</tr>
						<?php
						}
						?>
							</tbody></table>
						<?php
					}
				}
			} else {
				echo "Please enter text";
			}				
		} else {
				//echo "Got false bro";
		}		
	?>
		</div>
</body>
</html>