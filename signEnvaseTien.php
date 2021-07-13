<?php
require "DataBase.php";
$db = new DataBase();
if (isset($_POST['codin']) && isset($_POST['coden'])) {
    if ($db->dbConnect()) {
        if ($db->addTientito($_POST['codin'], $_POST['coden'])) {
            echo "Envase Asociado";
        } else echo "Fallo al Asociar";
    } else echo "Error: Database connection";
} else echo "All fields are required";
?>