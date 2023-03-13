<?php
    // WHEN YOU LOGIN TO THE PAGE YOU START THE SESSION
    session_start();
    $setTitle = 'Books';
    // ONLY THE ADMIN AND SUPPLIER CAN GO HERE
    if (isset($_SESSION['USER_NAME']) && ($_SESSION['GROUP_ID'] == 1)) {
        include 'initial.php';

	  $do = (isset($_GET['do'])) ? $_GET['do'] : 'Manage';

        if ($do == 'Manage') { ?>
            <div class="container mt-5">

                <form class="search" action="" method='POST'>
                    <input type="text" name="book_name" placeholder="Search by book name" id="search">
                    <input type="submit" name="search" value="Search" id="button">
                </form>
                
                <a href="?do=Add" class="btn btn-primary mb-3">ADD BOOK</a>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <tr style="background-color: #19283f; color: white">
                            <th>Book_Id</th>
                            <th>Book_Name</th>
                            <th>Price</th>
                            <th>Language</th>
                            <th>Size</th>
                            <th>Pages</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Supplier_Id</th>
                            <th>Inserted_Date</th>
                            <th>Control</th>
                        </tr>
                        <?php 
                        
                        $search = ''; 
                        if(isset($_POST['search'])) {
                            $book_name = $_POST['book_name'];
                            if(!empty($book_name)) {
                                $search = "WHERE PROD_NAME LIKE '%$book_name%'";
                            }
                        }

                        $stmt = $conn->prepare("SELECT * FROM PRODUCTS p JOIN BOOKS b ON p.PROD_ID = b.BOOK_ID $search");
                        $stmt->execute();
                        $rows = $stmt->fetchAll();
                        foreach($rows as $row): ?>
                            <tr >
                                <td><?php echo $row['PROD_ID']; ?></td>
                                <td><?php echo substr($row['PROD_NAME'], 0, 50); ?></td>
                                <td>$<?php echo $row['ITEM_PRICE']; ?></td>
                                <td><?php echo $row['LANG']; ?></td>
                                <td><?php echo $row['FILE_SIZE']; ?>MB</td>
                                <td><?php echo $row['PAGES']; ?></td>
                                <td><?php echo $row['CATEGORY']; ?></td>
                                <?php
                                    $stmt_cat = $conn->prepare("SELECT AUTHOR_NAME FROM AUTHORS WHERE BOOK_ID = ?");
                                    $stmt_cat->execute(array($row['PROD_ID']));
                                    $cat_rows = $stmt_cat->fetchAll();
                                    echo "<td>";
                                    foreach ($cat_rows as $cat_row):
                                        echo $cat_row['AUTHOR_NAME'] . ' '; 
                                    endforeach;
                                    echo "</td>";
                                ?>
                                <td><?php echo $row['SUP_ID']; ?></td>
                                <td><?php echo $row['INTRODUCE_DATE']; ?></td>
                                <td>
                                    <a href="?do=Edit&id=<?php echo $row['PROD_ID'];?>" class="btn" style="background-color: #4eb67f; margin-bottom: 5px">Edit</a>
                                    <a href="?do=Delete&id=<?php echo $row['PROD_ID'];?>" class="btn confirm" style="background-color: #ff6a00">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>               
            </div>
        <?php
        }
        elseif($do == 'Add'){ 
            // CHECK IF COMING FROM REQUEST
            if(isset($_POST['insert'])):
                //  print all the value from the form
                $book_name = $_POST['book_name'];
                $author1 = $_POST['author1'];
                $author2 = $_POST['author2'];
                $category = $_POST['category'];
                $pages = $_POST['pages'];
                $price = $_POST['price'];
                $size = $_POST['size'];
                $language = $_POST['language'];
                $sup_id = $_POST['sup_id'];
                
                $insert_errors = array();
                // IF supplier in the database
                if(checkSup('SUP_ID', 'SUPPLIERS', $sup_id) != -1){
                    $insert_errors['sup'] = "THE SUPPLIER NOT EXISTS";
                }
                if(empty($book_name)){
                    $insert_errors['name'] = "THE NAME NOT EMPTY";
                }
                if(empty($price)){
                    $insert_errors['price'] = "THE PRICE NOT EMPTY";
                }
                if(empty($author1) && empty($author2)){
                    $insert_errors['author'] = "THE AUTHOR NOT EMPTY";
                }
                if(empty($category)){
                    $insert_errors['cat'] = "THE CATEGORY NOT EMPTY";
                }
                if($author1 == $author2){
                    $insert_errors['author2'] = "THE AUTHOR ARE THE SAME";
                }

                if(empty($author1)) $author1 = null;
                if(empty($author2)) $author2 = null;

                if(empty($insert_errors)){
                    try {
                        $proc = $conn->prepare("EXECUTE ADD_BOOK ?, ?, ?, ?, ?, ?, ?, ?, ?");
                        $proc->execute(array($book_name, $price, $sup_id, $language, $pages, $size, $category, $author1, $author2));
                        echo "<script>
                            alert('BOOK ADDED...!');
                            window.open('books.php', '_self');
                            </script>";
                    }
                    catch(Exception $e) {
                        $err =$e->getMessage();
                        echo "<script>
                            alert('$err');
                            window.open('books.php', '_self');
                            </script>";
                    }
                }
            endif;
            ?>
            <h3 class="use-a-lot2 mb-2 mt-5">ADD BOOK</h3>
            <form class="content" action="?do=Add" method="POST" enctype="multipart/form-data">
                <div class="container">
                    <div class="about-book">
                        <div class="image">
                            <img src="<?php echo 'Themes/IMAGES/book.jpg'; ?>" alt="">
                        </div>
                        
                        <div class="info">
                            <div class="title">
                                <label for="">Book Name: </label>
                                <input type="text" name="book_name" placeholder="Book Name">
                                <span class="error">
                                    <?php
                                    if(isset($insert_errors['name'])) echo '*' . $insert_errors['name'];
                                    ?>
                                </span>  
                                <BR>

                                <label for="">Categories:</label>
                                <input type="text" name="category" placeholder="Category"> 
                                <span class="error">
                                    <?php
                                    if(isset($insert_errors['cat'])) echo '*' . $insert_errors['cat'];
                                    ?>
                                </span>                                  
                            </div>

                            <div class="data">
                                <div class="datum">
                                    <div class="datum-desc">
                                        <label for="cat">Author: </label>
                                        <input type="text" name="author1" placeholder="Author One">
                                        <input type="text" name="author2" placeholder="Author Tow">
                                        <span class="error">
                                            <?php
                                            if(isset($insert_errors['author'])) echo '*' . $insert_errors['author'];
                                            if(isset($insert_errors['author2'])) echo '*' . $insert_errors['author2'];
                                            ?>
                                        </span>                                   
                                    </div> 
                                    
                                    <?php
                                    $sup_stmt = $conn->prepare("SELECT * FROM SUPPLIERS");
                                    $sup_stmt->execute();
                                    $suppliers = $sup_stmt->fetchAll();
                                    ?>
                                    <label class="mb-2 mt-2" for="">Supplier Id: </label>

                                    <select class="btn select" name='sup_id' style='border: 1px solid var(--third-color)'>
                                    <?php
                                    foreach ($suppliers as $supplier):
                                        if($supplier['SUP_TYPE'] == 'Company'):
                                            $com_stmt = $conn->prepare("SELECT * FROM COMPANY WHERE SUP_ID = ?");
                                            $com_stmt->execute(array($supplier['SUP_ID']));
                                            $company = $com_stmt->fetch();
                                        ?>
                                            <option value="<?php echo $supplier['SUP_ID']; ?>"> <?php echo $company['COM_NAME']; ?> </option>
                                        <?php

                                        elseif($supplier['SUP_TYPE'] == 'Person'):
                                            $per_stmt = $conn->prepare("SELECT * FROM PERSON WHERE SUP_ID = ?");
                                            $per_stmt->execute(array($supplier['SUP_ID']));
                                            $person = $per_stmt->fetch();
                                            ?>
                                            <option value="<?php echo $supplier['SUP_ID']; ?>"> <?php echo $person['FIRST_NAME'] . ' ' . $person['LAST_NAME']; ?> </option>
                                            <?php
                                        endif;
                                    endforeach;
                                    ?>
                                    </select>
                                    <span class="error">
                                        <?php
                                        if(isset($insert_errors['sup'])) echo '*' . $insert_errors['sup'];
                                        ?>
                                    </span>

                                    <div class="datum-desc">
                                        <label for="pages">Pages:</label>
                                        <input type="number" name="pages" id="pages" placeholder="Pages">                                 
                                    </div> 
                                </div>

                                <div class="datum">
                                    <div class="datum-desc">
                                        <label for="price">Price:</label>
                                        <input type="text" name="price" id="price" placeholder="Price">
                                        <span class="error">
                                            <?php
                                            if(isset($insert_errors['price'])) echo '*' . $insert_errors['price'];
                                            ?>
                                        </span>                                 
                                    </div>
                                    <div class="datum-desc">
                                        <label for="size">Size:</label>
                                        <input type="text" name="size" id="size" placeholder="Size">                                 
                                    </div>
                                    <div class="datum-desc">
                                        <label for="language">Language:</label>
                                        <input type="text" name="language" id="language" placeholder="Language">                                 
                                    </div>                      
                                </div>
                            </div>
                        </div>
                        <input type="submit" value="Add" name="insert">
                    </div>
                </div>
            </form>
        <?php
        }


        // EDIT
        elseif($do == 'Edit'){ 
            $id = (isset($_GET['id'])) &&  is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

            $stmt = $conn->prepare("SELECT * FROM PRODUCTS JOIN BOOKS 
                                    ON PROD_ID = BOOK_ID
                                    WHERE PROD_ID = ?"); // THE variable condition is declare in the begin of the page
            $stmt->execute(array($id));
            $row = $stmt->fetch();

            if ($stmt->rowCount() == -1):
                // CHECK IF COMING FROM REQUEST
                if(isset($_POST['update'])):
                    //  print all the value from the form
                    $book_name = $_POST['book_name'];
                    $author1 = $_POST['author1'];
                    $author2 = $_POST['author2'];
                    $category = $_POST['category'];
                    $pages = $_POST['pages'];
                    $price = $_POST['price'];
                    $size = $_POST['size'];
                    $language = $_POST['language'];
                    $sup_id = $_POST['sup_id'];
                    
                    $insert_errors = array();
                    // IF supplier in the database
                    if(checkSup('SUP_ID', 'SUPPLIERS', $sup_id) != -1){
                        $insert_errors['sup'] = "THE SUPPLIER NOT EXISTS";
                    }
                    if(empty($book_name)){
                        $insert_errors['name'] = "THE NAME NOT EMPTY";
                    }
                    if(empty($price)){
                        $insert_errors['price'] = "THE PRICE NOT EMPTY";
                    }
                    if(empty($author1) && empty($author2)){
                        $insert_errors['author'] = "THE AUTHOR NOT EMPTY";
                    }
                    if(empty($category)){
                        $insert_errors['cat'] = "THE CATEGORY NOT EMPTY";
                    }
                    if($author1 == $author2){
                        $insert_errors['author2'] = "THE AUTHOR ARE THE SAME";
                    }

                    if(empty($author1)) $author1 = null;
                    if(empty($author2)) $author2 = null;

                    if(empty($insert_errors)){
                        try {
                            $proc = $conn->prepare("EXEC UPDATE_BOOK ?, ?, ?, ?, ?, ?, ?, ?, ?, ?");
                            $proc->execute(array($id, $book_name, $price, $sup_id, $language, $pages, $size, $category, $author1, $author2));
                            echo "<script>
                                alert('BOOK UPDATED...!');
                                window.open('books.php?do=Edit&id=$id', '_self');
                                </script>";
                        }
                        catch(Exception $e) {
                            $err = $e->getMessage();
                            echo "<script>
                                alert('$err');
                                window.open('books.php?do=Edit&id=$id', '_self');
                                </script>";
                        }
                    }
                endif;
            ?>
                <h3 class="use-a-lot2 mb-2 mt-5">EDIT BOOK</h3>
                <form class="content" action="?do=Edit&id=<?php echo $id ?>" method="POST" enctype="multipart/form-data">
                    <div class="container">
                        <div class="about-book">
                            <div class="image">
                                <img src="Themes/IMAGES/book.jpg" alt="">
                            </div>
                            
                            <div class="info">
                                <div class="title">
                                    <input type="hidden" name="book_id" value="<?php echo $row['PROD_ID']; ?>">
                                    <label for="">Book Name: </label>
                                    <input type="text" name="book_name" value="<?php echo $row['PROD_NAME']; ?>">
                                    <label for="">Category: </label>
                                    <input type="text" name="category" value="<?php echo $row['CATEGORY']; ?>">
                                </div>

                                <div class="data">
                                    <div class="datum">
                                        <div class="datum-desc">
                                            <label for="cat">Author:</label>
                                            <?php
                                                $stmt_auth = $conn->prepare("SELECT AUTHOR_NAME FROM AUTHORS WHERE BOOK_ID = ?");
                                                $stmt_auth->execute(array($id));
                                                $row_auth = $stmt_auth->fetchAll();
                                            ?>
                                            <input type="text" name="author1" value="<?php if(isset($row_auth[0][0])) echo $row_auth[0][0]?>" id="cat" >                                
                                            <input type='text' name='author2' value='<?php if(isset($row_auth[1][0])) echo $row_auth[1][0]?>' id='cat' >                                
                                        </div> 
                                        
                                            <div class="datum-desc">
                                                <label for="sup">Supplier Id:</label>
                                                <input type="number" name="sup_id" id="sup" value="<?php echo $row['SUP_ID']; ?>">
                                                <span class="error">
                                                    <?php
                                                    if(isset($update_errors['sup'])) echo '*' . $update_errors['sup'];
                                                    ?>
                                                </span>
                                            </div> 

                                        <div class="datum-desc">
                                            <label for="pages">Pages:</label>
                                            <input type="number" name="pages" value="<?php echo $row['PAGES']; ?>" id="pages" >                                 
                                        </div>                             
                                    </div>

                                    <div class="datum">
                                        <div class="datum-desc">
                                            <label for="price">Price:</label>
                                            <input type="text" name="price" value="<?php echo $row['ITEM_PRICE']; ?>" id="price" >                                 
                                        </div>
                                        <div class="datum-desc">
                                            <label for="size">Size:</label>
                                            <input type="text" name="size" value="<?php echo $row['FILE_SIZE']; ?>" id="size" >                                 
                                        </div>
                                        <div class="datum-desc">
                                            <label for="language">Language:</label>
                                            <input type="text" name="language" value="<?php echo $row['LANG']; ?>" id="language" >                                 
                                        </div>                      
                                    </div>
                                </div>
                            </div>
                            <input type="submit" value="Save" name="update">
                        </div>
                    </div>
                </form>

            <?php 
            else:
                $err =$e->getMessage();
                echo "<script>
                    alert('THE BOOK NOT FOUND');
                    window.open('books.php', '_self');
                    </script>";
            endif;
            ?>
        <?php
        }


        elseif($do == 'Delete') {
            $id = (isset($_GET['id'])) &&  is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

            $stmt = $conn->prepare("DELETE FROM PRODUCTS WHERE PROD_ID = ?");
            $stmt->execute(array($id));

            echo "<script>
                alert('" . $stmt->rowcount() . " RECORD DELETED...!');
                window.open('books.php', '_self');
                </script>";
        }



        include $tpl . 'footer.php';
    }
    else {
        header('location: index.php');
        exit();
    }