<nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fa-solid fa-cart-shopping me-2" style="font-size: 35px;"></i>Mo_Maresh</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav-li" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="nav-li">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                <a class="nav-link ps-lg-3 active" aria-current="page" href="dashboard.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link ps-lg-3" href="user.php" data-scroll="footer" id="contact">Customers</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link ps-lg-3" href="orders.php" data-scroll="footer" id="contact">Orders</a>
                </li>

                <li class="dropdown nav-item">
                    <button style="background: none; border: none;" class=" prof btn btn-secondary dropdown-toggle" type="button"  data-bs-toggle="dropdown" aria-expanded="false">
                        Products
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="books.php"><i class="fa fa-book" style="margin-right: 5px"></i>Books</a></li>
                        <div class="dropdown-divider"></div>
                        <li><a class="dropdown-item" href="computers.php"><i class="fa fa-computer" style="margin-right: 5px"></i>Computers</a></li>
                        <li><a class="dropdown-item" href="phones.php"><i class="fa fa-mobile" style="margin-right: 5px"></i>Phones</a></li>
                    </ul>
                </li>
                
                <li class="dropdown nav-item">
                    <button style="background: none; border: none;" class=" prof btn btn-secondary dropdown-toggle" type="button"  data-bs-toggle="dropdown" aria-expanded="false">
                        Suppliers
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="company.php"><i class="fa fa-house" style="margin-right: 5px"></i>Company</a></li>
                        <li><a class="dropdown-item" href="person.php"><i class="fa fa-user" style="margin-right: 5px"></i>Person</a></li>
                    </ul>
                </li>

                <li class="dropdown nav-item">
                    <button style="background: none; border: none;" class=" prof btn btn-secondary dropdown-toggle" type="button"  data-bs-toggle="dropdown" aria-expanded="false">
                        Others
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="stores.php"><i class="fa fa-store" style="margin-right: 5px"></i>Stores</a></li>
                        <div class="dropdown-divider"></div>
                        <li><a class="dropdown-item" href="stock.php"><i class="fa-brands fa-stack-overflow" style="margin-right: 5px"></i>Stock</a></li>
                        <div class="dropdown-divider"></div>
                        <li><a class="dropdown-item" href="locations.php"><i class="fa fa-location" style="margin-right: 5px"></i>Locations</a></li>
                        <div class="dropdown-divider"></div>
                        <li><a class="dropdown-item" href="cards.php"><i class="fa-solid fa-credit-card" style="margin-right: 5px" ></i>cards</a></li>
                    </ul>
                </li>

                <li class="dropdown nav-item ms-5 mx-5">
                    <button style="background: none; border: none;" class=" prof btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="img-fluid rounded-circle" style="height: 34px;" src="Themes/IMAGES/img.png" alt="">
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="user.php?do=Edit&userid=<?php echo $_SESSION['CUS_ID'];?>">Edit Profile</a></li>
                        <li><a class="dropdown-item ms-2 btn btn-outline-success rounded-pill" href="logout.php">Sign Out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

