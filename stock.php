
<?php
    session_start();
    $setTitle = "Company";

    if(isset($_SESSION['USER_NAME']) && isset($_SESSION['GROUP_ID']) == 1) {
        include('initial.php');

        $sql = "SELECT * FROM STOCKS";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $do = (isset($_GET['do']) ? $_GET['do'] : 'Manage');

        if($do == "Manage") {

            if(isset($_POST['insert'])) {
                $store_id = $_POST['store_id'];
                $prod_id = $_POST['prod_id'];
                $quant = $_POST['quantity'];

                $errors = array();
                if(empty($store_id)) {
                    $errors['store_id'] = "Store Name is required";
                }
                if(empty($prod_id)) {
                    $errors['prod_id'] = "Product Name is required";
                }
                if(empty($quant)) {
                    $errors['quant'] = "Quantity is required";
                }

                if(empty($errors)){
                    try {
                        $sql2 = "INSERT INTO STOCKS(STORE_ID, PROD_ID, QUANTITY) VALUES(?, ?, ?)";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->execute(array($store_id, $prod_id, $quant));
                        echo "<script>
                            alert('THE ITEM INSERTED');
                            window.open('stock.php', '_self');
                            </script>";
                    }
                    catch(Exception $e) {
                        $error = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                    }
                }
            }


            if(isset($_POST['update'])) {
                $store_id = $_POST['store_id'];
                $prod_id = $_POST['prod_id'];
                $quant = $_POST['quantity'];

                try {
                    $sql2 = "UPDATE STOCKS SET QUANTITY = ?
                                WHERE STORE_ID = ? AND PROD_ID = ?";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->execute(array($quant, $store_id, $prod_id));
                    echo "<script>
                            alert('The ITEM Updated');
                            window.open('stock.php', '_self');
                            </script>";
                }
                catch(Exception $e) {
                    $error_up = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                }
            }


            if(isset($_POST['delete'])) {
                $store_id = $_POST['store_id'];
                $prod_id = $_POST['prod_id'];
                $quant = $_POST['quantity'];

                try {
                    $sql2 = "DELETE FROM STOCKS
                                WHERE STORE_ID = ? AND PROD_ID = ?";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->execute(array($store_id, $prod_id));
                    echo "<script>
                            alert('The ITEM Deleted');
                            window.open('stock.php', '_self');
                            </script>";
                }
                catch(Exception $e) {
                    $error_del = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                }
            }
            ?>



            <h3 class="use-a-lot2 mb-2 mt-5">ADD PRODUCT</h3>
            <form class="form-row company-form" method="POST" action="">
                <?php
                if(isset($error)) echo $error;
                if(isset($error_up)) echo $error_up;
                if(isset($error_del)) echo $error_del;
                ?>
                <div class="form-group col-md-4">
                    <label for="inputEmail4">Store Name</label>
                    <select class="form-control" name="store_id" required>
                    <?php
                        $stmt1 = $conn->prepare("SELECT * FROM STORES");
                        $stmt1->execute();
                        $rows1 = $stmt1->fetchAll();
                        foreach($rows1 as $row1): ?>
                            <option value="<?php echo $row1['STORE_ID'] ?>"><?php echo $row1['STORE_NAME'] ?></option>
                        <?php
                        endforeach;
                    ?>
                    </select>
                    <span class="error">
                        <?php 
                        if(isset($errors['store_id'])) echo '* ' . $errors['store_id']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputPassword4">Product Name</label>
                    <select class="form-control" name="prod_id" required>
                    <?php
                        $stmt2 = $conn->prepare("SELECT * FROM PRODUCTS");
                        $stmt2->execute();
                        $rows2 = $stmt2->fetchAll();
                        foreach($rows2 as $row2): ?>
                            <option value="<?php echo $row2['PROD_ID'] ?>"><?php echo $row2['PROD_NAME'] ?></option>
                        <?php
                        endforeach;
                    ?>
                    </select>
                    <span class="error">
                        <?php 
                        if(isset($errors['prod_id'])) echo '* ' . $errors['prod_id']; 
                        ?>
                    </span>
                </div>

                <div class="form-group col-md-4">
                    <label for="inputPassword4">Quantity</label>
                    <input type="number" class="form-control" min="1" name="quantity" value="1" id="inputPassword4" placeholder="Quantity" required>
                    <span class="error">
                        <?php 
                        if(isset($errors['quant'])) echo '* ' . $errors['quant']; 
                        ?>
                    </span>
                </div>

                <button type="submit" name="insert" class="btn btn-primary">Add</button>
            </form>

            <div class="container" style="margin-top: 100px">
                <hr>
                <h2 class="mb-2">STOCKS: </h2>
                <div class="table-responsive" id='goto'>
                    <table class="table table-bordered text-center">
                        <tr style="background-color: #19283f; color: white">
                            <th>Store Name</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Control</th>
                        </tr>

                        <?php
                        foreach($stmt->fetchAll() as $row): 
                            $stmt_store = $conn->prepare("SELECT * FROM STORES WHERE STORE_ID = ?");
                            $stmt_store->execute(array($row['STORE_ID']));
                            $row_store = $stmt_store->fetch();

                            $stmt_prod = $conn->prepare("SELECT * FROM PRODUCTS WHERE PROD_ID = ?");
                            $stmt_prod->execute(array($row['PROD_ID']));
                            $row_prod = $stmt_prod->fetch();
                        ?>
                            <tr>
                                <td><?php echo $row_store['STORE_NAME'] ?></td>
                                <td><?php echo $row_prod['PROD_NAME'] ?></td>

                                <form action="stock.php" method="POST">
                                    <input type="hidden" name="store_id" value="<?php echo $row['STORE_ID'] ?>">
                                    <input type="hidden" name="prod_id" value="<?php echo $row['PROD_ID'] ?>">
                                    <td><input style="width: 100px;" class="form-control form-control-lg text-center m-auto" type="number" name="quantity" min="1" value="<?php echo $row['QUANTITY'] ?>"></td>
                                    <td>
                                        <button style="width: 60px; margin-top: 20px; margin-left: 10px" type="submit" name="update" class="btn btn-white border-secondary bg-white btn-md">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                        <button type="submit" class="btn confirm mt-3" name="delete" style="background-color: #ff6a00">
                                            Delete
                                        </button>
                                    </td>
                                </form>

                            </tr>
                        <?php
                        endforeach;
                        ?>
                    </table>
                </div>
            </div>
        <?php
        }

        include($tpl . "footer.php");
    }
    else {
        header('Location: index.php');
        exit();
    }

?>