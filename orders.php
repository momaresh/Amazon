<?php

    session_start();
    $setTitle = 'Orders';

    if (isset($_SESSION['USER_NAME']) && isset($_SESSION['GROUP_ID']) == 1):
        include 'initial.php';
        $do = (isset($_GET['do']) ? $_GET['do'] : 'Manage');

        if(isset($_POST['updateItem'])) {
            $order_id = $_POST['order_id'];
            $prod_id = $_POST['prod_id'];
            $quant = $_POST['quant'];
            $disc = $_POST['disc'];

            try {
                $sql_up = "EXEC UPDATE_ITEM_PROC ?, ?, ?, ?";
                $stmt_up = $conn->prepare($sql_up);
                $stmt_up->execute(array($order_id, $prod_id, $quant, $disc));
                echo "<script>
                    alert(' ITEM UPDATED...!');
                    window.open('orders.php', '_self');
                    </script>";                
            }
            catch(Exception $e) {
                $error_up = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
            }
        }

        if(isset($_POST['deleteItem'])) {
            $order_id = $_POST['order_id'];
            $prod_id = $_POST['prod_id'];

            try {
                $sql_del = "DELETE FROM ORDER_ITEMS WHERE ITEM_ORDER_ID = ? AND ITEM_PROD_ID = ?";
                $stmt_del = $conn->prepare($sql_del);
                $stmt_del->execute(array($order_id, $prod_id));
                echo "<script>
                    alert(' ITEM DELETED...!');
                    window.open('orders.php', '_self');
                    </script>";                
            }
            catch(Exception $e) {
                $error_del = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
            }
        }

        if($do == "Manage") {
            $orders_stmt = $conn->prepare('SELECT * FROM ORDERS ORDER BY ORDER_ID DESC');
            $orders_stmt->execute();
            
            if($orders_stmt->rowCount() == -1):
                $orders = $orders_stmt->fetchAll();
            ?>
            
            <h3 class="use-a-lot2 mb-2 mt-5">
                Orders
            </h3>

            <div class="container">
                <a href="?do=AddOrder" class="btn btn-primary mb-2">ADD ORDER</a>
            </div>

            <?php
                if(isset($error_up)) echo $error_up;
                if(isset($error_del)) echo $error_del;

                foreach($orders as $order):
                    $stmt_cus = $conn->prepare("SELECT LAST_NAME FROM CUSTOMERS WHERE CUS_ID = ?");
                    $stmt_cus->execute(array($order['ORDER_CUS_ID']));
                    $cus = $stmt_cus->fetch();

                    $stmt_item = $conn->prepare("SELECT * FROM ORDER_ITEMS WHERE ITEM_ORDER_ID = ?");
                    $stmt_item->execute(array($order['ORDER_ID']));
                    $items = $stmt_item->fetchAll();

            ?>

                    <section class="h-100 gradient-custom">
                        <div class="container py-5 h-100">
                            <div class="row d-flex justify-content-center align-items-center h-100">
                                <div class="col-lg-10 col-xl-8">
                                    <div class="card" style="border-radius: 10px;">
                                        <div class="card-header px-5 py-4" style="background-color: var(--main-color);">
                                            <h5 class="mb-0" style="color: white;">This order was by, <span style="color: var(--third-color);"><?php echo $cus['LAST_NAME'] ?></span>!</h5>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-0">
                                                <p class="lead fw-normal mb-0" style="color: var(--third-color);">Receipt</p>
                                            </div>
                                        </div>
                                        <div class="card shadow-0 border mb-4">
                                            <?php
                                            foreach($items as $item):
                                                $stmt_prod = $conn->prepare("SELECT * FROM PRODUCTS WHERE PROD_ID = ?");
                                                $stmt_prod->execute(array($item['ITEM_PROD_ID']));
                                                $prod = $stmt_prod->fetch();
                            
                                            ?>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                                        <p class="text-muted mb-0"><?php echo substr($prod['PROD_NAME'], 0, 20) ?></p>
                                                    </div>
                                                    <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                                        <p class="text-muted mb-0 small"><span class="fw-bold me-4">Price:</span> $<?php echo round($item['TOTAL_PRICE'], 2) ?></p>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <p class="text-muted mb-0"><span class="fw-bold me-4">Type:</span><?php echo substr($prod['PROD_TYPE'], 0, 20) ?></p>
                                                    </div>
                                                    <?php
                                                    if($order['STATUS'] == 'ordered'): ?>
                                                        <form action="orders.php" method="POST" class="col-md-6 d-flex justify-content-center align-items-center">
                                                            <input type="hidden" name="order_id" value="<?php echo $item['ITEM_ORDER_ID'] ?>">
                                                            <input type="hidden" name="prod_id" value="<?php echo $item['ITEM_PROD_ID'] ?>">
                                                            <div style="width: 90px">
                                                                <span class="fw-bold me-4">Qty:</span><input class="form-control form-control-lg text-center" type="number" name="quant" value="<?php echo $item['QUANTITY'] ?>">
                                                            </div>
                                                            <div style="width: 90px; margin-left: 10px">
                                                                <span class="fw-bold me-4">Disc:</span><input class="form-control form-control-lg text-center" type="text" name="disc" value="<?php echo round($item['DISCOUNT'], 2) ?>">
                                                            </div>
                                                            <button style="width: 60px; margin-top: 20px; margin-left: 10px" type="submit" name="updateItem" class="btn btn-white border-secondary bg-white btn-md">
                                                                <i class="fas fa-sync"></i>
                                                            </button>

                                                            <button style="width: 60px; margin-top: 20px; margin-left: 10px" type="submit" name="deleteItem" class="btn btn-white border-secondary bg-white btn-md">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php
                                                    elseif($order['STATUS'] == 'buyed'): ?>
                                                        <div class="col-md-2">
                                                            <span class="fw-bold me-4">Qty:</span><?php echo $item['QUANTITY'] ?>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <span class="fw-bold me-4">Disc:</span><?php echo round($item['DISCOUNT'], 2) ?>
                                                        </div>
                                                    <?php
                                                    endif;
                                                    ?>
                                                </div>
                                            </div>
                                            <hr class="mb-4" style="background-color: #e0e0e0; opacity: 1;">
                                            <?php
                                            endforeach;
                                            ?>
                                        </div>
                                            <?php
                                            $stmt_ship = $conn->prepare("SELECT * FROM SHIPPERS WHERE SHIPPER_ID = ?");
                                            $stmt_ship->execute(array($order['ORDER_SHIPPER_ID']));
                                            $ship = $stmt_ship->fetch();

                                            $stmt_loc = $conn->prepare("SELECT * FROM LOCATIONS WHERE LOCATION_ID = ?");
                                            $stmt_loc->execute(array($order['ORDER_LOCATION_ID']));
                                            $loc = $stmt_loc->fetch();

                                            $stmt_store = $conn->prepare("SELECT * FROM STORES WHERE STORE_ID = ?");
                                            $stmt_store->execute(array($order['ORDER_STORE_ID']));
                                            $store = $stmt_store->fetch();
                                            ?>

                                        <div class="d-flex justify-content-between p-2">
                                            <p class="fw-bold mb-0">Order Details</p>
                                            <p class="text-muted mb-0"><span class="fw-bold me-4">Total: </span> $<?php echo $order['TOTAL_PRICE'] ?></p>
                                            <p class="text-muted mb-0"><span class="fw-bold me-4">Invoice Date:</span> <?php echo $order['ORDER_DATE'] ?></p>
                                        </div>

                                        <div class="d-flex justify-content-between p-2">
                                            <p class="text-muted mb-0"><span class="fw-bold me-4">Location:</span> <?php echo $loc['COUNTRY'] . ' ' . $loc['CITY'] ?></p>
                                            <p class="text-muted mb-0"><span class="fw-bold me-4">Shipper:</span> <?php echo $ship['SHIP_NAME'] ?></p>
                                            <p class="text-muted mb-0"><span class="fw-bold me-4">Store:</span> <?php echo $store['STORE_NAME'] ?></p>
                                        </div>
                                        <?php
                                            if($order['STATUS'] == 'ordered'): ?>
                                                <div class="mt-2 mb-2">
                                                <a href="?do=AddItem&id=<?php echo $order['ORDER_ID'];?>" class="btn" style="background-color: #ff6a00; width: fit-content; display: inline">Add Item</a>
                                                <a href="?do=BuyOrder&id=<?php echo $order['ORDER_ID'];?>" class="btn" style="background-color: #4eb67f; width: fit-content; display: inline">Buy</a>
                                                </div>
                                        <?php
                                            elseif($order['STATUS'] == 'buyed'): ?>
                                                <div class="mt-2 mb-2">
                                                <a href="?do=Return&id=<?php echo $order['ORDER_ID'];?>" class="btn" style="background-color: #ff6a00; width: fit-content; display: inline">Return Order</a>
                                                </div>
                                        <?php
                                            endif;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

            <?php
                endforeach;
            else:
                echo '<div class="alert alert-info mt-5" style="width: 50%; margin: auto">THE ORDERS IS EMPTY</div>';
            endif;
        }
        elseif($do == 'AddItem') {
            $orderId = (isset($_GET['id']) ? $_GET['id'] : 0);

            if(isset($_POST['add'])) {
                $prod_id = $_POST['prod_id'];
                $quant = $_POST['quantity'];
                $disc = $_POST['disc'];


                try {
                    $sql2 = "EXEC INSERT_ITEM_PROC ?, ?, ?, ?";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->execute(array($orderId, $prod_id, $quant, $disc));
                    echo "<script>
                        alert(' ITEM ADDED...!');
                        window.open('orders.php', '_self');
                        </script>";
                }
                catch(Exception $e) {
                    $error = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                }
            }

            ?>
            <h3 class="use-a-lot2 mb-2 mt-5">ADD ITEM</h3>
                <form class="form-row company-form" method="POST" action="?do=AddItem&id=<?php echo $orderId; ?>">
                    <?php
                    if(isset($error)) echo $error;
                    ?>
                    <div class="form-group col-md-4">
                        <label for="inputEmail4">Product Name</label>
                        <select class="form-control" name="prod_id" required>
                        <?php
                            $stmt = $conn->prepare("SELECT * FROM PRODUCTS");
                            $stmt->execute();
                            $rows = $stmt->fetchAll();
                            foreach($rows as $row): ?>
                                <option value="<?php echo $row['PROD_ID'] ?>"><?php echo $row['PROD_NAME'] ?></option>
                            <?php
                            endforeach;
                        ?>
                        </select>
                        <span class="error">
                            <?php 
                            if(isset($errors['name'])) echo '* ' . $errors['name']; 
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

                    <div class="form-group col-md-4">
                        <label for="inputAddress">Discount</label>
                        <input type="number" class="form-control" name="disc" id="inputAddress" placeholder="rate %">
                        <span class="error">
                            <?php 
                            if(isset($errors['disc'])) echo '* ' . $errors['disc']; 
                            ?>
                        </span>
                    </div>
                <button type="submit" name="add" class="btn btn-primary">Add</button>
                </form>
            <?php
        }

        elseif($do == 'AddOrder') {
            if(isset($_POST['add'])) {
                $cus_id = $_POST['cus_id'];
                $card_number = $_POST['card_number'];
                $card_pin = $_POST['card_pin'];
                $prod_id = $_POST['prod_id'];
                $quant = $_POST['quantity'];
                $disc = $_POST['disc'];
                $store_id = $_POST['store_id'];
                $loc_id = $_POST['loc_id'];
                $ship_id = $_POST['ship_id'];

                $errors = array();
                if(empty($cus_id)) {
                    $errors['name'] = "The customer is required";
                }
                if(empty($card_number)) {
                    $errors['card'] = "The card number is required";
                }
                if(empty($prod_id)) {
                    $errors['prod'] = "The product is required";
                }
                if(empty($quant)) {
                    $errors['quant'] = "The quantity is required";
                }
                if($disc > 5) {
                    $errors['disc'] = "The discount is not more than 5%";
                }

                if(empty($errors)) {
                    try {
                        $sql2 = "EXEC ADD_ORDER_PROC ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->execute(array($cus_id, $_SESSION['EMP_ID'], $card_number, $card_pin, $prod_id, $quant, $disc, $store_id, $loc_id, $ship_id));
                        echo "<script>
                            alert(' ORDER ADDED...!');
                            window.open('orders.php', '_self');
                            </script>";
                    }
                    catch(Exception $e) {
                        $error = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
                    }                    
                } 
            }

            ?>
            <h3 class="use-a-lot2 mb-2 mt-5">ADD ORDER</h3>
                <form class="form-row company-form" method="POST" action="?do=AddOrder">
                    <?php
                    if(isset($error)) echo $error;
                    ?>
                    <div class="form-group col-md-4">
                        <label for="inputEmail4">Customer Name</label>
                        <select class="form-control" name="cus_id" required>
                        <?php
                            $stmt = $conn->prepare("SELECT * FROM CUSTOMERS ORDER BY FIRST_NAME");
                            $stmt->execute();
                            $rows = $stmt->fetchAll();
                            foreach($rows as $row): ?>
                                <option value="<?php echo $row['CUS_ID'] ?>"><?php echo $row['FIRST_NAME'] . ' '. $row['LAST_NAME'] ?></option>
                            <?php
                            endforeach;
                        ?>
                        </select>
                        <span class="error">
                            <?php 
                            if(isset($errors['name'])) echo '* ' . $errors['name']; 
                            ?>
                        </span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="inputPassword4">Card Number</label>
                        <input type="text" class="form-control" name="card_number" id="inputPassword4" placeholder="1111 2222 3333 4444" required>
                        <span class="error">
                            <?php 
                            if(isset($errors['card'])) echo '* ' . $errors['card']; 
                            ?>
                        </span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="inputPassword4">Card PIN</label>
                        <input type="password" class="form-control" name="card_pin" id="inputPassword4" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="inputEmail4">Product Name</label>
                        <select class="form-control" name="prod_id" required>
                        <?php
                            $stmt2 = $conn->prepare("SELECT * FROM PRODUCTS ORDER BY PROD_NAME");
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
                            if(isset($errors['prod'])) echo '* ' . $errors['prod']; 
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

                    <div class="form-group col-md-4">
                        <label for="inputAddress">Discount</label>
                        <input type="number" class="form-control" name="disc" id="inputAddress" placeholder="rate %">
                        <span class="error">
                            <?php 
                            if(isset($errors['disc'])) echo '* ' . $errors['disc']; 
                            ?>
                        </span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="inputAddress">Store Name</label>                        
                        <select class="form-control" name='store_id'>
                            <?php
                                $store_stmt = $conn->prepare('SELECT * FROM STORES');
                                $store_stmt->execute();
                                $stores = $store_stmt->fetchAll();

                                foreach ($stores as $store):
                            ?>
                                <option value='<?php echo $store['STORE_ID'] ?>'><?php echo $store['STORE_NAME']?></option>
                            <?php
                                endforeach;
                            ?>
                        </select>
                    </div>

                    <div>
                        <label for="">Location</label>
                        <select class="form-control" name='loc_id'>
                            <?php
                                $loc_stmt = $conn->prepare('SELECT * FROM LOCATIONS');
                                $loc_stmt->execute();
                                $locs = $loc_stmt->fetchAll();

                                foreach ($locs as $loc):
                            ?>
                                <option value='<?php echo $loc['LOCATION_ID'] ?>'><?php echo "$loc[COUNTRY] - $loc[CITY]"?></option>
                            <?php
                                endforeach;
                            ?>
                        </select>
                    </div>

                    <div>
                        <label for="">Shipper</label>
                        <select class="form-control" name='ship_id'>
                            <?php
                                $ship_stmt = $conn->prepare('SELECT * FROM SHIPPERS');
                                $ship_stmt->execute();
                                $ships = $ship_stmt->fetchAll();

                                foreach ($ships as $ship):
                            ?>
                                <option value='<?php echo $ship['SHIPPER_ID'] ?>'><?php echo $ship['SHIP_NAME']?></option>
                            <?php
                                endforeach;
                            ?>
                        </select>
                    </div>

                <button type="submit" name="add" class="btn btn-primary">Add</button>
                </form>
            <?php
        }

        elseif($do == 'BuyOrder') {
            $id = (isset($_GET['id']) ? $_GET['id'] : 0);
            try {
                $stmt = $conn->prepare("UPDATE ORDERS SET STATUS= ? WHERE ORDER_ID = ?");
                $stmt->execute(array('buyed', $id));
                echo "<script>
                    alert('THANKS FOR BUYING');
                    window.open('orders.php', '_self');
                    </script>";
            }
            catch(Exception $e) {
                $err = $e->getMessage();
                echo "<script>
                    alert('$err');
                    window.open('orders.php', '_self');
                    </script>";
            }
        }
        elseif($do == 'Return') {
            $id = (isset($_GET['id']) ? $_GET['id'] : 0);
            try {
                $stmt = $conn->prepare("EXEC RETURN_ORDER_PROC ?");
                $stmt->execute(array($id));
                echo "<script>
                    alert('The order returned');
                    window.open('orders.php', '_self');
                    </script>";
            }
            catch(Exception $e) {
                $err = $e->getMessage();
                echo "<script>
                    alert('$err');
                    window.open('orders.php', '_self');
                    </script>";
            }
        }

        include($tpl . 'footer.php');

    else:
        header('location: admin/index.php');
        exit();
    endif;
?>