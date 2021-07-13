<?php
require "DataBase.php";
$db = new DataBase();
$ingrediente = $_GET['selectIngrediente'];
if ($db->dbConnect()) {
    echo $db->obtenerEnvase($ingrediente);
} else echo "Error: Database connection";

?>