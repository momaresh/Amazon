<?php
    session_start();
    // ONLY THE ADMIN CAN GO HERE
    //if (isset($_SESSION['USER_NAME']) && ($_SESSION['GROUP_ID'] == 1 || $_SESSION['GROUP_ID'] == 3)) {
    include 'initial.php';
    if(isset($_SESSION['USER_NAME'])) {
        $do = (isset($_GET['do']) ? $_GET['do'] : 'Manage');     

        if ($do == 'Manage') { ?>
        <div class="container mt-5">

            <form class="search" action="" method='POST'>
                <input type="text" name="user_name" placeholder="Search by user name" id="search">
                <input type="submit" name="search" value="Search" id="button">
            </form>

            <a href="?do=Add" class="btn btn-primary mb-3">ADD CUSTOMER</a>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <tr style="background-color: #19283f; color: white">
                        <th>Customer_Id</th>
                        <th>Full_Name</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Sex</th>
                        <th>Location<th>
                    </tr>
                    <?php 


                    $search = ''; 
                    if(isset($_POST['search'])) {
                        $user_name = $_POST['user_name'];
                        if(!empty($user_name)) {
                            $search = "WHERE FIRST_NAME LIKE '%$user_name%' OR LAST_NAME LIKE '%$user_name%'";
                        }
                    }

                    $stmt = $conn->prepare("SELECT * FROM CUSTOMERS $search");
                    $stmt->execute();
                    $rows = $stmt->fetchAll();
                    foreach($rows as $row): 
                        $stmt_loc = $conn->prepare("SELECT * FROM LOCATIONS WHERE LOCATION_ID = ?");
                        $stmt_loc->execute(array($row['LOCATION_ID']));
                        $loc_row = $stmt_loc->fetch();
                    ?>
                        <tr>
                            <td><?php echo $row['CUS_ID']; ?></td>
                            <td><?php echo $row['FIRST_NAME'] . ' ' . $row['LAST_NAME']; ?></td>
                            <td><?php echo $row['EMAIL']; ?></td>
                            <td><?php echo $row['PASSWORD']; ?></td>
                            <td><?php echo $row['SEX']; ?></td>
                            <?php if($row['LOCATION_ID'] != null):
                            ?>
                            <td><?php echo $loc_row['COUNTRY'] . ' ' . $loc_row['CITY'] . ' ' . $loc_row['STREET_ADDRESS']; ?></td>
                            <?php
                            else:
                                echo "<td>...</td>";
                            endif;
                            ?>
                            <td>
                                <a href="?do=Edit&id=<?php echo $row['CUS_ID'];?>" class="btn" style="background-color: #4eb67f">Edit</a>
                                <a href="?do=Delete&id=<?php echo $row['CUS_ID'];?>" class="btn confirm" style="background-color: #ff6a00">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <?php
        }
        elseif($do == 'Add'){ 
            if(isset($_POST['ADD'])):
                //  print all the value from the form
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $sex = $_POST['sex'];
                $loc_id = $_POST['loc_id'];


                // Make some validation for the form
                // Create array that will take all error
                $errors = array();

                // Make the password 8 character and more
                if(strlen($password) < 8):
                    $errors['pass2'] = "The password can't be <strong>less than 8 character</strong>";
                endif;
                if(empty($first_name)):
                    $errors['first_name'] = "The user name must not be <strong>empty</strong>";
                endif;
                if(empty($last_name)):
                    $errors['last_name'] = "The user name must not be <strong>empty</strong>";
                endif;
                if(empty($password)):
                    $errors['pass1'] = "The password must not be <strong>empty</strong>";
                endif;
                if(empty($email)):
                    $errors['email1'] = "The email must not be <strong>empty</strong>";
                endif;
                // Check if the email is exists in the database
                if(checkUser('EMAIL', 'CUSTOMERS', $email) == -1):
                    $errors['email2'] = "The email is <strong>already exists</strong>";
                endif;

                // IF no error occur update in the database
                if(empty($errors)):
                    $max = $conn->prepare("SELECT MAX(CUS_ID) FROM CUSTOMERS");
                    $max->execute();
                    $id = $max->fetchColumn();
                    $id = $id + 1; 
            
                    try {
                        $stmt = $conn->prepare("INSERT INTO CUSTOMERS (CUS_ID, FIRST_NAME, LAST_NAME, EMAIL, PASSWORD, SEX, LOCATION_ID)
                                                VALUES (:ID, :FN, :LN, :EMAIL, :PASS, :SEX, :LOC)");
                        $stmt->execute(array(
                            'ID' => $id,
                            'FN' => $first_name,
                            'LN' => $last_name,
                            'EMAIL' => $email,
                            'PASS' => $password,
                            'SEX' => $sex,
                            'LOC' => $loc_id
                        ));

                        echo "<script>
                            alert('". $stmt->rowcount() . " RECORD INSERTED...!');
                            window.open('user.php', '_self');
                            </script>";
                    }
                    catch(Exception $e) {
                        $error = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                    }
                endif;
            endif;
            ?>
            <div class='signup'>
                <h1 class="text-center mb-5" style="color: #ff6a00; font-weight: bold;">Add Customer</h1>
                <div class="container">
                    <?php
                    if(isset($error)) echo $error;
                    ?>
                </div>
                <form class="profile" action="?do=Add" method="POST" enctype="multipart/form-data">
                    <div class="info">
                        <div class="user-name">
                            <label>First name:</label>
                            <input type="text" name="first_name" required='required' placeholder='first Name' class="input">
                            <label>Last name:</label>
                            <input type="text" name="last_name" required='required' placeholder='Last Name' class="input">
                            <span class="error">
                                <?php 
                                if(isset($errors['first_name'])) echo '* ' . $errors['first_name']; 
                                if(isset($errors['last_name'])) echo '* ' . $errors['last_name']; 
                                ?>
                            </span>
                        </div>
                        <div class="email">
                            <label>Email:</label>
                            <input type="email" name="email" required='required' placeholder='Email' class="input">
                            <span class="error">
                                <?php 
                                if(isset($errors['email1'])) echo '* ' . $errors['email1']; 
                                if(isset($errors['email2'])) echo '* ' . $errors['email2']; 
                                ?>
                            </span>
                        </div>
                        <div class="pass">
                            <label>Password:</label>
                            <input type="password" name="password" required='required' placeholder='Password' class="input">
                            <span class="error">
                                <?php 
                                if(isset($errors['pass1'])) echo '* ' . $errors['pass1']; 
                                if(isset($errors['pass2'])) echo '* ' . $errors['pass2']; 
                                ?>
                            </span>
                        </div>
                    
                        <div class="sex">
                            <label>Sex:</label>
                            <select class="btn select" style="background-color: white" id="sex" name="sex" required>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                        </div>
                        <?php
                            $loc_stmt = $conn->prepare('SELECT * FROM LOCATIONS');
                            $loc_stmt->execute();
                            $locs = $loc_stmt->fetchAll();
                        ?>
                        <div>  
                            <label>Location:</label> 
                            <select class="btn select" style="background-color: white" name='loc_id'>
                            <?php
                                foreach ($locs as $loc):
                            ?>
                                <option value='<?php echo $loc['LOCATION_ID'] ?>'><?php echo "$loc[COUNTRY] - $loc[CITY]"?></option>
                            <?php
                                endforeach;
                            ?>
                            </select>
                        </div>
                        <datalist id="nationality">
                            <option value="Yemeni">
                            <option value="Saudi">
                        </datalist>

                        <input class="submit" type="submit" value="Add" name='ADD'>
                    </div>
                </form>
            </div>
        <?php
        }
        elseif ($do == 'Edit') { 
            $userId = (isset($_GET['id'])) &&  is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

            $stmt = $conn->prepare("SELECT * FROM CUSTOMERS WHERE CUS_ID = ?");
            $stmt->execute(array($userId));
            $row = $stmt->fetch();

            if ($stmt->rowcount() == -1): 
                if(isset($_POST['UPDATE'])):
                    //  print all the value from the form
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $sex = $_POST['sex'];
                $loc_id = $_POST['loc_id'];


                // Make some validation for the form
                // Create array that will take all error
                $errors = array();

                // Make the password 8 character and more
                if(strlen($password) < 8):
                    $errors['pass2'] = "The password can't be <strong>less than 8 character</strong>";
                endif;
                if(empty($first_name)):
                    $errors['first_name'] = "The user name must not be <strong>empty</strong>";
                endif;
                if(empty($last_name)):
                    $errors['last_name'] = "The user name must not be <strong>empty</strong>";
                endif;
                if(empty($password)):
                    $errors['pass1'] = "The password must not be <strong>empty</strong>";
                endif;
                if(empty($email)):
                    $errors['email1'] = "The email must not be <strong>empty</strong>";
                endif;
                // Check if the email is exists in the database
                if(checkDuplicate('EMAIL', 'CUSTOMERS', $email, "CUS_ID", $userId) == -1):
                    $errors['email2'] = "The email is <strong>already exists</strong>";
                endif;

                // IF no error occur update in the database
                if(empty($errors)): 
            
                    try {
                        $stmt = $conn->prepare("UPDATE CUSTOMERS 
                                        SET FIRST_NAME = :FN, 
                                            LAST_NAME = :LN,
                                            EMAIL = :EM, 
                                            PASSWORD = :PA, 
                                            SEX = :SE,
                                            LOCATION_ID = :LI
                                        WHERE CUS_ID = :CI");
                        $stmt->execute(array(
                                    'FN' => $first_name,
                                    'LN' => $last_name,
                                    'EM' => $email,
                                    'PA' => $password,
                                    'SE' => $sex,
                                    'LI' => $loc_id,
                                    'CI' => $userId));

                        echo "<script>
                        alert('". $stmt->rowCount() . " RECORD UPDATED...!');
                        window.open('user.php', '_self');
                        </script>";
                    }
                    catch(Exception $e) {
                        $error = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                    }

                        
                endif;
            endif;
            ?>
            <div class='signup'>
                <h1 class="text-center mb-5" style="color: #ff6a00; font-weight: bold;">Edit Customer</h1>
                <div class="container">
                    <?php
                    if(isset($error)) echo $error;
                    ?>
                </div>
                <form class="profile" action="?do=Edit&id=<?php echo $row['CUS_ID'] ?>" method="POST" enctype="multipart/form-data">
                    <div class="info">
                        <div class="user-name">
                            <label>First name:</label>
                            <input type="text" name="first_name" required='required' value=<?php echo $row['FIRST_NAME'] ?> class="input">
                            <label>Last name:</label>
                            <input type="text" name="last_name" required='required' value=<?php echo $row['LAST_NAME'] ?> class="input">
                            <span class="error">
                                <?php 
                                if(isset($errors['first_name'])) echo '* ' . $errors['first_name']; 
                                if(isset($errors['last_name'])) echo '* ' . $errors['last_name']; 
                                ?>
                            </span>
                        </div>
                        <div class="email">
                            <label>Email:</label>
                            <input type="email" name="email" required='required' value=<?php echo $row['EMAIL'] ?> class="input">
                            <span class="error">
                                <?php 
                                if(isset($errors['email1'])) echo '* ' . $errors['email1']; 
                                if(isset($errors['email2'])) echo '* ' . $errors['email2']; 
                                ?>
                            </span>
                        </div>
                        <div class="pass">
                            <label>Password:</label>
                            <input type="password" name="password" required='required' value=<?php echo $row['PASSWORD'] ?> class="input">
                            <span class="error">
                                <?php 
                                if(isset($errors['pass1'])) echo '* ' . $errors['pass1']; 
                                if(isset($errors['pass2'])) echo '* ' . $errors['pass2']; 
                                ?>
                            </span>
                        </div>

                        <div class="sex">
                            <label>Sex:</label>
                            <select class="btn select" value=<?php echo $row['SEX'] ?> style="background-color: white" id="sex" name="sex" required>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                        </div>
                        <?php
                            $loc_stmt = $conn->prepare('SELECT * FROM LOCATIONS');
                            $loc_stmt->execute();
                            $locs = $loc_stmt->fetchAll();
                        ?>
                        <div>  
                            <label>Location:</label> 
                            <select class="btn select" value=<?php echo $row['LOCATION_ID'] ?> style="background-color: white" name='loc_id'>
                            <?php
                                foreach ($locs as $loc):
                            ?>
                                <option value='<?php echo $loc['LOCATION_ID'] ?>'><?php echo "$loc[COUNTRY] - $loc[CITY]"?></option>
                            <?php
                                endforeach;
                            ?>
                            </select>
                        </div>

                        <input class="submit" type="submit" value="Update" name='UPDATE'>
                    </div>
                </form>
            </div>

            <?php 
            else:
                echo "<script>
                    alert('THE USER NOT FOUND...!');
                    window.open('user.php', '_self');
                    </script>";
            endif;
            ?>
        <?php }
        elseif($do == 'Delete') {
            echo '<div class=container>';
            // CHECK IF THE COMING USER NAME IS NUMERIC AND STOR
            $userId = (isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0);

            // CHECK IF THE USER EXISTS
            $stmt = $conn->prepare("SELECT * FROM CUSTOMERS WHERE CUS_ID = ?");
            $stmt->execute(array($userId));

            if($stmt->rowcount() == -1):
                try {
                    $stmt2 = $conn->prepare("DELETE FROM CUSTOMERS WHERE CUS_ID = ?");
                    $stmt2->execute(array($userId));
                }
                catch(Exception $e)
                {
                    redirectToHome("<div class='alert alert-danger'>" . $e->getMessage() . "</div>", 'back');
                }
            else:
                echo "<script>
                    alert('THE USER NOT FOUND...!');
                    window.open('user.php', '_self');
                    </script>";
            endif;
            echo '</div>';
        }

        include $tpl . 'footer.php';
    }
    else {
        header('Location: index.php');
        exit();
    }
?>