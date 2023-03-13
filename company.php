<?php
    session_start();
    $setTitle = "Company";

    if(isset($_SESSION['USER_NAME']) && isset($_SESSION['GROUP_ID']) == 1) {
        include('initial.php');

        $sql = "SELECT * FROM COMPANY";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $do = (isset($_GET['do']) ? $_GET['do'] : 'Manage');

        if($do == "Manage") {

            if(isset($_POST['insert'])) {
                $name = $_POST['name'];
                $boss = $_POST['boss'];
                $email = $_POST['email'];

                $errors = array();
                if(empty($name)) {
                    $errors['name'] = "Name is required";
                }
                if(empty($boss)) {
                    $errors['name'] = "Boss name is required";
                }
                if(empty($email)) {
                    $errors['name'] = "Email is required";
                }
                if(checkSup("EMAIL", "COMPANY", $email) == -1) {
                    $errors['email1'] = "Email is already exsits";
                }
                if(checkSup("COM_NAME", "COMPANY", $name) == -1) {
                    $errors['name1'] = "Name is already exsits";
                }

                if(empty($errors)){
                    try {
                        $sql2 = "EXEC INSERT_COMPANY ?, ?, ?";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->execute(array($name, $boss, $email));
                        echo "<script>
                            alert('THE SUPPLIER INSERTED');
                            window.open('company.php', '_self');
                            </script>";
                    }
                    catch(Exception $e) {
                        $error = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                    }
                }
            } ?>

            
            <h3 class="use-a-lot2 mb-2 mt-5">ADD COMPANY</h3>
            <form class="form-row company-form" method="POST" action="">
                <?php
                if(isset($error)) echo $error;
                ?>
                <div class="form-group col-md-4">
                    <label for="inputEmail4">Name</label>
                    <input type="text" class="form-control" name="name" id="inputEmail4" placeholder="Name" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['name'])) echo '* ' . $errors['name']; 
                        else if(isset($errors['name1'])) echo '* ' . $errors['name1']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputPassword4">Boss Name</label>
                    <input type="text" class="form-control" name="boss" id="inputPassword4" placeholder="Boss Name" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['boss'])) echo '* ' . $errors['boss']; 
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
                <button type="submit" name="insert" class="btn btn-primary">Add</button>
            </form>

            <div class="container" style="margin-top: 100px">
                <hr>
                <h2>COMPANIES</h2>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <tr style="background-color: #19283f; color: white">
                            <th>Supplier ID</th>
                            <th>Company Name</th>
                            <th>Company Boss</th>
                            <th>Email</th>
                            <th>Control</th>
                        </tr>

                        <?php
                        foreach($stmt->fetchAll() as $row): ?>
                            <tr>
                                <td><?php echo $row['SUP_ID'] ?></td>
                                <td><?php echo $row['COM_NAME'] ?></td>
                                <td><?php echo $row['BOSS_NAME'] ?></td>
                                <td><?php echo $row['EMAIL'] ?></td>
                                <td>
                                    <a href="?do=Edit&id=<?php echo $row['COM_ID'] ?>" class="btn" style="background-color: #4eb67f; margin-bottom: 5px;">Edit</a>
                                    <a href="?do=Delete&id=<?php echo $row['COM_ID'];?>" class="btn confirm" style="background-color: #ff6a00">Delete</a>
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
            $stmt3 = $conn->prepare("SELECT * FROM COMPANY WHERE COM_ID = ?");
            $stmt3->execute(array($id));

            if($stmt3->rowCount() == -1) {
                $row = $stmt3->fetch();

                if(isset($_POST['update'])) {
                    $name = $_POST['name'];
                    $boss = $_POST['boss'];
                    $email = $_POST['email'];

                    $errors = array();
                    if(empty($name)) {
                        $errors['name'] = "Name is required";
                    }
                    if(empty($boss)) {
                        $errors['name'] = "Boss name is required";
                    }
                    if(empty($email)) {
                        $errors['name'] = "Email is required";
                    }
                    if(checkDuplicate("EMAIL", "COMPANY", $email, "COM_ID", $id) == -1) {
                        $errors['email1'] = "Email is already exsits";
                    }
                    if(checkDuplicate("COM_NAME", "COMPANY", $name, "COM_ID", $id) == -1) {
                        $errors['name1'] = "Name is already exsits";
                    }

                    if(empty($errors)){

                        try {
                            $sql4 = "UPDATE COMPANY SET COM_NAME = ?, BOSS_NAME = ?, EMAIL = ? WHERE COM_ID = ?";
                            $stmt4 = $conn->prepare($sql4);
                            $stmt4->execute(array($name, $boss, $email, $id));
                            echo "<script>
                                alert('THE SUPPLIER UPDATED');
                                window.open('company.php?do=Edit&id=$id', '_self');
                                </script>";
                        }
                        catch(Exception $e) {
                            $err = $e->getMessage();
                            echo "<script>
                                alert('$err');
                                window.open('company.php?do=Edit&id=$id', '_self');
                                </script>";
                        }

                    }
                }
            
            ?>

            <h3 class="use-a-lot2 mb-2 mt-5">EDIT COMPANY</h3>
            <form class="form-row company-form" method="POST" action="?do=Edit&id=<?php echo $row['COM_ID'] ?>">
                <div class="form-group col-md-4">
                    <label for="inputEmail4">Name</label>
                    <input type="text" class="form-control" name="name" id="inputEmail4" value="<?php echo $row['COM_NAME'] ?>">
                    <span class="error">
                        <?php 
                        if(isset($errors['name'])) echo '* ' . $errors['name']; 
                        else if(isset($errors['name1'])) echo '* ' . $errors['name1']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputPassword4">Boss Name</label>
                    <input type="text" class="form-control" name="boss" id="inputPassword4" value="<?php echo $row['BOSS_NAME'] ?>">
                    <span class="error">
                        <?php 
                        if(isset($errors['boss'])) echo '* ' . $errors['boss']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputAddress">Email</label>
                    <input type="email" class="form-control" name="email" id="inputAddress" value="<?php echo $row['EMAIL'] ?>">
                    <span class="error">
                        <?php 
                        if(isset($errors['email'])) echo '* ' . $errors['email']; 
                        else if(isset($errors['email1'])) echo '* ' . $errors['email1']; 
                        ?>
                    </span>
                </div>
            <button type="submit" name="update" class="btn btn-primary">Save</button>
            </form>
        <?php
            }
            else {
                redirectToHome("<div class='alert alert-danger'>The supplier not found</div>", 'back');
            }
        }
        elseif($do == 'Delete') {
            $id = (isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0);

            $stmt5 = $conn->prepare("SELECT * FROM COMPANY WHERE COM_ID = ?");
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
                        window.open('company.php', '_self');
                        </script>";
                }
                
            else:
                echo "<script>
                    alert('THE SUPPLIER NOT EXISTS');
                    window.open('company.php', '_self');
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