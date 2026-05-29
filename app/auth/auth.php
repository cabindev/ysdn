<?php
session_start();

if(!isset($_SESSION['login'])) {
	header("location: /ysdn/app/auth/login.php");
	exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/ysdn/app/auth/csrf.php";
