<?php
session_start();

if(!isset($_SESSION['login'])) {
	header("location: /ysdn/app/auth/login.php");
	exit;
}

require_once __DIR__ . "/../../app/auth/csrf.php";
