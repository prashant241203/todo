<?php 
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "todo";
    
    $conn = new mysqli($servername,$username,$password,$dbname);

    if ($conn->connect_error) {
        echo "error" . $conn->connect_error;    
    }else{
        //echo "done";
    }
?>