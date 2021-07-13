<?php
require "DataBase.php";
$db = new DataBase();
if ($db->dbConnect()) {
    echo $db->obtenerIngredientes() ;
} else echo "Error: Database connection";
?>