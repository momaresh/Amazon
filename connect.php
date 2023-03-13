
<?php

    $dsn = 'sqlsrv:Server=MO_MARESH;DATABASE=Amazon';
    $user = 'Maresh';
    $pass = 'maresh2003';

    try {
        $conn = new PDO($dsn, $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {
        echo 'The connection failed ' . $e; 
    }

