<?php
session_start();

if(!isset($_SESSION['login'])) {
	header("location: /ysdn/auth/login.php");
	exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/auth/csrf.php";
