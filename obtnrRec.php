<?php
require "DataBase.php";
$db = new DataBase();

$codusu = $_GET['codU'];

if ($db->dbConnect()) {
    echo $db->obtenerRecet($codusu) ;
} else echo "Error: Database connection";

?>