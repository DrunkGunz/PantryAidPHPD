<?php
require "DataBase.php";
$db = new DataBase();
$provi = $_GET['selectdProvin'];

if ($db->dbConnect()) {
    echo $db->obtenerComuna($provi) ;
} else echo "Error: Database connection";

?>