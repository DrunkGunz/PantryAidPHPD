<?php
require "DataBase.php";
$db = new DataBase();
if (isset($_POST['username']) && isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['prep']) && isset($_POST['codDF'])) {
    if ($db->dbConnect()) {
        if ($db->addRec("receta", $_POST['username'], $_POST['nombre'], $_POST['descripcion'], $_POST['prep'], $_POST['codDF'])) {
            echo "Receta Creada";
        } else echo "Fallo al crear receta";
    } else echo "Error: Database connection";
} else echo "All fields are required";
?>