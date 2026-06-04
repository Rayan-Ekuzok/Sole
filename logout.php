<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

session_start();
session_unset();
session_destroy();
session_start();

if (isset($_GET['timeout'])) {
    $_SESSION['timeout_message'] = "Vous avez été déconnecté automatiquement après 10 minutes d'inactivité.";
}

header('Location: login.php');
exit;