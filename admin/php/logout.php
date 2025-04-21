<?php
    session_start();
    session_destroy();
    header("Location: ../../php/login-admin.php");
    exit();
?>