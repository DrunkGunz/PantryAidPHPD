<?php
require "DataBase.php";
$db = new DataBase();
if ($db->dbConnect()) {
    echo $db->obtenerRegion() ;
} else echo "Error: Database connection";
?>