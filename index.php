<?php
    // WHEN YOU LOGIN TO THE PAGE YOU START THE SESSION
    session_start();

    //THIS IF YOU ALREADY SIGN AND YOU ARE ADMIN, WILL CHANGE YOU TO THE DASHBOARD AUTOMATICALLY
    if (isset($_SESSION['USER_NAME']) && isset($_SESSION['GROUP_ID']) == 1) {
        header('Location: dashboard.php');
        exit();
    }

    $noNavbar = ''; // THIS VARIABLE PREVENT THE PAGE FROM THE NAVBAR TO BE INCLUDE
    $setTitle = 'Sign'; // THIS VARIABLE MAKE THE TITLE HEADER OF THE PAGE, WE HAVE MAKE FUNCTION TO DO THIS

    include 'initial.php'; // THIS TO INCLUDE ALL WE NEED LIKE THE HEADER, ROOTS, CONNECTION

    // IF THERE IS A USER COMING FROM POST REQUEST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $v_email = $_POST['email'];
        $v_password = $_POST['password'];


        // CHECK IF THE USER EXISTS IN THE DATABASE
        $stmt = $conn->prepare("SELECT * FROM EMPLOYEES WHERE EMAIL = ? AND PASSWORD = ?");
        $stmt->execute(array($v_email, $v_password));
        $row = $stmt->fetch();
        
        if ($stmt->rowCount() == -1) {
            // IF I'M EXIST I WILL MAKE A SESSION 
            $_SESSION['USER_NAME'] = $row['EMAIL']; // WE ADD SESSION FOR THE USER
            $_SESSION['EMP_ID'] = $row['EMPLOYEE_ID'];
            $_SESSION['GROUP_ID'] = $row['GROUP_ID'];
            header('Location: dashboard.php');
            exit();
        }
    }
?>
    <!-- start sign in -->
    <div class="signin">
        <form class="fill" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST"> <!-- $_SERVER['PHP_SELF'], To make it go to the same page -->
            <i class="fa-solid fa-fingerprint"></i>

            <div class="user">
                <input type="text"  name="email" id="user_name" placeholder="Your email" autocomplete="off" required>
                <i class="fa-solid fa-user"></i>
            </div>

            <div class="pass">
                <input type="password" name="password" id="pass" placeholder="Your password" required>
                <i class="fa-solid fa-lock"></i>
            </div>
            
            <input type="submit" value="Sign in" class="btn btn-outline-success rounded-pill fw-bold fs-5">
            <p>Create new account? <a href="signup.php">Sign up</a></p> <!-- If you don't have an account create new account -->
        </form>
    </div>
    <!-- end sign in -->

<?php include $tpl . 'footer.php' // Include the footer with all its links ?>