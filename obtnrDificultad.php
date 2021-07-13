<?php
require "DataBase.php";
$db = new DataBase();
if ($db->dbConnect()) {
    echo $db->obtenerDif() ;
} else echo "Error: Database connection";
?>