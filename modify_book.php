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
                            <form action="" method="post">
                            <label> Book Title : 
                            <input type="text" name="title_txt" value="<?php echo $row['title']?>" />
                            <span class="invalid-feedback"><?php echo $title_err; ?></span>
                            </label><br/>
                            <label> Author : 
                            <input type="text" name="author_txt" value="<?php echo $row['author']?>"/>
                            <span class="invalid-feedback"><?php echo $author_err; ?></span>
                            </label><br/>
                            <label> Publisher : 
                            <input type="text" name="publisher_txt" value="<?php echo $row['publisher']?>"/>
                            <span class="invalid-feedback"><?php echo $publisher_err; ?></span>
                            </label><br/>
                            <label> Edition :
                            <input type="text" name="edition_txt" value="<?php echo $row['edition']?>"/>
                            <span class="invalid-feedback"><?php echo $edition_err; ?></span>
                            </label><br/>
                            <label> Price : 
                            <input type="number" name="price_txt" value="<?php echo $row['price']?>"/>
                            <span class="invalid-feedback"><?php echo $price_err; ?></span>
                            </label><br/>
                            <label> Category :
                            <input type="text" name="category_txt" value="<?php echo $row['category']?>"/>
                            <span class="invalid-feedback"><?php echo $category_err; ?></span>
                            </label><br/>
                            <label> Availability :
                            <input type="text" name="availability_txt" value="<?php echo $row['availability']?>"/>
                            <span class="invalid-feedback"><?php echo $availability_err; ?></span>
                            </label><br/>
                            <br/>
                            <input type="submit" name="modify_book" value="Save Changes"/>
                            <input type="submit" name="delete_book" value="Delete Book"/>
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