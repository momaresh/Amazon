<?php
    // WHEN YOU LOGIN TO THE PAGE YOU START THE SESSION
    session_start();
    $setTitle = 'Computers';
    //THIS IF YOU ALREADY SIGN WILL CHANGE YOU TO THE DASHBOARD AUTOMATIC
    if (isset($_SESSION['USER_NAME']) && ($_SESSION['GROUP_ID'] == 1)) {
        include 'initial.php';
    
        // WE MAKE THIS BECAUSE THE SUPPLIER AND ADMIN WILL USE THIS PAGE TO SHOW THE COMPUTERS
        // IF THE USER IS SUPPLIER IT WILL SHOW THE COMPUTER THAT HE HAS SUPPLIED ONLY
    

        $do = (isset($_GET['do'])) ? $_GET['do'] : 'Manage';

        if ($do == 'Manage') { ?>
            <div class="container mt-5">
                
                <form class="search" action="" method='POST'>
                    <input type="text" name="computer_name" placeholder="Search by computer name" id="search">
                    <input type="submit" name="search" value="Search" id="button">
                </form>

                <a href="?do=Add" class="btn btn-primary mb-2">ADD COMPUTER</a>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <tr style="background-color: #19283f; color: white">
                            <th>Computer_Id</th>
                            <th>Computer_Name</th>
                            <th>Price</th>
                            <th>Brand</th>
                            <th>Color</th>
                            <th>Screen_Size</th>
                            <th>Storage_Size</th>
                            <th>Storage_Type</th>
                            <th>OS</th>
                            <th>Ram_Size</th>
                            <th>Graphic_Brand</th>
                            <th>Graphic_Size</th>
                            <th>Control</th>
                        </tr>
                        <?php 

                        $search = ''; 
                        if(isset($_POST['search'])) {
                            $computer_name = $_POST['computer_name'];
                            if(!empty($computer_name)) {
                                $search = "WHERE PROD_NAME LIKE '%$computer_name%'";
                            }
                        }

                        $stmt = $conn->prepare("SELECT * FROM PRODUCTS P JOIN ELECTRONICS E ON P.PROD_ID = E.ELEC_ID JOIN COMPUTERS C ON E.ELEC_ID = C.COMPUTER_ID $search");
                        $stmt->execute();
                        $rows = $stmt->fetchAll();
                        foreach($rows as $row): ?>
                            <tr >
                                <td><?php echo $row['PROD_ID']; ?></td>
                                <td><?php echo substr($row['PROD_NAME'], 0, 50); ?></td>
                                <td>$<?php echo $row['ITEM_PRICE']; ?></td>
                                <td><?php echo $row['BRAND']; ?></td>
                                <td><?php echo $row['COLOR']; ?></td>
                                <td><?php echo $row['SCREEN_SIZE']; ?></td>
                                <td><?php echo $row['STORAGE_SIZE']; ?></td>
                                <td><?php echo $row['STORAGE_TYPE']; ?></td>
                                <td><?php echo $row['OS']; ?></td>
                                <td><?php echo $row['RAM_SIZE']; ?></td>
                                <td><?php echo $row['GRAPHIC_BRAND']; ?></td>
                                <td><?php echo $row['GRAPHIC_SIZE']; ?></td>
                                <td>
                                    <a href="?do=Edit&id=<?php echo $row['PROD_ID'];?>" class="btn" style="background-color: #4eb67f; margin-bottom: 5px;">Edit</a>
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
                $computer_name = $_POST['computer_name'];
                $brand = $_POST['brand'];
                $color = $_POST['color'];
                $scr_size = $_POST['screen_size'];
                $storage_size = $_POST['storage_size'];
                $storage_type = $_POST['storage_type'];
                $price = $_POST['price'];
                $ram_size = $_POST['ram_size'];
                $os = $_POST['os'];
                $graphic_size = $_POST['graphic_size'];
                $graphic_brand = $_POST['graphic_type'];
                $sup_id = $_POST['sup_id'];
                
                
                $insert_errors = array();

                // IF supplier in the database
                if(checkSup('SUP_ID', 'SUPPLIERS', $sup_id) != -1):
                    $insert_errors['sup'] = "THE SUPPLIER NOT EXISTS";
                endif;
                if(empty($brand)):
                    $insert_errors['brand'] = "THE SUPPLIER NOT EXISTS";
                endif;
                if(empty($price)):
                    $insert_errors['price'] = "THE SUPPLIER NOT EXISTS";
                endif;

                if(empty($insert_errors)):

                    try {
                        $proc = $conn->prepare("EXEC ADD_COMPUTER ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?");
                        $proc->execute(array($computer_name, $price, $sup_id, $brand, $os, $color, $ram_size, $storage_size, $storage_type, $scr_size, $graphic_brand, $graphic_size));
                        echo "<script>
                            alert('" . $proc->rowcount() . " RECORD INSERTED...!');
                            window.open('computers.php', '_self');
                            </script>";
                    }
                    catch(Exception $e) {
                        $error = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                    }

                endif;
            endif;
            ?>
            <h3 class="use-a-lot2 mb-2 mt-5">ADD COMPUTER</h3>
            <form class="content" action="?do=Add" method="POST" enctype="multipart/form-data">
                <div class="container">
                    <?php
                        if(isset($error)) echo $error;
                    ?>
                    <div class="about-book">
                        <div class="image">
                            <img src="<?php echo 'Themes/IMAGES/computer.jpg'; ?>" alt="">
                        </div>
                        <div class="info">
                            <div class="title">
                                <label for="">Computer Name: </label>
                                <input type="text" name="computer_name" placeholder="Computer Name" required>
                                <?php
                                $sup_stmt = $conn->prepare("SELECT * FROM SUPPLIERS");
                                $sup_stmt->execute();
                                $suppliers = $sup_stmt->fetchAll();
                                ?>
                                <label for="">Supplier Id: </label>

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
                            </div>

                            <div class="data">
                                <div class="datum">
                                    <div class="datum-desc">
                                        <label for="brand">Brand:</label>
                                        <input type="text" name="brand" id="brand" placeholder="Brand" required>  
                                        <span class="error">
                                            <?php
                                            if(isset($insert_errors['brand'])) echo '*' . $insert_errors['brand'];
                                            ?>
                                        </span>                               
                                    </div> 
                                    
                                    <div class="datum-desc">
                                        <label for="color">Color:</label>
                                        <input type="text" name="color" id="color" placeholder="Color">                                 
                                    </div> 

                                    <div class="datum-desc">
                                        <label for="scrSize">Screen Size:</label>
                                        <input type="text" name="screen_size" id="scrSize" placeholder="Screen Size">                                 
                                    </div>
                                    
                                    <div class="datum-desc">
                                        <label for="stgSize">Storage Size:</label>
                                        <input type="text" name="storage_size" id="stgSize" placeholder="Storage Size">                                 
                                    </div>

                                    <div class="datum-desc">
                                        <label for="stgType">Storage Type:</label>
                                        <input type="text" name="storage_type" id="stgType" placeholder="Storage Type">                                 
                                    </div>

                                </div>

                                <div class="datum">
                                    <div class="datum-desc">
                                        <label for="price">Price:</label>
                                        <input type="text" name="price" id="price" placeholder="Price" required>
                                        <span class="error">
                                            <?php
                                            if(isset($insert_errors['price'])) echo '*' . $insert_errors['price'];
                                            ?>
                                        </span>                                 
                                    </div>
                                    <div class="datum-desc">
                                        <label for="ramSize">Ram Size:</label>
                                        <input type="text" name="ram_size" id="ramSize" placeholder="Ram Size">                                 
                                    </div>
                                    <div class="datum-desc">
                                        <label for="os">OS:</label>
                                        <input type="text" name="os" id="os" placeholder="OS">                                 
                                    </div>     

                                    <div class="datum-desc">
                                        <label for="graphicSize">Graphic Size:</label>
                                        <input type="text" name="graphic_size" id="graphicSize" placeholder="Graphic Size">                                 
                                    </div>

                                    <div class="datum-desc">
                                        <label for="graphicType">Graphic Type:</label>
                                        <input type="text" name="graphic_type" id="graphicType" placeholder="Graphic Type">                                 
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

        elseif($do == 'Edit') { 

            $id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? $_GET['id'] : 0;

            $stmt = $conn->prepare("SELECT * FROM PRODUCTS P JOIN ELECTRONICS E ON P.PROD_ID = E.ELEC_ID JOIN COMPUTERS C ON E.ELEC_ID = C.COMPUTER_ID WHERE P.PROD_ID = ?");
            $stmt->execute(array($id));
            $row = $stmt->fetch();

            if ($stmt->rowcount() == -1):
                if(isset($_POST['update'])):    
                    // TAKING THE VALUES FROM THE FORM
                    $computer_id = $_POST['computer_id'];
                    $computer_name = $_POST['computer_name'];
                    $brand = $_POST['brand'];
                    $color = $_POST['color'];
                    $scr_size = $_POST['screen_size'];
                    $storage_size = $_POST['storage_size'];
                    $storage_type = $_POST['storage_type'];
                    $price = $_POST['price'];
                    $ram_size = $_POST['ram_size'];
                    $os = $_POST['os'];
                    $graphic_size = $_POST['graphic_size'];
                    $graphic_brand = $_POST['graphic_brand'];
                    $sup_id = $_POST['sup_id'];
                                        
                    $update_errors = array();
                    
                    // IF supplier in the database
                    if(checkSup('SUP_ID', 'SUPPLIERS', $sup_id) != -1):
                        $update_errors['sup'] = "THE SUPPLIER NOT EXISTS";
                    endif;
                    if(empty($brand)):
                        $update_errors['brand'] = "THE SUPPLIER NOT EXISTS";
                    endif;
                    if(empty($price)):
                        $update_errors['price'] = "THE SUPPLIER NOT EXISTS";
                    endif;

                    if(empty($update_errors)):

                        try {
                            $proc = $conn->prepare("EXEC UPDATE_COMPUTER ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?");
                            $proc->execute(array($computer_id, $computer_name, $price, $sup_id, $brand, $os, $color, $ram_size, $storage_size, $storage_type, $scr_size, $graphic_brand, $graphic_size));
                            echo "<script>
                            alert('" . $proc->rowcount() . " RECORD UPDATED...!');
                            window.open('computers.php?do=Edit&id=$id', '_self');
                            </script>";
                        }
                        catch(Exception $e) {
                            $err = $e->getMessage();
                            echo "<script>
                            alert('$err');
                            window.open('computers.php?do=Edit&id=$id', '_self');
                            </script>";
                        }
                    endif;
                endif;
                ?>
                <h3 class="use-a-lot2 mb-2 mt-5">EDIT COMPUTER</h3>
                <form class="content" action="?do=Edit&id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="container">
                        <div class="about-book">
                            <div class="image">
                                <img src="<?php echo 'Themes/IMAGES/computer.jpg'; ?>" alt="">
                            </div> 
                            <div class="info">
                                <div class="title">
                                    <input type="hidden" name="computer_id" value="<?php echo $row['PROD_ID'] ?>">
                                    <label for="">Computer Name: </label>
                                    <input type="text" name="computer_name" value="<?php echo $row['PROD_NAME'] ?>" required>
                                    <label for="">Supplier Id: </label>
                                    <input type="number" name="sup_id"value="<?php echo $row['SUP_ID'] ?>" required>
                                    <span class="error">
                                        <?php
                                        if(isset($update_errors['sup'])) echo '*' . $update_errors['sup'];
                                        ?>
                                    </span>
                                </div>

                        

                                <div class="data">
                                    <div class="datum">
                                        <div class="datum-desc">
                                            <label for="brand">Brand:</label>
                                            <input type="text" name="brand" id="brand" value="<?php echo $row['BRAND'] ?>" required>                                 
                                        </div> 
                                        
                                        <div class="datum-desc">
                                            <label for="color">Color:</label>
                                            <input type="text" name="color" id="color" value="<?php echo $row['COLOR'] ?>">                                 
                                        </div> 

                                        <div class="datum-desc">
                                            <label for="scrSize">Screen Size:</label>
                                            <input type="text" name="screen_size" id="scrSize" value="<?php echo $row['SCREEN_SIZE'] ?>">                                 
                                        </div>
                                        
                                        <div class="datum-desc">
                                            <label for="stgSize">Storage Size:</label>
                                            <input type="text" name="storage_size" id="stgSize" value="<?php echo $row['STORAGE_SIZE'] ?>">                                 
                                        </div>

                                        <div class="datum-desc">
                                            <label for="stgType">Storage Type:</label>
                                            <input type="text" name="storage_type" id="stgType" value="<?php echo $row['STORAGE_TYPE'] ?>">                                 
                                        </div>

                                    </div>

                                    <div class="datum">
                                        <div class="datum-desc">
                                            <label for="price">Price:</label>
                                            <input type="text" name="price" id="price" value="<?php echo $row['ITEM_PRICE'] ?>" required>                                 
                                        </div>
                                        <div class="datum-desc">
                                            <label for="ramSize">Ram Size:</label>
                                            <input type="text" name="ram_size" id="ramSize" value="<?php echo $row['RAM_SIZE'] ?>">                                 
                                        </div>
                                        <div class="datum-desc">
                                            <label for="os">OS:</label>
                                            <input type="text" name="os" id="os" value="<?php echo $row['OS'] ?>">                                 
                                        </div>     

                                        <div class="datum-desc">
                                            <label for="graphicSize">Graphic Size:</label>
                                            <input type="text" name="graphic_size" id="graphicSize" value="<?php echo $row['GRAPHIC_SIZE'] ?>">                                 
                                        </div>

                                        <div class="datum-desc">
                                            <label for="graphicType">Graphic Brand:</label>
                                            <input type="text" name="graphic_brand" id="graphicType" value="<?php echo $row['GRAPHIC_BRAND'] ?>">                                 
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
                echo "<script>
                    alert('THE COMPUTER NOT FOUND...!');
                    window.open('computers.php', '_self');
                    </script>";
            endif;
            ?>

        <?php
        }

        elseif($do == 'Delete') {
            $id = (isset($_GET['id'])) &&  is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

            try {
                $stmt2 = $conn->prepare("DELETE FROM PRODUCTS WHERE PROD_ID = ?");
                $stmt2->execute(array($id));
                echo "<script>
                    alert('" . $stmt2->rowcount() . " RECORD DELETED...!');
                    window.open('computers.php', '_self');
                    </script>";
            } 
            catch (Exception $e) {
                $err = $e->getMessage();
                echo "<script>
                    alert('$err');
                    window.open('computers.php', '_self');
                    </script>";
            }

        }

        include $tpl . 'footer.php';
    }
    else {
        header('location: index.php');
        exit();
    }