<?php
require "DataBase.php";
$db = new DataBase();
if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['direccion']) && isset($_POST['password'])) {
    if ($db->dbConnect()) {
        if ($db->signUp("usuario", $_POST['username'], $_POST['email'], $_POST['direccion'], $_POST['password'])) {
            echo "Sign Up Success";
        } else echo $_POST['password'];
    } else echo "Error: Database connection";
} else echo "All fields are required";
?>
