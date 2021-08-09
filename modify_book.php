<?php
    // Initialize the session
    session_start();
    date_default_timezone_set("Asia/Calcutta"); 

    // Check if the user is logged in, if not then redirect him to login page
    if(!isset($_SESSION["loggedin_staff"]) || $_SESSION["loggedin_staff"] !== true){
        header("location: login_staff.php");
        exit;
    }

    require 'connect_db.php';
    $userID = $_SESSION["id"];

    $bookID = $_SESSION["mod_bookID"];

    $title_err = $author_err = $publisher_err = $edition_err = $category_err = $price_err = $availability_err = "";

    if(($_SERVER["REQUEST_METHOD"]=="POST") && isset($_POST["modify_book"])) {
        $new_title = $_POST["title_txt"];
        $new_author = $_POST["author_txt"];
        $new_publisher = $_POST["publisher_txt"];
        $new_edition = $_POST["edition_txt"];
        $new_price = $_POST["price_txt"];
        $new_category = $_POST["category_txt"];
        $new_availability = $_POST["availability_txt"];

        //queries
        //update BOOK table
        $updateBook_Que = "UPDATE book SET 
                                title='".$new_title."', 
                                author='".$new_author."', 
                                publisher='".$new_publisher."', 
                                edition='".$new_edition."', 
                                price=".$new_price.", 
                                category='".$new_category."', 
                                availability='".$new_availability."'
                            WHERE book_ID=".$bookID."";

        //executing queries
        //updating the BOOK table
		if ($link->query($updateBook_Que) === TRUE) {
			//echo "Update successfull";
			//we do nothing here
		} else {
			echo "Error: " . $updateBook_Que . "<br>" . $link->error;
		}
        echo "<script>alert('Book modified successfully');document.location='bookcatalogue.php'</script>";
    }

    if(($_SERVER["REQUEST_METHOD"]=="POST") && isset($_POST["delete_book"])) {
        //queries
        //to delete book from BOOK table
        $deleteBook_Que = "DELETE FROM book WHERE book_ID=".$bookID."";

        //executing queries
        //deleting from BOOK table
		if ($link->query($deleteBook_Que) === TRUE) {
			//echo "Delete successfull";
			//we do nothing here
		} else {
			echo "Error: " . $deleteBook_Que . "<br>" . $link->error;
		}
        echo "<script>alert('Book deleted successfully');document.location='bookcatalogue.php'</script>";
    }

    if(($_SERVER["REQUEST_METHOD"]=="POST") && isset($_POST["cancel"])) {
        header("location: bookcatalogue.php");
        exit;
    }

    if($bookID) {
        $selectBook_Que = "SELECT * FROM book WHERE book_ID=".$bookID."";

        $result = $link->query($selectBook_Que);

        if($result == True) {
            $row_num = mysqli_num_rows($result);
            
            if($row_num>0) {
                while($row = $result->fetch_assoc())
						{
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
					<a href="welcome_staff.php" class="nav-link active bg-dark text-light"><i class="fa fa-th-large mr-3 text-primary fa-fw"></i>home</a>
				</li>
				<li class="nav-item">
					<a href="staffsearch.php" class="nav-link bg-dark text-light"><i class="fas fa-search mr-3 text-primary fa-fw"></i>search book</a>
				</li>
				<li class="nav-item">
					<a href="newbook.php" class="nav-link bg-dark text-light"><i class="fas fa-plus-square mr-3 text-primary fa-fw"></i>add book</a>
				</li>
				<li class="nav-item">
					<a href="bookcatalogue.php" class="nav-link bg-dark text-dark" id="highlight"><i class="fas fa-book-reader mr-3 text-primary fa-fw"></i>Book Catalogue</a>
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
			<h1>Modify Books</h1></br>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                                <table class="table">
                                    <tr>
                                        <td><label for="bookID"> Book ID : </label></td>
                                        <td><input id="bookID" type="text" name="id_txt" size="50" value="<?php echo $row['book_ID']?>" disabled/></td>
                                    </tr>
                                    <tr>
                                        <td><label for="title"> Book Title : </label></td>
                                        <td><input id="title" type="text" name="title_txt" size="50" value="<?php echo $row['title']?>" /></td>
                                    </tr>
                                    <tr>
                                        <td><label for="author"> Author : </label></td>
                                        <td><input id="author" type="text" name="author_txt" size="50" value="<?php echo $row['author']?>" /></td>
                                    </tr>
                                    <tr>
                                        <td><label for="publisher"> Publisher : </label></td>
                                        <td><input id="publisher" type="text" name="publisher_txt" size="50" value="<?php echo $row['publisher']?>" /></td>
                                    </tr>
                                    <tr>
                                        <td><label for="edition"> Edition : </label></td>
                                        <td><input id="edition" type="text" name="edition_txt" size="50" value="<?php echo $row['edition']?>" /></td>
                                    </tr>
                                    <tr>
                                        <td><label for="price"> Price : </label></td>
                                        <td><input id="price" type="text" name="price_txt" size="50" value="<?php echo $row['price']?>" /></td>
                                    </tr>
                                    <tr>
                                        <td><label for="category"> Category : </label></td>
                                        <td><input id="category" type="text" name="category_txt" size="50" value="<?php echo $row['category']?>" /></td>
                                    </tr>
                                    <tr>
                                        <td><label for="availability"> Availability : </label></td>
                                        <td><input id="availability" type="text" name="availability_txt" size="50" value="<?php echo $row['availability']?>" /></td>
                                    </tr>
                                </table>
                            <br/>
                            <input type="submit" class="btn btn-success" name="modify_book" value="Save Changes"/>
                            <input type="submit" class="btn btn-danger" name="delete_book" value="Delete Book"/>
                            <input type="submit" class="btn btn-primary" name="cancel" value="Cancel"/>
                            </form>
						</div>
					</body>
					</html>
				 <?php
                        }
            }
        }
    }

?>