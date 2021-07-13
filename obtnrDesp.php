<?php
require "DataBase.php";
$db = new DataBase();

$codusu = $_GET['codU'];

if ($db->dbConnect()) {
    echo $db->obtenerDesp($codusu) ;
} else echo "Error: Database connection";

?>