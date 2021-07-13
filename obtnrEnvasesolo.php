<?php
require "DataBase.php";
$db = new DataBase();
if ($db->dbConnect()) {
    echo $db->obtenerEnvasesolo();
} else echo "Error: Database connection";
?>