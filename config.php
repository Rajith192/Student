<?php
$servername="localhost";
$username="root";
$password="";
$database="grade";
$conn=new mysqli($servername,$username,$password,$database);
if($conn->connect_error)
{
    die("database error".$conn->connect_error);
}
echo "connected successfully !";
?>