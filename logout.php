<?php require_once 'functions/init.php' ?>

<?php


session_unset();
session_destroy();
Redirect_to("login.php");


?>
