<?php
    // WHEN YOU LOGIN TO THE PAGE YOU START THE SESSION
    session_start();
    $setTitle = 'Dashboard';
    if(isset($_SESSION['USER_NAME']) && isset($_SESSION['GROUP_ID']) == 1) {
    include 'initial.php';
        
?>
    <div class="dash">
        <h1 class="text-left ms-5 mt-5 mb-5" style="color: #ff6a00; font-weight: bold;"><i class="fa fa-dashboard"></i>Dashboard</h1>
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="stat">
                        <i class="fa-solid fa-user"></i>
                        Total Users
                        <span><a href="user.php"><?php echo countItems('CUSTOMERS'); ?></a></span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="stat">
                        <i class="fa-solid fa-user-lock"></i>
                        Total Suppliers
                        <span><a href="user.php?do=Manage&page=bind"><?php echo countItems('SUPPLIERS'); ?></a></span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="stat">
                        <i class="fa-solid fa-book"></i>
                        Total Books
                        <span><a href="books.php"><?php echo countItems('BOOKS'); ?></a></span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="stat">
                        <i class="fa-solid fa-computer"></i>
                        Total Computers
                        <span><a href="computers.php"><?php echo countItems('COMPUTERS'); ?></a></span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="stat">
                        <i class="fa-solid fa-mobile"></i>
                        Total Phones
                        <span><a href="phones.php"><?php echo countItems('PHONES'); ?></a></span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="stat">
                        <i class="fa-solid fa-tags"></i>
                        Total Orders
                        <span><a href="orders.php"><?php echo countItems('ORDERS'); ?></a></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="last">
            <div class="container">
                <div class="row">

                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-user"></i>
                                Latest Registerd Users
                            </div>
                            <div class="table-responsive latest">
                                <table class="table table-bordered text-center">
                                    <tr style="background-color: #5c5c5c; color: white">
                                        <th>First_Name</th>
                                        <th>Last_Name</th>
                                        <th>Control</th>
                                    </tr>
                                    <?php 

                                    $lasts = getLatest("CUSTOMERS", "CUS_ID", 5);
                                    foreach($lasts as $last): ?>
                                        <tr>
                                            <td><?php echo $last['FIRST_NAME']; ?></td>
                                            <td><?php echo $last['LAST_NAME']; ?></td>
                                            <td>
                                                <a href="user.php?do=Edit&userid=<?php echo $last['CUS_ID'];?>" class="btn" style="background-color: #4eb67f">Edit</a>
                                                <a href="user.php?do=Delete&userid=<?php echo $last['CUS_ID'];?>" class="btn confirm" style="background-color: #ff6a00">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-book"></i>
                                Latest Registerd Books
                            </div>
                            <div class="table-responsive latest">
                                <table class="table table-bordered text-center">
                                    <tr style="background-color: #5c5c5c; color: white">
                                        <th>Book_Name</th>
                                        <th>Insert_Date</th>
                                        <th>Control</th>
                                    </tr>
                                    <?php 

                                    $statment = $conn->prepare("SELECT top(5) * FROM products WHERE prod_type = 'Books' ORDER BY prod_id DESC");
                                    $statment->execute();
                                    $lasts = $statment->fetchAll();
                                    foreach($lasts as $last): ?>
                                        <tr>
                                            <td><?php echo substr($last['PROD_NAME'],0,30); ?></td>
                                            <td><?php echo $last['INTRODUCE_DATE']; ?></td>
                                            <td>
                                                <a href="books.php?do=Edit&bookid=<?php echo $last['PROD_ID'];?>" class="btn" style="background-color: #4eb67f; margin-bottom: 5px">Edit</a>
                                                <a href="books.php?do=Delete&bookid=<?php echo $last['PROD_ID'];?>" class="btn confirm" style="background-color: #ff6a00">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-computer"></i>
                                Latest Registerd Computers
                            </div>
                            <div class="table-responsive latest">
                                <table class="table table-bordered text-center">
                                    <tr style="background-color: #5c5c5c; color: white">
                                        <th>Copmputer_Name</th>
                                        <th>Insert_Date</th>
                                        <th>Control</th>
                                    </tr>
                                    <?php 

                                    $statment = $conn->prepare("SELECT TOP(5) * FROM PRODUCTS P JOIN  ELECTRONICS E ON P.PROD_ID = E.ELEC_ID WHERE P.PROD_TYPE = 'ELECTRONICS' AND E.ELEC_TYPE = 'COMPUTERS' ORDER BY P.PROD_ID DESC");
                                    $statment->execute();
                                    $lasts = $statment->fetchAll();
                                    foreach($lasts as $last): ?>
                                        <tr>
                                            <td><?php echo substr($last['PROD_NAME'],0,30); ?></td>
                                            <td><?php echo $last['INTRODUCE_DATE']; ?></td>
                                            <td>
                                                <a href="computers.php?do=Edit&compid=<?php echo $last['PROD_ID'];?>" class="btn" style="background-color: #4eb67f; margin-bottom: 5px">Edit</a>
                                                <a href="computers.php?do=Delete&compid=<?php echo $last['PROD_ID'];?>" class="btn confirm" style="background-color: #ff6a00">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-mobile"></i>
                                Latest Registerd Phones
                            </div>
                            <div class="table-responsive latest">
                                <table class="table table-bordered text-center">
                                    <tr style="background-color: #5c5c5c; color: white">
                                        <th>Phone_Name</th>
                                        <th>Insert_Date</th>
                                        <th>Control</th>
                                    </tr>
                                    <?php 

                                    $statment = $conn->prepare("SELECT TOP(5) * FROM PRODUCTS P JOIN  ELECTRONICS E ON P.PROD_ID = E.ELEC_ID WHERE P.PROD_TYPE = 'ELECTRONICS' AND E.ELEC_TYPE = 'PHONES' ORDER BY P.PROD_ID DESC");
                                    $statment->execute();
                                    $lasts = $statment->fetchAll();
                                    foreach($lasts as $last): ?>
                                        <tr>
                                            <td><?php echo substr($last['PROD_NAME'],0,30); ?></td>
                                            <td><?php echo $last['INTRODUCE_DATE']; ?></td>
                                            <td>
                                                <a href="computers.php?do=Edit&compid=<?php echo $last['PROD_ID'];?>" class="btn" style="background-color: #4eb67f; margin-bottom: 5px">Edit</a>
                                                <a href="computers.php?do=Delete&compid=<?php echo $last['PROD_ID'];?>" class="btn confirm" style="background-color: #ff6a00">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-tags"></i>
                                Latest Registerd Orders
                            </div>
                            <div class="table-responsive latest">
                                <table class="table table-bordered text-center">
                                    <tr style="background-color: #5c5c5c; color: white">
                                        <th>Order_Id</th>
                                        <th>User_Name</th>
                                        <th>Product_Name</th>
                                        <th>Quantitiy</th>
                                    </tr>
                                    <?php 

                                    $statment = $conn->prepare("SELECT TOP(5) oi.item_order_id, p.prod_name, oi.quantity, c.last_name FROM order_items oi JOIN products P
                                                                ON oi.item_prod_id = p.prod_id JOIN orders o
                                                                ON oi.item_order_id = o.order_id JOIN CUSTOMERS c 
                                                                ON o.order_cus_id = c.cus_id ORDER BY oi.item_order_id DESC");
                                    $statment->execute();
                                    $lasts = $statment->fetchAll();
                                    foreach($lasts as $last): ?>
                                        <tr>
                                            <td><?php echo $last['item_order_id']; ?></td>
                                            <td><?php echo $last['last_name']; ?></td>
                                            <td><?php echo $last['prod_name']; ?></td>
                                            <td><?php echo $last['quantity']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>


<?php
        include $tpl . 'footer.php';
    }
    else {
        header('location: index.php');
        exit();
    }
?>