<?php
require "DataBase.php";
$db = new DataBase();
if (isset($_POST['username']) && isset($_POST['cantidad']) && isset($_POST['stockmin']) && isset($_POST['cod_tien'])) {
    if ($db->dbConnect()) {
        if ($db->addDesp("despensa", $_POST['username'], $_POST['cantidad'], $_POST['stockmin'], $_POST['cod_tien'])) {
            echo "Ingrediente añadido";
        } else echo "Fallo al añadir ingrediente";
    } else echo "Error: Database connection";
} else echo "All fields are required";
?>