<?php
require "DataBase.php";
$db = new DataBase();
if (isset($_POST['codin']) && isset($_POST['nombGra']) && isset($_POST['gramo']) ) {
    if ($db->dbConnect()) {
        if ($db->addEnvase("envase", $_POST['codin'], $_POST['nombGra'], $_POST['gramo'])) {
            $db->addtien( $_POST['codin'], $_POST['nombGra'], $_POST['gramo']);
            echo "Envase Ingresado";
        } else echo "Fallo al ingresar Envase";
    } else echo "Error: Database connection";
} else echo "All fields are required";
?>