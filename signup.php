<?php
require "DataBase.php";
$db = new DataBase();
if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['direccion']) && isset($_POST['password']) && isset($_POST['COD_CO'])) {
    if ($db->dbConnect()) {
        if ($db->signUp("usuario", $_POST['username'], $_POST['email'], $_POST['direccion'], $_POST['password'], $_POST['COD_CO'])) {
            echo "Sign Up Success";
        } else echo "Sign up Failed";
    } else echo "Error: Database connection";
} else echo "All fields are required";
?>
