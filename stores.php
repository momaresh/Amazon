
<?php
    session_start();
    $setTitle = "Company";

    if(isset($_SESSION['USER_NAME']) && isset($_SESSION['GROUP_ID']) == 1) {
        include('initial.php');

        $sql = "SELECT * FROM STORES";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $do = (isset($_GET['do']) ? $_GET['do'] : 'Manage');

        if($do == "Manage") {
            if(isset($_POST['insert'])) {
                $name = $_POST['name'];
                $desc = $_POST['desc'];

                $errors = array();
                if(empty($name)) {
                    $errors['name'] = "Name is required";
                }

                if(checkSup("STORE_NAME", "STORES", $name) == -1) {
                    $errors['name1'] = "Name is already exists";
                }

                

                if(empty($errors)){
                    $sql1 = "SELECT MAX(STORE_ID) FROM STORES";
                    $stmt1 = $conn->prepare($sql1);
                    $stmt1->execute();
                    try {
                        $sql2 = "INSERT INTO STORES(STORE_ID, STORE_NAME, DESCRIPTION) VALUES(?, ?, ?)";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->execute(array(($stmt1->fetchColumn() + 1), $name, $desc));
                        echo "<script>
                            alert('THE STORE INSERTED');
                            window.open('stores.php', '_self');
                            </script>";
                    }
                    catch(Exception $e) {
                        $error = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                    }
                }
            }
            ?>

            <h3 class="use-a-lot2 mb-2 mt-5">ADD STORE</h3>
            <form class="form-row company-form" method="POST" action="">
                <?php
                if(isset($success)) echo $success;
                if(isset($error)) echo $error;
                ?>
                <div class="form-group col-md-4">
                    <label for="inputEmail4">Store Name</label>
                    <input type="text" class="form-control" name="name" id="inputEmail4" placeholder="Store Name" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['name'])) echo '* ' . $errors['name']; 
                        else if(isset($errors['name1'])) echo '* ' . $errors['name1']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputPassword4">Description</label>
                    <textarea type="text" class="form-control" name="desc" id="inputPassword4" placeholder="Description"></textarea>
                </div>

                <button type="submit" name="insert" class="btn btn-primary">Add</button>
            </form>

            <div class="container" style="margin-top: 100px">
                <hr>
                <h2>Stores: </h2>
                <div class="table-responsive" id='goto'>
                    <table class="table table-bordered text-center">
                        <tr style="background-color: #19283f; color: white">
                            <th>Store ID</th>
                            <th>Store Name</th>
                            <th>Description</th>
                            <th>Control</th>
                        </tr>

                        <?php
                        foreach($stmt->fetchAll() as $row): ?>
                            <tr>
                                <td><?php echo $row['STORE_ID'] ?></td>
                                <td><?php echo $row['STORE_NAME'] ?></td>
                                <td><?php echo $row['DESCRIPTION'] ?></td>
                                <td>
                                    <a href="?do=Edit&id=<?php echo $row['STORE_ID'] ?>" class="btn" style="background-color: #4eb67f; margin-bottom: 5px;">Edit</a>
                                    <a href="?do=Delete&id=<?php echo $row['STORE_ID'];?>" class="btn confirm" style="background-color: #ff6a00">Delete</a>
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
            $stmt3 = $conn->prepare("SELECT * FROM STORES WHERE STORE_ID = ?");
            $stmt3->execute(array($id));

            if($stmt3->rowCount() == -1) {
                $row = $stmt3->fetch();

                if(isset($_POST['update'])) {
                    $name = $_POST['name'];
                    $desc = $_POST['desc'];

                    $errors = array();
                    if(empty($name)) {
                        $errors['name'] = "Name is required";
                    }

                    if(CheckDuplicate("STORE_NAME", "STORES", $name, 'STORE_ID', $id) == -1) {
                        $errors['name1'] = "Name is already exists";
                    }

                    if(empty($errors)){
                        try {
                            $sql2 = "UPDATE STORES SET STORE_NAME = ?, DESCRIPTION = ? WHERE STORE_ID = ?";
                            $stmt2 = $conn->prepare($sql2);
                            $stmt2->execute(array($name, $desc, $id));
                            echo "<script>
                                alert('The Store Updated');
                                window.open('stores.php#goto', '_self');
                                </script>";
                        }
                        catch(Exception $e) {
                            $err = $e->getMessage();
                            echo "<script>
                                alert('$err');
                                window.open('stores.php?do=Edit&id=$id', '_self');
                                </script>";                            }
                    }
                }
            
            ?>

            <h3 class="use-a-lot2 mb-2 mt-5">EDIT STORE</h3>
            <form class="form-row company-form" method="POST" action="?do=Edit&id=<?php echo $id ?>">
                <div class="form-group col-md-4">
                    <label for="inputEmail4">Store Name</label>
                    <input type="text" class="form-control" name="name" id="inputEmail4" value="<?php echo $row['STORE_NAME'] ?>" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['name'])) echo '* ' . $errors['name']; 
                        else if(isset($errors['name1'])) echo '* ' . $errors['name1']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputPassword4">Description</label>
                    <textarea type="text" class="form-control" name="desc" id="inputPassword4"><?php echo $row['DESCRIPTION'] ?></textarea>
                </div>

                <button type="submit" name="update" class="btn btn-primary">Save</button>
            </form>
        <?php
            }
            else {
                echo "<script>
                    alert('THE STORE NOT FOUND');
                    window.open('stores.php', '_self');
                    </script>";                
            }
        }
        elseif($do == 'Delete') {
            $id = (isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0);

            // CHECK IF THE USER EXISTS
            $stmt5 = $conn->prepare("SELECT * FROM STORES WHERE STORE_ID = ?");
            $stmt5->execute(array($id));
            $row5 = $stmt5->fetch(); 

            if($stmt5->rowcount() == -1):
                try {
                    $stmt2 = $conn->prepare("DELETE FROM STORES WHERE STORE_ID = ?");
                    $stmt2->execute(array($id));
                    echo "<script>
                        alert('THE STORE DELETED');
                        window.open('stores.php', '_self');
                        </script>";
                }
                catch(Exception $e) {
                    $err = $e->getMessage();
                    echo "<script>
                        alert('$err');
                        window.open('stores.php', '_self');
                        </script>";
                }
                
            else:
                echo "<script>
                    alert('THE STORE NOT EXISTS');
                    window.open('stores.php', '_self');
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