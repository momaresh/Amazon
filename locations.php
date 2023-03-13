
<?php
    session_start();
    $setTitle = "Company";

    if(isset($_SESSION['USER_NAME']) && isset($_SESSION['GROUP_ID']) == 1) {
        include('initial.php');

        $sql = "SELECT * FROM LOCATIONS";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $do = (isset($_GET['do']) ? $_GET['do'] : 'Manage');

        if($do == "Manage") {

            if(isset($_POST['insert'])) {
                $country = $_POST['country'];
                $city = $_POST['city'];
                $street = $_POST['street'];
                $zip = $_POST['zipcode'];

                $errors = array();
                if(empty($country)) {
                    $errors['country'] = "Country is required";
                }
                if(empty($city)) {
                    $errors['city'] = "City is required";
                }

                if(empty($errors)){
                    $sql1 = "SELECT MAX(LOCATION_ID) FROM LOCATIONS";
                    $stmt1 = $conn->prepare($sql1);
                    $stmt1->execute();
                    try {
                        $sql2 = "INSERT INTO LOCATIONS(LOCATION_ID, COUNTRY, CITY, STREET_ADDRESS, ZIP_CODE) VALUES(?, ?, ?, ?, ?)";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->execute(array(($stmt1->fetchColumn() + 1), $country, $city, $street, $zip));
                        echo "<script>
                            alert('THE LOCATION INSERTED');
                            window.open('locations.php', '_self');
                            </script>";
                    }
                    catch(Exception $e) {
                        $error = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                    }
                }
            }
            ?>

            <h3 class="use-a-lot2 mb-2 mt-5">ADD LOCATION</h3>
            <form class="form-row company-form" method="POST" action="">
                <?php
                if(isset($error)) echo $error;
                ?>
                <div class="form-group col-md-4">
                    <label for="">Country</label>
                    <input type="text" class="form-control" name="country" placeholder="Country" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['country'])) echo '* ' . $errors['country']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="">City</label>
                    <input type="text" class="form-control" name="city" placeholder="City" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['city'])) echo '* ' . $errors['city']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="">Street</label>
                    <input type="text" class="form-control" name="street" placeholder="Street">
                </div>

                <div class="form-group col-md-4">
                    <label for="">ZipCode</label>
                    <input type="text" class="form-control" name="zipcode" placeholder="ZipCode">
                </div>

                <button type="submit" name="insert" class="btn btn-primary">Add</button>
            </form>

            <div class="container" style="margin-top: 100px">
                <hr>
                <h2 class="mb-2">LOCATIONS: </h2>
                <div class="table-responsive" id='goto'>
                    <table class="table table-bordered text-center">
                        <tr style="background-color: #19283f; color: white">
                            <th>Location ID</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>Street</th>
                            <th>Zip Code</th>
                            <th>Control</th>
                        </tr>

                        <?php
                        foreach($stmt->fetchAll() as $row): ?>
                            <tr>
                                <td><?php echo $row['LOCATION_ID'] ?></td>
                                <td><?php echo $row['COUNTRY'] ?></td>
                                <td><?php echo $row['CITY'] ?></td>
                                <td><?php echo $row['STREET_ADDRESS'] ?></td>
                                <td><?php echo $row['ZIP_CODE'] ?></td>
                                <td>
                                    <a href="?do=Edit&id=<?php echo $row['LOCATION_ID'] ?>" class="btn" style="background-color: #4eb67f; margin-bottom: 5px;">Edit</a>
                                    <a href="?do=Delete&id=<?php echo $row['LOCATION_ID'];?>" class="btn confirm" style="background-color: #ff6a00">Delete</a>
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
            $stmt3 = $conn->prepare("SELECT * FROM LOCATIONS WHERE LOCATION_ID = ?");
            $stmt3->execute(array($id));

            if($stmt3->rowCount() == -1) {
                $row = $stmt3->fetch();

                if(isset($_POST['update'])) {
                    $country = $_POST['country'];
                    $city = $_POST['city'];
                    $street = $_POST['street'];
                    $zip = $_POST['zipcode'];

                    $errors = array();
                    if(empty($country)) {
                        $errors['country'] = "Country is required";
                    }
                    if(empty($city)) {
                        $errors['city'] = "City is required";
                    }

                    if(empty($errors)){
                        try {
                            $sql2 = "UPDATE LOCATIONS SET COUNTRY = ?, CITY = ?, STREET_ADDRESS = ?, ZIP_CODE = ?
                            WHERE LOCATION_ID = ?";
                            $stmt2 = $conn->prepare($sql2);
                            $stmt2->execute(array($country, $city, $street, $zip, $id));
                            echo "<script>
                                alert('THE LOCATION UPDATED');
                                window.open('locations.php#goto', '_self');
                                </script>";
                        }
                        catch(Exception $e) {
                            $error = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                        }
                    }
                }
            
            ?>

            <h3 class="use-a-lot2 mb-2 mt-5">EDIT LOCATION</h3>
            <form class="form-row company-form" method="POST" action="?do=Edit&id=<?php echo $id ?>">
                <?php
                if(isset($error)) echo $error;
                ?>
                <div class="form-group col-md-4">
                    <label for="">Country</label>
                    <input type="text" class="form-control" name="country" value="<?php echo $row['COUNTRY'] ?>" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['country'])) echo '* ' . $errors['country']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="">City</label>
                    <input type="text" class="form-control" name="city" value="<?php echo $row['CITY'] ?>" required="required">
                    <span class="error">
                        <?php 
                        if(isset($errors['city'])) echo '* ' . $errors['city']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="">Street</label>
                    <input type="text" class="form-control" name="street" value="<?php echo $row['STREET_ADDRESS'] ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="">ZipCode</label>
                    <input type="text" class="form-control" name="zipcode" value="<?php echo $row['ZIP_CODE'] ?>">
                </div>

                <button type="submit" name="update" class="btn btn-primary">Save</button>
            </form>
        <?php
            }
            else {
                echo "<script>
                    alert('THE LOCATION NOT FOUND');
                    window.open('locations.php', '_self');
                    </script>";                
            }
        }
        elseif($do == 'Delete') {
            $id = (isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0);

            try {
                $stmt2 = $conn->prepare("DELETE FROM LOCATIONS WHERE LOCATION_ID = ?");
                $stmt2->execute(array($id));
                echo "<script>
                    alert('The location Deleted');
                    window.open('locations.php#goto', '_self');
                    </script>";
            }
            catch(Exception $e) {
                $err = $e->getMessage();
                echo "<script>
                    alert('$err');
                    window.open('locations.php', '_self');
                    </script>";
            }
            
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