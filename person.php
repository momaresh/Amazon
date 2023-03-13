<?php
    session_start();
    $setTitle = "Company";

    if(isset($_SESSION['USER_NAME']) && isset($_SESSION['GROUP_ID']) == 1) {
        include('initial.php');

        $sql = "SELECT * FROM PERSON";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $do = (isset($_GET['do']) ? $_GET['do'] : 'Manage');

        if($do == "Manage") {
            
            if(isset($_POST['insert'])) {
                $fname = $_POST['fname'];
                $lname = $_POST['lname'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];

                $errors = array();
                if(empty($fname)) {
                    $errors['fname'] = "First Name is required";
                }
                if(empty($lname)) {
                    $errors['lname'] = "Last name is required";
                }
                if(empty($email)) {
                    $errors['name'] = "Email is required";
                }
                if(empty($phone)) {
                    $errors['phone'] = "Phone is required";
                }
                if(checkSup("EMAIL", "PERSON", $email) == -1) {
                    $errors['email1'] = "Email is already exsits";
                }
                if(checkSup("PHONE", "PERSON", $phone) == -1) {
                    $errors['phone1'] = "Phone number is already exsits";
                }
                if(!is_numeric($phone)) {
                    $errors['phone2'] = "Phone number is only numbers";
                }

                if(empty($errors)){
                    try {
                        $sql2 = "EXEC INSERT_PERSON ?, ?, ?, ?";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->execute(array($fname, $lname, $email, $phone));
                        echo "<script>
                            alert('THE SUPPLIER INSERTED');
                            window.open('person.php', '_self');
                            </script>";
                    }
                    catch(Exception $e) {
                        $error = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                    }
                }
            }
            ?>

            <h3 class="use-a-lot2 mb-2 mt-5">ADD PERSON</h3>
            <form class="form-row company-form" method="POST" action="person.php">
                <?php
                if(isset($error)) echo $error;
                ?>
                <div class="form-group col-md-4">
                    <label for="inputEmail4">First Name</label>
                    <input type="text" class="form-control" name="fname" id="inputEmail4" placeholder="First Name" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['fname'])) echo '* ' . $errors['fname']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputEmail4">Last Name</label>
                    <input type="text" class="form-control" name="lname" id="inputEmail4" placeholder="Last Name" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['lname'])) echo '* ' . $errors['lname']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputAddress">Email</label>
                    <input type="email" class="form-control" name="email" id="inputAddress" placeholder="Email" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['email'])) echo '* ' . $errors['email']; 
                        else if(isset($errors['email1'])) echo '* ' . $errors['email1']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputAddress">Phone Number</label>
                    <input type="text" class="form-control" name="phone" id="inputAddress" placeholder="Phone Number" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['phone'])) echo '* ' . $errors['phone']; 
                        else if(isset($errors['phone1'])) echo '* ' . $errors['phone1']; 
                        else if(isset($errors['phone2'])) echo '* ' . $errors['phone2']; 
                        ?>
                    </span>
                </div>

            <button type="submit" name="insert" class="btn btn-primary">Add</button>
            </form>


            <div class="container" style="margin-top: 100px">
                <hr>
                <h2>SUPPLIERS</h2>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <tr style="background-color: #19283f; color: white">
                            <th>Supplier ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Control</th>
                        </tr>

                        <?php
                        foreach($stmt->fetchAll() as $row): ?>
                            <tr>
                                <td><?php echo $row['SUP_ID'] ?></td>
                                <td><?php echo $row['FIRST_NAME'] ?></td>
                                <td><?php echo $row['LAST_NAME'] ?></td>
                                <td><?php echo $row['EMAIL'] ?></td>
                                <td><?php echo $row['PHONE'] ?></td>
                                <td>
                                    <a href="?do=Edit&id=<?php echo $row['PER_ID'] ?>" class="btn" style="background-color: #4eb67f; margin-bottom: 5px;">Edit</a>
                                    <a href="?do=Delete&id=<?php echo $row['PER_ID'];?>" class="btn confirm" style="background-color: #ff6a00">Delete</a>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                        ?>
                    </table>
                </div>
            </div>
        <?php
        }

        elseif($do == "Edit") { 
            $id = (isset($_GET['id']) ? $_GET['id'] : 0);
            $stmt3 = $conn->prepare("SELECT * FROM PERSON WHERE PER_ID = ?");
            $stmt3->execute(array($id));

            if($stmt3->rowCount() == -1) {
                $row = $stmt3->fetch();

                if(isset($_POST['update'])) {
                    $fname = $_POST['fname'];
                    $lname = $_POST['lname'];
                    $email = $_POST['email'];
                    $phone = $_POST['phone'];

                    $errors = array();
                    if(empty($fname)) {
                        $errors['fname'] = "First Name is required";
                    }
                    if(empty($lname)) {
                        $errors['lname'] = "Last name is required";
                    }
                    if(empty($email)) {
                        $errors['name'] = "Email is required";
                    }
                    if(empty($phone)) {
                        $errors['phone'] = "Phone is required";
                    }
                    if(checkDuplicate("EMAIL", "PERSON", $email, "PER_ID", $id) == -1) {
                        $errors['email1'] = "Email is already exsits";
                    }
                    if(checkDuplicate("PHONE", "PERSON", $phone, "PER_ID", $id) == -1) {
                        $errors['phone1'] = "Phone number is already exsits";
                    }
                    if(!is_numeric($phone)) {
                        $errors['phone2'] = "Phone number is only numbers";
                    }

                    if(empty($errors)) {
                        try {
                            $sql4 = "UPDATE PERSON SET FIRST_NAME = ?, LAST_NAME = ?, EMAIL = ?, PHONE = ? WHERE PER_ID = ?";
                            $stmt4 = $conn->prepare($sql4);
                            $stmt4->execute(array($fname, $lname, $email, $phone, $id));
                            echo "<script>
                                alert('THE SUPPLIER UPDATED');
                                window.open('person.php?do=Edit&id=$id', '_self');
                                </script>";
                    }
                    catch(Exception $e) {
                        $err = $e->getMessage();
                        echo "<script>
                            alert('$err');
                            window.open('person.php?do=Edit&id=$id', '_self');
                            </script>";
                    }
                }

                }
            
            ?>
            <h3 class="use-a-lot2 mb-2 mt-5">EDIT PERSON</h3>
            <form class="form-row company-form" method="POST" action="?do=Edit&id=<?php echo $id ?>">
                <div class="form-group col-md-4">
                    <label for="inputEmail4">First Name</label>
                    <input type="text" class="form-control" name="fname" id="inputEmail4" value="<?php echo $row['FIRST_NAME'] ?>" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['fname'])) echo '* ' . $errors['fname']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputEmail4">Last Name</label>
                    <input type="text" class="form-control" name="lname" id="inputEmail4" value="<?php echo $row['LAST_NAME'] ?>" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['lname'])) echo '* ' . $errors['lname']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputAddress">Email</label>
                    <input type="email" class="form-control" name="email" id="inputAddress" value="<?php echo $row['EMAIL'] ?>" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['email'])) echo '* ' . $errors['email']; 
                        else if(isset($errors['email1'])) echo '* ' . $errors['email1']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputAddress">Phone Number</label>
                    <input type="text" class="form-control" name="phone" id="inputAddress" value="<?php echo $row['PHONE'] ?>" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['phone'])) echo '* ' . $errors['phone']; 
                        else if(isset($errors['phone1'])) echo '* ' . $errors['phone1']; 
                        else if(isset($errors['phone2'])) echo '* ' . $errors['phone2']; 
                        ?>
                    </span>
                </div>

                <button type="submit" name="update" class="btn btn-primary">Save</button>
            </form>
        <?php
            }
            else {
                echo "<script>
                    alert('THE SUPPLIER NOT FOUND');
                    window.open('person.php', '_self');
                    </script>";                
                }
        }
        elseif($do == 'Delete') {
            echo '<div class=container>';
            // CHECK IF THE COMING USER NAME IS NUMERIC AND STOR
            $id = (isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0);

            // CHECK IF THE USER EXISTS
            $stmt5 = $conn->prepare("SELECT * FROM PERSON WHERE PER_ID = ?");
            $stmt5->execute(array($id));
            $row5 = $stmt5->fetch(); 

            if($stmt5->rowcount() == -1):
                try {
                    $stmt2 = $conn->prepare("DELETE FROM SUPPLIERS WHERE SUP_ID = ?");
                    $stmt2->execute(array($row5['SUP_ID']));
                    echo "<script>
                        alert('THE SUPPLIER DELETED');
                        window.open('company.php', '_self');
                        </script>";
                }
                catch(Exception $e) {
                    $err = $e->getMessage();
                    echo "<script>
                        alert('$err');
                        window.open('person.php', '_self');
                        </script>";
                }
                
            else:
                echo "<script>
                    alert('THE SUPPLIER NOT EXISTS');
                    window.open('person.php', '_self');
                    </script>";
            endif;
        }
        ?>


<?php

        include($tpl . "footer.php");
    }
    else {
        header('Location: index.php');
        exit();
    }

?>