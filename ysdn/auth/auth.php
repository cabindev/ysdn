<?php
session_start();

if(!isset($_SESSION['login'])) {
	header("location: /ysdn_thailand/ysdn/auth/login.php");
	exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/ysdn/auth/csrf.php";
