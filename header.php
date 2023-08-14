<?php
session_start();

// login check
if (!isset($_SESSION['account_id']))
{
    header("Location: login.php");
    exit;
}

require_once ('config.php');

?>