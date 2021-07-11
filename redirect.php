<?php
require "DataBase.php";
$db = new DataBase();
$username = $_GET['slcU'];
if (isset($username)) {
    if ($db->dbConnect()) {
        if ($db->reDirect($username)) {
            echo "pantry";
        } else echo "fillpantry";
    } else echo "Error: Database connection";
} else echo "All fields are required";
?>