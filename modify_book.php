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
        echo "<script>alert('Book modified successfully');document.location='bookCatalogue.php'</script>";
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
        echo "<script>alert('Book deleted successfully');document.location='bookCatalogue.php'</script>";
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
                            <title>
                                Modify Book
                            </title>
                        </head>
                        <body>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                                <table>
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
                            <input type="submit" name="modify_book" value="Save Changes"/>
                            <input type="submit" name="delete_book" value="Delete Book"/>
                            <input type="submit" name="cancel" value="Cancel"/>
                            </form>
                            <p>
                                <a href="bookCatalogue.php"> Back to catalogue </a>
                            </p>
                        </body>
                    </html>

                <?php
                        }
            }
        }
    }

?>