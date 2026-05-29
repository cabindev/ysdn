<?php
session_start();

$_SESSION = [];

session_destroy();
header("location: /ysdn/activity/index.php");

?>