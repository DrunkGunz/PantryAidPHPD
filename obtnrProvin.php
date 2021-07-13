<?php
require "DataBase.php";
$db = new DataBase();
$region = $_GET['selectdRegion'];

if ($db->dbConnect()) {
    echo $db->obtenerProvi($region) ;
} else echo "Error: Database connection";

?>