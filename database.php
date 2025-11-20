<?php
$db_server="100.91.122.29";
$db_user="webapp";
$db_pass="strongpass";
$db_name="KentTubedb";
$conn=mysqli_connect($db_server, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>